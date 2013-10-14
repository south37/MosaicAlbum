<?php
//parameter無しのget
$app->get('/master/start_master', function() use ($app, $container) {
  $app->render('master/start_master.html.twig');
})
  ->name('start_master')
  ;

