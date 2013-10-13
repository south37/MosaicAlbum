<?php
namespace Vg\Model;

class Image
{
    public $id;
    public $fb_image_id;
    public $resize_image_path;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('id', 'fb_image_id', 'resize_image_path') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
