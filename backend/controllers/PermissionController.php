<?php
namespace backend\controllers;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class PermissionController extends Controller
{
    public function actionAdd()
    {
        $model = new PermissionForm(['scenario' => PermissionForm::SCENARIO_ADD]);
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());//加载数据
            if ($model->validate()) {//验证
                $auth = \Yii::$app->authManager;//获取authmangager组件
                $permission = $auth->createPermission($model->name);//创建权限
                $permission->description = $model->description;//描述
                $auth->add($permission);//保存到数据库
//                var_dump($auth);exit;
                return $this->redirect(['permission/index']);
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    public function actionIndex()
    {
        $auth = \Yii::$app->authManager;//获取authmangager组件
        $permissions = $auth->getPermissions();//获取权限
        return $this->render('index', ['permissions' => $permissions]);
    }

    public function actionEdit($name)
    {
        $model = new PermissionForm(['scenario' => PermissionForm::SCENARIO_EDIT]);
        $auth = \Yii::$app->authManager;//获取authmangager组件
        $permission = $auth->getPermission($name);//获取指定的name权限
        $model->name = $permission->name;//找到的对象复制到model
        $model->description = $permission->description;//描述
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $auth = \Yii::$app->authManager;
                $permission = $auth->createPermission($model->name);//创建权限
                $permission->description = $model->description;//描述
                $auth->update($name, $permission);//保存到数据库
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['permission/index']);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    public function actionDel()
    {
        $model = \Yii::$app->request->post('name');//post传的name
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($model);//找到指定name的权限
        if ($permission) {//判断是否有
            $auth->remove($permission);//移除
            return 'success';
        } else {
            return 'fail';
        }
    }

    //添加角色
    public function actionAddRole()
    {
        $model = new RoleForm();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $auth = \Yii::$app->authManager;
                $role = $auth->createRole($model->name);//创建角色
                $role->description = $model->description;//描述
                $auth->add($role);//保存
                if ($model->permissions) {
                    foreach ($model->permissions as $permissionName) {//遍历所有的权限
                        $permission = $auth->getPermission($permissionName);
                        $auth->addChild($role, $permission);
                    }
                }
            } else {
//                var_dump($model->getErrors());exit;
            }
            return $this->redirect(['permission/index-role']);
        }
        return $this->render('role_add', ['model' => $model]);
    }

    public function actionIndexRole()
    {
        $auth = \Yii::$app->authManager;
        $role = $auth->getRoles();//获取所有的角色
        return $this->render('role_index', ['role' => $role]);
    }

    public function actionDelRole()
    {
        $name = \Yii::$app->request->post('name');
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);//找到制定一条数据
        $permission = $auth->getPermission('permission/add');
        if ($role) {
            //移除角色的权限
            $auth->removeChild($role, $permission);
            return 'success';
        } else {
            return 'fail';
        }
    }
    public function actionEditRole($name){
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);//根据name查出一条数据
        $model = new RoleForm();//实例化表单
        $model->scenario = RoleForm::SCENARIO_EDIT;
        $model->name = $role->name;//描述 赋值
        $model->description = $role->description;
        $model->permissions = array_keys($auth->getPermissionsByRole($name));
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $role->name = $model->name;//描述
                $role->description = $model->description;
                $auth->update($name,$role);//跟新数据
                $auth->removeChildren($role);//移除
                if($model->permissions){
                    foreach ($model->permissions as $permissionName){//遍历所有的权限
                        $permission = $auth->getPermission($permissionName);
                        $auth->addChild($role,$permission);
                    }
                }
                return $this->redirect(['permission/index-role']);
            }
        }
        return $this->render('role_add',['model'=>$model]);
    }
}