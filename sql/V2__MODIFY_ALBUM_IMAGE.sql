# MosaicAlbum
# CREATE DATABASE MosaicAlbum;
# USE MosaicAlbum;

USE groupwork;

# reset
DROP TABLE IF EXISTS album_image;

# album_image
CREATE TABLE album_image(id INT AUTO_INCREMENT PRIMARY KEY,
						 album_id INT,
						 image_id INT,
						 x INT,
						 y INT,
						 is_used_mosaic BOOLEAN,
                                                 is_latest BOOLEAN);
