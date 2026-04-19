<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $userfullname;
    public $firstname;
    public $surname;
    public $UserCompanyName;
 //   public $captcha;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            // ['username', 'required'],
            // ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            // ['username', 'string', 'min' => 2, 'max' => 255],

            ['firstname', 'trim'],
            ['firstname', 'required'],
            ['firstname', 'string', 'min' => 2, 'max' => 255],

            ['surname', 'trim'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['UserCompanyName', 'string'],
            ['UserCompanyName', 'required'],

            ['password', 'required'],
            ['password', 'match', 'pattern'=>"/^(?=.*[A-Z])(?=.*?[0-9])/",'message'=>'Password needs to contain at least: - 1 digit - 1 uppercase letter - 6 characters'],
            ['password', 'string', 'min' => 6]
            

            // ['captcha', 'required'],
            // ['captcha', 'captcha'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username =  $this->email;
        $user->firstname = $this->firstname;
        $user->surname = $this->surname;
        $user->userfullname = $this->firstname . ' '. $this->surname;
        $user->email = $this->email;
        $user->company_admin = 0;
        $user->status = 11; //custom trial
        
        $user->UserCompanyName = $this->UserCompanyName;

        $time = date("Y-m-d");  
        $dt2 = strtotime( "+1 month", strtotime( $time ) );
        $user->expiry_date = $dt2;

        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        //return $user->save() && $this->sendEmail($user);
        return $user->save();
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Login',
            'email' => 'Email',
            'status' => 'Status',
            'userfullname' => 'Full Name',
            'firstname' => 'First Name',
            'surname' => 'Surname',
            'userIsAdmin' => 'Admin',
            'userIsSupport' => 'Support',
            'userPayment' => 'Payment',


        ];
    }

}
