<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    public static $status_options = [1=>'正常',0=>'删除'];
    public function rules()
    {
        return [
            [['name', 'article_category_id', 'status', 'sort'], 'required'],
            [['article_category_id', 'status', 'sort',], 'integer'],
            [['intro'], 'string'],
            [['name','logo'], 'string', 'max' => 50],
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'article_category_id' => '分类',
            'intro' => '简介',
            'status' => '状态',
            'sort' => '排序',
            'inputtime' => '创建时间',
        ];
    }
    public function getArticleCategory(){
        //hasOne() 代表对应一个  参数1 class 关联对象的类名
        //参数2 表示对应的键 [k=>v]  k表示关联对象的主键  v表示当前对象的关联主键
        return $this->hasOne(Category::className(),['id'=>'article_category_id']);
    }
}