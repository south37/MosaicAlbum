<?php
namespace Vg\Repository;

use Vg\Model\Album;

class AlbumRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    /**
     * 新規登録
     * @param  Album
     * @return $albumId 
     */
    public function insert($album)
    {
        $sql = "INSERT INTO album
                SET user_id = :userId,
                    session_id = :sessionId
                    fb_album_id = :fbAlbumId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':userId', $album->user_id, \PDO::PARAM_INT);
        $sth->bindValue(':sessionId', $album->session_id, \PDO::PARAM_INT);
        $sth->bindParam(':fbAlbumId', $album->fb_album_id, \PDO::PARAM_STR);
        $sth->execute();
        // insertされたカラムのIDを取得する
        $albumId = $this->getLatestId();
        return $albumId;
    }

    /**
    * 最後のalbumカラムのIDを取得する
    * @return $albumID
    */
    private function getLatestId()
    {
        // IDを降順にして取得
        $sql = "SELECT * FROM album ORDER BY id DESC";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        // 最初の一個目を取得
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return $data['id'];
    }

    /**
     * ゴールイメージIDでアルバムIDを検索
     * @param  $goalImageId
     * @return albumId[]
     */
    public function getAlbumIdList($goalImageId)
    {
        $sql = "SELECT * FROM album WHERE goal_image_id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $albumIds = [];
        while($data = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            array_push($albumIds, $data['id']);
        }
        return $albumIds;
    }

    /**
     * ゴールイメージIDでFacebookアルバムIDを検索
     * @param  $goalImageId
     * @return fbAlbumId[]
     */
    public function getFbAlbumIdList($goalImageId)
    {
        $sql = "SELECT * FROM album WHERE goal_image_id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $fbAlbumIds = [];
        while($data = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            array_push($fbAlbumIds, $data['fb_album_id']);
        }
        return $fbAlbumIds;
    }

    /**
     * ゴールイメージIDでユーザーIDを検索
     * @param  $goalImageId
     * @return userId[]
     */
    public function getUserIdList($goalImageId)
    {
        $sql = "SELECT * FROM album WHERE goal_image_id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $userIds = [];
        while($data = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            array_push($userIds, $data['user_id']);
        }
        return $userIds;
    }
}
