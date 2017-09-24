<?php
namespace frontend\controllers;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use Behat\Gherkin\Loader\YamlFileLoader;
use frontend\models\Address;
use frontend\models\Member;
use frontend\models\MemberForm;
use frontend\models\SmsDemo;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Cookie;

class MemberController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionRegister()
    {
        $model = new Member();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
//           var_dump($model);exit();
            if ($model->validate()) {
//                var_dump($model);exit();
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);//将接受到的密码在加密保存到数据库
                $model->created_at = time();
                $model->save(false);
//                \Yii::$app->session->setFlash('success','恭喜！~注册成功');
                return $this->redirect(['member/login']);
            }
            var_dump($model->getErrors());
            exit();
        }
        return $this->renderPartial('register');
    }

    //ajax验证用户唯一性
    public function actionValidateUser($username)
    {
        return 'true';//通过 false拦截
    }

    //登陆功能
    public function actionLogin()
    {
        $model = new MemberForm();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
//            var_dump($model);exit();
            if ($model->validate()) {
//                var_dump($model->errors);exit();
                if ($model->login()) {
                    \Yii::$app->session->setFlash('success', '登陆成功');
                    return $this->redirect(['member/list']);
//                }
//                var_dump($model->getErrors());exit();
                }
            }
        }
        return $this->renderPartial('login', ['model' => $model]);
    }
    //首页
    public function actionIndex()
    {
        $admin = GoodsCategory::find()->where(['parent_id'=>0])->all();
        return $this->renderPartial('index',['admin'=>$admin]);
    }
    //商品列表页
    public function actionList($category_id){
        $cate = GoodsCategory::findOne(['id'=>$category_id]);
        if($cate->depth == 2){
            $models = Goods::find()->where(['goods_category_id'=>$category_id])->all();
        }else{
            $ids = $cate->leaves()->asArray()->column();
            //var_dump($ids);exit;
            $models = Goods::find()->where(['in','goods_category_id',$ids])->all();

        }
//        var_dump($models);exit();
        return $this->renderPartial('list',['models'=>$models]);
    }
    //商品详情页面
    public function actionGoods($id){
        $model = Goods::findOne(['id'=>$id]);
//        var_dump($model);exit();
        $gallerys=GoodsGallery::findAll(['goods_id'=>$id]);
//        var_dump($gallerys);exit();
//        foreach($gallerys as &$gallery){
//            var_dump(str_replace('/upload/','http://www.yii2shop.com/upload/',$gallery->path));
//        }

        return $this->renderPartial('goods',['model'=>$model,'gallerys'=>$gallerys]);
    }
    //短信测试
    public function actionSms()
    {
        $admin = \Yii::$app->request->post('phone');
        $code = rand(1000, 9999);
        \Yii::$app->session->set('code_' . $admin, $code);
        /*$demo = new SmsDemo(
            "LTAIHGWcWeH8K9Nh",
            "dIKKMnML6PKdL9pzHBn5sO6OMhmwHg"
        );

        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "李小小的面馆", // 短信签名
            "SMS_97795009", // 短信模板编号
            "13880318122", // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>rand(1000,9999),
//                "product"=>"dsd"
            )
        );
        print_r($response);
    }*/
        echo $code;
    }
    //三级联动地址修改
    public function actionAddress(){
        return $this->renderPartial('address');
    }
    public function actionAddtocart($goods_id,$amount){

        if(\Yii::$app->user->isGuest){//判断是否登陆
            $cookies = \Yii::$app->request->cookies;//获取cookies的值
            $value = $cookies->getValue('carts');
            if($value){
                $carts = unserialize($value);
            }else{
                $carts = [];
            }
            if(array_key_exists($goods_id,$carts)){//是否有指定的key
                $carts[$goods_id] +=$amount;
            }else{
                $carts[$goods_id] = intval($amount);//intval整数
            }
            $cookies = \Yii::$app->response->cookies;//设置cookies
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time()+7*24*3600;//过期时间戳
            $cookies->add($cookie);
        }else{

        }
        return $this->renderPartial('cart');
    }
    //购物车页面
    public function actionCart(){
        //获取购物车数据
        if(\Yii::$app->user->isGuest){
            //从cookie
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if($value){
                $carts = unserialize($value);//$carts = [1=>2,2=>10]
            }else{
                $carts = [];
            }
            $models = Goods::find()->where(['in','id',array_keys($carts)])->all();
        }else{
        }
        return $this->renderPartial('cart',['models'=>$models,'carts'=>$carts]);
    }
}