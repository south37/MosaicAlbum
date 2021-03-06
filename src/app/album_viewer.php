<?php
// アルバムビューア：現在の素材画像一覧を表示
$app->get('/album_viewer', function() use ($app, $container) {
	# セッションからゴールイメージIDを取得
	$goalImageId = $container['session']->get('goalImageId');
	# ゴールイメージIDを渡してDBからFacebookアルバムIDのリストを取得
	$fbAlbumIds = $container['repository.album']->getFbAlbumIdList($goalImageId);
	# イメージパスリスト（現在の素材画像一覧として表示する）	
	$imagePathList = [];
	foreach ($fbAlbumIds as $fbAlbumId) {
		# FacebookアルバムIDを渡してFBHelperからFacebookイメージIDとイメージパスのリストを取得
		$images = $container['FBHelper']->getImagesInAlbum($fbAlbumId);
		foreach ($images as $image) {
			// validation
			$validator = new \Vg\Validator\ImageRegister();
			if ($validator->validate($image)) {
				# DBに登録
				$imageID = $container['repository.image']->insert($image['id']);
				# イメージパスリストにイメージパスを保存
				array_push($imagePathList, $image['thumbnailPath']);
			}
			else {
				$app->render('album_viewer/album_viewer.html.twig', ['errors' => $validator->errors()]);
			}
		}
	}
	$app->render('album_viewer/album_viewer.html.twig', ["imagePathList"=>$imagePathList]);
})
	->name('album_viewer')
    ;

// アルバムを追加
$app->post('/album_viewer', function() use ($app, $container) {
    $input = $app->request()->post();
    #各変数
    $userId      = $container['session']->get('userId');
    $goalImageId = $container['session']->get('goalImageId');
    $fbAlbumId   = $input['albumId'];
    #アルバムをDBに保存 
    $album = new \Vg\Model\Album();
    $album->setProperties([
        'user_id'       => $userId,
        'goal_image_id' => $goalImageId,
        'fb_album_id'   => $fbAlbumId,
    ]);
    $container['repository.album']->insert($album);
    // アルバムを追加したので、モザイク画を未作成の状態にする
    $container['repository.goalImage']->setIsMakeMosaic(0, $goalImageId);
	#アルバムビューアへ戻る
	$app->redirect($app->urlFor('album_viewer', ['goalImageId' => $goalImageId]));
})
	->name('album_viewer_post')
    ;

/**
 * アルバムを選択
 */
$app->get('/album_viewer/modal', function () use ($app, $container) {
    $fbAlbums = $container['FBHelper']->getAlbums();
    $app->render('album_viewer/modal.html.twig', ['fbAlbums' => $fbAlbums]);
})
    ->name('album_viewer_modal')
    ;
