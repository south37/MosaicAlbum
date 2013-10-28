<?php
require_once 'Mail.php';
$container = include __DIR__ .'/../src/bootstrap.php';

use Slim\Slim;


アプリケーションの構築: {
    $app = new Slim();
    $app->view($container['twig']($app->request(), $app->router()));
}

各コントローラーの読み込み: {
    // コントローラーを増やす場合はここにrequireでコントローラーへのパスを追加する
    require  __DIR__ . '/../src/app/top.php';
    require  __DIR__ . '/../src/app/select.php';
    require  __DIR__ . '/../src/app/album_viewer.php';
    require  __DIR__ . '/../src/app/common/mosaic_viewer.php';

    require  __DIR__ . '/../src/app/guest/album_select_guest.php';
}

アプリケーションの実行: {
    $app->run();
}

