<?php
// PHP 5.4.16 動作テスト済

namespace Mosaic;

class CreateMosaic
{
	const WEIGHT = 0.1; // 出現回数による重み付けのウェイト
	private $splitX;
	private $splitY;
	private $splitWidth;
	private $splitHeight;
	private $goalImage;
	private $albumImageDataList;


	// コンストラクタ
	function __construct($splitX, $splitY, $splitWidth, $splitHeight)
	{
		$this->splitX = $splitX;
		$this->splitY = $splitY;
		$this->splitWidth = $splitWidth;
		$this->splitHeight = $splitHeight;
		$this->goalImage = null;
		$this->albumImageDataList = [];
	}

	// デストラクタ
	function __destruct()
	{
		// メモリ解法
		unset($this->goalImage);
		unset($this->createMosaic);
		for($i = 0, $n = count($this->albumImageDataList); $i < $n; ++$i)
		{
			unset($this->albumImageDataList[$i]);
		}
	}

	/**
	* モザイク画像の作成を統括して行う関数
	* @param string $filePath モザイク画ファイルの保存先
	* @param int $goalImageId データベース上のID
	* @return array(array(int)) $corrTwoDimension アルバム画像が目標画像のどの部分に対応しているかの情報 
	* (i.e.) $corrTwoDimension[8][4] = 5, アルバム画像リストの5番目の画像が(x, y) = (4, 8)に対応している
	*/
	public function execMakeMosaicImage($filePath, $goalImageId, $fbGoalImageId)
	{
		// 対応関係を計算
		$corrTwoDimension = self::calcCorrAlbumImageOnGoal($this->goalImage, $this->albumImageDataList);
		// 対応関係から画像を作成
		$mosaicImage = self::makeMosaicImageByCorrAlbumImage($corrTwoDimension, $this->goalImage, $this->albumImageDataList);

		// update内容
		$data = [
			'id' => $goalImageId,
			'fb_goal_image_id' => $fbGoalImageId,
			'mosaic_path' => $filePath,
			'is_make_mosaic' => true
		];

		// Repositoryの処理が固まり次第記載
		// $goalImageRespository = $container['repository.goal_image'];
		// $goalImageRepository->update($date);

		// ファイルの保存
		imagepng($mosaicImage, $filePath);
		imagedestroy($mosaicImage);

		return $corrTwoDimension;
	}

	/**
	* 必要な画像を読み込む関数
	* @param Mosaic\Image $goalImageUrl 目標画像の読み込み先
	* @param Mosaic\Image[] $albumImageUrlList アルバム画像の読み込み先
	* @param int $goalReizeWidth 目標画像のリサイズ横幅
	* @param int $goalReizeHeight 目標画像のリサイズ縦幅
	*/
	public function loadRequiredImages($goalImageUrl, $albumImageUrlList, $goalResizeWidth, $goalResizeHeight)
	{
		// ゴール画像の読み込みを行う
		$this->goalImage = self::loadImage($goalImageUrl, $goalResizeWidth, $goalResizeHeight);
		// 読み込み失敗
		if($this->goalImage === FALSE)
		{
			exit($goalImageUrl . '画像の読み込みに失敗しました' . PHP_EOL .
				'目標画像の読み込みに失敗したので処理を終了します'. PHP_EOL);
		}

		// 取得したURLから画像を読み込む
		foreach ($albumImageUrlList as $url)
		{
			// 画像をリサイズして読み込む
			$resizeImage = self::loadImage($url, $this->splitWidth, $this->splitHeight);
			// 読み込み失敗
			if($resizeImage === FALSE)
			{
				echo $url, '画像の読み込みに失敗しました', PHP_EOL;
				continue;
			}
			// 配列に格納する
			array_push($this->albumImageDataList, $resizeImage);
		}
	}

