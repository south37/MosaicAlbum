<?php
namespace Vg\Validator;

use Respect\Validation\Validator as v;

// http://documentup.com/Respect/Validation/
class AlbumImageRegister
{
    private $validator;
    private $errors = [];

    /**
     * バリデータを作成
     */
    public function __construct()
    {
        $this->validator = v::arr()
        ->key('albumId', v::int()->setName('albumId')->notEmpty())
        ->key('imageId', v::int()->setName('imageId')->notEmpty())
        ->key('x', v::int()->setName('x')->notEmpty()->max(640))
        ->key('y', v::int()->setName('y')->notEmpty()->max(480))
        ->key('isUsedMosaic', v::int()->setName('isUsedMosaic')->notEmpty())
        ;
    }

    /**
     * $input のバリデーションを行う
     *
     * @param  array   $input チェックする値を含む配列
     * @return boolean 有効かどうか
     */
    public function validate($input)
    {
        try {
            $this->validator->assert($input);
        } catch (\InvalidArgumentException $e) {
            $this->errors = $e->findMessages([
                'albumId.notEmpty' => 'アルバムIDを入力してください',
                'imageId.notEmpty' => 'イメージIDを入力してください',
                'x.notEmpty' => '座標ｘを入力してください',
                'x.max' => '座標ｘの最大値は640です',
                'y.notEmpty' => '座標ｙを入力してください',
                'y.max' => '座標yの最大値は480です',
                'isUsedMosaic.notEmpty' => 'モザイクに使用したかチェックを入力してください',
                ]);
            return false;
        }
        return true;
    }

    /**
     * エラーメッセージの配列を返す
     */
    public function errors()
    {
        return $this->errors;
    }
}
