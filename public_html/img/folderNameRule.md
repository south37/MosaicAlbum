## folder名とその役割

* goal_img
目標となるimageを登録していく．消えない．
masterがgoal登録時に保存される．
filenameは(fb_img_id).(ext)?

ex.
/img/goal_img/12345678.png

* mosaic_img  
変換されたモザイク画が保存されていく．消えない．
filenameはmosaic($goaliImageId).(ext)

ex
/img/mosaic_img/mosaic1.png

* resize_img  
fbから拾ってきたアルバム画像をリサイズしてがんがんぶっ込んでおく．
filenameは(number).(extension)

ex
/img/resize_img/1.png

* resource_img
mosaicを作る際に一時的に画像を拾ってくるフォルダ．
filenameは($fb_img_id).(ext)
mosaic生成時点で必ず消去すること！

ex.
/img/resource_img/12345678.png

