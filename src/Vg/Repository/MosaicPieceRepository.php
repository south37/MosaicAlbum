<?php
namespace Vg\Repository;

use Vg\Model\MosaicPiece;

class MosaicPieceRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * ゴールイメージIDでモザイクピースを検索する
     * @param  $goalImageId
     * @return MosaicPiece[]
     */
    public function getMosaicPieceList($goalImageId)
    {
        $sql = "SELECT  album.user_id,
                        album_image.x,
                        album_image.y,
                        image.fb_image_id,
                        album_image.image_id
                FROM    album,
                        album_image,
                        image
                WHERE   album.goal_image_id = :goalImageId AND 
                        album.id = album_image.album_id AND 
                        album_image.image_id = image.id";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $mosaicPieces = [];
        while($data = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            $mosaicPiece = new MosaicPiece();
            $mosaicPiece->setProperties($data);
            array_push($mosaicPieces, $mosaicPiece);
        }
        return $mosaicPieces;
    }
    
    /**
     * ゴールイメージIDで画像のパスを全て取得する
     * @param  $goalImageId
     * @return MosaicPiece[image_id => resize_image_path]
     */
    public function getResizeImagePathList($goalImageId)
    {
        $sql = <<< SQL
            SELECT DISTINCT album_image.image_id, resize_image_path FROM image
                INNER JOIN album_image
                    ON image_id = image.id
            WHERE
                is_used_mosaic = 1 AND
                album_image.album_id IN
                    (SELECT album.id FROM album
                    WHERE album.goal_image_id = :goalImageId);
SQL;
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $resizeImagePathList = [];
        while($data = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            $resizeImageId = $data['image_id'];
            $resizeImagePath = $data['resize_image_path'];
            $resizeImagePathList[$resizeImageId] = $resizeImagePath;
        }
        return $resizeImagePathList;
    }
}
