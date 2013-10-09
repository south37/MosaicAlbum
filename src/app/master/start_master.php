<?php
//parameter無しのget
$app->get('/master/start_master', function() use ($app) {
  $app->render('master/start_master.html.twig');
})
  ->name('start_master')
  ;

