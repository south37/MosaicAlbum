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
  ->name('add_album_guest')
  ;


CreateMosaic:{
  $app->get('/guest/album_select_guest/create', function() use ($app, $container){

    # 1:init
    # repository準備
    $goalImageId = $container['session']->get('goalImageId');

    $GoalImageRep = $container['repository.goalImage'];
    $AlbumRep = $container['repository.album'];
    $AlbumImageRep = $container['repository.albumImage'];
    $UsedImageRep = $container['repository.usedImage'];
    $FBHelper = $container['FBHelper'];

    # 2:prepare target & src
    # ゴールイメージ取得
	$fbGoalId = $GoalImageRep->getFbGoalImageId($goalImageId);
	//$goalPath = $FBHelper->downloadImageFromFbId($fbGoalImageId);
        //[DEUBG @ datch]
        $goalPath = 'img/resource_img/ism/miku.jpg';
	$goalImagePath = ['path'=>$goalPath, 'id'=>$fbGoalId];
    #####$goalImagePath = $GoalImageRep->getMosaicImg($goalImageId);

    # アルバムid取得
    $albumIdList = $AlbumRep->getAlbumIdList($goalImageId);
    # albumImagePathList[albumId][imageNo]=>[path, id]
    //$albumImagePathList = $UsedImageRep->getUsedImageList($goalImageId, $container);
    // [DEBUG @ datch]
    $albumImagePathList = [
        1 => [
            ['path' => 'img/resource_img/ism/figure001.png', 'id' => 1],
            ['path' => 'img/resource_img/ism/figure002.png', 'id' => 2],
            ['path' => 'img/resource_img/ism/figure003.png', 'id' => 3],
            ['path' => 'img/resource_img/ism/figure004.png', 'id' => 4]
        ],
        2 => [
            ['path' => 'img/resource_img/ism/figure005.png', 'id' => 5],
            ['path' => 'img/resource_img/ism/figure006.png', 'id' => 6]
        ],
        3 => [
            ['path' => 'img/resource_img/ism/figure007.png', 'id' => 7],
            ['path' => 'img/resource_img/ism/figure008.png', 'id' => 8],
            ['path' => 'img/resource_img/ism/figure009.png', 'id' => 9]
        ]
    ];
    

    # 3.process
    # だっちプログラムにtarget/srcListなげる
    createMosaic($goalImageId, $goalImagePath, $albumImagePathList, $container);

    # 4.notification
    # モザイク作成されたことをお知らせする
    //createNotif($container);

    $link = '/common/mosaic_viewer/'.$goalImageId;
    //$app->redirect($link);

  })
    ->name('create_mosaic')
    ;

  function createMosaic($goalImageId, $goalImagePath, $albumImagePathList, $container){
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
    // $fbGoalImageId = $goalImagePath['id'];

    // DEBUG
    $goalImageId = 1;

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
