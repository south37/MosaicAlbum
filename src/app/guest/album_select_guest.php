<?php

ini_set('default_charset', 'UTF-8');
include('../src/mosaic/CreateMosaic.php');
include('../src/mosaic/Image.php');
include('../src/mosaic/gd_bmp_util.php');

// スクリプトの実行時間を300秒に設定
set_time_limit(300);

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
  ->name('add_album_guest')
  ;


CreateMosaic:{
  $app->get('/guest/album_select_guest/create', function() use ($app, $container){
    $link = '/common/mosaic_viewer';
    # 1:init
    # repository準備
    $goalImageId = $container['session']->get('goalImageId');

    $GoalImageRep = $container['repository.goalImage'];
    $AlbumRep = $container['repository.album'];
    $AlbumImageRep = $container['repository.albumImage'];
    $UsedImageRep = $container['repository.usedImage'];
    $FBHelper = $container['FBHelper'];

    # 既に現在の状態のモザイク画で作成されているかを調べる
    $isMakedMosaic = $GoalImageRep->isMakeMosaic($goalImageId);
    if($isMakedMosaic == 1)
    {
        // 直接アクセスした場合はこちらでリダイレクトを行う
        $app->redirect($link);
        // 作成されているなら処理を終了する
        exit;
    }
    // モザイク画像を作成済みにする
    $GoalImageRep->setIsMakeMosaic(1, $goalImageId);

    # 2:prepare target & src
    # ゴールイメージ取得
    $fbGoalImageId = $GoalImageRep->getFbGoalImageId($goalImageId);
    $goalPath = $FBHelper->downloadImageFromFbId($fbGoalImageId);
    $goalImagePath = ['path'=>$goalPath, 'id'=>$fbGoalImageId];

    # アルバムid取得
    $albumIdList = $AlbumRep->getAlbumIdList($goalImageId);
    # albumImagePathList[albumId][imageNo]=>[path, id]
    $albumImagePathList = $UsedImageRep->getUsedImageList($goalImageId, $container);

    # 3.process
    createMosaic($goalImageId, $goalImagePath, $albumImagePathList, $container);

    # 4.notification
    # モザイク作成されたことをお知らせする
    //createNotif($container);

    //$app->redirect($link);
  })
    ->name('create_mosaic')
    ;

  function createMosaic($goalImageId, $goalImagePath, $albumImagePathList, $container){
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

    ########
    # 処理 #
    ########
    $timeStart = microtime(true);

    # mosaicクラスのインスタンス 
    $createMosaic = new Mosaic\CreateMosaic($splitX, $splitY, $splitWidth, $splitHeight);
   
    # 画像の読み込み
    $createMosaic->loadRequiredImages($goalImagePath, $albumImagePathList, $goalResizeWidth, $goalResizeHeight, $albumResizeWidth, $albumResizeHeight);
    
    # モザイク画像生成
    $corrTwoDimension = $createMosaic->execMakeMosaicImage($saveFilePath, $goalImageId, $container);

    # 画像保存
    $createMosaic->saveAlbumImages($albumResizeWidth, $albumResizeHeight, $goalImageId, $albumImagePathList, $corrTwoDimension, $container);

    # 実行時間
    $timeEnd = microtime(true);
    $time = $timeEnd - $timeStart;

    unset($createMosaic);
  }

  function createNotif($container){
    # mosaic作ったことをみんなにおしらせ
    # opt:goalImgが生成されているかチェック
    $GoalImageRep = $container['repository.goalImage'];
    $isMakeMosaic = $GoalImageRep->isMakeMosaic($container['session']->get('goalImageId'));
    if (!$isMakeMosaic) {
      # モザイク未生成時($isMakeMosaic==False)
      return $isMakeMosaic;
    }
    # FBヘルパー使ってお知らせ
    $FBHelper = $container['FBHelper'];
    $FBHelper->notifCreateMosaic();
  }
}
