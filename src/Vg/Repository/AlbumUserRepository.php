<?php
namespace Vg\Repository;

use Vg\Model\AlbumImage;

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
            $fbIconUrl = $userRepository->getUserIconImgUrlList($userId);
            # FacebookアイコンURLからFacebookアイコンパスを取得
            $fbIconPath = $fbHelper->downloadImage($fbIconUrl);
            # FacebookアイコンパスをFacebookアイコンパスリストに追加
            array_push($fbIconPathList, $fbIconPath);
        }
        return $fbIconPathList;
    }
}
