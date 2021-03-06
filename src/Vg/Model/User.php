<?php
namespace Vg\Model;

class User
{
    public $id;
    public $fb_user_id;
    public $token;
    public $name;
    public $fb_icon_url;
    public $mail_address;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('id', 'fb_user_id', 'token', 'name', 'fb_icon_url', 'mail_address') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
