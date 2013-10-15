<?php

ini_set('default_charset', 'UTF-8');
include('../mosaic/CreateMosaic.php');
include('../mosaic/Image.php');
include('../mosaic/gd_bmp_util.php');


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
    $goalImagePath = $GoalImageRep->getMosaicImg($goalImageId);
    #TODO:oathから画像をどう取るのか？


    # アルバムid取得
    $albumIdList = $AlbumRep->getAlbumIdList($goalImageId);

    # 各アルバムの写真を取得
    #TODO:albumIDからimagepathがないよ

    # 3.process
    # だっちプログラムにtarget/srcListなげる
    createMosaic();

    # 4.notification
    # モザイク作成されたことをお知らせする
    createNotif($container);

    $link = '/common/mosaic_viewer/'.$goalImageId;
    //$app->redirect($link);

    echo "hoge\n";
  })
    ->name('create_mosaic')
    ;





  function createMosaic(){
    # だっちのプログラムはここに移植
    
  }

  function createNotif($container){
    # mosaic作ったことをみんなにおしらせ
   
    # opt:goalImgが生成されているかチェック


    # FBヘルパー使ってお知らせ#
  }
}
