<?php
ページ読み込みテスト:{
  $app->get('/common/mosaic_viewer/test', function() use ($app) {
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
}

ajax_mosaic画像リスト取得:{
  $app->get('/common/mosaic_viewer/ajax_list', function() use ($app, $container){
    # 1.リポジトリ，必要変数の確保 
    $FBRep = $container['FBHelper'];
    $goalImageId = $container['session']->get('goalImageId');
    $goalImageId = 1;

    $mosaicPieceRep = $container['repository.mosaicPiece'];

    # 2.参加ユーザの画像リスト取得
    # 参加ユーザリスト作成

    # 参加ユーザアイコン取得
    $userIconPathList = ['img/miku.jpg'];

    # 3.mosaic画像リスト取得(テクスチャリスト/ピースマップ)
    $mosaicTextures = [
     '1.png', 
     '2.png',
     '3.png',
     '4.png',
     '5.png',
     '6.png',
     '7.png',
     '8.png',
     '9.png'
      ];
    $mosaicPieces = $mosaicPieceRep->getMosaicPieceList($goalImageId);

    # 4.mosaic画像本体取得
    $mosaicImage = '/img/mosaic_img/mosaic'.$goalImageId.'.png';

    # 5.ajax_return
    $response = [
      "userIcons" => $userIconPathList,
      "mosaicPieces" => $mosaicPieces,
      "mosaicTextures"=>$mosaicTextures,
      "mosaicImage"=>$mosaicImage
      ];
    echo json_encode($response);
  })
    ->name('get mosaiclist')
    ;
}

ajaxでFB_Image_DLしてリンク取得:{
  $app->get('/common/mosaic_viewer/ajax_fb_image/:fb_image_id', function($fb_image_id) use ($app,$container){
    # 1.repository用意
    $FBHelper = $container['FBHelper'];

    # 2.画像パス取得
    # TODO:fb_image_idから対応パスを取得しましょう
    $fb_image_path = "/img/miku.jpg";

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
  $app->get('/common/mosaic_viewer/ajax', function() use ($app){
    $res = ["hogehoge"=>"var","bar"=>"yes"];

    echo json_encode($res);
  })
    ->name('ajaxtest')
    ;
}
