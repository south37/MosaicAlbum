<?php
namespace Vg\Repository;

use Vg\Model\AlbumImage;

class AlbumImageRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * 新規登録
     * @param  $albumId
     * @param  $imageId
     * @param  $x
     * @param  $y
     * @return $albumImageId 
     */
    public function insert($albumId, $imageId, $x, $y, $isUsedMosaic)
    {
        $sql = "INSERT INTO album_image
                SET album_id = :albumId,
                    image_id = :imageId,
                    x = :x,
                    y = :y,
                    is_used_mosaic = :isUsedMosaic,
                    is_latest = 1";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':albumId', $albumId, \PDO::PARAM_INT);
        $sth->bindValue(':imageId', $imageId, \PDO::PARAM_INT);
        $sth->bindValue(':x', $x, ($x !== NULL) ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $sth->bindValue(':y', $y, ($y !== NULL) ? \PDO::PARAM_INT : \PDO::PARAM_NULL);
        $sth->bindValue(':isUsedMosaic', $isUsedMosaic, \PDO::PARAM_INT);
        $sth->execute();
        // insertされたカラムのIDを取得する
        $albumImageId = $this->getLatestId();
        return $albumImageId;
    }

    /**
    * 最後のalbumImageカラムのIDを取得する
    * @return $albumImageID
    */
    private function getLatestId()
    {
        // IDを降順にして取得
        $sql = "SELECT * FROM album_image ORDER BY id DESC";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        // 最初の一個目を取得
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return $data['id'];
    }

    /**
     * アルバムIDで関連するイメージIDを検索する
     * @param  $albumId
     * @return imageId[]
     */
    public function getImageIdList($albumId)
    {
        $sql = "SELECT * FROM album_image WHERE album_id = :albumId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':albumId', $albumId, \PDO::PARAM_INT);
        $sth->execute();
        $imageIds = [];
        while($data = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            array_push($imageIds, $data['image_id']);
        }
        return $imageIds;
    }

    /**
    * mosaic_viewerで表示する対象になっている画像を非対象にする
    * @return {boolean} updateが成功ならtrue, 失敗ならfalse
    */
    public function modifyIsLatest()
    {
        $sql = <<< SQL
            UPDATE album_image SET 
                is_latest = 0
            WHERE is_latest = 1;
SQL;
        $sth = $this->db->prepare($sql);
        return $sth->execute();
    }
}
