# MosaicAlbum
# CREATE DATABASE MosaicAlbum;
# USE MosaicAlbum;

USE groupwork;

# reset
DROP TABLE IF EXISTS goal_image;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS album;
DROP TABLE IF EXISTS album_image;
DROP TABLE IF EXISTS image;

# goal_image
CREATE TABLE goal_image(id INT AUTO_INCREMENT PRIMARY KEY,
						fb_goal_image_id VARCHAR(255),
						mosaic_path VARCHAR(255),
						tate_division INT,
						yoko_division INT,
						is_make_mosaic BOOLEAN);
# user
CREATE TABLE user(id INT AUTO_INCREMENT PRIMARY KEY,
				  fb_user_id VARCHAR(255) UNIQUE,
				  token VARCHAR(255),
				  name VARCHAR(255),
				  fb_icon_url VARCHAR(255),
				  mail_address VARCHAR(255));
# album
CREATE TABLE album(id INT AUTO_INCREMENT PRIMARY KEY,
				   user_id INT,
				   goal_image_id INT,
				   fb_album_id VARCHAR(255));
# album_image
CREATE TABLE album_image(id INT AUTO_INCREMENT PRIMARY KEY,
						 album_id INT,
						 image_id INT,
						 x INT,
						 y INT,
						 is_used_mosaic BOOLEAN);
# image
CREATE TABLE image(id INT AUTO_INCREMENT PRIMARY KEY,
				   fb_image_id VARCHAR(255),
				   resize_image_path VARCHAR(255));
