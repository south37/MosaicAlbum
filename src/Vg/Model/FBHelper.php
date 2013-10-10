<?php
require_once(dirname(__FILE__).'facebook-php-sdk/src/facebook.php')

namespace Vg\Model;

use Vg\Model\Stretcher;

class FBHelper
{
    const APP_ID     = '344617158898614';
    const APP_SECRET = '6dc8ac871858b34798bc2488200e503d';
    
    private facebook;

    public function __construct()
    {
        $this->facebook = new Facebook([
            'appId'  => APP_ID,
            'secret' => APP_SECRET,
        ]);
    }

    public function getUserId()
    {
        return $this->facebook->getUser();
    }

    public function getLoginUrl()
    {
        $scope = 'user_photos, frinends_photos';
        return $this->facebook->getLoginUrl($scope);
    }
}
