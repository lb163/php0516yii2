<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleDetail extends ActiveRecord{
    public function rules()
    {
        return [
            [[ 'content'], 'required'],
            [['article_id'], 'integer'],
            [['content'], 'string'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'article_id' => '文章id',
            'content' => '内容',
        ];
    }
}