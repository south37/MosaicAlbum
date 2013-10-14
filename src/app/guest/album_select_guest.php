<?php
//parameter無しのget
$app->get('/guest/album_select_guest', function() use ($app) {


  $app->render('guest/album_select_guest.html.twig');
})
  ->name('album_select_guest')
  ;

$app->get('/guest/album_select_guest/create', function() use ($app, $container){

  # 1init
  # repository準備
  $goalImageID = '1';
  $goalImageID = $container['session']->get('goalImageID');

  $GoalImageRepo = $container['repository.goalImage'];
  $AlbumRep = $container['repository.album'];
  $AlbumImageRep = $container['repository.albumImage'];

  # 2:prepare target & src
  # ゴールイメージ取得
  $goalImagePath = $GoalImageRepo->getMosaicImg($goalImageID);

  # アルバムid取得


  # 各アルバムの写真を取得


  # 3.process
  # だっちにtarget/srcListなげる

  $app->redirect($app->urlFor('create_notif'));

})
  ->name('create_mosaic')
  ;

$app->get('/guest/album_select_guest/notif',function() use ($app, $container){
  print "notif dayo";
  # opt:goalImgが生成されているかチェック


  # FBヘルパー使ってお知らせ

  
  # モザイクビューワ画面にリダイレクト
  $id = 1234;
  $link = '/common/mosaic_viewer/'."$id"; // きもい
  $app->redirect($link);
})
  ->name('create_notif')
  ;
