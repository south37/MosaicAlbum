<?php
// PHP 5.4.16 動作テスト済

ini_set('default_charset', 'UTF-8');
include('CreateMosaic.php');
include('Image.php');
include('gd_bmp_util.php');

// 保存するファイルパス
$saveFilePath = './mosaic_img/' . 'mosaicImage.png';

$albumImageUrlList = [];
// [DEBUG]
// 現状では目標画像とモザイクを行うための画像をコマンド引数で取得する
if($argc < 3 || empty($argv[1]) || empty($argv[2]))
{
	echo 'コマンド引数を指定してください', PHP_EOL;
	die;
}
$goalImageUrl = $argv[1];
$pictureFolder = $argv[2];

// [DEBUG]
// 現状では横に20、縦に15で目標画像を分割する
$splitX = 80;
$splitY = 60;
// [DEBUG]
// 目標画像を640x480の大きさに固定する
$goalResizeWidth = 640;
$goalResizeHeight = 480;
// 部分画像をリサイズしてサーバーに保存大きさ
$albumResizeWidth = 100;
$albumResizeHeight = 75;
// 部分画像の大きさを計算
$splitWidth = $goalResizeWidth / $splitX;
$splitHeight = $goalResizeHeight / $splitY;

// [DEBUG]
// 現状では指定したフォルダから画像を読み込むような処理で動作を確認する
$dp = opendir($pictureFolder);
while($fileName = readdir($dp))
{
	if($fileName == '.' || $fileName == '..') continue;
	$filePath = $pictureFolder . '/' . $fileName;
	array_push($albumImageUrlList, $filePath);
}

// 時間の測定
{
	// [DEBUG]
	$fbImageIdList = [];
	// アルバム画像に対応するアルバムIDを保持
	// 事前にどの画像がどのアルバムに対応するかを入れておく必要がある
	$albumIdList = [];
	$goalImageId = 1;
	$fbGoalImageId = 1;

	$timeStart = microtime(true);
	// モザイク画像を作成するためのクラスを用意
	$createMosaic = new Mosaic\CreateMosaic($splitX, $splitY, $splitWidth, $splitHeight);
	// 画像の読み込み
	$createMosaic->loadRequiredImages($goalImageUrl, $albumImageUrlList, $goalResizeWidth, $goalResizeHeight, $albumResizeWidth, $albumResizeHeight);
	// 画像生成開始
	$corrTwoDimension = $createMosaic->execMakeMosaicImage($saveFilePath, $goalImageId, $fbGoalImageId);
	// [DEBUG]
	$albumIdList = array_fill(0, count($corrTwoDimension), 0);
	$fbImageIdList = array_fill(0, count($corrTwoDimension), 0);
	// 画像保存
	$createMosaic->saveAlbumImages($albumResizeWidth, $albumResizeHeight, $goalImageId, $fbImageIdList, $albumIdList, $corrTwoDimension);
	$timeEnd = microtime(true);
	$time = $timeEnd - $timeStart;
	echo "実行時間:". $time ."秒", PHP_EOL;
}

unset($createMosaic);
