<?php
//parameter無しのget
$app->get('/master/member_select', function() use ($app) {
  $app->render('master/member_select.html.twig');
})
  ->name('member_select')
  ;

