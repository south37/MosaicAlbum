<?php
namespace Vg\Model;

class MosaicPiece
{
    public $user_id;
    public $x;
    public $y;
    public $fb_image_id;
    public $image_id;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('user_id', 'x', 'y', 'fb_image_id', 'image_id') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
