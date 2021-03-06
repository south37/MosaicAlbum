<?php
/**
 * ゴールイメージ選択
 */
$app->get('/select', function() use ($app, $container) {
    $app->render('select/select.html.twig');
})
    ->name('select')
    ;

/**
 * request dialogが立ち上がる前。この時点でDBにinsert
 */
$app->get('/select/insertGoalImage/:fbGoalImageId', function($fbGoalImageId) use ($app, $container) {
    // validation
    $validator = new \Vg\Validator\GoalImageRegister();
    if ($validator->validate(['goalImageId' => $fbGoalImageId])) {
        $goalImageId = $container['repository.goalImage']->insert($fbGoalImageId);
        $container['session']->set('goalImageId', $goalImageId);
        
        echo $goalImageId;
        exit;
    }
    $app->render('select/select.html.twig', ['errors' => $validator->errors()]);
})
    ->name('select_insert_goal_image')
    ;

$app->post('/select', function() use ($app, $container) {
    $app->redirect($app->urlFor('album_viewer'));
})
    ->name('select_post')
    ;

/**
 * ゴールイメージ submit後
 */
//$app->post('/select', function() use ($app, $container) {
//    $input = $app->request()->post();
//    $fbGoalImageId = $input['goalImageId'];
//    // validation
//    $validator = new \Vg\Validator\GoalImageRegister();
//    if ($validator->validate($input)) {
//        $goalImageId = $container['repository.goalImage']->insert($fbGoalImageId);
//        $container['session']->set('goalImageId', $goalImageId);
//        $app->redirect($app->urlFor('album_viewer', ['goalImageId' => $goalImageId]));
//    }
//    $app->render('select/select.html.twig', ['errors' => $validator->errors(), 'input' => $input]);
//})
//    ->name('select_post')
//    ;

/**
 * ゴールイメージの入ったアルバムを選択
 */
$app->get('/select/modal_album', function () use ($app, $container) {
    $fbAlbums = $container['FBHelper']->getAlbums();
    /* 
    $fbAlbums = [[ 
        "id"            => "400193440110501",
        "name"          => "2013年秋日本物理学会",
        "thumbnailPath" => "https://fbcdn-photos-g-a.akamaihd.net/hphotos-ak-prn2/1378561_400194690110376_1611527405_s.jpg"
    ], [
        "id"            => "367905490005963",
        "name"          => "Profile Pictures",
        "thumbnailPath" => "https://fbcdn-photos-e-a.akamaihd.net/hphotos-ak-ash3/533896_367905493339296_1533302163_s.jpg"    
    ]];
     */ 
    $app->render('select/modal_album.html.twig', ['fbAlbums' => $fbAlbums]);
})
    ->name('select_goal_modal_album')
    ;

/**
 * ゴール写真を選択
 */
$app->post('/select/modal_image', function () use ($app, $container) {
    $input = $app->request()->post();

    $images = $container['FBHelper']->getImagesInAlbum($input['albumId']);
    /* 
    $images = [[
        "id"            => "400192746777237",
        "imagePath"     => "https://fbcdn-sphotos-d-a.akamaihd.net/hphotos-ak-prn1/s720x720/1385787_400192746777237_774514542_n.jpg",
        "thumbnailPath" => "https://fbcdn-photos-d-a.akamaihd.net/hphotos-ak-prn1/1385787_400192746777237_774514542_s.jpg",
    ], [
        "id"            => "400192783443900",
        "imagePath"     => "https://fbcdn-sphotos-f-a.akamaihd.net/hphotos-ak-prn2/s720x720/1384302_400192783443900_479273178_n.jpg",
        "thumbnailPath" => "https://fbcdn-photos-f-a.akamaihd.net/hphotos-ak-prn2/1384302_400192783443900_479273178_s.jpg",
    ]];
     */ 
    $app->render('select/modal_image.html.twig', ['images' => $images]);
})
    ->name('select_modal_image')
    ;


/**
 * 友達を招待
 */
$app->get('/select/modal_request', function () use ($app, $container) {
    $app->render('select/modal_request.html.twig');
})
    ->name('select_modal_request')
    ;

