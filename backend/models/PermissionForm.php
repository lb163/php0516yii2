<?php
namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    public $name;
    public $description;
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    public function rules()
   {
       return [
       [['name','description'],'required'],
       ['name','validateName','on'=>[self::SCENARIO_ADD,self::SCENARIO_EDIT]],
      ];
   }
    //验证权限名称
    public function validateName(){
        if(\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }
    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'description'=>'描述'
        ];
    }
}