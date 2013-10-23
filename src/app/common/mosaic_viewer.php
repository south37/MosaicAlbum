<?php
ページ読み込みテスト:{
  $app->get('/common/mosaic_viewer/test', function() use ($app) {
    $app->render('common/mosaic_viewer.html.twig');
  })
    ->name('mosaic_viewer_test')
    ;
}

ゴールIDをもとにアクセスされたときの処理:{
  $app->get('/common/mosaic_viewer', function() use ($app, $container){

    # 1:init
    #リポジトリの準備
    $FBRep = $container['FBHelper'];
    echo $FBRep->getUserId();

    $goalImageId = $container['session']->get('goalImageId');
    echo "goal:".$goalImageId;

    # 2:user
    # opt:セキュリティ処理


    #参加ユーザリスト作成


    #参加ユーザ：FBアイコンパス取得


    # 3:mosaic
    # モザイクオリジナル画像取得


    #モザイク画像取得


    #モザイクピースのリストを取得


    # 4:render
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
    $mosaicPieceRep = $container['repository.mosaicPiece'];

    # 2.参加ユーザの画像リスト取得
    $userIconPathList = ['img/miku.jpg'];

    # 3.mosaic画像リスト取得
    $mosaicPieces = [];
    $mosaicPieces = $mosaicPieceRep->getMosaicPieceList($goalImageId);
    # 4.ajax_return
    $response = ["userIconPathLsit" => $userIconPathList, "mosaicPieces" => $mosaicPieces];
    echo json_encode($response);
  })
    ->name('get mosaiclist')
    ;
}

ajaxのてすと:{
  $app->get('/common/mosaic_viewer_test/ajax', function() use ($app){
    $res = ["hogehoge"=>"var","bar"=>"yes"];

    echo json_encode($res);
  })
    ->name('ajaxtest')
    ;
}
