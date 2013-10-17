<?php

ini_set('default_charset', 'UTF-8');
include('../src/mosaic/CreateMosaic.php');
include('../src/mosaic/Image.php');
include('../src/mosaic/gd_bmp_util.php');


// アルバムセレクト＿ゲスト：使うアルバムを選択
$app->get('/guest/album_select_guest', function() use ($app, $container) {
  $albumList = [];
  # 自分のFacebookアルバムのリストを取得
  $fbAlbums = $container['FBHelper']->getAlbums();
  foreach ($fbAlbums as $fbAlbum) {
    # アルバムの写真一覧を取得（fbImageId, imagePath）
    $images = $container['FBHelper']->getImagesInAlbum($fbAlbum['id']);
    # アルバムリストにアルバムの写真一覧を保存
    $fbAlbum['images'] = $images;
    array_push($albumList, $fbAlbum);
  }
  $app->render('guest/album_select_guest.html.twig', ["albumList"=>$albumList]);
})
  ->name('album_select_guest')
  ;

// アルバムを追加
$app->post('/guest/album_select_guest', function() use ($app, $container) {
  # 追加するアルバム
    $fbAlbum = $app->request()->post();
  # DBに登録
  $userId = $container['session']->get('userId');
  $data = array('id' => 0, 'user_id' => $userId, 'goal_image_id' => $goalImageId, 'fb_album_id' => $fbAlbum['id']);
  $album = new \Vg\Model\Album();
  $album->setProperties($data);
  $albumId = $container['repository.album']->insert($album);
  # アルバムビューアへ
  $app->redirect($app->urlFor('album_viewer', ['goalImageId'=>$container['session']->get('goalImageId')]));
})
  ->name('add_album')
  ;


CreateMosaic:{
  $app->get('/guest/album_select_guest/create', function() use ($app, $container){

    # 1:init
    # repository準備
    $goalImageId = $container['session']->get('goalImageId');
    $goalImageId = '1';

    $GoalImageRep = $container['repository.goalImage'];
    $AlbumRep = $container['repository.album'];
    $AlbumImageRep = $container['repository.albumImage'];
    $FBHelper = $container['FBHelper'];

    # 2:prepare target & src
    # ゴールイメージ取得
    $goalImageUrl = $GoalImageRep->getMosaicImg($goalImageId);

    #TODO:pathから画像をどう取るのか？:helperに処理追加
    $goalImagePath = 'img/goal_img/miku.jpg'; //使い終わったらけす．

    # アルバムid取得
    $albumIdList = $AlbumRep->getAlbumIdList($goalImageId);
    # 各アルバムの写真を取得
    $albumImageUrlList = []; 
    foreach ($albumIdList as $albumId) {
      $tmpAlbumImageUrlList = $FBHelper->getImagesInAlbum($albumId);
      array_push($albumImageUrlList,$tmpAlbumImageUrlList);
    }


    var_dump($albumImageUrlList);

    #TODO:pathから画像をどう取るのか？:helperに処理追加
    $albumImagePathList = [['img/tmp/hoge'],['img/tmp/huga']];
    $albumImagePathList = [
      'img/resource_img/ism/figure001.png',
      'img/resource_img/ism/figure002.png',
      'img/resource_img/ism/figure003.png',
      'img/resource_img/ism/figure004.png',
      'img/resource_img/ism/figure005.png',
      'img/resource_img/ism/figure006.png',
      'img/resource_img/ism/figure007.png',
      'img/resource_img/ism/figure008.png',
      'img/resource_img/ism/figure009.png',
      'img/resource_img/ism/miku.jpg',

      ]; //[debug]

    # 3.process
    # だっちプログラムにtarget/srcListなげる


    // [debug]
    



    createMosaic($goalImageId,$goalImagePath,$albumImagePathList,$albumIdList);

    # 4.notification
    # モザイク作成されたことをお知らせする
    createNotif($container);

    $link = '/common/mosaic_viewer/'.$goalImageId;
    //$app->redirect($link);

  })
    ->name('create_mosaic')
    ;

  function createMosaic($goalImageId,$goalImagePath,$albumImagePathList,$albumIdList){
    # だっちのプログラムはここに移植
    $saveFilePath = 'img/mosaic_img/'.'mosaic'.$goalImageId.'.png';

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

    $fbGoalImageId = 1; //何に使うのこれ:DBに保存する．

    ########
    # 処理 #
    ########
    $timeStart = microtime(true);

    # mosaicクラスのインスタンス 
    $createMosaic = new Mosaic\CreateMosaic($splitX, $splitY, $splitWidth, $splitHeight);
   
    # 画像の読み込み
    $createMosaic->loadRequiredImages($goalImagePath,$albumImagePathList,$goalResizeWidth,$goalResizeHeight,$albumResizeWidth,$albumResizeHeight);
    
    # モザイク画像生成
    $corrTwoDimension = $createMosaic->execMakeMosaicImage($saveFilePath,$goalImageId,$fbGoalImageId);

    # TODO:n番目の画像が，どのアルバムのものだったか，というリスト
    # mgmgさんがきれいな配列をつくってくれます．
    $albumIdList = array_fill(0, count($corrTwoDimension), 0);
    $fbImageIdList = array_fill(0, count($corrTwoDimension), 0);

    # 画像保存
    $createMosaic->saveAlbumImages($albumResizeWidth,$albumResizeHeight, $goalImageId, $fbImageIdList, $albumIdList, $corrTwoDimension);

    # 実行時間
    $timeEnd = microtime(true);
    $time = $timeEnd - $timeStart;

    unset($createMosaic);
  }

  function createNotif($container){
    # mosaic作ったことをみんなにおしらせ
   
    # opt:goalImgが生成されているかチェック


    # FBヘルパー使ってお知らせ#
  }
}
