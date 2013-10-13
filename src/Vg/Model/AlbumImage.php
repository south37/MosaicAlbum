<?php
namespace Vg\Model;

class AlbumImage
{
    public $id;
    public $album_id;
    public $image_id;
    public $x;
    public $y;
    public $is_used_mosaic;

    public function __construct()
    {
    }

    public function setProperties($data)
    {
        foreach (array('id', 'album_id', 'image_id', 'x', 'y', 'is_used_mosaic') as $property) {
            $this->{$property} = (isset($data[$property]))? $data[$property]: "";
        }
    }
}
