<?php
//parameter無しのget
$app->get('/common/album_viewer', function() use ($app) {
  $app->render('common/album_viewer.html.twig');
})
  ->name('album_viewer')
  ;

