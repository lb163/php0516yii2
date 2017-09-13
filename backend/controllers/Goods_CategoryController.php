<?php
namespace backend\controllers;
use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\web\Controller;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class Goods_categoryController extends Controller{
    public function actionIndex()
    {
        
        $models = GoodsCategory::find();
        $pager = new Pagination([
            'totalCount'=>$models->count(),
            'defaultPageSize'=>6,
        ]);
        $models=$models->limit($pager->limit)->offset($pager->offset)->orderBy('tree ASC,lft ASC')->asArray()->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
        public function actionAdd(){
            $model = new GoodsCategory();
            $request = \Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    //判断添加顶级分类还是非顶级分类(子分类)
                    if($model->parent_id){
                        //非顶级分类(子分类)id'=>$model->parent_id大于父ID说明是子分类
                        $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                        $model->prependTo($parent);//添加子分类
                    }else{
                        //顶级分类
                        $model->makeRoot();//创建父分类
                    }
                    //$model->save();
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['index']);
                }
            }
            return $this->render('add',['model'=>$model]);
        }
    public function actionZtree(){
        $goodsCategories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->renderPartial('ztree',['goodsCategories'=>$goodsCategories]);
    }
    //删除商品分类
    public function actionDel($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('商品分类不存在');
        }
        if(!$model->isLeaf()){//判断是否是子节点，非叶子节点说明有子分类
            throw new ForbiddenHttpException('该分类下有子分类，无法删除');
        }
        $model->deleteWithChildren();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['index']);
    }
    public function actionEdit($id)
    {
        $model = GoodsCategory::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('分类不存在');
        }
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //$model->save();
            //不能移动节点到自己节点下
            /*if($model->parent_id == $model->id){
                throw new HttpException(404,'不能移动节点到自己节点下');
            }*/
            try{
                //判断是否是添加一级分类
                if($model->parent_id){
                    //非一级分类
                    $category = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    if($category){
                        $model->appendTo($category);
                    }else{
                        throw new HttpException(404,'上级分类不存在');
                    }
                }else{
                    //一级分类
                    //bug fix:修复根节点修改为根节点的bug
                    if($model->oldAttributes['parent_id']==0){
                        $model->save();
                    }else{
                        $model->makeRoot();
                    }
                }
                \Yii::$app->session->setFlash('success','分类添加成功');
                return $this->redirect(['index']);
            }catch (Exception $e){
                $model->addError('parent_id',GoodsCategory::exceptionInfo($e->getMessage()));
            }
        }
        return $this->render('add2',['model'=>$model]);
    }
}