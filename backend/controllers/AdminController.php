<?php
namespace backend\controllers;
use backend\models\Admin;
use backend\models\AdminForm;
use backend\models\PasswordForm;
use Behat\Gherkin\Loader\YamlFileLoader;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\User;

class AdminController extends Controller{
    //用户列表
    public function actionIndex(){
        $admin = Admin::find();//查询admin表的数据
        $pager = new Pagination([//调用YII自带的分页
            'totalCount'=>$admin->count(),
            'defaultPageSize'=>2,
        ]);
        $admin = $admin->limit($pager->limit)->offset($pager->offset)->where(['>','status','-1'])->all();//sql
       return $this->render('index',['admin'=>$admin,'pager'=>$pager]);//调用视图处理数据
    }
    //添加用户
    public function actionAdd(){
        $admin = new Admin(['scenario'=>Admin::SCENARIO_ADD]);//调用模型
        $request = \Yii::$app->request;//获取请求
        if($request->isPost){//
            $admin->load($request->post());//post请求方法加载数据
            if ($admin->validate()){//验证数据
                //$admin->password_hash=md5('password_hash');
                /*$admin->password_hash = \Yii::$app->security->generatePasswordHash($admin->password_hash);
                $admin->create_at=time();//设置时间戳*/
                $admin->save();//保存数据
                \Yii::$app->session->setFlash('warning','添加成功');
                return $this->redirect(['admin/index']);//调转到index页面
            }else{
                var_dump($admin->getErrors());exit;//打印错误信息
            }
        }
        return $this->render('add',['admin'=>$admin]);//调用试图处理数据
    }
    //修改用户
    public function actionEdit($id){
        $admin = Admin::findOne(['id'=>$id]);
        $admin->scenario = Admin::SCENARIO_EDIT;
//        $admin = new Admin();//调用模型
        if($admin==null){
            throw new NotAcceptableHttpException('用户不存在');
        }
        $request = \Yii::$app->request;//获取请求
        if($request->isPost){//
            $admin->load($request->post());//post请求方法加载数据
            if ($admin->validate()){//验证数据
//                $admin->create_at=time();//设置时间戳
                $admin->save();//保存数据
                \Yii::$app->session->setFlash('info','修改成功');
                return $this->redirect(['admin/index']);//调转到index页面
            }else{
                var_dump($admin->getErrors());exit;//打印错误信息
            }
        }
        return $this->render('add',['admin'=>$admin]);//调用试图处理数据
    }
    //删除用户
          public function actionDel(){
            $id = \Yii::$app->request->post('id');//接受请求参数id  破环性的数据一般都用post
            $admin = Admin::findOne($id);//根据ID在admin表中查出一条数据
            if($admin){//判断如果有
                $admin ->status= -1;//就给状态赋值为-1
                $admin->save(false);//保存数据  
                return 'success';//成功
            }
            return 'fail';//失败
        }
    //登陆功能
       public function actionLogin(){
        $model = new AdminForm(['scenario'=>Admin::SCENARIO_LOGIN]);
        $request=\Yii::$app->request;//请求方式
        if($request->isPost){//判断是否为post请求
            $model->load($request->post());//加载表单数据
            if($model->validate()){//验证数据
                if($model->login()){//判断是否有 通过直接登陆跳转页面
                    \Yii::$app->session->setFlash('success','登陆成功');
                    return $this->redirect(['admin/index']);
                }else{
                    var_dump($model->getErrors());exit;
                }
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    public function actionLogout(){
        \Yii::$app->user->logout;
        return $this->redirect(['admin/login']);
    }
    public function actionUser(){
//        var_dump(\Yii::$app->user->isGuest);
        $admin = \Yii::$app->user->identity;
    }
    //修改自己密码
    public function actionPassword(){
        $model = new PasswordForm();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $admin = \Yii::$app->user->identity;//根据组件找到用户ID
                $admin->password=$model->newpassword;//赋值
                //var_dump($admin);exit();
                $admin->save();
                return $this->redirect(['admin/index']);
            }
        }
        return $this->render('password',['model'=>$model]);
    }
}