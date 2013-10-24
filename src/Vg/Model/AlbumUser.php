<?php
namespace Vg\Model;

class AlbumUser
{
    public $goal_image_id;
    public $user_id_list;
    public $fb_icon_url_list;
    public $fb_icon_path_list;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('goal_image_id', 'user_id_list', 'fb_icon_url_list', 'fb_icon_path_list') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
