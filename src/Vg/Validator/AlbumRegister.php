<?php
namespace Vg\Validator;

use Respect\Validation\Validator as v;

// http://documentup.com/Respect/Validation/
class AlbumRegister
{
    private $validator;
    private $errors = [];

    /**
     * バリデータを作成
     */
    public function __construct()
    {
        $this->validator = v::arr()
        ->key('userId', v::int()->setName('userId')->notEmpty())
        ->key('goalImageId', v::int()->setName('goalImageId')->notEmpty())
        ->key('fbAlbumId', v::string()->setName('fbAlbumId')->notEmpty())
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
                'userId.notEmpty' => 'ユーザーIDを入力してください',
                'goalImageId.notEmpty' => 'ゴールイメージIDを入力してください',
                'fbAlbumId.notEmpty' => 'FacebookアルバムIDを入力してください',
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
