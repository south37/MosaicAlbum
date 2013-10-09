<?php
//parameter無しのget
$app->get('/common/mosaic_viewer', function() use ($app) {
  $app->render('common/mosaic_viewer.html.twig');
})
  ->name('mosaic_viewer')
  ;

