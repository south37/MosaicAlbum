<?php
ページ読み込みテスト:{
  $app->get('/common/mosaic_viewer/test', function() use ($app) {
    $app->render('common/mosaic_viewer.html.twig');
  })
    ->name('mosaic_viewer_test')
    ;
}

ゴールIDをもとにアクセスされたときの処理:{
  $app->get('/common/mosaic_viewer/:goalId', function($goalId) use ($app){

    # 1:init
    #リポジトリの準備
    

    # 2:user
    #セキュリティ処理


    #参加ユーザリスト作成


    #参加ユーザ：FBアイコンパス取得


    # 3:mosaic
    #モザイク画像取得


    #モザイクピースのリストを取得


    # 4:render
    #画面レンダリング
    $app->render('common/mosaic_viewer.html.twig',['goalId' => $goalId]);
  })
    ->name('mosaic_viewer')
    ;
}
