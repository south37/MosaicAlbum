<?php
/**
 * トップページ
 */
$app->get('/', function() use ($app, $container) {
    $input = $app->request()->get();
    $session = $container['session'];

    // getパラメータとしてcodeを受け取っていれば、facebook認証後のリダイレクトと判定
    if (array_key_exists('code', $input)) {
        $app->redirect($app->urlFor('login_process'));
    }

    // getパラメータとしてgoalImageIdを受け取っていればguest、そうでなければhostと判定
    if (array_key_exists('goalImageId', $input)) {
        $session->set('goalImageId', $input['goalImageId']);
        $isHost = false;
    } else {
        $isHost = true;
    }

    // ログイン判定
    if ($session->get('isLogin') !== true) {
        $loginUrl = $container['FBHelper']->getLoginUrl();
    } else {
        $loginUrl = '';
    }

  $app->render('top/index.html.twig', ['loginUrl' => $loginUrl, 'goalImageId' => $session->get('goalImageId'), 'isHost' => $isHost]);
})
  ->name('top')
  ;

/**
 * facebookに埋め込まれると、最初にpostリクエストが送られるため、このページが見られる
 */
$app->post('/', function() use ($app, $container) {
    $session = $container['session'];

    if ($session->get('isLogin') === true) {
        $loginUrl = '';
    } else {
        $loginUrl = $container['FBHelper']->getLoginUrl();
    }

  $app->render('top/index.html.twig', ['loginUrl' => $loginUrl, 'goalImageId' => $session->get('goalImageId')]);
})
  ->name('top_post')
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
    $fbUserId = $FBHelper->getUserId();
    
    $redirect = $redirectIfNotLogin($container['session']);
    if (!$fbUserId) {
        $redirect();
    }

    $user = $container['repository.user']->findByFbId($fbUserId);

    if ($user->id == '') {
        $userProfile = $FBHelper->getUserProfileForRegistration();
        
        if ($userProfile === []) {
            $redirect();
        }

        $user = new \Vg\Model\User();
        $user->setProperties($userProfile);

        try {
            $userId = $container['repository.user']->insert($user);
            $user->setProperties(['id' => $userId]);
        } catch (Exception $e) {
            $app->halt(500, $e->getMessage());
        }
    }

    $container['session']->set('isLogin', true);
    $container['session']->set('userId', $user->id);

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

