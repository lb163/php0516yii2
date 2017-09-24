<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    public function rules()
    {
        return [
            [['name', 'province', 'city', 'county', 'detail', 'tel'], 'required'],
            [['member_id', 'is_default'], 'integer'],
            [['name', 'province', 'city', 'county'], 'string', 'max' => 100],
            [['detail'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '所属用户',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'county' => '县',
            'detail' => '详细地址',
            'tel' => '手机号码',
            'is_default' => '是否默认地址',
        ];
    }
}