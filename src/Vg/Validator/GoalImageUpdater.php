<?php
namespace Vg\Validator;

use Respect\Validation\Validator as v;

// http://documentup.com/Respect/Validation/
class GoalImageUpdater
{
    private $validator;
    private $errors = [];

    /**
     * バリデータを作成
     */
    public function __construct()
    {
        $this->validator = v::arr()
        ->key('mosaicPath', v::string()->setName('mosaicPath')->notEmpty())
        ->key('tateDivision', v::int()->setName('tateDivision')->notEmpty()->max(80))
        ->key('yokoDivision', v::int()->setName('yokoDivision')->notEmpty()->max(60))
        ->key('goalImageId', v::int()->setName('goalImageId')->notEmpty())
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
                'mosaicPath.notEmpty' => 'モザイクパスを入力してください',
                'tateDivision.notEmpty' => '縦分割数を入力してください',
                'tateDivision.max' => '縦分割数の最大値は80です',
                'yokoDivision.notEmpty' => '横分割数を入力してください',
                'yokoDivision.max' => '横分割数の最大値は60です',
                'goalImageId.notEmpty' => 'ゴールイメージIDを入力してください',
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
