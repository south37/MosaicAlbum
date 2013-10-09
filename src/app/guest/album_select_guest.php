<?php
//parameter無しのget
$app->get('/guest/album_select_guest', function() use ($app) {
  $app->render('guest/album_select_guest.html.twig');
})
  ->name('album_select_guest')
  ;

