<?php
/**
 * このファイルはアプリケーションで利用するライブラリや設定項目などを定義する場所
 */
require __DIR__ .'/../vendor/autoload.php';

$container = include __DIR__ . '/config.php';

// Twig
$container['twig'] = $container->protect(function($request, $router) use ($container) {
        \Slim\Extras\Views\Twig::$twigTemplateDirs = $container['twig.templateDir'];

        $twig = new \Slim\Extras\Views\Twig;
        $env = $twig->getEnvironment();
        // asset function
        $env->addFunction(new Twig_SimpleFunction('asset', function ($path) use ($request) {
                return $request->getRootUri() . '/' .  trim($path, '/');
            }));
        // urlFor function
        $env->addFunction(new Twig_SimpleFunction('urlFor', function ($name, $params = []) use ($router) {
                return $router->urlFor($name, $params);
            }));
        // debug
        $env->addFunction(new Twig_SimpleFunction('debug', function (){
                echo "<pre>";
                debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                echo "</pre>";
            }));
        // global session
        $env->addGlobal('session', $container['session']);

        return $twig;
    });

// データベース PDO
$container['db'] = $container->share(function($c){
        return \Vg\Database::connection($c['db.host'], $c['db.database'], $c['db.user'], $c['db.password']);
    });

// セッション
$container['session'] = $container->share(function() {
        return new \Vg\Session();
    });

// ゴールイメージリポジトリ
$container['repository.goalImage'] = $container->share(function($c){
        return new \Vg\Repository\GoalImageRepository($c['db']);
    });

// アルバムリポジトリ
$container['repository.album'] = $container->share(function($c){
        return new \Vg\Repository\AlbumRepository($c['db']);
    });

// アルバムイメージリポジトリ
$container['repository.albumImage'] = $container->share(function($c){
        return new \Vg\Repository\AlbumImageRepository($c['db']);
    });

// アルバムユーザーリポジトリ
$container['repository.albumUser'] = $container->share(function($c){
        return new \Vg\Repository\AlbumUserRepository($c['db']);
    });

// イメージリポジトリ
$container['repository.image'] = $container->share(function($c){
        return new \Vg\Repository\ImageRepository($c['db']);
    });

// モザイクピースリポジトリ
$container['repository.mosaicPiece'] = $container->share(function($c){
        return new \Vg\Repository\MosaicPieceRepository($c['db']);
    });

// ユーザーリポジトリ
$container['repository.user'] = $container->share(function($c){
        return new \Vg\Repository\UserRepository($c['db']);
    });

// ユーズドイメージリポジトリ
$container['repository.usedImage'] = $container->share(function($c){
        return new \Vg\Repository\UsedImageRepository($c['db']);
});

// FBHelper
$container['FBHelper'] = $container->share(function($c){
        return new \Vg\Repository\FBHelperRepository($c);
    });

return $container;
