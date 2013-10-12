<?php
//parameter無しのget
$app->get('/guest/album_select_guest', function() use ($app) {


  $app->render('guest/album_select_guest.html.twig');
})
  ->name('album_select_guest')
  ;

$app->get('/guest/album_select_guest/create', function() use ($app){
  print "create!:";

  # 1.repository
  #


  $app->render('guest/album_select_guest.html.twig');

})
  ->name('create_mosaic')
  ;
