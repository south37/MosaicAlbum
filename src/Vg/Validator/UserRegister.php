<?php
namespace Vg\Validator;

use Respect\Validation\Validator as v;

// http://documentup.com/Respect/Validation/
class UserRegister
{
    private $validator;
    private $errors = [];

    /**
     * バリデータを作成
     */
    public function __construct()
    {
        $this->validator = v::arr()
        ->key('fbUserId', v::string()->setName('fbUserId')->notEmpty())
        ->key('token', v::string()->setName('token')->notEmpty())
        ->key('name', v::string()->setName('name')->notEmpty()->length(1, 255))
        ->key('fbIconUrl', v::string()->setName('fbIconUrl')->notEmpty())
        ->key('mailAddress', v::email()->setName('mailAddress')->notEmpty()->length(1, 255))
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
                'fbUserId.notEmpty' => 'FacebookユーザーIDを入力してください',
                'token.notEmpty' => 'トークンを入力してください',
                'name.notEmpty' => '名前を入力してください',
                'name.length' => '名前は{{minValue}}〜{{maxValue}}文字で入力してください',
                'fbIconUrl.notEmpty' => 'FacebookアイコンURLを入力してください',
                'mailAddress.notEmpty' => 'メールアドレスを入力してください',
                'mailAddress.length' => 'メールアドレスは{{minValue}}〜{{maxValue}}文字で入力してください',
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
