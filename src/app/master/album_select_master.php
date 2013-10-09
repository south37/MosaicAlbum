<?php
//parameter無しのget
$app->get('/master/album_select_master', function() use ($app) {
  $app->render('master/album_select_master.html.twig');
})
  ->name('album_select_master')
  ;

