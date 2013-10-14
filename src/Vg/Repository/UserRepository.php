<?php
namespace Vg\Repository;

use Vg\Model\User;

class UserRepository
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * 新規登録
     * @param  User
     * @return $userId 
     */
    public function insert($user)
    {
        $sql = "INSERT INTO user
                SET fb_user_id = :fbUserId,
                    token = :token,
                    name = :name,
                    fb_icon_url = :fbIconUrl,
                    mail_address = :mailAddress";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':fbUserId', $user->fb_user_id, \PDO::PARAM_STR);
        $sth->bindValue(':token', $user->token, \PDO::PARAM_STR);
        $sth->bindValue(':name', $user->name, \PDO::PARAM_STR);
        $sth->bindValue(':fbIconUrl', $user->fb_icon_url, \PDO::PARAM_STR);
        $sth->bindValue(':mailAddress', $user->mail_address, \PDO::PARAM_STR);
        $sth->execute();
        // insertされたカラムのIDを取得する
        $userId = $this->getLatestId();
        return $userId;
    }

    /**
    * 最後のuserカラムのIDを取得する
    * @return $userID
    */
    private function getLatestId()
    {
        // IDを降順にして取得
        $sql = "SELECT * FROM user ORDER BY id DESC";
        $sth = $this->db->prepare($sql);
        $sth->execute();
        // 最初の一個目を取得
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        return $data['id'];
    }

    /**
     * ユーザーIDでユーザーを検索する
     * @param $userId
     * @return User
     */
    public function findById($userId)
    {
        $sql = "SELECT * FROM user WHERE id = :id";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':id', $userId, \PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        $user = new User();
        $user->setProperties($data);
        return $user;
    }

    /**
     * facebookユーザーIDでユーザーを検索する
     * @param $fbUserId
     * @return User
     */
    public function findByFbId($fbUserId)
    {
        $sql = "SELECT * FROM user WHERE fb_user_id = :fb_user_id";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':fb_user_id', $fbUserId, \PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(\PDO::FETCH_ASSOC);
        $user = new User();
        $user->setProperties($data);
        return $user;
    }

    /**
     * ユーザーIDでアイコンURLを検索する
     * @param  $userId
     * @return fbIconUrl[]
     */
    public function getUserIconImgPathList($userId)
    {
        $sql = "SELECT * FROM user WHERE id = :userId";
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':id', $userId, \PDO::PARAM_INT);
        $sth->execute();
        $fbIconUrls = [];
        while($data = $sth->fetch(\PDO::FETCH_ASSOC))
        {
            array_push($fbIconUrls, $data['fb_icon_url']);
        }
        return $fbIconUrls;
    }
}
