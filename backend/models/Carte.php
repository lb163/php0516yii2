<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "carte".
 *
 * @property string $id
 * @property string $name
 * @property integer $parent_id
 * @property string $url
 * @property integer $sort
 */
class Carte extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carte';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'sort'], 'integer'],
            [['name', 'url','parent_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'parent_id' => '上级菜单',
            'url' => '地址',
            'sort' => '排序',
        ];
    }
    //获取顶级菜单
    public static function getMenuOptions()
    {
        //ArrayHelper数组的辅助类 merger合并数组支持无线个参数    map以键值对的方式返回数组
        return ArrayHelper::merge([0=>'顶级菜单'],ArrayHelper::map(self::find()->where(['parent_id'=>0])->asArray()->all(),'id','name'));
    }
}
