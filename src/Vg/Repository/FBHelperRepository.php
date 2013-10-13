<?php
require_once(dirname(__FILE__).'facebook-php-sdk/src/facebook.php')

namespace Vg\Repository;

class FBHelperRepository
{
    const APP_ID     = '638792116141666';
    const APP_SECRET = '3fd441f744cca9929774227d058690e2';
    
    private $facebook;

    public function __construct()
    {
        $this->facebook = new Facebook([
            'appId'  => APP_ID,
            'secret' => APP_SECRET,
        ]);
    }

    public function getUserId()
    {
        $userId = $this->facebook->getUser();
    
        if ($userId) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $this->$facebook->api('/me');
            } catch (FacebookApiException $e) {
                error_log($e);
                $userId = 0;
            }
        }
   
        return $userId;
    }

    public function getLoginUrl()
    {
        $scope = 'user_photos,frinends_photos';
        $loginUrl = $this->facebook->getLoginUrl(['scope' => $scope]);
  
        return $loginUrl;
    }

    public function getUserProfile()
    {
        $this->facebook->setExtendedAccessToken();
        
        $me = $this->facebook->api('/me?locale=ja_JP');
        
        $userProfile = [
            'fb_user_id'  => $me['id'],
            'token'       => $this->facebook->getAccessToken(),
            'name'        => $me['name'],
            'fb_icon_url' => 'https://graph.facebook.com/'.$me['id'].'/picture',
        ]
       
        return $userProfile;
    }

    public function getAlbums()
    {
        $fbAlbums = $this->facebook->api('/me/albums', 'GET')['data'];
      
        $albums = [];
        foreach($fbAlbums as $fbAlbum) { 
            $r = $this->facebook->api('/'.$fbAlbum['id'].'/picture?redirect=false', 'GET');
            $thumbnailPath = $r['data']['url'];

            $album = [
                'id'            => $fbAlbum['id']
                'name'          => $fbAlbum['name'],
                'thumbnailPath' => $thumbnailPath,
            ];

            array_push($albums, $album);
        }
     
        return $albums;
    }

    public function getImagesInAlbum($albumId)
    {
        $fbImages = $this->facebook->api('/'.$albumId.'/photos', 'GET')['data'];
    
        $images = [];
        foreach($fbImages as $fbImage) {
            $image = [
                'id'        => $fbImage['id'],
                'imagePath' => $fbImage['picture'],
            ];
            array_push($images, $image);
        }
   
        return $images;
    }

    public function getFriends()
    {
        $fbFriends = $this->facebook->api('/me/friends');
  
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

    public function notify($friendId)
    {
        $data = [
            'href'         => '//mosaicalbum.com/guest/start_guest',
            'access_token' => $this->facebook->getAccessToken(),
            'template'     => 'アルバムの作成に招待されました'
        ];

        try {
            $this->facebook->api("/".$to_userId."/notifications", 'POST', $data);
        } catch (FacebookApiException $e) {
            error_log($e);
        }
    }
}