	/**
	* リサイズしたアルバム画像の保存とデーベースへの挿入
	* @param int $albumResizeWidth アルバム画像のリサイズ横幅
	* @param int $albumResizeHeight アルバム画像のリサイズ縦幅
	* @param int[] $fbImageId 画像のfacebook固有のID配列
	* @param int[] $albumId 中間テーブル"iamge_album"でに挿入する関連するAlbumId配列
	* @param int[][] $corrTwoDimension アルバム画像が目標画像のどの部分に対応しているかの情報 
	*/
	public function saveAlbumImages($albumResizeWidth, $albumResizeHeight, $fbImageIdList, $albumIdList, $corrTwoDimension)
	{
		for($i = 0, $n = count($this->albumImageDataList); $i < $n; ++$i)
		{
			$image = $this->albumImageDataList[$i];
			//$filePath = '../../public_html/img/resize_img/' . $imageDataBaseId . $image->extension;
			// [DEBUG]	
			$filePath = '../../public_html/img/resize_img/' . $i . $image->extension;
      
      //[debug by 123]
      $filePath = 'img/resize_img/resize_'.$i.$image->extension;
      // imageテーブルへの挿入リクエスト
			//$imageDataBaseId = $repository->insert($input);
			// [DEBUG]
			$imageDataBaseId = 1;
			// update内容
			$date = [
				'id' => $imageDataBaseId,
				'fb_image_id' => $fbImageIdList[$i],
				'resize_image_path' => $filePath
			];
			// Repositoryの処理が固まり次第記載
			// $imageRespository = $container['repository.image']
			// $repository->update($date);

			// 画像のリサイズ
			$resizeId = imagecreatetruecolor($albumResizeWidth, $albumResizeHeight);
			$result = imagecopyresized($resizeId, $image->id, 0, 0, 0, 0, $albumResizeWidth, $albumResizeHeight, $image->width, $image->height);
			// リサイズ失敗
			if($result === FALSE)
			{

			}
			// 画像の保存
			$result = imagepng($resizeId, $filePath);
			// 保存失敗
			if($result === FALSE)
			{

			}

			// 画像がモザイクに使用されているかを全て列挙する
			$usingImagePos = [];
			foreach ($corrTwoDimension as $y => $v)
			{
				$xs = array_keys($v, $i);
				foreach ($xs as $x)
				{
					array_push($usingImagePos, ['x' => $x, 'y' => $y]);
				}
			}

			// Repositoryの処理が固まり次第記載
			//$albumImageRepository = $container['repository.album_image'];
			// 使用されていない場合
			// モザイクに使用されない情報を持たせて保存する
			if(count($usingImagePos) === 0)
			{
				// album_image insert内容
				$date = [
					'album_id' => $albumIdList[$i],
					'image_id' => $imageDataBaseId,
					'x' => null,
					'y' => null,
					'is_used_mosaic' => false
				];
				// $albumImageRepository->insert($date);
			}
			else
			{
				// 全ての位置の情報を中間テーブルに保存
				foreach ($usingImagePos as $pos)
				{
					// album_image insert内容
					$date = [
						'album_id' => $albumIdList[$i],
						'image_id' => $imageDataBaseId,
						'x' => $pos['x'],
						'y' => $pos['y'],
						'is_used_mosaic' => true
					];
					// Repositoryの処理が固まり次第記載
					// $albumImageRepository->insert($date);
				}
			}
		}
	}

