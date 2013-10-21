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
        $albumImageRepository = $container['repository.albumImage'];
        $imageRepository = $container['repository.image'];
        $fbHelper = $container['FBHelper'];

        // ゴールイメージIDに関連するアルバムIDのリスト
        $albumIdList = $albumRepository->getAlbumIdList($goalImageId);
        $albumId2imageId_fbImageId = [];
        $albumImageUrlList = [];
        foreach ($albumIdList as $albumId) {
            // アルバムIDに関連するイメージURLのリスト
            $imageUrlList = $fbHelper->getImagesInAlbum($albumId);
            // アルバムIDに関連するイメージIDのリスト
            $imageIds = $albumImageRepository->getImageIdList($albumId);
            // イメージIDとFacebookイメージIDの連想配列
            $imageId2fbImageId = $imageRepository->getFbImageIdList($imageIds);
            // アルバムIDと（イメージIDとFacebookイメージIDの連想配列）の連想配列
            $i = 0;
            foreach ($imageId2fbImageId as $fbImageId) {
                $imagePath = $fbHelper->downloadImage($imageUrlList[$i])
                $albumId2imageId_fbImageId[$albumId][] = ["path"=>$imagePath, "id"=>$fbImageId];
                $i++;
            }
        }
        return $albumId2imageId_fbImageId;
    }
}