<?php
ページ読み込みテスト:{
  $app->get('/common/mosaic_viewer/test', function() use ($app) {
    $app->render('common/mosaic_viewer.html.twig');
  })
    ->name('mosaic_viewer_test')
    ;
}

ゴールIDをもとにアクセスされたときの処理:{
  $app->get('/common/mosaic_viewer/:goalID', function($goalID) use ($app){

    #セキュリティ処理


    #参加ユーザリスト作成


    #参加ユーザ：FBアイコンパス取得


    #モザイク画像取得


    #モザイクピースのリストを取得


    #画面レンダリング
    $app->render('common/mosaic_viewer.html.twig',['goalId' => $goalID]);
  })
    ->name('mosaic_viewer')
    ;
}
