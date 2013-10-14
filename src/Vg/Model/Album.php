<?php
namespace Vg\Model;

class Album
{
    public $id;
    public $user_id;
    public $session_id;
    public $fb_album_id;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('id', 'user_id', 'session_id', 'fb_album_id') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
