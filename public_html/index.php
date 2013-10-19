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
//    require  __DIR__ . '/../src/app/user.php';
    require  __DIR__ . '/../src/app/common/mosaic_viewer.php';
    require  __DIR__ . '/../src/app/common/album_viewer.php';
    require  __DIR__ . '/../src/app/master/start_master.php';
    require  __DIR__ . '/../src/app/master/member_select.php';
    require  __DIR__ . '/../src/app/master/album_select_master.php';
    require  __DIR__ . '/../src/app/guest/start_guest.php';
    require  __DIR__ . '/../src/app/guest/album_select_guest.php';
}

アプリケーションの実行: {
    $app->run();
}

