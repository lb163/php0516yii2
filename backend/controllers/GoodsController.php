<?php
namespace backend\controllers;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\Goods_day_count;
use backend\models\Goods_intro;
use backend\models\GoodsCategory;
use backend\models\GoodsCount;
use backend\models\GoodsSearchForm;
use yii\data\Pagination;
use yii\web\Controller;
use backend\models\GoodsGallery;
use flyok666\uploadifive\UploadAction;
use yii\web\NotFoundHttpException;
use flyok666\qiniu\Qiniu;

class GoodsController extends Controller{
    public function actionIndex(){
        $model = new GoodsSearchForm();
        $goods = Goods::find();
        $model->load(\Yii::$app->request->get());
        if($model->name){
            $goods->andWhere(['like','name',$model->name]);
        }
        if($model->sn){
            $goods->andWhere(['like','sn',$model->sn]);
        }
        if($model->min){
            $goods->andWhere(['>=','stock',$model->min]);
        }
        if($model->max){
            $goods->andWhere(['<=','stock',$model->max]);
        }
        $goods->andWhere(['>','status','-1']);
        $pager = new Pagination([
            'totalCount'=>$goods->count(),
            'defaultPageSize'=>2,
        ]);
        $goods=$goods->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['goods'=>$goods,'pager'=>$pager,'model'=>$model]);
    }
    public function actionAdd(){
        $goods  = new Goods();
        $goods_intro = new Goods_intro();
        $goods_category_id = Goods_day_count::find()->all();
//        $brand_id = Brand::find()->all();
        $request = \Yii::$app->request;
        if($request->isPost){
            $goods->load($request->post());
            $goods_intro->load($request->post());
            if ($goods->validate() && $goods_intro->validate()){
                //自动生成sn
                $day = date('Y-m-d');
                $goodsCount = Goods_day_count::findOne(['day'=>$day]);
                if($goodsCount==null){
                    $goodsCount = new Goods_day_count();
                    $goodsCount-> day = $day;
                    $goodsCount->count= 0;
                    $goodsCount->save();
                }
                //不全字符串长度
                $goods->sn = date('Ymd').sprintf("%04d",$goodsCount->count+1);
                $goods->create_time=time();
//                var_dump($goods);exit;
                $goods->save();
                $goods_intro->good_id = $goods->id;
                $goods_intro->save();
                //var_dump($goods->getErrors());exit;
                $goodsCount->count++;
                $goodsCount->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($goods->getErrors());exit;
            }
        }
        return $this->render('add',['goods'=>$goods,'goods_category_id'=>$goods_category_id,'goods_intro'=>$goods_intro]);
    }
    public function actionDel(){
        $id = \Yii::$app->request->post('id');
        $goods = Goods::findOne($id);
        if($goods){
            $goods ->status= -1;
            $goods->save(false);
            return 'success';
        }
        return 'fail';
    }
    public function actionEdit($id){
//        $goods  = new Goods();
        $goods = Goods::findOne(['id'=>$id]);
        $goods_intro = Goods_intro::findOne($id);
        $goods_category_id = GoodsCategory::find()->all();
        $brand_id = Brand::find()->all();
        $request = \Yii::$app->request;
        if($request->isPost){
            $goods->load($request->post());
            $goods_intro->load($request->post());
            if ($goods->validate() && $goods_intro->validate()){
                $goods->create_time=time();
//                var_dump($goods);exit;
                $goods->save(false);
                $goods_intro->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($goods->getErrors());exit;
            }
        }
        return $this->render('add',['goods'=>$goods,'goods_category_id'=>$goods_category_id,'brand_id'=>$brand_id,'goods_intro'=>$goods_intro]);
    }
    /*
 * 商品相册
 */
    public function actionGallery($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('gallery',['goods'=>$goods]);
    }
    /*
     * AJAX删除图片
     */
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }
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
                    $goods_id = \Yii::$app->request->post('goods_id');
                    if($goods_id){
                        $model = new GoodsGallery();
                        $model->goods_id = $goods_id;
                        $model->path = $action->getWebUrl();
                        $model->save();
                        $action->output['fileUrl'] = $model->path;
                        $action->output['id'] = $model->id;
                    }else{
                        $action->output['fileUrl'] = $action->getWebUrl();//输出文件的相对路径
                    }
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
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }
}