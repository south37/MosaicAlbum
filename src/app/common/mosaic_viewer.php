<?php
ページ読み込みテスト:{
  $app->get('/common/mosaic_viewer/test', function() use ($app,$container) {
    # DBアクセステスト
    $goalImageId = $container['session']->get('goalImageId');
    print_r($goalImageId);


    //$userIdList =$container['repository.album']->getUserIdList(1);
    //print_r($userIdList);

    //$userIconPathList = $container['repository.user']->getUserIconImgUrl($userIdList[0]);
    //print_r($userIconPathList);

    $mosaicImage = $container['repository.goalImage']->getMosaicImg($goalImageId,$container);
    print_r($mosaicImage);

   
    //print_r($container['repository.albumUser']->getFbIconPathList(1,$container)); 
    //print_r($container['repository.albumUser']->getFbIconPathList(1,$container));

    $app->render('common/mosaic_viewer.html.twig');
  })
    ->name('mosaic_viewer_test')
    ;
}

アクセスされたときの処理:{
  $app->get('/common/mosaic_viewer', function() use ($app, $container){
    # TODO:user認証いるかな？
    $goalImageId = $container['session']->get('goalImageId');

    #画面レンダリング
    $app->render('common/mosaic_viewer.html.twig',['goalId' => $goalImageId]);
  })
    ->name('mosaic_viewer')
    ;

  $app->get('/common/mosaic_viewer/:goalImageId', function($goalImageId) use ($app, $container){
    //echo $goalImageId;
    $container['session']->set("goalImageId",$goalImageId);
    $app->render('common/mosaic_viewer.html.twig', ['goalId' => $goalImageId]); 
  });
}

ajax_mosaic画像リスト取得:{
  $app->get('/common/mosaic_viewer/ajax/list', function() use ($app, $container){

    ########################
    # ajax_list:仕様メモ
    ########################
    #
    # mosaicInfo(obj):
    #   userNum:
    #   splitX
    #   splitY
    #   goalResizeWidth
    #   goalResizeHeight
    #   album
    #   mosaicPath
    #   goalPath
    #
    # userInfo(hash):
    #   userId => userIconPath
    #
    # mosaicPiece(array):
    #   mosaicpiece(obj):
    #     x
    #     y
    #     imageId
    #     userId
    #     fb_image_id
    #
    # mosaicPieceMap(hash):
    #   image_id => resizeImgPath
    #
    # mosaicImage:
    #   mosaicImagePath
    # 
    # test
    #

    # 1.リポジトリ，必要変数の確保 
    $FBRep = $container['FBHelper'];

    //$goalImageId = 1;
    $goalImageId = $container['session']->get('goalImageId');

    $mosaicPieceRep = $container['repository.mosaicPiece'];
    $AlbumUserRep   = $container['repository.albumUser'];
    $goalImageRep   = $container['repository.goalImage'];

    # 2.参加ユーザの画像リスト取得
    # 参加ユーザリスト作成
    $userInfo = [
      '2147483647'=>'img/test/miku1.jpg',
      '3'=>'img/test/miku2.jpg',
      '1'=>'img/resource_img/1/picture.jpg'
     
      ];

    #TODO:userInfoの取得
    $userInfo = $AlbumUserRep->getFbIconPathList($goalImageId,$container);

    # 参加ユーザ数取得
    $userNum = count($userInfo);

    # 3.mosaic画像リスト取得(テクスチャリスト/ピースマップ)
    $mosaicPieces   = $mosaicPieceRep->getMosaicPieceList($goalImageId);
    $mosaicPieceMap = $mosaicPieceRep->getResizeImagePathList($goalImageId);
    
    # 4.mosaiInfoつくる
    #
    # TODO:DBからデータをひろう
    $mosaicPath    = '/img/mosaic_img/mosaic'.$goalImageId.'.png';
    $mosaicImage   = $goalImageRep->getMosaicImg($goalImageId, $container);
 
    $originalImage = '/img/test/miku3.jpg';
    $originalImage = $mosaicImage["originalPath"];

    $mosaicInfo = [
      "mosaicPath"   => $mosaicPath,
      "originalPath" => $originalImage,
      "userNum" => $userNum,
      "splitX" => 80,
      "splitY" => 60
      ];

    # 5.ajax_return
    $response = [
      "userInfo"        => $userInfo,
      "mosaicPieces"    => $mosaicPieces,
      "mosaicPieceMap"  => $mosaicPieceMap,
      //"mosaicImage"     => $mosaicImage,
      "mosaicInfo"      => $mosaicInfo
      ];
    echo json_encode($response);
  })
    ->name('get mosaiclist')
    ;
}

ajaxでFB_Image_DLしてリンク取得:{
  $app->get('/common/mosaic_viewer/ajax/fb_image/:fb_image_id', function($fb_image_id) use ($app,$container){
    # 1.repository用意
    $FBHelper = $container['FBHelper'];

    # 2.オリジナル画像パス取得
    # TODO:fb_image_idから対応パスを取得しましょう
    $fb_image_path = "/img/miku.jpg";
    $fb_image_path = $FBHelper->getImagePath($fb_image_id);

    # 3.ajax_return
    $response = [
      "fb_image_path"=>$fb_image_path
      ];
    echo json_encode($response);
  })
    ->name('get fbimage')
    ;
}


ajaxのてすと:{
  $app->get('/common/mosaic_viewer/ajax', function() use ($app,$container){
    $res = ["hogehoge"=>"var","bar"=>"yes"];
    echo json_encode($res);
  })
    ->name('ajaxtest')
    ;
}
