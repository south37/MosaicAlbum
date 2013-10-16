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
    public function getUsedImageList($goalImageId)
    {
        $albumRepository = new AlbumRepository();
        $albumImageRepository = new AlbumImageRepository();
        $imageRepository = new ImageRepository();
        // ゴールイメージIDに関連するアルバムIDのリスト
        $albumIds = $albumRepository->getAlbumIdList($goalImageId)
        foreach ($albumIds as $albumId) {
            // アルバムIDに関連するイメージIDのリスト
            $imageIds = $albumImageRepository->getImageIdList($albumId);
            // イメージIDとFacebookイメージIDの連想配列
            $imageId2fbImageId = $imageRepository->getFbImageIdList($imageIds);
            // アルバムIDと（イメージIDとFacebookイメージIDの連想配列）の連想配列
            foreach ($imageId2fbImageId as $fbImageId) {
                $albumId2imageId_fbImageId[$albumId][] = $fbImageId;
            }
        }
        return $albumId2imageId_fbImageId;
    }
}