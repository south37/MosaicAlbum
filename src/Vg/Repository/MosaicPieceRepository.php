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
                        image.resize_image_path
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
}
