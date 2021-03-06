<?php
namespace Vg\Repository;

use Vg\Model\AlbumUser;

class AlbumUserRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * ゴールイメージIDで関連するユーザのFBアイコンパスを検索する
     * @param  $goalImageId
     * @return fbIconPath[]
     */
    public function getFbIconPathList($goalImageId, &$container)
    {
        # Facebookアイコンを保存したパスのリスト
        $fbIconPathList = [];

        # Repository
        $albumRepository = $container['repository.album'];
        $userRepository = $container['repository.user'];
        $fbHelper = $container['FBHelper'];

        # ゴールイメージIDでユーザIDリストを取得
        $userIdList = $albumRepository->getUserIdList($goalImageId);
        foreach ($userIdList as $userId) {
            # ユーザIDでFacebookアイコンURLを取得
            $fbIconUrl = $userRepository->getUserIconImgUrl($userId);
            //$fbIconPathList[$userId] = $fbIconUrl;
          
            # FacebookアイコンURLからFacebookアイコンパスを取得
            $fbIconPath = $fbHelper->downloadImageWithUserId($fbIconUrl, $userId);
            # FacebookアイコンパスをFacebookアイコンパスリストに追加
            $fbIconPathList[$userId] = $fbIconPath;
            
        }
        return $fbIconPathList;
    }

    /**
     * ゴールイメージIDで関連するユーザのメールアドレスを検索する
     * @param  $goalImageId
     * @return mailAddress[]
     */
    public function getMailAddressList($goalImageId, &$container)
    {
        # メールアドレスリスト
        $mailAddressList = [];

        # Repository
        $albumRepository = $container['repository.album'];
        $userRepository = $container['repository.user'];

        # ゴールイメージIDでユーザIDリストを取得
        $userIdList = $albumRepository->getUserIdList($goalImageId);
        foreach ($userIdList as $userId) {
            # ユーザIDでメールアドレスを取得
            $mailAddress = $userRepository->getMailAddress($userId);
            # メールアドレスをメールアドレスリストに追加
            $mailAddressList[$userId] = $mailAddress;
        }
        return $mailAddressList;
    }
}
