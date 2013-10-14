<?php
namespace Vg\Model;

class MosaicPieces
{
    public $user_id;
    public $x;
    public $y;
    public $fb_image_id;
    public $resize_image_path;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('user_id', 'x', 'y', 'fb_image_id', 'resize_image_path') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
