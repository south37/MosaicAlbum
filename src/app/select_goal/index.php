<?php
/**
 * ゴールイメージ選択
 */
$app->get('/select_goal', function() use ($app, $container) {
    $app->render('select_goal/select_goal.html.twig');
})
    ->name('select_goal')
    ;

/**
 * ゴールイメージ submit後
 */
$app->post('/select_goal', function() use ($app, $container) {
    $input = $app->request()->post();
    var_dump($input);

    $app->render('select_goal/select_goal.html.twig');
})
    ->name('select_goal_post')
    ;

/**
 * ゴールイメージの入ったアルバムを選択
 */
$app->get('/select_goal/select_album', function () use ($app, $container) {
    $fbAlbums = [[ 
        "id"            => "400193440110501",
        "name"          => "2013年秋日本物理学会",
        "thumbnailPath" => "https://fbcdn-photos-g-a.akamaihd.net/hphotos-ak-prn2/1378561_400194690110376_1611527405_s.jpg"
    ], [
        "id"            => "367905490005963",
        "name"          => "Profile Pictures",
        "thumbnailPath" => "https://fbcdn-photos-e-a.akamaihd.net/hphotos-ak-ash3/533896_367905493339296_1533302163_s.jpg"    
    ]];
    $app->render('select_goal/select_album.html.twig', ['fbAlbums' => $fbAlbums]);
})
    ->name('select_album')
    ;

/**
 * ゴール写真を選択
 */
$app->post('/select_goal/select_image', function () use ($app, $container) {
    $input = $app->request()->post();

    //    $images = $container['FBHelper']->getImagesInAlbum($input['albumId']);
    $images = [[
        "id"            => "400192746777237",
        "imagePath"     => "https://fbcdn-sphotos-d-a.akamaihd.net/hphotos-ak-prn1/s720x720/1385787_400192746777237_774514542_n.jpg",
        "thumbnailPath" => "https://fbcdn-photos-d-a.akamaihd.net/hphotos-ak-prn1/1385787_400192746777237_774514542_s.jpg",
    ], [
        "id"            => "400192783443900",
        "imagePath"     => "https://fbcdn-sphotos-f-a.akamaihd.net/hphotos-ak-prn2/s720x720/1384302_400192783443900_479273178_n.jpg",
        "thumbnailPath" => "https://fbcdn-photos-f-a.akamaihd.net/hphotos-ak-prn2/1384302_400192783443900_479273178_s.jpg",
    ]];
    
    $app->render('select_goal/select_image.html.twig', ['images' => $images]);
})
    ->name('select_image')
    ;