	/**
	* 目標画像に対応するアルバム画像の位置を求める
	* @param Mosaic\Image &$goalImage 目標画像
	* @param Mosaic\Image[] &$albumImageDataList アルバム画像
	* @param array() &$usingCounter
	* @return array(array()) $corrTwoDimension アルバム画像が目標画像のどの部分に対応しているかの情報
	*/
	private function calcCorrAlbumImageOnGoal(&$goalImage, &$albumImageDataList)
	{
		$corrTwoDimension = [];
		// 対応するアルバム画像がどれだけ使われたかを保持する配列
		$usingCounter = array_fill(0, count($albumImageDataList), 0);
		for($y = 0; $y < $this->splitY; ++$y)
		{			
			$corr = [];
			for($x = 0; $x < $this->splitX; ++$x)
			{
				// 全てのアルバム画像と目標画像の部分領域の画像の距離を求める
				$minD = 1e+6;
				$minIndex = 0;
				// 開始地点を算出
				$fx = (int)($x * $this->splitWidth);
				$fy = (int)($y * $this->splitHeight);
				// 部分画像を取得
				$subId = $this->goalImage->makeSubImage($fx, $fy, $this->splitWidth, $this->splitHeight);
				$subImage = new Image($subId);

				for($i = 0, $n = count($albumImageDataList); $i < $n; ++$i)
				{
					// 画像間から局所距離を求める
					$d = self::calcDistanceBetweenPixels($subImage, $albumImageDataList[$i]);
					// 使われた回数で重み付けする
					// これにより、同じ画像が複数回使用されるのを防ぐ
					// 局所距離が0になるのを避けるために、1を足している
					$d *= 1 + $usingCounter[$i] * self::WEIGHT;
					// 求められた距離が現状の最小値よりも小さければ
					if($d < $minD)
					{
						$minD = $d;
						$minIndex = $i;
					}
				}
				// 使用回数をカウントアップする
				$usingCounter[$minIndex]++;
				// 目標画像の部分領域に最適に対応するアルバム画像の位置を格納する
				array_push($corr, $minIndex);
				unset($subImage);
			}
			array_push($corrTwoDimension, $corr);
		}
		return $corrTwoDimension;
	}

	/**
	* モザイク画像をアルバム画像の対応関係作成する関数
	* @param array(array()) $corrTwoDimension アルバム画像が目標画像のどの部分に対応しているかの情報
	* @param Mosaic\Image &$goalImage 目標画像
	* @param Mosaic\Image[] &$albumImageDataList アルバム画像
	* @return $mosaicImage モザイク画像のリソースID
	*/
	public function makeMosaicImageByCorrAlbumImage(&$corrTwoDimension, &$goalImage, &$albumImageDataList)
	{
		$mosaicImage = imagecreatetruecolor($goalImage->width, $goalImage->height);
		// 白で塗りつぶしておく
		$white = imagecolorallocate($mosaicImage, 0xFF, 0xFF, 0xFF);
		imagefill($mosaicImage, 0, 0, $white);
		for($y = 0; $y < $this->splitY; ++$y)
		{
			for($x = 0; $x < $this->splitX; ++$x)
			{
				// 開始地点を算出
				$fx = (int)($x * $this->splitWidth);
				$fy = (int)($y * $this->splitHeight);
				// 対応する画像を取得
				$image = $albumImageDataList[$corrTwoDimension[$y][$x]];
				// 対応する位置にアルバムの画像を貼り付ける 
				imagecopy($mosaicImage, $image->id, $fx, $fy, 0, 0, $this->splitWidth, $this->splitHeight);
			}
		}
		return $mosaicImage;
	}

	/**
	* 画像間の距離（RGBの差）を求める
	* @param imgA, imgB
	* @return 画像間の距離
	*/
	private function calcDistanceBetweenPixels($imgA, $imgB)
	{
		$d = [];
		foreach(Image::$COLOR_SPACE as $c)
		{
			$d[$c] = 0;
		}
		for($y = 0; $y < $imgA->height; ++$y)
		{
			for($x = 0; $x < $imgA->width; ++$x)
			{
				// A, B間の画素の取得
				$rgbA = imagecolorat($imgA->id, $x, $y);
				$colorsA = imagecolorsforindex($imgA->id, $rgbA);
				$rgbB = imagecolorat($imgB->id, $x, $y);
				$colorsB = imagecolorsforindex($imgB->id, $rgbB);
				// RGBの差分を計算
				foreach(Image::$COLOR_SPACE as $c)
				{
					$d[$c] += ($colorsA[$c] - $colorsB[$c]);
				}
			}
		}
		return sqrt($d['red'] * $d['red'] + $d['green'] * $d['green'] + $d['blue'] * $d['blue']);
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
