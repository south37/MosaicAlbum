<?php
// PHP 5.4.16 動作テスト済

namespace Mosaic;
class Image
{
	// 定数の配列が宣言できないので泣く泣く変数として宣言
	public static $COLOR_SPACE = ['red', 'green', 'blue'];

	public $id;
	public $width;
	public $height;
	public $extension;

	function __construct($id)
	{
		$this->id = $id;
		$this->width = imagesx($id);
		$this->height = imagesy($id);
		$this->exntension = null;
	}

	function __destruct()
	{
		imagedestroy($this->id);
	}

	/**
	* [現在未使用]
	* 自身の持つ画像リソースIDからヒストグラムを生成する
	* @return 画像から生成したヒストグラム
	*/
	public function makeHistogram($fx = 0, $fy = 0, $tx = null, $ty = null)
	{
		$tx = $tx ?: $this->width;
		$ty = $ty ?: $this->height;
		$hist = ['red' => null, 'green' => null, 'blue' => null];
		// 配列の初期化
		// 色空間毎にヒストグラムを初期化
		foreach(self::$COLOR_SPACE as $c)
		{
			$hist[$c] = array_fill(0, 256, 0);
		}

		// 指定された区間の全てのピクセルにアクセスする
		for($y = $fy; $y < $ty; ++$y)
		{
			for($x = $fx; $x < $tx; ++$x)
			{
				$rgb = imagecolorat($this->id, $x, $y);
				$colors = imagecolorsforindex($this->id, $rgb);
				// 透明度は計算に必要ないので削除
				unset($colors['alpha']);
				// 色空間毎にヒストグラムの値を足しいていく
				foreach(self::$COLOR_SPACE as $c)
				{
					$hist[$c][$colors[$c]]++;
				}
			}
		}

		// ヒストグラムの値を正規化する
		foreach(self::$COLOR_SPACE as $c)
		{
			for($i = 0; $i < 256; ++$i)
			{
				$hist[$c][$i] *= 100; // 値が小さくなり過ぎて誤差が発生しないようにスケールしておく
				$hist[$c][$i] /= $this->width * $this->height;
			}
		}

		return $hist;
	}

	/**
	* 自身の持つ画像リソースIDから部分画像を生成する
	* @return 画像から生成した部分画像のリソースID
	*/
	public function makeSubImage($fx, $fy, $width, $height)
	{
		$subId = imagecreatetruecolor($width, $height);
		imagecopy($subId, $this->id, 0, 0, $fx, $fy, $width, $height);
		return $subId;
	}

	/**
	* 拡張子に合わせた画像を保存する
	*/
	public function saveImage($filePath)
	{
		$res = null;
		switch ($this->extension) {
			case '.gif': // GIF
				$res = imagegif($this->id, $filePath);
				break;
			case '.jpg': // JPEG
				$res = imagejpeg($this->id, $filePath);
				break;
			case '.png': // PNG
				$res = imagepng($this->id, $filePath);
				break;
			case '.bmp': // BMP
				$res = imageBMP($this->id, $filePath);
				break;
			//case 7: // TIFF
			//case 8:
			// 現状,TIFFのサポートはしない
			//	break; 
			default:
				echo $this->extension, PHP_EOL;
				break;
		}
		return $res;
	}

	/**
	* 画像をURLから読み込む（サイズが指定されている場合はリサイズを行う）
	* @param 画像のURL
	* @return 読み込みが失敗したらFALSE,成功ならばMosaic\Image
	* @note FaceBookの対応画像形式 .jpg、.bmp、.png、.gif、.tiff
	*/
	public static function loadImage($url, $width = null, $height = null)
	{
		$id = null;
		$fileExtension = null;
		$type = exif_imagetype($url);
		switch ($type) {
			case 1: // GIF
				$id = imagecreatefromgif($url);
				$fileExtension = '.gif';
				break;
			case 2: // JPEG
				$id = imagecreatefromjpeg($url);
				$fileExtension = '.jpg';
				break;
			case 3: // PNG
				$id = imagecreatefrompng($url);
				$fileExtension = '.png';
				break;
			case 6: // BMP
				// 独自ライブラリを使用
				$id = imageCreateFromBMP($url);
				$fileExtension = '.bmp';
				break;
			//case 7: // TIFF
			//case 8:
			// 現状,TIFFのサポートはしない
			//	break; 
			default:
				echo $url, '対応する画像形式ではありません', PHP_EOL;
				break;
		}
		// 読み込み失敗
		if($id === FALSE || $id == null) return FALSE;
		// Imageクラスを生成
		$image = new Image($id);
		$image->extension = $fileExtension;
		// リサイズを行う場合
		if(empty($width) === FALSE && empty($height) === FALSE)
		{
			// リサイズ用のリソースを用意する
			$resizeId = imagecreatetruecolor($width, $height);
			$ret = imagecopyresized($resizeId, $id, 0, 0, 0, 0, $width, $height, $image->width, $image->height);
			// リサイズ処理に成功した場合
			if($ret)
			{
				// メモリを解法して、リサイズした画像のクラスを作成
				unset($image);
				$image = new Image($resizeId);
				$image->extension = $fileExtension;
			}
			// リサイズ処理に失敗した場合
			else
			{
				// リサイズ画像のメモリ領域を解法
				imagedestroy($resizeId);
				return FALSE;
			}
		}
		return $image;
	}
}