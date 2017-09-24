<?php
namespace backend\models;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $status
 * @property integer $create_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property integer $last_login_ip
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    public $password;
    public $remember;
    const SCENARIO_ADD = 'add';
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_EDIT = 'edit';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email','status'], 'required','on'=>self::SCENARIO_EDIT],
            ['password','required','on'=>[self::SCENARIO_ADD,self::SCENARIO_LOGIN]],
            [['status', 'create_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['password'], 'string'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['remember','boolean','on'=>self::SCENARIO_LOGIN],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'password'=>'密码',
            'auth_key' => '标识',
            'password_hash' => '密码',
            'password_reset_token' => '重置密码',
            'email' => '邮箱',
            'status' => '状态',
            'create_at' => '创建时间',
            'updated_at' => '修改时间',
            'last_login_time' => '登陆时间',
            'last_login_ip' => '最后登录时间',
        ];
    }
    public function beforeSave($insert){
        if($insert){
            //添加
            //密码加密
            $this->password_hash=Yii::$app->security->generatePasswordHash($this->password);
            //auth_key
            $this->auth_key=Yii::$app->security->generateRandomString();//随机字符串
            //保存时间
            $this->create_at=time();
        }else{
            //修改
            if($this->password){
                $this->password_hash=Yii::$app->security->generatePasswordHash($this->password);
                $this->auth_key=Yii::$app->security->generateRandomString();//随机字符串
                $this->updated_at=time();
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key==$authKey;
    }
}
