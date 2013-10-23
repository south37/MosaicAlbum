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
        private $albumSaveImageDataList;
        private $albumCalculationImageDataList;


        // コンストラクタ
        function __construct($splitX, $splitY, $splitWidth, $splitHeight)
        {
                $this->splitX = $splitX;
                $this->splitY = $splitY;
                $this->splitWidth = $splitWidth;
                $this->splitHeight = $splitHeight;
                $this->goalImage = null;
                $this->albumSaveImageDataList = [];
                $this->albumCalculationImageDataList = [];
        }

        // デストラクタ
        function __destruct()
        {
                // メモリ解法
                unset($this->goalImage);
                unset($this->createMosaic);
                for($i = 0, $n = count($this->albumCalculationImageDataList); $i < $n; ++$i)
                {
                        unset($this->albumCalculationImageDataList[$i]);
                }
        }

        /**
         * モザイク画像の作成を統括して行う関数
         * @param ['path' => string, 'id' => int] $goalImageInfo 'path':モザイク画ファイルの保存先, 'id':FaceBook上の画像ID 
         * @param int $goalImageId データベース上のID
         * @param Pimple $container ['repository.albumImage']を使用する
         * @return int[][] $corrTwoDimension アルバム画像が目標画像のどの部分に対応しているかの情報 
         * (i.e.) $corrTwoDimension[8][4] = 5, アルバム画像リストの5番目の画像が(x, y) = (4, 8)に対応している
         */
        public function execMakeMosaicImage($filePath, $goalImageId, $container)
        {
                // 対応関係を計算
                $corrTwoDimension = self::calcCorrAlbumImageOnGoal($this->goalImage, $this->albumCalculationImageDataList);
                // 対応関係から画像を作成
                $mosaicImage = self::makeMosaicImageByCorrAlbumImage($corrTwoDimension, $this->goalImage, $this->albumCalculationImageDataList);

                // [サーバー上でコメント解除]
                $container['repository.goalImage']->update($goalImageId, $filePath);

                $old = umask(0002);
                // ファイルの保存
                var_dump($filePath); echo '<br>';
                var_dump($old); echo '<br>';
                imagepng($mosaicImage, $filePath);
                imagedestroy($mosaicImage);

                umask($old);

                return $corrTwoDimension;
        }

        /**
         * 必要な画像を読み込む関数
         * @param ['path' => string, 'id' => int] $goalImageInfo 'path':モザイク画ファイルの読み込み先, 'id':FaceBook上の画像ID 
         * @param [][]['id' => int, 'path' => int] $albumImageInfosList アルバム画像の読み込み先
         * @param int $goalReizeWidth 目標画像のリサイズ横幅
         * @param int $goalReizeHeight 目標画像のリサイズ縦幅
         * @param int $goalReizeWidth アルバム画像のリサイズ横幅
         * @param int $goalReizeHeight アルバム画像のリサイズ縦幅
         */
        public function loadRequiredImages($goalImagePath, $albumImageInfosList, $goalResizeWidth, $goalResizeHeight, $albumResizeWidth, $albumResizeHeight)
        {
                // ゴール画像の読み込みを行う
                $this->goalImage = Image::loadImage($goalImagePath['path'], $goalResizeWidth, $goalResizeHeight);
                // 読み込み失敗
                if($this->goalImage === FALSE)
                {
                        exit($goalImagePath['path'] . '画像の読み込みに失敗しました' . PHP_EOL .
                                        '目標画像の読み込みに失敗したので処理を終了します'. PHP_EOL);
                }

                // 取得したパスから画像を読み込む
                foreach ($albumImageInfosList as $albumId => $albumImageInfos)
                {
                        $this->albumSaveImageDataList[$albumId] = [];
                        $this->albumCalculationImageDataList[$albumId] = [];
                        foreach($albumImageInfos as $hash)
                        {
                                $url = $hash['path'];
                                // 画像をリサイズして読み込む
                                $saveImage = Image::loadImage($url, $albumResizeWidth, $albumResizeHeight);
                                $calcImage = Image::loadImage($url, $this->splitWidth, $this->splitHeight);
                                // 読み込み失敗
                                if($saveImage === FALSE || $calcImage === FALSE)
                                {
                                        echo $url, '画像の読み込みに失敗しました', PHP_EOL;
                                        continue;
                                }
                                // 配列に格納する
                                array_push($this->albumSaveImageDataList[$albumId], $saveImage);
                                array_push($this->albumCalculationImageDataList[$albumId], $calcImage);
                        }
                }
        }

        // @param int[] $fbImageId 画像のfacebook固有のID配列
        // @param int[] $albumId 中間テーブル"iamge_album"でに挿入する関連するAlbumId配列
        /**
         * リサイズしたアルバム画像の保存とデーベースへの挿入
         * @param int $albumResizeWidth アルバム画像のリサイズ横幅
         * @param int $albumResizeHeight アルバム画像のリサイズ縦幅
         * @param int $goalImageId DBの対応するgoalImageのID
         * @param [][]['id' => int, 'path' => int] $albumImageInfosList アルバム画像の読み込み先
         * @param int[][][] $corrTwoDimension アルバム画像が目標画像のどの部分に対応しているかの情報
         * @param Pimple $container ['repository.album'], ['repository.albumImage']を使用する
         */
        public function saveAlbumImages($albumResizeWidth, $albumResizeHeight, $goalImageId, $albumImageInfosList, $corrTwoDimension, $container)
        {
                // ファイルの保存されるパーミッションを775変更する
                $old = umask(0002);
                // 対応するディレクトリを作成
                $folderPath = 'img/resize_img/' . $goalImageId;
                if(!is_dir($folderPath)) mkdir($folderPath, 0775);
                foreach($albumImageInfosList as $albumId => $albumImageInfos)
                {
                        for($i = 0, $n = count($this->albumSaveImageDataList[$albumId]); $i < $n; ++$i)
                        {
                                try
                                {
                                        $image = $this->albumSaveImageDataList[$albumId][$i];
                                        $fbImageId = $albumImageInfos[$i]['id'];
                                        $filePath = 'img/resize_img/' . $goalImageId . '/'.  $fbImageId . $image->extension;
                                        // [DEBUG]	
                                        //$filePath = 'resize_img/' . $goalImageId . '/' . $i . $image->extension;
                                        //[debug by 123]
                                        //$filePath = 'img/resize_img/' . $goalImageId . '/resize_'.$i.$image->extension;

                                        // [サーバー上でコメント解除]
                                        // imageテーブルへの挿入リクエスト
                                        $imageDataBaseId = $container['repository.image']->insert($fbImageId);
                                        $container['repository.image']->update($filePath);

                                        // 画像のリサイズ
                                        $resizeId = imagecreatetruecolor($albumResizeWidth, $albumResizeHeight);
                                        $result = imagecopyresized($resizeId, $image->id, 0, 0, 0, 0, $albumResizeWidth, $albumResizeHeight, $image->width, $image->height);
                                        // リサイズ失敗
                                        if($result === FALSE)
                                        {

                                        }
                                        // 画像の保存
                                        $result = $image->saveImage($filePath);
                                        // 保存失敗
                                        if($result === FALSE)
                                        {

                                        }

                                        // 画像がモザイクに使用されているかを全て列挙する
                                        $usingImagePos = [];
                                        foreach ($corrTwoDimension as $y => $corrDimension)
                                        {
                                                foreach($corrDimension as $x => $v)
                                                {
                                                        if($v['albumId'] !== $albumId || $v['index'] !== $i) continue;
                                                        array_push($usingImagePos, ['x' => $x, 'y' => $y]);
                                                }
                                        }

                                        // [サーバー上でコメント解除]
                                        $albumImageRepository = $container['repository.albumImage'];
                                        // 使用されていない場合
                                        // モザイクに使用されない情報を持たせて保存する
                                        if(count($usingImagePos) === 0)
                                        {
                                                // album_image insert内容
                                                $data = [
                                                        'album_id' => $albumId,
                                                        'image_id' => $imageDataBaseId,
                                                        'x' => null,
                                                        'y' => null,
                                                        'is_used_mosaic' => false
                                                                ];
                                                // [サーバー上でコメント解除]
                                                $albumImageRepository->insert($albumId, $imageDataBaseId, null, null, false);
                                        }
                                        else
                                        {
                                                // 全ての位置の情報を中間テーブルに保存
                                                foreach ($usingImagePos as $pos)
                                                {
                                                        // album_image insert内容
                                                        $data = [
                                                                'album_id' => $albumId,
                                                                'image_id' => $imageDataBaseId,
                                                                'x' => $pos['x'],
                                                                'y' => $pos['y'],
                                                                'is_used_mosaic' => true
                                                                        ];
                                                        // [サーバー上でコメント解除]
                                                        $albumImageRepository->insert($albumId, $imageDataBaseId, $pos['x'], $pos['y'], true);
                                                }
                                        }
                                }
                                catch(PDOException $e)
                                {
                                        print 'Error:' . $e->getMessage();
                                }
                        }
                }
                // パーミッションの設定を元に戻す
                umask($old);
        }

        /**
         * 目標画像に対応するアルバム画像の位置を求める
         * @param Mosaic\Image &$goalImage 目標画像
         * @param Mosaic\Image[][] &$albumCalculationImageDataList アルバム画像
         * @param array() &$usingCounter
         * @return array(array()) $corrTwoDimension アルバム画像が目標画像のどの部分に対応しているかの情報
         */
        private function calcCorrAlbumImageOnGoal(&$goalImage, &$albumCalculationImageDataList)
        {
                $corrTwoDimension = [];
                // 対応するアルバム画像がどれだけ使われたかを保持する配列
                $n = count($albumCalculationImageDataList);
                $usingCounter = array_keys($albumCalculationImageDataList);
                foreach($usingCounter as  $albumId)
                {
                        $usingCounter[$albumId] = array_fill(0, count($albumCalculationImageDataList[$albumId]), 0);
                }
                // 対応する位置の局所距離を計算する
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

                                foreach($albumCalculationImageDataList as $albumId => $datas)
                                {
                                        for($i = 0, $n = count($datas); $i < $n; ++$i)
                                        {
                                                // 画像間から局所距離を求める
                                                $d = self::calcDistanceBetweenPixels($subImage, $albumCalculationImageDataList[$albumId][$i]);
                                                // 使われた回数で重み付けする
                                                // これにより、同じ画像が複数回使用されるのを防ぐ
                                                // 局所距離が0になるのを避けるために、1を足している
                                                $d *= 1 + $usingCounter[$albumId][$i] * self::WEIGHT;
                                                // 求められた距離が現状の最小値よりも小さければ
                                                if($d < $minD)
                                                {
                                                        $minD = $d;
                                                        $minAlbumId = $albumId;
                                                        $minIndex = $i;
                                                }
                                        }
                                }
                                // 使用回数をカウントアップする
                                $usingCounter[$minAlbumId][$minIndex]++;
                                // 目標画像の部分領域に最適に対応するアルバム画像の位置を格納する
                                array_push($corr, ['albumId' => $minAlbumId, 'index' => $minIndex]);
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
         * @param Mosaic\Image[][] &$albumCalculationImageDataList アルバム画像
         * @return $mosaicImage モザイク画像のリソースID
         */
        private function makeMosaicImageByCorrAlbumImage(&$corrTwoDimension, &$goalImage, &$albumCalculationImageDataList)
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
                                $image = null;

                                foreach($albumCalculationImageDataList as $albumId => $datas)
                                {
                                        for($i = 0, $n = count($datas); $i < $n; ++$i)
                                        {
                                                if($corrTwoDimension[$y][$x]['index'] === $i && $corrTwoDimension[$y][$x]['albumId'] === $albumId)
                                                {
                                                        $image = $albumCalculationImageDataList[$albumId][$i];
                                                        break;
                                                }
                                        }
                                }
                                //$image = $albumCalculationImageDataList[$corrTwoDimension[$y][$x]];
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
}
