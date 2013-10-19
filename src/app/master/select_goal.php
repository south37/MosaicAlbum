<?php
/**
 * ゴールイメージ選択
 */
$app->get('/master/select_goal', function() use ($app, $container) {
	$fbAlbums = $container['FBHelper']->getAlbums();
	$app->render('master/select_goal.html.twig', ['fbAlbums' => $fbAlbums]);
})
  ->name('select_goal')
  ;

$app->get('/master/get_images/:albumId', function ($albumId) use ($app, $container) {
   	echo $container['FBHelper']->getImagesInAlbum($albumId);
})
    ->name('get_images')
    ;
