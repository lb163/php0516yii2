<?php
namespace frontend\models;
use yii\base\Model;

class MemberForm extends Model{
    public $password;
    public $username;
    public $remember;
    const SCENARIO_LOGIN = 'login';
    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['remember','boolean','on'=>self::SCENARIO_LOGIN],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'remember'=>'记住密码',
        ];
    }
    public function login(){
        $model = Member::findOne(['username'=>$this->username]);
//        $model=new Member();
        if($model){
            if(\Yii::$app->security->validatePassword($this->password,$model->password_hash)){
                $admin = \Yii::$app->user;
                $admin->login($model,$this->remember?7*24*3600:0);
//                var_dump($model);exit();
                $model->save();
                return true;
            }else{
                $this->addError('password','密码错误');
            }
        }else{
            $this->addError('username','用户名不存在');
        }
        return false;
    }
}