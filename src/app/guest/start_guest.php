<?php
//parameter無しのget
$app->get('/guest/start_guest', function() use ($app) {
  $app->render('guest/start_guest.html.twig');
})
  ->name('start_guest')
  ;

