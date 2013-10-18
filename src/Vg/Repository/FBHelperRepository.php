<?php
namespace Vg\Repository;

require_once(dirname(__FILE__).'/facebook-php-sdk/src/facebook.php');
use Facebook;

class FBHelperRepository
{
    const APP_ID     = '638792116141666';
    const APP_SECRET = '3fd441f744cca9929774227d058690e2';
    
    public $facebook;
    private $userId;

    public function __construct()
    {
        $this->facebook = new Facebook([
            'appId'  => self::APP_ID,
            'secret' => self::APP_SECRET,
        ]);

        $this->userId = $this->facebook->getUser();
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getLoginUrl()
    {
        $scope = 'user_photos,friends_photos';
        $loginUrl = $this->facebook->getLoginUrl(['scope' => $scope]);
        
        return $loginUrl;
    }

    public function getUserProfile()
    {
        try {
            //            $this->facebook->setExtendedAccessToken();
            $me = $this->facebook->api('/'.$this->getUserId().'?locale=ja_JP');
        } catch (FacebookApiException $e) {
            return [];
        }
        
        $userProfile = [
            'fb_user_id'  => $this->userId,
            'token'       => $this->facebook->getAccessToken(),
            'name'        => $me['name'],
            'fb_icon_url' => 'https://graph.facebook.com/'.$me['id'].'/picture',
        ];
       
        return $userProfile;
    }

    public function getAlbums()
    {
        try {
            $fbAlbums = $this->facebook->api('/'.$this->userId.'/albums')['data'];

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
        try {
            $fbImages = $this->facebook->api('/'.$albumId.'/photos', 'GET')['data'];
        } catch (FacebookApiException $e) {
            return [];
        }

        $images = [];
        foreach($fbImages as $fbImage) {
            $image = [
                'id'        => $fbImage['id'],
                'imagePath' => $fbImage['source'],
            ];
            array_push($images, $image);
        }
   
        return $images;
    }

    public function getFriends()
    {
        try {
            $fbFriends = $this->facebook->api('/'.$this->userId.'/friends')['data'];
        } catch (FacebookApiException $e) {
            return [];
        }

        $friends = [];
        foreach($fbFriends as $fbFriend) {
            $friend = [
                'id'            => $fbFriend['id'],
                'name'          => $fbFriend['name'],
                'iconImagePath' => 'https://graph.facebook.com/'.$fbFriend['id'].'/picture',
            ];
            array_push($friends, $friend);
        }
 
        return $friends;
    }

      // facebook 埋め込みのページでないと通知は出来ない模様
//    public function notify($friendId)
//    {
//        $data = [
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

    public function downloadImage($imagePath) {
        $image = file_get_contents($imagePath);
        $savePath = '/img/resource_img/'.basename($imagePath);

        file_put_contents($savePath, $image);

        return $savePath;
    }

    public function deleteImage($imagePath) {
        unlink($imagePath);
    }

}
