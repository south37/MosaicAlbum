<?php
/**
 * トップページ
 */
$app->get('/master/start_master', function() use ($app, $container) {
    $FBHelper = $container['FBHelper'];

    /*
    $userId = $FBHelper->getUserId();

    #DEBUG:
    echo $userId;

    if (!$userId) {
     */ 
    if ( $container['session']->get('isLogin') !== true ) {
      $loginUrl = $FBHelper->getLoginUrl();
    } else {
      $loginUrl = '';
    }

  $app->render('master/start_master.html.twig', ['loginUrl' => $loginUrl]);
})
  ->name('top')
  ;

/**
 * アプリ独自のログイン処理
 */
$app->get('/login_process', function() use ($app, $container) {
    $FBHelper = $container['FBHelper'];

    $userId = $FBHelper->getUserId();

    if (!$userId) {
        $app->redirect($app->urlFor('top'));
    }

    $user = $container['repository.user']->findByFbId($userId);

    if ($user->id == '') {
        $userProfile = $FBHelper->getUserProfile();

        $user = new \Vg\Model\User();
        $user->setProperties($userProfile);

        try {
            $container['repository.user']->insert($user);
        } catch (Exception $e) {
            $app->halt(500, $e->getMessage());
        }
    }

    $container['session']->set('isLogin', true);

    $app->redirect($app->urlFor('top'));
})
    ->name('login_process')
    ;
    

/**
 * ログインしていない場合はtop画面にリダイレクト
 *
 * http://docs.slimframework.com/#Route-Middleware
 *
 * @param $session
 *
 * @return callable
 */
$rediretIfNotLogin = function ( $session ) {
    return function () use ( $session ) {
        if ( $session->get('isLogin') !== true ) {
            $app = \Slim\Slim::getInstance();
            $app->flash('error', 'Login required');
            $app->redirect($app->urlFor('top'));
        }
    };
};
