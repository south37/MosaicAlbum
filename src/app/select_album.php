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
    $app->render('select_album/select_album.html.twig');
})
    ->name('select_album')
    ;

/**
 * アルバムを選択
 */
$app->get('/select_album/modal', function () use ($app, $container) {
    $fbAlbums = [[ 
        "id"            => "400193440110501",
        "name"          => "2013年秋日本物理学会",
        "thumbnailPath" => "https://fbcdn-photos-g-a.akamaihd.net/hphotos-ak-prn2/1378561_400194690110376_1611527405_s.jpg"
    ], [
        "id"            => "367905490005963",
        "name"          => "Profile Pictures",
        "thumbnailPath" => "https://fbcdn-photos-e-a.akamaihd.net/hphotos-ak-ash3/533896_367905493339296_1533302163_s.jpg"    
    ]];
    $app->render('select_album/modal.html.twig', ['fbAlbums' => $fbAlbums]);
})
    ->name('select_album_modal')
    ;
