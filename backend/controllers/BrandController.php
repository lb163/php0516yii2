<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $brand = Brand::find();//查出brand表中所有的数据
        $pager = new Pagination([
            'totalCount'=>$brand->count(),
            'defaultPageSize'=>2,
        ]);
        $brand=$brand->limit($pager->limit)->offset($pager->offset)->where(['!=','status',-1])->all();
        return $this->render('index',['brand'=>$brand,'pager'=>$pager]);
    }
    public function actionAdd(){
        $brand = new Brand();
        $request = new Request();
        if($request->isPost){
            $brand->load($request->post());
            $brand->file=UploadedFile::getInstance($brand,'file');
            if($brand->validate()){
                if($brand->file){
                    $file = '/upload/'.uniqid().'.'.$brand->file->getExtension();
                    //保存图片文件.绝对路径.
                    $brand->file->saveAs(\Yii::getAlias('@webroot').$file,false);
                    $brand->logo = $file;
                }
                $brand->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($brand->getErrors());exit;
            }
        }
        return $this->render('add',['brand'=>$brand]);
    }
    public function actionDel(){
        $id = \Yii::$app->request->post('id');
       $brand = Brand::findOne($id);
        if($brand){
            $brand->status= -1;
            $brand->save(false);
            return 'success';
        }
        return 'fail';
    }
    public function actionEdit($id){
        $request = \Yii::$app->request;
        $brand = Brand::findOne(['id'=>$id]);
        if($request->isPost){
            $brand->load($request->post());
            $brand->file=UploadedFile::getInstance($brand,'file');
            if($brand->validate()){
                if($brand->file){
                    $file = '/upload/'.uniqid().'.'.$brand->file->getExtension();
                    //保存图片文件.绝对路径.
                    $brand->file->saveAs(\Yii::getAlias('@webroot').$file,false);
                    $brand->logo = $file;
                }
                $brand->save(false);
                \Yii::$app->session->setFlash('suceess','修改成功');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($brand->getErrors());exit;
            }
        }
        return $this->render('add',['brand'=>$brand]);
    }
}
