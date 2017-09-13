<?php
namespace backend\models;
use yii\base\Model;

class ArticleSearchForm extends Model{
    public $name;//文章标题
    public $intro;//文章简介
    public function rules()
    {
        return [
            [['name','intro'],'safe'],
        ];
    }
}