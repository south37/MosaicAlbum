<?php
$container = include __DIR__ .'/../src/bootstrap.php';

use Slim\Slim;


アプリケーションの構築: {
    $app = new Slim();
    $app->view($container['twig']($app->request(), $app->router()));
}

各コントローラーの読み込み: {
    // コントローラーを増やす場合はここにrequireでコントローラーへのパスを追加する
    require  __DIR__ . '/../src/app/welcome.php';
    require  __DIR__ . '/../src/app/document.php';
    require  __DIR__ . '/../src/app/user.php';
    require  __DIR__ . '/../src/app/mosaic/mosaic_regist.php';
    require  __DIR__ . '/../src/app/mosaic/mosaic_album.php';
    require  __DIR__ . '/../src/app/mosaic/mosaic_viewer.php';
}

アプリケーションの実行: {
    $app->run();
}

