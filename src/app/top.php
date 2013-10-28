<?php
/**
 * トップページ(host)
 */
$app->get('/', function() use ($app, $container) {
    $input = $app->request()->get();
    $session = $container['session'];

    // ログイン判定
    if ($session->get('isLogin') !== true) {
        $loginUrl = $container['FBHelper']->getLoginUrl();
    } else {
        $loginUrl = '';
    }

    // guestかどうかの判定
    if ($container['session']->get('isGuest') == true) {
        $isGuest = true;
    } else {
        $isGuest = false;
    }

    $app->render('top/index.html.twig', [
        'loginUrl'    => $loginUrl, 
        'goalImageId' => $session->get('goalImageId'),
        'isGuest'     => $isGuest,
    ]);
})
  ->name('top')
  ;

/**
 *fbGoalImageIdにアクセスされた時は、guestとして処理
 */
$app->get('/guest/:fbGoalImageId', function($goalImageId) use ($app, $container) {
    $container['session']->set('goalImageId', $goalImageId);
    $container['session']->set('isGuest', true);
    

    $app->redirect($app->urlFor('top'));
})
    ->name('top_guest')
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
        
        // validation未実装
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

    if ($container['session']->get('isGuest') == true) {
        $app->redirect($app->urlFor('album_viewer'));
    } else {
        $app->redirect($app->urlFor('select'));
    }
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

