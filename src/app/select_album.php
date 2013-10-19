<?php
// アルバムセレクト＿マスター：使うアルバムを選択
$app->get('/master/album_select_master', function() use ($app, $container) {
	$albumList = [];
	# 自分のFacebookアルバムのリストを取得
	$fbAlbums = $container['FBHelper']->getAlbums();
	foreach ($fbAlbums as $fbAlbum) {
		# アルバムの写真一覧を取得（fbImageId, imagePath）
		$images = $container['FBHelper']->getImagesInAlbum($fbAlbum['id']);
		# アルバムリストにアルバムの写真一覧を保存
		$fbAlbum['images'] = $images;
		array_push($albumList, $fbAlbum);
	}
	$app->render('master/album_select_master.html.twig', ["albumList"=>$albumList]);
})
  ->name('album_select_master')
  ;

// アルバムを追加
$app->post('/master/album_select_master', function() use ($app, $container) {
	# 追加するアルバム
    $fbAlbum = $app->request()->post();
	# DBに登録
	$userId = $container['session']->get('userId');
	$data = array('id' => 0, 'user_id' => $userId, 'goal_image_id' => $goalImageId, 'fb_album_id' => $fbAlbum['id']);
	$album = new \Vg\Model\Album();
	$album->setProperties($data);
	$albumId = $container['repository.album']->insert($album);
	# アルバムビューアへ
	$app->redirect($app->urlFor('album_viewer', ['goalImageId'=>$container['session']->get('goalImageId')]));
})
	->name('add_album_master')
    ;

/**
 * アルバムセレクト
 */
$app->get('/select_album', function() use ($app, $container) {
    $app->render('select_goal/select_goal.html.twig');
})
    ->name('select_album')
    ;
