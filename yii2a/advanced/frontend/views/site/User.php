<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\debug\panels\EventPanel;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\db\Expression;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property string $userfullname
 * @property integer $userIsAdmin
 * @property integer $userIsSupport
 * @property integer $userPayment
 * @property integer $last_login
 * @property integer $clientid
 * @property integer $maxSession
 * @property integer $userStencilid
 * @property integer $nrelease
 * @property integer $totSession
 * @property integer $company_admin
 * @property integer $expiry_date
 * @property string $firstname
 * @property string $surname
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_TRIAL = 11;

    public $new_password ="";
    public $user_templates =[];
    public $user_stencils =[];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED, self::STATUS_TRIAL]],
            ['status', 'validateStatus'],
            [['userIsAdmin','userIsSupport','userPayment','company_admin'],'boolean'],

            ['auth_key', 'default', 'value' => Yii::$app->security->generateRandomString()],
            ['password_hash', 'default', 'value' => Yii::$app->security->generatePasswordHash("welcome")],


            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['userfullname', 'trim'],
            ['userfullname', 'required'],
            ['userfullname', 'string', 'min' => 2, 'max' => 255],

            ['firstname', 'trim'],
            ['firstname', 'string', 'min' => 0, 'max' => 255],
            ['surname', 'trim'],
            ['surname', 'string', 'min' => 0, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['new_password', 'trim'],
            ['new_password', 'string', 'min' => 6],

            ['clientid', 'trim'],
            ['user_templates', 'each', 'rule' => ['integer']],

            //['maxSession','required'],
            [['maxSession'], 'number', 'min' => 1, 'max' => 999],
            ['maxSession', 'default', 'value' => 1],

            [['totSession'], 'number', 'min' => 0],
            ['totSession', 'default', 'value' => 0],

            [['nrelease'], 'number', 'min' => 0],
            ['nrelease', 'default', 'value' => 0],

            ['userStencilid', 'trim'],
            ['user_stencils', 'each', 'rule' => ['integer']],

            ['expiry_date', 'date', 'timestampAttribute' => 'expiry_date'],
            [['expiry_date'], 'default', 'value' => null],



        ];
    }

public  function validateStatus($attribute, $params)
{
    if ($this->$attribute == self::STATUS_TRIAL) {
        if (!$this->expiry_date) {
            $this->addError($attribute, 'Please set Expiry date for Custom trial status');
        }
    }
}
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        //return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        return static::findOne(['username' => $username]);
    }

    public static function findByUsernameAdmin($username)
    {
        return static::findOne(['username' => $username, 'userIsAdmin' => 1]);
    }
    public static function findByUsernameClient($username)
    {
        return static::findOne(['username' => $username, 'company_admin' => 1]);
    }
    public function getClientName()
    {
        $Client=Client::findOne(['id' => $this->clientid]);
        if ($Client){
            return $Client->clientName;
        }
        else{
            return null;
        }
    }

    public function getStatusName()
    {
        if ($this->status ==9){
            return 'Trial';
        }
        elseif ($this->status ==10){
            return 'Full version';
        }
        else
        {
            return 'Custom trial';
        }
    }

    public static function getClientList()
    {
        $clients = \common\models\Client::find()->all();

        $items = ArrayHelper::map($clients,'id','clientName');
        return $items;
    }

    public static function getTemplateList()
{
    $tmpls = \common\models\Template::findAll([
        'templateActive' => 1,
    ]);

    $items = ArrayHelper::map($tmpls,'id','templateName');
    return $items;
}
    public static function getTemplateListClient()
    {
        $tmpls = \common\models\Template::findAll([
            'templateActive' => 1,'clientid' => Yii::$app->user->identity->clientid
        ]);

        $items = ArrayHelper::map($tmpls,'id','templateName');
        return $items;
    }
    public static function getUserTemplates($id)
    {
        $tmpls = \common\models\UserTemplates::findAll([
            'userid' => $id,
        ]);
        $items =  ArrayHelper::getColumn($tmpls, 'templateid');
        return $items;
    }

    public function getStencilName()
    {
        $Stencil=Stencil::findOne(['id' => $this->userStencilid]);
        if ($Stencil){
            return $Stencil->stencilName;
        }
        else{
            return null;
        }
    }

    public static function getStencilList()
    {
        $tmpls = \common\models\Stencil::findAll([
            'stencilActive' => 1,
        ]);

        $items = ArrayHelper::map($tmpls,'id','stencilName');
        return $items;
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function  getPaymentStatus()
    {
        if ($this->status == self::STATUS_ACTIVE)
        {
            return 1;
        }
        else if ($this->status == self::STATUS_INACTIVE)
        {
            return 0;
        }
        else  // trial
        {
            if ($this->expiry_date > time())
            {
                return 2;
            }
            else
            {
                return -1;
            }
        }
    }
    public function setLastLogin()
    {
        $this->user_templates = -1;

        $this->touch('last_login');
        //increase nRelease if its less 1000
        if ($this->nrelease < 1000)
        {
            $this->nrelease = $this->nrelease +1;
            $this->save();
        }

    }

    public function setReleaseN($nRelease)
    {
            $this->nrelease = $nRelease;
            $this->save();
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Login',
            'email' => 'Email',
            'status' => 'Status',
            'userfullname' => 'Full Name',
            'userIsAdmin' => 'Admin',
            'userIsSupport' => 'Support',
            'userPayment' => 'Payment',
            'created_at'=> 'Created',
            'updated_at'=> 'Updated',
            'last_login'=> 'Last login',
            'clientid'=> 'Client',
            'user_templates' => 'Template list',
            'maxSession' => 'Max Sessions',
            'totSession' => 'Total Sessions',
            'userStencilid' => 'Stencil',
            'company_admin' => 'Company Admin',
            'expiry_date' => 'Expiry Date',
            'firstname' => 'First Name',
            'surname' => 'Surname'
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->new_password) {
                $this->setPassword($this->new_password);
            }
            //save user templates
            if ($this->user_templates != -1) {
                UserTemplates::setUserTmpl($this->id, $this->user_templates);
            }

            //if this user is Company admin - set companyid his companyid
//            if (Yii::$app->user->identity->company_admin) {
//                if (Yii::$app->user->identity->company_admin == 1) {
//                    $this->clientid = Yii::$app->user->identity->clientid;
//                }
//            }
            $this->userfullname = $this->firstname . ' ' . $this->surname;
            return true;
        } else {
            return false;
        }

    }
}
