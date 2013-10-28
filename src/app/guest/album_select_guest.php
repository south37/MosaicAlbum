<?php

ini_set('default_charset', 'UTF-8');
include('../src/mosaic/CreateMosaic.php');
include('../src/mosaic/Image.php');
include('../src/mosaic/gd_bmp_util.php');

// スクリプトの実行時間を300秒に設定
set_time_limit(300);

// アルバムセレクト＿ゲスト：使うアルバムを選択
$app->get('/guest/album_select_guest/mailtest', function() use ($app, $container) {
    sendMail('wega315@gmail.com', 'http://mosaicalbum.me', 1);
})
  ->name('album_select_guest')
  ;

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
  // validation
  $validator = new \Vg\Validator\AlbumRegister();
  if ($validator->validate($data)) {
    $album = new \Vg\Model\Album();
    $album->setProperties($data);
    $albumId = $container['repository.album']->insert($album);
    # アルバムビューアへ
    $app->redirect($app->urlFor('album_viewer', ['goalImageId'=>$container['session']->get('goalImageId')]));
  }
  $app->render('guest/album_select_guest.html.twig', ['errors' => $validator->errors(), 'fbAlbum' => $fbAlbum]);
})
  ->name('add_album_guest')
  ;


CreateMosaic:{
  $app->get('/guest/album_select_guest/create', function() use ($app, $container){
    $link = '/common/mosaic_viewer';

    # 1:init
    # repository準備
    $goalImageId   = $container['session']->get('goalImageId');

    $GoalImageRep  = $container['repository.goalImage'];
    $AlbumRep      = $container['repository.album'];
    $AlbumImageRep = $container['repository.albumImage'];
    $UsedImageRep  = $container['repository.usedImage'];
    $FBHelper      = $container['FBHelper'];

    /*
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
    //*/

    # 2:prepare target & src
    # ゴールイメージ取得
    $fbGoalImageId = $GoalImageRep->getFbGoalImageId($goalImageId);
    $goalPath = $FBHelper->downloadImageFromFbId($fbGoalImageId);
    //$goalPath = 'img/resource_img/ism/miku3.jpg';
    $goalImagePath = ['path'=>$goalPath, 'id'=>$fbGoalImageId];

    # アルバムid取得
    $albumIdList = $AlbumRep->getAlbumIdList($goalImageId);
 
    $albumImagePathList = $UsedImageRep->getUsedImageList($goalImageId, $container);
    /*
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
            ],
            4 =>[
                ['path' => 'img/resource_img/ism/miku1.jpg', 'id' => 10],
                ['path' => 'img/resource_img/ism/miku2.jpg', 'id' => 11],
                ['path' => 'img/resource_img/ism/miku3.jpg', 'id' => 12],
                ['path' => 'img/resource_img/ism/miku4.jpg', 'id' => 13],
                ['path' => 'img/resource_img/ism/miku5.jpg', 'id' => 14],
                ['path' => 'img/resource_img/ism/rin1.jpg', 'id' => 15],
                ['path' => 'img/resource_img/ism/len1.jpg', 'id' => 16]
    
            ]
    ];
     */

    # 3.process
    createMosaic($goalImageId, $goalImagePath, $albumImagePathList, $container);
    
    // img/resource_img/以下のデータを全て削除
    //deleteDirectoryData('img/resource_img');

    # 4.notification
    # モザイク作成されたことをお知らせする
    //createNotif($container);

    $app->redirect($link);
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

  function deleteDirectoryData($dirPath)
  {
    if($dirHandle = opendir($dirPath))
    {
        while(($underPath = readdir($dirHandle)) !== FALSE)
        {
            if($underPath == '.' || $underPath == '..' || $underPath == 'ism') continue;
            $fullPath = $dirPath.'/'.$underPath;
            if(is_dir($fullPath) === TRUE)
            {
                deleteDirectoryData($fullPath);
                rmdir($fullPath);
            }
            else
            {
                unlink($fullPath);
            }
        }
        closedir($dirHandle);
    }
  }

  function sendMail($to, $url, $goalId)
  {
   //言語設定、内部エンコーディングを指定する
   mb_language("japanese");
   mb_internal_encoding("UTF-8");
   //日本語添付メールを送る
   $params = [
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'auth' => true,
        'username' => 'notification.mosaic@gmail.com',
        'password' => 'MashUp_2013'
   ];
   $subject = "モザイク画が作成されました"; //題名
   $body = "あなたが参加したモザイク画が完成しました。\n以下のURLにアクセスすることで完成したモザイクアルバムを見ることが出来ます。\n" . $url; //本文
   $from = "notification.mosaic@gmail.com"; //差出人
   $fromname = "MosaicAlbum"; //差し出し人名
   $mail = @Mail::factory("smtp", $params);
   $body = mb_convert_encoding($body,"JIS","UTF-8");

   //添付ファイル追加
   $body_encode = [
          "head_charset" => "ISO-2022-JP",
          "text_charset" => "ISO-2022-JP"
         ];
   $headers = [
       "To" => $to, //宛先
       "From" => mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","UTF-8"))."<".$from.">",
       "Subject" => mb_encode_mimeheader(mb_convert_encoding($subject,"JIS","UTF-8"))
   ];
   $return = @$mail->send($to,$headers,$body);
   if (@PEAR::isError($return))
   {
       echo 'メールが送信できませんでした エラー：' .$return->getMessage(). '<br>';
   }
   echo 'Exit send Mail<br>';
  }
}
