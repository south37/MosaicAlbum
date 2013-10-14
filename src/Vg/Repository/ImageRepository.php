<?php
namespace Vg\Repository;

use Vg\Model\Image;

class ImageRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * 新規登録
     * @param  $fbImageId
     * @return $imageId 
     */
    public function insert($fbImageId)
    {
        $sql = "INSERT INTO image SET fb_image_id = :fbImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':fbImageId', $fbImageId, \PDO::PARAM_STR);
        $sth->execute();
        // insertされたカラムのIDを取得する
        $imageId = $this->getLatestId();
        return $imageId;
    }

    /**
    * 最後のimageカラムのIDを取得する
    * @return $imageID
    */
    private function getLatestId()
    {
        // IDを降順にして取得
        $sql = "SELECT * FROM image ORDER BY id DESC";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        // 最初の一個目を取得
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return $data['id'];
    }

    /**
     * 更新（リサイズイメージパスを追加）
     * @param  $resizeImagePath
     */
    public function update($resizeImagePath)
    {
        // 更新するカラムのIDを取得する
        $imageId = $this->getLatestId();        
        $sql = "UPDATE image SET resize_image_path = :resizeImagePath WHERE id = :imageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':resizeImagePath', $resizeImagePath, \PDO::PARAM_STR);
        $sth->bindValue(':imageId', $imageId, \PDO::PARAM_INT);
        $sth->execute();
        return $imageId;
    }
}