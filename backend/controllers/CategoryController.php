<?php
namespace backend\controllers;
use backend\models\Category;
use yii\data\Pagination;
use yii\web\Controller;

class CategoryController extends Controller{
    public function actionIndex(){
        $category = Category::find();
        $pager = new Pagination([
            'totalCount'=>$category->count(),
            'defaultPageSize'=>3,
        ]);
        $category = $category->limit($pager->limit)->offset($pager->offset)->where(['>','status','-1'])->all();
     return $this->render('index',['category'=>$category,'pager'=>$pager]);
    }
        public function actionAdd(){
            $category = new Category();
            $request = \Yii::$app->request;
            if($request->isPost){
                $category->load($request->post());
                if($category->validate()){
                    $category->save();
//                    var_dump($category);exit;
                    return $this->redirect(['category/index']);
                }else{
                    var_dump($category->getErrors());exit;
                }
            }
            return $this->render('add',['category'=>$category]);
        }
    public function actionDel(){
        $id = \Yii::$app->request->post('id');
        $category = Category::findOne($id);
        if($category){
            $category ->status= -1;
            $category->save(false);
            return 'success';
        }
        return 'fail';
    }
    public function actionEdit($id){
        $category = Category::findOne($id);
        $request = \Yii::$app->request;
        if($request->isPost){
            $category->load($request->post());
            if($category->validate()){
                $category->save();
                return $this->redirect(['category/index']);
            }else{
                var_dump($category->getErrors());
            }
        }
        return $this->render('edit',['category'=>$category]);
    }
}