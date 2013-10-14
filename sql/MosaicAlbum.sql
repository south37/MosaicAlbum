# MosaicAlbum
CREATE DATABASE MosaicAlbum;
USE MosaicAlbum;
# goal_image
CREATE TABLE goal_image(id INT AUTO_INCREMENT PRIMARY KEY, fb_goal_image_id INT, mosaic_path VARCHAR(255), tate_division INT, yoko_division INT, is_make_mosaic BOOLEAN);
# user
CREATE TABLE user(id INT AUTO_INCREMENT PRIMARY KEY, fb_user_id INT, token VARCHAR(255), name VARCHAR(255), fb_icon_url VARCHAR(255), mail_address VARCHAR(255));
# album
CREATE TABLE album(id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, goal_image_id INT, fb_album_id INT);
# album_image
CREATE TABLE album_image(id INT AUTO_INCREMENT PRIMARY KEY, album_id INT, image_id INT, x INT, y INT, is_used_mosaic BOOLEAN);
# image
CREATE TABLE image(id INT AUTO_INCREMENT PRIMARY KEY, fb_image_id INT, resize_image_path VARCHAR(255));
