<?php
namespace backend\controllers;

use backend\models\Carte;

class CarteController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = Carte::find()->where(['parent_id'=>0])->all();
        return $this->render('index',['model'=>$model]);
    }
    public function actionAdd(){
        $model = new Carte();
        $request= \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
        public function actionEdit($id){
            $model = Carte::findOne(['id'=>$id]);
            $request= \Yii::$app->request;
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $model->save();
                    return $this->redirect(['index']);
                }
            }
            return $this->render('add',['model'=>$model]);
        }
    public function actionDel(){
        $id = \Yii::$app->request->post('id');
        $model = Carte::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }
    }
}
