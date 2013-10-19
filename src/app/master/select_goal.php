<?php
/**
 * ゴールイメージ選択
 */
$app->get('/master/select_goal', function() use ($app, $container) {
    $app->render('master/select_goal.html.twig');
})
    ->name('select_goal')
    ;

$app->get('/master/albums', function () use ($app, $container) {
    $fbAlbums = [[ 
        "id"            => "400193440110501",
        "name"          => "2013年秋日本物理学会",
        "thumbnailPath" => "https://fbcdn-photos-g-a.akamaihd.net/hphotos-ak-prn2/1378561_400194690110376_1611527405_s.jpg"
    ], [
        "id"            => "367905490005963",
        "name"          => "Profile Pictures",
        "thumbnailPath" => "https://fbcdn-photos-e-a.akamaihd.net/hphotos-ak-ash3/533896_367905493339296_1533302163_s.jpg"    
    ]];
    $app->render('master/albums.html.twig', ['fbAlbums' => $fbAlbums]);
})
    ->name('get_albums')
    ;

$app->get('/master/get_images/:albumId', function ($albumId) use ($app, $container) {
   	$images = $container['FBHelper']->getImagesInAlbum($albumId);
    $imagesJson = json_encode($images);
    
    echo $imagesJson;
})
    ->name('get_images')
    ;
