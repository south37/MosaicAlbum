<?php
//parameter無しのget
$app->get('/master/start_master', function() use ($app, $container) {
    $FBHelper = $container['FBHelper'];
    
    $userId = $FBHelper->getUserId();

    if (!$userId) {
        $loginUrl = $FBHelper->getLoginUrl();

    } else {
        $user = $container['user.repository']->findById($userId);

        if (!$user) {
            $userProfile = $FBHelper->getUserProfile();
        }
    }
  $app->render('master/start_master.html.twig');
})
  ->name('start_master')
  ;

