<?php
namespace common\models;

use common\utils\SecurityUtil;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = false;

    private $_user;
    public $verifyCode;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['verifyCode', 'captcha','captchaAction'=>'site/captcha', 'message' => '验证码不正确'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {//$this就是$model对象
            // 私钥解密
            $private_key = Yii::$app->params['private_key'];
            $res=openssl_private_decrypt(base64_decode($this->password),$this->password,$private_key);//登录密码私钥解密
            //openssl_private_decrypt(base64_decode($this->Passwd),$passwd,$private_key);//原密码私钥解密
            if(!$res){//如果$res没有值，也就是说客户端浏览器没有传过来，说明网络错误
                $this->addError($attribute, '网络错误');
                return;
            }
            // 完整性校验
            $passArr = explode(',',$this->password);
            $this->password = $passArr[0];
            $md5Password = $passArr[1];
            if($md5Password != md5($this->password)){
                $this->addError($attribute, '信息保存失败');
                return;
            }

            $user = $this->getUser();
            //记录日志
            if(!$user){
                $this->addError($attribute, '用户名不存在');
            }elseif(!$user->validatePassword($this->password)){
                $this->addError($attribute, '用户名或密码错误');
            }
            //die;
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        \Yii::$app->session->set('_secret',SecurityUtil::generateSecret());
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
