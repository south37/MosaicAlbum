<?php
/**
 * トップページ
 */
$app->get('/top', function() use ($app, $container) {
    $input = $app->request()->get();
    $session = $container['session'];

    if (array_key_exists('code', $input)) {
        $app->redirect($app->urlFor('login_process'));
    }

    if (array_key_exists('goalImageId', $input)) {
        $session->set('goalImageId', $input['goalImageId']);
    }

    if ($session->get('isLogin') === true) {
        $loginUrl = '';
    } else {
        $loginUrl = $container['FBHelper']->getLoginUrl();
    }

  $app->render('top/index.html.twig', ['loginUrl' => $loginUrl, 'goalImageId' => $session->get('goalImageId')]);
})
  ->name('top')
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
$redirectIfNotLogin = function ( $session ) {
    return function () use ( $session ) {
        if ( $session->get('isLogin') !== true ) {
            $app = \Slim\Slim::getInstance();
            $app->flash('error', 'Login required');
            $app->redirect($app->urlFor('top'));
        }
    };
};

/**
 * アプリ独自のログイン処理
 */
$app->get('/login_process', function() use ($app, $container, $redirectIfNotLogin) {
    $FBHelper = $container['FBHelper'];
    $userId = $FBHelper->getUserId();
    
    $redirect = $redirectIfNotLogin($container['session']);
    if (!$userId) {
        $redirect();
    }

    $user = $container['repository.user']->findByFbId($userId);

    if ($user->id == '') {
        $userProfile = $FBHelper->getUserProfileForRegistration();
        
        if ($userProfile === []) {
            $redirect();
        }

        $user = new \Vg\Model\User();
        $user->setProperties($userProfile);

        try {
            $container['repository.user']->insert($user);
        } catch (Exception $e) {
            $app->halt(500, $e->getMessage());
        }
    }

    $container['session']->set('isLogin', true);
    $container['session']->set('userId', $userId);

    $app->redirect($app->urlFor('select'));
})
    ->name('login_process')
    ;
    
/**
 * ログアウト
 */
$app->get('/logout', function() use ($app, $container) {
        $container['FBHelper']->destroySession();
        $container['session']->clear();
        $app->redirect($app->urlFor('top'));
    })
    ->name('logout')
    ;

