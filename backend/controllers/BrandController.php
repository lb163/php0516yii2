<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

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
//            $brand->file=UploadedFile::getInstance($brand,'file');
            if($brand->validate()){
                /*if($brand->file){
                    $file = '/upload/'.uniqid().'.'.$brand->file->getExtension();
                    //保存图片文件.绝对路径.
                    $brand->file->saveAs(\Yii::getAlias('@webroot').$file,false);
                    $brand->logo = $file;
                }*/
                $brand->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($brand->getErrors());exit;
            }
        }
        return $this->render('add',['brand'=>$brand]);
    }
    //uploadfive插件自动上传文件
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
//                    $action->output['fileUrl'] = $action->getWebUrl();//输出图片的路径
                    //$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    //$action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //将图片上传到七牛云,并且返回七牛云的图片地址
                   /* $config = [
                        'accessKey'=>'cUOwUe8N3I-MO9p-or9XvuvrzusD0S7GZMWb7yvu',
                        'secretKey'=>'3iH9_1iPlTKgcaGr5JhTHsRlpu_4TFHZ9HIQ-o7S',
                        'domain'=>'http://ow0o0e5o5.bkt.clouddn.com/',
                        'bucket'=>'libophp',
                        'area'=>Qiniu::AREA_HUADONG
                    ];*/
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $key = $action->getWebUrl();
                    //上传文件到七牛云  同时指定一个key(名称,文件名)
                    $file = $action->getSavePath();
                    $qiniu->uploadFile($file,$key);
                    //获取七牛云上文件的url地址
                    $url = $qiniu->getLink($key);
                    $action->output['fileUrl'] = $url;//输出图片的路径
                },
            ],
        ];
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
    public function actionQiniu(){
        $config = [
            'accessKey'=>'cUOwUe8N3I-MO9p-or9XvuvrzusD0S7GZMWb7yvu',
            'secretKey'=>'3iH9_1iPlTKgcaGr5JhTHsRlpu_4TFHZ9HIQ-o7S',
            'domain'=>'http://ow0o0e5o5.bkt.clouddn.com/',
            'bucket'=>'libophp',
            'area'=>Qiniu::AREA_HUADONG
        ];
        $qiniu = new Qiniu($config);
        $key = '1.jpg';
        //上传文件到七牛云
        $file =\Yii::getAlias('@webroot/upload/1.jpg');
        $qiniu->uploadFile($file,$key);
        $url = $qiniu->getLink($key);
        var_dump($url);
    }
}
