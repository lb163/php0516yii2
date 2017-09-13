<?php
namespace backend\models;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
class Goods extends ActiveRecord{
    public function rules()
    {
        return [
          [['name','brand_id','market_price','shop_price','stock','is_on_sale','status'], 'required' ],
          [['goods_category_id', 'status', 'sort',], 'integer'],
            [['name', 'logo'], 'string', 'max' => 255],
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'商品名称',
            'sn'=>'货号',
            'logo'=>'LOGO图片',
            'goods_category_id'=>'商品分类',
            'brand_id'=>'品牌分类',
            'market_price'=>'市场价格',
            'shop_price'=>'商品价格',
            'stock'=>'库存',
            'is_on_sale'=>'是否在售',
            'status'=>'状态',
            'sort'=>'排序',
            'create_time'=>'添加时间',
            'view_times'=>'浏览数',
        ];
    }
    public static function getBrandOptions(){
        return ArrayHelper::map(Brand::find()->where(['!=','status',-1])->asArray()->all(),'id','name');
    }
    public function getGalleries()
    {
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }

}