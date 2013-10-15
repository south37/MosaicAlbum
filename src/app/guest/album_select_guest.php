<?php

ini_set('default_charset', 'UTF-8');
include('../src/mosaic/CreateMosaic.php');
include('../src/mosaic/Image.php');
include('../src/mosaic/gd_bmp_util.php');


//parameter無しのget
$app->get('/guest/album_select_guest', function() use ($app) {


  $app->render('guest/album_select_guest.html.twig');
})
  ->name('album_select_guest')
  ;


CreateMosaic:{
  $app->get('/guest/album_select_guest/create', function() use ($app, $container){

    # 1init
    # repository準備
    $goalImageId = $container['session']->get('goalImageId');
    $goalImageId = '1';

    $GoalImageRep = $container['repository.goalImage'];
    $AlbumRep = $container['repository.album'];
    $AlbumImageRep = $container['repository.albumImage'];

    # 2:prepare target & src
    # ゴールイメージ取得
    $goalImageUrl = $GoalImageRep->getMosaicImg($goalImageId);

    #TODO:pathから画像をどう取るのか？:helperに処理追加
    $goalImagePath = '/img/goal_img/shake_hand.jpg';

    # アルバムid取得
    $albumIdList = $AlbumRep->getAlbumIdList($goalImageId);
    # 各アルバムの写真を取得
    #TODO:albumIDからimagepathがないよ

    $albumImageUrlList;
    $albumImagePathList = [['/img/tmp/hoge'],['/img/tmp/huga']];
    #TODO:pathから画像をどう取るのか？:helperに処理追加

       # 3.process
    # だっちプログラムにtarget/srcListなげる
    
    //createMosaic($goalImageId,$goalImagePath,$albumImagePathList);

    # 4.notification
    # モザイク作成されたことをお知らせする
    createNotif($container);

    $link = '/common/mosaic_viewer/'.$goalImageId;
    //$app->redirect($link);

  })
    ->name('create_mosaic')
    ;

  function createMosaic($goalImageId,$goalImagePath,$albumImagePathList){
    # だっちのプログラムはここに移植
    $saveFilePath = '/img/mosaic_img'.'mosaic'.$goalImageId.'.png';

    # モザイク処理設定
    $splitX = 80;
    $splitY = 60;
    $goalResizeWidth = 640;
    $goalResizeHeight = 480;
    $albumResizeHeight = 100;
    $albumResizeWidth = 75;

    $splitWidth = $goalResizeWidth / $splitX;
    $splitHeight = $goalResizeHeight / $splitY;
    
    # 処理用変数
    $fbImageIdList = [];
    $albumIdList = [];

    $fbGoalImageId = 1; //何に使うのこれ

    ########
    # 処理 #
    ########
    $timeStart = microtime(true);

    # mosaicクラスのインスタンス 
    $createMosaic = new Mosaic\CreateMosaic($splitX, $splitY, $splitWidth, $splitHeight);
   
    # 画像の読み込み
    $createMosaic->loadRequiredImages($goalImagePath,$albumImagePathList,$goalResizeWidth,$goalResizeHeight);
    
    # モザイク画像生成
    $corrTwoDimension = $createMosaic->execMakeMosaicImage($saveFilePath,$goalImageId,$fbGoalImageId);

  $albumIdList = array_fill(0, count($corrTwoDimension), 0);
  $fbImageIdList = array_fill(0, count($corrTwoDimension), 0);

    # 画像保存
    $createMosaic->saveAlbumImages($albumResizeWidth,$albumResizeHeight, $fbImageIdList, $albumIdList, $corrTwoDimension);

    # 実行時間
    $timeEnd = microtime(true);
    $time = $timeEnd - $timeStart;
  }

  function createNotif($container){
    # mosaic作ったことをみんなにおしらせ
   
    # opt:goalImgが生成されているかチェック


    # FBヘルパー使ってお知らせ#
  }
}
