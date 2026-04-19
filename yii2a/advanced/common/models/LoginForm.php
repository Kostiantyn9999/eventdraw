<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;


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
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
            //check about no access for user
            else if ($user->status == 0) {
                $this->addError('rememberMe', 'Access to your account is currently locked. <br/>Please contact <a href = "mailto: sales@eventdraw.com">sales@eventdraw.com</a> for assistance');
            }

            //check about expiry date
            // else if ($user->expiry_date) {

            //     if ($user->expiry_date < strtotime("now")) {

            //         if ($user->status == 10)  //full version
            //         {
            //             $this->addError('rememberMe', 'Your account is temporarily locked. <br/>Please contact <a href = "mailto: support@eventdraw.com">support@eventdraw.com</a>  for assistance');
            //         }
            //         else{  //trial user
            //             $this->addError('rememberMe', 'Your trial has ended. <br/>Please contact <a href = "mailto: sales@eventdraw.com">sales@eventdraw.com</a>  for assistance');
            //         }

            //     }
            // }

            if ($user->clientid > 0) {
                //check no access status for client
                $client = Client::findIdentity($user->clientid);
                if ($client)
                {
                    if ($client->status == 0)  {
                        $this->addError('rememberMe', 'Access to your client account is currently locked. <br/>Please contact <a href = "mailto: sales@eventdraw.com">sales@eventdraw.com</a> for assistance');
                    }
                }
            }
        }
    }


    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
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

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'User Login',
            'email' => 'Email',
            'status' => 'Status',
            'userfullname' => 'Full Name',
            'userIsAdmin' => 'Admin',
            'userIsSupport' => 'Support',
            'userPayment' => 'Payment',


        ];
    }

}
