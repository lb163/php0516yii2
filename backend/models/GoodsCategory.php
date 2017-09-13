<?php
namespace backend\models;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class GoodsCategory extends ActiveRecord{
    public function rules()
    {
        return [
            [['name', 'parent_id'], 'required'],
            [['tree', 'lft', 'rgt', 'depth', 'parent_id'], 'integer'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    //获取商品分类的ztree数据
    public static function getZNodes(){
        $top = ['id'=>0,'name'=>'顶级分类','parent_id'=>0];
        $goodsCategories =  GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        /*
        array_unshift($goodsCategories,$top);*/
        return ArrayHelper::merge([$top],$goodsCategories);
        //var_dump($goodsCategories);exit;
    }


    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'parent_id'=>'上级分类',
            'intro'=>'简介',
        ];
    }
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
}