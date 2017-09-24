<?php
namespace backend\models;
use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permissions;
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_ADD = 'add';
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['permissions','safe'],
            ['name','validateName','on'=>self::SCENARIO_ADD],
            ['name','validateEditName','on'=>self::SCENARIO_EDIT],
        ];
    }
    //自定义验证规则.只考虑出问题的.
    public function validateName()
    {
        //输入的权限已存在.getPermission查询权限是否存在,返回null或者对象.
        if (\Yii::$app->authManager->getRole($this->name)){
            $this->addError('name','权限已存在');
        }
    }
    public function validateEditName(){
        $auth = \Yii::$app->authManager;
        //没有修改名称(主键)
        //修改了名称,新名称不能存在
        //怎么判断名称修改没有?通过get参数获取旧名称
        if(\Yii::$app->request->get('name') != $this->name){
            if($auth->getRole($this->name)){
                $this->addError('name',' 该角色已存在');
            }
        }
    }
    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'描述',
            'permissions'=>'权限列表'
        ];
    }
    //关联权限.
    public static function getPermissionItems()
    {
        $permissions = \Yii::$app->authManager->getPermissions();
        $items = [];
        foreach ($permissions as $permission){
            $items[$permission->name] = $permission->description;
        }
        return $items;
    }
}