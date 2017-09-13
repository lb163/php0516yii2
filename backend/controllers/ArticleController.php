<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleDetail;
use backend\models\ArticleSearchForm;
use backend\models\Category;
use yii\data\Pagination;
use yii\web\Controller;

class ArticleController extends Controller{
    public function actionIndex()
    {
//        $sort = new Sort([
//            'attributes' => ['id', 'name', 'sort','status'],
//        ]);
        $model = new ArticleSearchForm();
        //$keywords = \Yii::$app->request->get('keywords');
        //搜索sql   where name like %四川%
        $query = Article::find();
        $model->load(\Yii::$app->request->get());
        if($model->name){
            $query->andWhere(['like','name',$model->name]);
        }
        if($model->intro){
            $query->andWhere(['like','intro',$model->intro]);
        }
        /*if($keywords){
            $query->where(['like','name',$keywords]);
        }*/
        $pager = new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>2
        ]);
        $article = $query->limit($pager->limit)->offset($pager->offset)->where(['>','status','-1'])->all();
        return $this->render('index',['article'=>$article,'pager'=>$pager,'model'=>$model]);
    }
    public function actionAdd(){
        $article = new Article();
        $article_detail = new ArticleDetail();
        $category = Category::find()->all();
        $request = \Yii::$app->request;
        if($request->isPost){
            $article->load($request->post());
            $article_detail->load($request->post());
            if($article->validate() && $article_detail->validate()){
                $article->create_time = time();
                $article->save();
                $article_detail->article_id=$article->id;
                $article_detail->save();
            \Yii::$app->session->setFlash('success','文章添加成功');
            return $this->redirect(['article/index']);
        }else{
            var_dump($article->getErrors(),$article_detail->getErrors());exit;
            }
        }
        return $this->render('add',['article'=>$article,'article_detail'=>$article_detail,'category'=>$category]);
    }
    public function actionEdit($id){
        $article = Article::findOne(['id'=>$id]);
        $article_detail = ArticleDetail::findOne(['article_id'=>$id]);
        $category = Category::find()->all();
        $request = \Yii::$app->request;
        if($request->isPost){
            $article->load($request->post());
            $article_detail->load($request->post());
            if($article->validate() && $article_detail->validate()){
                $article->save();
                $article_detail->save();
                \Yii::$app->session->setFlash('success','文章修改成功');
                return $this->redirect(['index']);
            }else{
                var_dump($article->getErrors(),$article_detail->getErrors());
            }
        }
        return $this->render('add',['article'=>$article,'article_detail'=>$article_detail,'category'=>$category]);
    }
    //删除数据
    public function actionDelete()
    {
        $id = \Yii::$app->request->post('id');
        $model = Article::findOne($id);
        if($model){
            $model->status = -1;
            $model->save(false);
            return 'success';
        }
        return 'fail';
    }
}