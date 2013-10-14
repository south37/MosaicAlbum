<?php
/*
 *  GD 利用、bitmap 形式画像の読み込み
 * comment 編集miz [2009/04/25] →公開サイト移動 [2012/08/28]
 * @param $filename : 実行中phpからアクセスできる画像パス名
 * @return GD truecolor イメージオブジェクト
      作成失敗時 false
/*********************************************/
/* Fonction: ImageCreateFromBMP */
/* Author: DHKold */
/* Contact: admin@dhkold.com */
/* Date: The 15th of June 2005 */
/* Version: 2.0B */
/* http://php.benscom.com/manual/ja/function.imagecreate.php#53879 */
/*********************************************/
function imageCreateFromBMP($filename)
{
	//  画像ファイルをバイナリーモードでopen
	if (! $f1 = fopen($filename,"rb")) return FALSE;

	//1 : 概要データのロード：file_type, file_size, reserved, bitmap_offset
	$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));

	//1' : file type のチェック
	if ($FILE['file_type'] != 19778) return FALSE;
	// 19778=> 0x4D42 ->`MB`  先頭２バイトに BM と入っている、リトルエンディアンで読み出すとMB となる

	//2 : BMPデータのロード：
	$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
	'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
	'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
	$BMP['colors'] = pow(2,$BMP['bits_per_pixel']);

	//2' : pixel情報のセット
	if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
	$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
	$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
	$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
	$BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
	$BMP['decal'] = 4-(4*$BMP['decal']);
	if ($BMP['decal'] == 4) $BMP['decal'] = 0;

	//3 : paletteデータのロード：
	//  16 bit images (= color 65536 )以上ではパレットを持っていないので、8bit colorまでを対象とする
	$PALETTE = array();
	if ($BMP['colors'] <  65536){
		$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
	}

	//4 : imageデータのロード：ビットごとの色情報読みとり
	$IMG = fread($f1,$BMP['size_bitmap']);
	//4' : file からの読みとり完了
	fclose($f1);

	//5 : GD による TrueColor イメージ作成
	$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
	$P = 0;
	$Y = $BMP['height']-1;
	$VIDE = chr(0);	//  桁合わせ用
	//5' :  TrueColor イメージの各ビットに色設定
	while ($Y >= 0){
		$X=0;
		while ($X < $BMP['width']){
			if ($BMP['bits_per_pixel'] == 24){
				$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
			}elseif ($BMP['bits_per_pixel'] == 16){ 
				$COLOR = unpack("v",substr($IMG,$P,2));
				$blue = ($COLOR[1] & 0x001f) << 3;
				$green = ($COLOR[1] & 0x07e0) >> 3;
				$red = ($COLOR[1] & 0xf800) >> 8;
				$COLOR[1] = $red * 65536 + $green * 256 + $blue;
			}elseif ($BMP['bits_per_pixel'] == 8){ 
	// 8bit palette mode, 256colors
				$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
				$COLOR[1] = $PALETTE[$COLOR[1]+1];
			}elseif ($BMP['bits_per_pixel'] == 4){
				$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
				if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
				$COLOR[1] = $PALETTE[$COLOR[1]+1];
			}elseif ($BMP['bits_per_pixel'] == 1){
				$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
				if (($P*8)%8 == 0) $COLOR[1] = $COLOR[1] >>7;
				elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
				elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
				elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
				elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
				elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
				elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
				elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
				$COLOR[1] = $PALETTE[$COLOR[1]+1];
			}else{
				return FALSE;
			}
			imagesetpixel($res,$X,$Y,$COLOR[1]);
			// 1 dot 処理完了
			$X++;
			$P += $BMP['bytes_per_pixel'];
		}
		//  x 方向完了
		$Y--;
		$P+=$BMP['decal'];
	}
	//  all line end

	//6 : 作業終了： TrueColor イメージを返す
	return $res;
}

/*
 * bitmap 形式画像を出力
 * @param $image : GD2.0 true colour object (ImageCreateTrueColorなどで作ったイメージオブジェクトを指定)
 * @param $filename : 出力先ファイル名、省略時は、php://output ＝標準出力へ出力。
 * return 成功時 true ,  出力先に Windows BMP file(24bit true color 形式のみ対応) が出来る
     失敗時 false
*/
function imageBMP (&$image, $filename = false){
	if (!$image) return false;
	if ( empty($filename) ) $filename = 'php://output';
	$f = fopen ($filename, "w");
	if ( false !== $f) return false;

	//Image dimensions
	$biWidth = imagesx ($image);
	$biHeight = imagesy ($image);
	$biBPLine = $biWidth * 3;
	$biStride = ($biBPLine + 3) & ~3;
	$biSizeImage = $biStride * $biHeight;
	$bfOffBits = 54;
	$bfSize = $bfOffBits + $biSizeImage;
	//BITMAPFILEHEADER
	fwrite ($f, 'BM', 2);
	fwrite ($f, pack ('VvvV', $bfSize, 0, 0, $bfOffBits));
	//BITMAPINFO (BITMAPINFOHEADER)
	fwrite ($f, pack ('VVVvvVVVVVV', 40, $biWidth, $biHeight, 1, 24, 0, $biSizeImage, 0, 0, 0, 0));

	$numpad = $biStride - $biBPLine;
	for ($y = $biHeight - 1; $y >= 0; --$y){	// 画像の下端からデータ書き出し
		for ($x = 0; $x < $biWidth; ++$x){
			$col = imagecolorat ($image, $x, $y);
			fwrite ($f, pack ('V', $col), 3);
		}
		for ($i = 0; $i < $numpad; ++$i)
			fwrite ($f, pack ('C', 0));
	}
	fclose ($f);
	return true;
}