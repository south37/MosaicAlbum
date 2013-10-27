<?php
namespace Vg\Validator;

use Respect\Validation\Validator as v;

// http://documentup.com/Respect/Validation/
class ImageUpdater
{
    private $validator;
    private $errors = [];

    /**
     * バリデータを作成
     */
    public function __construct()
    {
        $this->validator = v::arr()
        ->key('resizeImagePath', v::string()->setName('resizeImagePath')->notEmpty())
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
                'resizeImagePath.notEmpty' => 'リサイズイメージパスを入力してください',
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
