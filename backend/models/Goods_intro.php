<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Goods_intro extends ActiveRecord{
    public function rules()
    {
        return [
            [['content'],'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'content'=>'商品描述',
        ];
    }
}