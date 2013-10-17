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
	$app->render('common/album_viewer.html.twig', ["imagePathList"=>$imagePathList]);
})
	->name('album_viewer')
	;
