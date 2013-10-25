<?php
namespace Vg\Repository;

use Vg\Model\UsedImage;

class UsedImageRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * ゴールイメージIDで関連する画像を検索する
     * @param  $goalImageId
     * @return x[albumId][0~n-1(imageId)][fbImageId]
     */
    public function getUsedImageList($goalImageId, &$container)
    {
        $albumRepository = $container['repository.album'];
        $fbHelper = $container['FBHelper'];
        // ゴールイメージIDに関連するアルバムIDのリスト
        $albumIdList = $albumRepository->getAlbumIdList($goalImageId);
        $albumId2imageId_fbImageId = [];
        foreach ($albumIdList as $albumId) {
            // アルバムIDに関連するFacebookアルバムIDを取得
            $fbAlbumId = $albumRepository->getFbAlbumId($albumId);
            // FacebookアルバムIDから関連するFacebookイメージリストを取得
            $fbImageList = $fbHelper->getImagesInAlbum($fbAlbumId);
            foreach ($fbImageList as $fbImage) {
                // FacebookイメージIDからイメージPathを取得
                $imagePath = $fbHelper->downloadImageFromFbId($fbImage['id']);
                // リストに追加
                $albumId2imageId_fbImageId[$albumId][] = ["path"=>$imagePath, "id"=>$fbImageId];
            }
        return $albumId2imageId_fbImageId;
    }
}
