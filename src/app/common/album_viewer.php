<?php
// アルバムビューア：現在の素材画像一覧を表示
$app->get('/common/album_viewer/:goalImageId', function($goalImageId) use ($app, $container) {
	# セッションにゴールイメージIDを登録
	$container['session']->set('goalImageId', $goalImageId);
	# ゴールイメージIDを渡してDBからFacebookアルバムIDのリストを取得
	$fbAlbumIds = $container['repository.album']->getFbAlbumIdList($goalImageId);
	# イメージパスリスト（現在の素材画像一覧として表示する）	
	$imagePathList = [];
	foreach ($fbAlbumIds as $fbAlbumId) {
		# FacebookアルバムIDを渡してFBHelperからFacebookイメージIDとイメージパスのリストを取得
		$images = $container['FBHelper']->getImagesInAlbum($fbAlbumId);
		foreach ($images as $image) {
			# DBに登録
			$imageID = $container['repository.image']->insert($image['id']);
			# イメージパスリストにイメージパスを保存
			array_push($imagePathList, $image['imagePath']);
		}
	}
	# アルバムリスト（アルバム選択画面で表示する）
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
	$app->render('common/album_viewer.html.twig',
  		["imagePathList"=>$imagePathList, "albumList"=>$albumList]);
})
	->name('album_viewer')
	;

// アルバムを追加
$app->post('/common/album_viewer', function() use ($app,$container) {
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
	->name('add_album')
	;
