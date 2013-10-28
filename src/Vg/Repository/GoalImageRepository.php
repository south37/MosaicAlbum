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
        $sth->bindValue(':fbGoalImageId', $fbGoalImageId, \PDO::PARAM_STR);
        $sth->bindValue(':tateDivision', 80, \PDO::PARAM_INT);
        $sth->bindValue(':yokoDivision', 60, \PDO::PARAM_INT);
        $sth->bindValue(':isMakeMosaic', 0, \PDO::PARAM_INT);
        $sth->execute();
        // insertされたカラムのIDを取得する
        $goalImageId = $this->getLatestId();
        return $goalImageId;
    }

    /**
     * 更新（モザイクを保存）
	 * @param  $goalImageId
     * @param  $mosaicPath
     */
    public function update($goalImageId, $mosaicPath, $tateDivision, $yokoDivision)
    {
        $sql = "UPDATE goal_image SET
            mosaic_path = :mosaicPath,
            is_make_mosaic = :isMakeMosaic,
            tate_division = :tateDivision,
            yoko_division = :yokoDivision
            WHERE id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':mosaicPath', $mosaicPath, \PDO::PARAM_STR);
        $sth->bindValue(':isMakeMosaic', 1, \PDO::PARAM_INT);
        $sth->bindValue(':tateDivision', $tateDivision, \PDO::PARAM_INT);
        $sth->bindValue(':yokoDivision', $yokoDivision, \PDO::PARAM_INT);
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
    public function getMosaicImg($goalImageId, &$container)
    {
        $albumRepository = $container['repository.album'];
        $fbHelper = $container['FBHelper'];
        $sql = "SELECT * FROM goal_image WHERE id = :goalImageId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return [
            'mosaicPath' => $data['mosaic_path'],
            'splitX' => $data['yoko_division'],
            'splitY' => $data['tate_division'],
            'splitWidth' => 640 / $data['yoko_division'],
            'splitHeight' => 480 / $data['tate_division'],
            'resizeWidth' => 640,
            'resizeHeight' => 480,
            //'originalPath' => $fbHelper->downloadImageFromFbId($data['fb_goal_image_id']),
            'originalPath' => $fbHelper->getImagePath($data['fb_goal_image_id']), 
            'originalUserCount' => count($albumRepository->getUserIdList($goalImageId))
        ];
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

    /**
    * @param $isMakeMosaic
    * @param $goalImageId
    * @return {Boolean}
    */
    public function setIsMakeMosaic($isMakeMosaic, $goalImageId)
    {
        $sql = <<< SQL
            UPDATE goal_image
            SET is_make_mosaic = :isMakeMosaic
            WHERE id = :goalImageId;
SQL;
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':isMakeMosaic', $isMakeMosaic, \PDO::PARAM_INT);
        $sth->bindValue(':goalImageId', $goalImageId, \PDO::PARAM_INT);
        return $sth->execute();
    }
}
