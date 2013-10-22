<?php
namespace Vg\Repository;

use Vg\Model\GoalImage;

class GoalImageRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * 新規登録　縦・横とも、分割数100にしてます。
     * @param  $fbGoalImageId
     * @return $goalImageId 
     */
    public function insert($fbGoalImageId)
    {
        $sql = "INSERT INTO goal_image
                SET fb_goal_image_id = :fbGoalImageId,
                    tate_division = :tateDivision,
                    yoko_division = :yokoDivision,
                    is_make_mosaic = :isMakeMosaic";
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':fbGoalImageId', $fbGoalImageId, \PDO::PARAM_STR);
        $sth->bindValue(':tateDivision', 100, \PDO::PARAM_INT);
        $sth->bindValue(':yokoDivision', 100, \PDO::PARAM_INT);
        $sth->bindParam(':isMakeMosaic', 'FALSE', \PDO::PARAM_STR);
        var_dump($sth->execute());
        // insertされたカラムのIDを取得する
        $goalImageId = $this->getLatestId();
        return $goalImageId;
    }

    /**
     * 更新（モザイクを保存）
	 * @param  $goalImageId
     * @param  $mosaicPath
     */
    public function update($goalImageId, $mosaicPath)
    {
        $sql = "UPDATE goal_image SET mosaic_path = :mosaicPath, is_make_mosaic = :isMakeMosaic WHERE id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':mosaicPath', $mosaicPath, \PDO::PARAM_STR);
        $sth->bindParam(':isMakeMosaic', 'TRUE', \PDO::PARAM_STR);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
    }

    /**
    * 最後のgoal_imageカラムのIDを取得する
    * @return $goalImageID
    */
    private function getLatestId()
    {
        // IDを降順にして取得
        $sql = "SELECT * FROM goal_image ORDER BY id DESC";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        // 最初の一個目を取得
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return $data['id'];
    }

    /**
     * ゴールイメージIDでFacebookゴールイメージIDを検索する
     * @param  $goalImageId
     * @return $fbGoalImageId
     */
    public function getFbGoalImageId($goalImageId)
    {
        $sql = "SELECT * FROM goal_image WHERE id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return $data['fb_goal_image_id'];
    }

    /**
     * ゴールイメージIDでモザイクを検索する
     * @param  $goalImageId
     * @return $mosaicPath
     */
    public function getMosaicImg($goalImageId)
    {
        $sql = "SELECT * FROM goal_image WHERE id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return ['path'=>$data['mosaic_path'], 'id'=>$data['fb_goal_image_id']];
    }

    /**
     * ゴールイメージIDでモザイクが生成されているかをチェックする
     * @param  $goalImageId
     * @return True or False
     */
    public function isMakeMosaic($goalImageId)
    {
        $sql = "SELECT * FROM goal_image WHERE id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return $data['is_make_mosaic'];
    }
}
