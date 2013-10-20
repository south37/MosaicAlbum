<?php
namespace Vg\Model;

class UsedImage
{
    public $album_id;
    public $image_id;
    public $image_path;
    public $fb_image_id;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('album_id', 'image_id', 'image_path', 'fb_image_id') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
