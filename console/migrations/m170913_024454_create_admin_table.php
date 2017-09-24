<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170913_024454_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
            'username'=> $this->string(20)->comment('用户名'),
            'auth_key'=>$this->string(50)->comment('标识'),
            'password_hash'=>$this->string(20)->comment('密码'),
            'password_reset_token'=>$this->string(20)->comment('重置密码'),
            'email'=>$this->string(50)->comment('邮箱'),
            'status'=>$this->string(10)->comment('状态'),
            'create_at'=>$this->integer()->comment('创建时间'),
            'updated_at'=>$this->integer()->comment('修改时间'),
            'last_login_time'=>$this->integer()->comment('登陆时间'),
            'last_login_ip'=>$this->integer()->comment('最后登录时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
