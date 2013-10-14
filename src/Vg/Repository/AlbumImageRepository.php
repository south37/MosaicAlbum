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
    public function insert($albumId, $imageId, $x, $y)
    {
        $sql = "INSERT INTO album_image
                SET album_id = :albumId,
                    image_id = :imageId,
                    x = :x,
                    y = :y,
                    is_used_mosaic = :isUsedMosaic";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':albumId', $albumId, \PDO::PARAM_INT);
        $sth->bindValue(':imageId', $imageId, \PDO::PARAM_INT);
        $sth->bindValue(':x', $x, \PDO::PARAM_INT);
        $sth->bindValue(':y', $y, \PDO::PARAM_INT);
        $sth->bindValue(':isUsedMosaic', FALSE, \PDO::PARAM_BOOL);
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
}