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
}