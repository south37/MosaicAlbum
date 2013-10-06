<?php
//parameter無しのget
$app->get('/mosaic/regist', function() use ($app) {
        $app->render('mosaic/mosaic_regist.html.twig');
    })
    ->name('render_regist')
;

