<?php
namespace backend\models;
use yii\base\Model;
class GoodsSearchForm extends Model{
    public $name;
    public $sn;
    public $min;
    public $max;
    public function rules()
    {
        return [
            [['name','sn','min','max'],'safe'],//safe说明这些字段是安全的
        ];
    }
}