<?php
/**
 * このファイルはアプリケーションで利用するライブラリや設定項目などを定義する場所
 */
require __DIR__ .'/../vendor/autoload.php';

$container = include __DIR__ . '/config.php';

// Twig
$container['twig'] = $container->protect(function($request, $router) use($container){
        \Slim\Extras\Views\Twig::$twigTemplateDirs = $container['twig.templateDir'];

        $twig = new \Slim\Extras\Views\Twig;
        $env = $twig->getEnvironment();
        // asset function
        $env->addFunction(new Twig_SimpleFunction('asset', function ($path) use ($request){
                return $request->getRootUri() . '/' .  trim($path, '/');
            }));
        // urlFor function
        $env->addFunction(new Twig_SimpleFunction('urlFor', function ($name, $params = []) use ($router){
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

// バリデーター
$container['validator'] = function() {
    return \Symfony\Component\Validator\Validation::createValidator();
};

// Validationのエラーリストにエラーを追加するためのエイリアス
$container['error'] = $container->protect(function($message) {
    return new \Symfony\Component\Validator\ConstraintViolation($message, '', [], [], [], []);
});


// モデル
$container['repository.user'] = $container->share(function($c){
        return new \Vg\Repository\UserRepository($c['db']);
    });

return $container;