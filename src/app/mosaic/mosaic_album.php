<?php

//parameter無しのget
$app->get('/mosaic/album', function() use ($app) {
        $app->render('mosaic/mosaic_album.html.twig');
    })
    ->name('render_album')
;

