<?php
//parameter無しのget
$app->get('/master/start_master', function() use ($app, $container) {
    $FBHelper = $container['FBHelper'];
    
    $userId = $FBHelper->getUserId();

    if (!$userId) {
        $loginUrl = $FBHelper->getLoginUrl();

    } else {
        $user = $container['repository.user']->findById($userId);
        var_dump($user);

        if ($user->id == '') {
            $userProfile = $FBHelper->getUserProfile();

            $user = new \Vg\Model\User();
            $user->setProperties($userProfile);
            var_dump($user);

            try {
                $container['repository.user']->insert($user);
            } catch (Exception $e) {
                $app->halt(500, $e->getMessage());
            }
            
            $user = $container['repository.user']->findById($userId);
            var_dump($user);
        }
    }
  $app->render('master/start_master.html.twig');
})
  ->name('start_master')
  ;

