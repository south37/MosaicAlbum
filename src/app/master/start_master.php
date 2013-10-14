<?php
//parameter無しのget
$app->get('/master/start_master', function() use ($app, $container) {
    $FBHelper = $container['FBHelper'];
    
    $userId = $FBHelper->getUserId();

    if (!$userId) {
        $loginUrl = $FBHelper->getLoginUrl();

    } else {
        var_dump($userId);
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
            
            $user = $container['repository.user']->findByFbId($userId);
        }
    }
  $app->render('master/start_master.html.twig');
})
  ->name('start_master')
  ;

