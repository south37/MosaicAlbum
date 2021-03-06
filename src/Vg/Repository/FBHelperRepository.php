<?php
namespace Vg\Repository;

require_once(dirname(__FILE__).'/facebook-php-sdk/src/facebook.php');
use Facebook;

class FBHelperRepository
{
    const APP_ID     = '638792116141666';
    const APP_SECRET = '3fd441f744cca9929774227d058690e2';
    
    private $facebook;
    private $userRepository;
    private $session;
    
    private $userId;
    private $accessToken;

    public function __construct($container)
    {
        $this->facebook = new Facebook([
            'appId'  => self::APP_ID,
            'secret' => self::APP_SECRET,
        ]);

        $this->userRepository = $container['repository.user'];
        $this->session        = $container['session'];
    }

    public function destroySession() {
        $this->facebook->destroySession();
    }

    public function getUserId()
    {
        if (isset($this->userId)) {
            return $this->userId;
        }

        $userId = $this->session->get('user.id');
        if ($userId !== NULL) {
            return $this->userId = $userId;
        } else {
            return $this->userId = $this->facebook->getUser();
        }
    }

    public function getLoginUrl()
    {
        $params = [
            'scope'        => 'user_photos,friends_photos',
            'redirect_uri' => 'http://dev.mosaicalbum.me/login_process',
            'display'      => 'popup'
        ];
        $loginUrl = $this->facebook->getLoginUrl($params);
        
        return $loginUrl;
    }

    public function getUserProfileForRegistration()
    {
        try {
            $this->facebook->setExtendedAccessToken();
            $me = $this->facebook->api('/'.$this->getUserId().'?locale=ja_JP');
        } catch (FacebookApiException $e) {
            return [];
        }
        
        $userProfile = [
            'fb_user_id'  => $this->getUserId(),
            'token'       => $this->facebook->getAccessToken(),
            'name'        => $me['name'],
            'fb_icon_url' => 'https://graph.facebook.com/'.$me['id'].'/picture',
        ];
       
        return $userProfile;
    }

    private function setAccessToken()
    {
        if (!isset($this->accessToken)) {
            $user = $this->userRepository->findByFbId($this->getUserId());
            
            $this->accessToken = $user->token;
        }

        $this->facebook->setAccessToken($this->accessToken);
    }

    public function getAlbums()
    {
        $this->setAccessToken();

        try {
            $fbAlbums = $this->facebook->api('/'.$this->getUserId().'/albums')['data'];

        } catch (FacebookApiException $e) {
            return [];
        }

        $albums = [];
        foreach($fbAlbums as $fbAlbum) {
            if (array_key_exists('cover_photo', $fbAlbum)) {
                $r = $this->facebook->api('/'.$fbAlbum['cover_photo'], 'GET');
                $thumbnailPath = $r['picture'];
            } else {
                $thumbnailPath = '';
            }

            $album = [
                'id'            => $fbAlbum['id'],
                'name'          => $fbAlbum['name'],
                'thumbnailPath' => $thumbnailPath,
            ];
            array_push($albums, $album);
        }
        
        return $albums;
    }

    public function getImagesInAlbum($albumId)
    {
        $this->setAccessToken();
        
        try {
            $fbImages = $this->facebook->api('/'.$albumId.'/photos?limit=100', 'GET')['data'];
        } catch (FacebookApiException $e) {
            return [];
        }

        $images = [];
        foreach($fbImages as $fbImage) {
            $image = [
                'id'            => $fbImage['id'],
                'imagePath'     => $fbImage['source'],
                'thumbnailPath' => $fbImage['picture'],
            ];
            array_push($images, $image);
        }
   
        return $images;
    }

    public function getRequestUrl($fbGoalImageId)
    {
        return 'http://www.facebook.com/dialog/send?app_id='.self::APP_ID.
            '&link=http://mosaicalbum.me/'.'400192746777237'.
            '&redirect_uri=http://mosaicalbum.com/album_viewer';
    }

//    public function getFriends()
//    {
//        $this->setAccessToken();
//        
//        try {
//            $fbFriends = $this->facebook->api('/'.$this->getUserId().'/friends')['data'];
//        } catch (FacebookApiException $e) {
//            return [];
//        }
//
//        $friends = [];
//        foreach($fbFriends as $fbFriend) {
//            $friend = [
//                'id'            => $fbFriend['id'],
//                'name'          => $fbFriend['name'],
//                'iconImagePath' => 'https://graph.facebook.com/'.$fbFriend['id'].'/picture',
//            ];
//            array_push($friends, $friend);
//        }
// 
//        return $friends;
//    }

      // facebook 埋め込みのページでないと通知は出来ない模様
//    public function notify($friendId)
//    {
//       $this->setAccessToken();
//       
//       $data = [
//            'href'         => '//mosaicalbum.com/guest/start_guest',
//            'access_token' => $this->facebook->getAccessToken(),
//            'template'     => 'アルバムの作成に招待されました'
//        ];
//
//        try {
//            $this->facebook->api("/".$friendId."/notifications", 'POST', $data);
//        } catch (FacebookApiException $e) {
//            error_log($e);
//        }
//    }

    public function downloadImage($imagePath)
    {
        $image = file_get_contents($imagePath);
        $folderPath = 'img/resource_img/'.$this->session->get('goalImageId');
    
        //var_dump($folderPath);
        if ( !is_dir($folderPath) ) {
            //mkdir($folderPath, 0775);
            mkdir($folderPath, 0777);
        }

        $savePath = $folderPath.'/'.basename($imagePath);
        if (basename($imagePath) === "picture") {
            $savePath = $folderPath.'/'.basename($imagePath).$this->userId.'.jpg';
        }

        if(file_exists($savePath)===FALSE) { 
          file_put_contents($savePath, $image);
        }
        return $savePath;
    }
    
    public function downloadImageWithUserId($imagePath, $userId)
    {
        $image = file_get_contents($imagePath);
        $folderPath = 'img/resource_img/'.$this->session->get('goalImageId');
    
        //var_dump($folderPath);
        if ( !is_dir($folderPath) ) {
            //mkdir($folderPath, 0775);
            mkdir($folderPath, 0777);
        }

        $savePath = $folderPath.'/'.basename($imagePath);
        if (basename($imagePath) === "picture") {
            $savePath = $folderPath.'/'.basename($imagePath).$userId.'.jpg';
        }

        if(file_exists($savePath)===FALSE) { 
          file_put_contents($savePath, $image);
        }
        return $savePath;
    }

    public function deleteImage($imagePath)
    {
        unlink($imagePath);
    }

    public function downloadImageFromFbId($fbImageId)
    {
        $r = $this->facebook->api('/'.$fbImageId);
        $imagePath = $r['source'];
        return $this->downloadImage($imagePath);
    }

    //add by 1000ch
    public function getImagePath($fbImageId)
    {
      $r = $this->facebook->api('/'.$fbImageId);
      return $r['source'];
    }

}
