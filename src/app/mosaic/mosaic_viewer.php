<?php
//parameter無しのget
$app->get('/mosaic/viewer', function() use ($app) {
        $app->render('mosaic/mosaic_viewer.html.twig');
    })
    ->name('render_viewer')
;

