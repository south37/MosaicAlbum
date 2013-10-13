<?php
namespace Vg\Model;

class GoalImage
{
    public $id;
    public $fb_goal_image_id;
    public $mosaic_path;
    public $is_make_mosaic;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('id', 'fb_goal_image_id', 'mosaic_path', 'is_make_mosaic') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
