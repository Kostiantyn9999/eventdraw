<?php

namespace common\models;

use kartik\password\StrengthValidator;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

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
 * @property integer $is_subscribed
 * @property integer $email_count
 * @property string $last_email_date
 * @property string $firstname
 * @property string $surname
 * @property string $template_number
 * @property string $ShowMaxCapPlans
 * @property integer $user_type
 * @property int $AllowSaveCloud
 * @property int $userSavedFloorplansCount
 * @property string $UserCompanyName
 * @property string $UserCountry
 * @property integer $UserDoNotEmail
 * @property int $siDate

 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREATE = 'create';
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_TRIAL = 11;
    const STATUS_CHURN = 12;
    const STATUS_ARCHIVE = 13;


    const USER_NOT_SET = 0;
    const USER_VENUE = 1;
    const USER_EVENT_ORGANISER = 2;



    public $new_password = "";
    public $user_templates = [];
    public $user_stencils = [];
    public $user_settings = [];
    public $user_floorplans = [];
    public $user_settings_meas_unit;
    public $user_settings_localDir;
    public $user_type;

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
     * @return array|array[]
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = [
            'clientid',
            'firstname',
            'surname',
            'email',
            'status',
            'expiry_date',
            'maxSession',
            'userIsSupport',
            'company_admin',
            'username',
            'template_number',
            'userType',
        ];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_TRIAL],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED, self::STATUS_TRIAL, self::STATUS_CHURN, self::STATUS_ARCHIVE]],

            //[['user_type'],'string'],

            ['userType', 'default', 'value' => self::USER_NOT_SET],
            ['userType', 'in', 'range' => [self::USER_VENUE, self::USER_EVENT_ORGANISER, self::USER_NOT_SET]],



            //['status', 'validateStatus'],*/
            [['userIsAdmin', 'userIsSupport', 'userPayment', 'company_admin', 'is_subscribed'], 'boolean'],
            [['UserCountry'], 'string', 'max' => 3],
            ['auth_key', 'default', 'value' => Yii::$app->security->generateRandomString()],
            [
                ['password_hash'],
                StrengthValidator::className(),
                'min' => 8,
                'lower' => 1,
                'upper' => 1,
                'digit' => 1,
                'special' => 1,
            ],
            ['password_hash', 'default', 'value' => Yii::$app->security->generatePasswordHash("eventdraw55ax22")],
            [['siDate'], 'number'],
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email_count', 'number'],
            ['user_settings_localDir', 'number'],
            ['template_number', 'number'],
            ['last_email_date', 'string'],

            ['userfullname', 'trim'],

            ['UserCompanyName', 'string'],

            ['firstname', 'required'],
            ['firstname', 'trim'],
            ['firstname', 'string', 'min' => 0, 'max' => 255],
            ['surname', 'trim'],


            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'required'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['new_password', 'trim'],
            ['new_password', 'string', 'min' => 6],

            ['clientid', 'trim'],
            ['user_templates', 'each', 'rule' => ['integer']],
            ['user_settings_meas_unit', 'trim'],

            [['AllowSaveCloud'], 'number', 'min' => 0],
            ['AllowSaveCloud', 'default', 'value' => 0],

            [['maxSession'], 'number', 'min' => 1, 'max' => 999],
            [['maxSession'], 'default', 'value' => 1],

            [['ShowMaxCapPlans'], 'number', 'min' => 0],
            ['ShowMaxCapPlans', 'default', 'value' => 0],

            [['userSavedFloorplansCount'], 'number', 'min' => 0],
            ['userSavedFloorplansCount', 'default', 'value' => 0],

            [['UserDoNotEmail'], 'number', 'min' => 0],
            ['UserDoNotEmail', 'default', 'value' => 0],
         
         
            [['totSession'], 'number', 'min' => 0],
            ['totSession', 'default', 'value' => 0],

            [['nrelease'], 'number', 'min' => 0],
            ['nrelease', 'default', 'value' => 0],

            ['userStencilid', 'trim'],
            ['user_stencils', 'each', 'rule' => ['integer']],

            ['user_settings', 'each', 'rule' => ['string']],

            ['expiry_date', 'date', 'timestampAttribute' => 'expiry_date'],
            [['expiry_date'], 'default', 'value' => null],


        ];
    }

    public function validateStatus($attribute, $params)
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

    public function getClientShowMaxCapPlans()
    {
        $Client = Client::findOne(['id' => $this->clientid]);
        if ($Client) {
            return $Client->ShowMaxCapPlans;
        } else {
            return false;
        }
    }

    public function getSiDateName()
    {
        if ($this->siDate == 1) {
            return 'January';
        }elseif ($this->siDate == 2) {
            return 'February'; 
        }elseif ($this->siDate == 3) {
            return 'March'; 
        }elseif ($this->siDate == 4) {
            return 'April'; 
        }elseif ($this->siDate == 5) {
            return 'May'; 
        }elseif ($this->siDate == 6) {
            return 'June'; 
        }elseif ($this->siDate == 7) {
            return 'July'; 
        }elseif ($this->siDate == 8) {
            return 'August';
        }elseif ($this->siDate == 9) {
            return 'September'; 
        }elseif ($this->siDate == 10) {
            return 'October'; 
        }elseif ($this->siDate == 11) {
            return 'November'; 
        }elseif ($this->siDate == 12) {
            return 'December';                             
              
        } else {
            return 'Not set';
        }
    }


    public function getClientAllowSaveCloud()
    {
        $Client = Client::findOne(['id' => $this->clientid]);
        if ($Client) {
            return $Client->AllowSaveCloud;
        } else {
            return false;
        }
    }

    public function getCountSavedFloorplans()
    {
        $retValue = 0;

        //  $Events =  \common\models\Event::findAll([
        //  'userid' => $this->id,
        //  ]);
        // if ($Events) {
        //     $retValue = count($Events);
        //  }

        return $retValue;
    }
    public function getClientAllowFavStencils()
    {
        $retValue = 0;

        $Client = Client::findOne(['id' => $this->clientid]);
        if ($Client) {
            if ($Client->AllowFavouriteStencils > 0) {
                $retValue = $this->clientid;
            }
        }

        return $retValue;
    }

    public function getClientUserType()
    {
        // $Client = Client::findOne(['id' => $this->clientid]);
        //     if ($Client) {
        //         $retValue = $Client->clientType;
        //     }
        //     else
        //     {
               $retValue = $this->userType;
            // }    
        return $retValue;
    }

    public function getClientUserStatus()
    {
        // $Client = Client::findOne(['id' => $this->clientid]);
        //     if ($Client) {
        //         $retValue = $Client->status;
        //     }
        //     else
        //     {
               $retValue = $this->status;
            // }    
        return $retValue;
    }

    public function getClientUserExpiryDate()
    {
        // $Client = Client::findOne(['id' => $this->clientid]);
        //     if ($Client) {
        //         $retValue = $Client->expiry_date;
        //     }
        //     else
        //     {
               $retValue = $this->expiry_date;
            // }    
        return $retValue;
    }


    public function getUserMeasurementUnit()
    {
        $user_meas = 'Meters';//meters by default
        $UserSettings = Usersettings::findOne(['userid' => $this->id]);
        if ($UserSettings) {
            if ($UserSettings->meas_unit == 'FT') {
                $user_meas = 'Feet';
            } else if ($UserSettings->meas_unit == 'BOTH') {
                $user_meas = 'Both (Meeters and Feet)';
            }

        }


        return $user_meas;
    }


    public static function findByUsernameAdmin($username)
    {
        return static::findOne(['username' => $username, 'userIsAdmin' => 1]);
    }

    public static function findByUsernameClient($username)
    {
        return static::findOne(['username' => $username, 'company_admin' => 1]);
    }

    public static function findByUsernamePSW($username, $psw)
    {
        return static::findOne(['username' => $username, 'company_admin' => 1]);
    }


    public function getClientName()
    {
        $Client = Client::findOne(['id' => $this->clientid]);
        if ($Client) {
            return $Client->clientName;
        } else {
            return null;
        }
    }

    public function getCountryName()
    {
        $Country = \common\models\Country::findOne(['COUNTRY_ISO3' => $this->UserCountry]);
        if ($Country) {
            return $Country->NAME;
        } else {
            return null;
        }
    }

    public function getStatusName()
    {

        $curStatus = $this->getClientUserStatus();
        if ($curStatus == 9) {
            return 'Trial';
        } elseif ($curStatus == 10) {
            return 'Full version';
        } elseif ($curStatus == 0) {
            return 'No Access';    
        } elseif ($curStatus == 12) {
            return 'Churn';        
        } elseif ($curStatus == 13) {
            return 'Archive';        
        } else {
            return 'Custom trial';
        }
    }
    public function getuserType()
    {
        if ($this->userType == 0) {
            return 'Not Set';
        } elseif ($this->status == 1) {
            return 'Venue';
        } else {
            return 'Event Organiser';
        }
    }
    public static function getClientList()
    {
        //$clients = \common\models\Client::find()->all();

        $clients = \common\models\Client::find()
            ->select(['id', 'clientName'])
            ->orderBy(['clientName' => SORT_ASC])->all();

        $items = ArrayHelper::map($clients, 'id', 'clientName');
        return $items;
    }

public function getTypeName()
    {
        $curType = $this->getClientUserType();

        if ($curType == 1) {
            return 'Venue';
        } elseif ($curType == 2) {
            return 'Event Organiser';
        } else {
            return 'Not Set';
        }
    }


    public static function getTemplateList()
    {
//    $tmpls = \common\models\Template::findAll([
//        'templateActive' => 1,
//    ]);

        $tmpls = \common\models\Template::find()
            ->select(['id', 'clientid', 'templateName'])
            ->where(['templateActive' => 1])
            ->orderBy(['templateName' => SORT_ASC])->all();


        $items = ArrayHelper::map($tmpls, 'id', 'templateName');
        return $items;
    }

    public static function getTemplateListClient()
    {
        $tmpls = \common\models\Template::findAll([
            'templateActive' => 1, 'clientid' => Yii::$app->user->identity->clientid
        ]);

        $items = ArrayHelper::map($tmpls, 'id', 'templateName');
        return $items;
    }

    public static function getUserTemplates($id)
    {
        $tmpls = \common\models\UserTemplates::findAll([
            'userid' => $id,
        ]);
        $items = ArrayHelper::getColumn($tmpls, 'templateid');
        return $items;
    }

    public static function getUserFloorplans($id)
    {
        $floorplans = \common\models\Event::find()
            ->select(['id', 'eventName'])
            ->where(['userid' => $id, 'eventActive' => 1])
            ->orderBy(['eventName' => SORT_ASC])->all();

        $items = ArrayHelper::map($floorplans, 'id', 'eventName');
        return $items;

    }


    public static function getUserStencils($id)
    {
        $stencils = \common\models\UserStencils::findAll([
            'userid' => $id,
        ]);
        $items = ArrayHelper::getColumn($stencils, 'stencilid');
        return $items;
    }

    public static function getUserSettings($id, $paramName)
    {
        $UserSetting = Usersettings::findOne(['userid' => $id]);
        if ($UserSetting) {
            return $UserSetting->$paramName;
        } else {
            return null;
        }
    }

    public function getStencilName()
    {
        $Stencil = Stencil::findOne(['id' => $this->userStencilid]);
        if ($Stencil) {
            return $Stencil->stencilName;
        } else {
            return null;
        }
    }

    public static function getStencilList()
    {
//        $stensils = \common\models\Stencil::findAll([
//            'stencilActive' => 1,
//        ]);

        $stensils = \common\models\Stencil::find()
            ->select(['id', 'stencilName'])
            ->where(['stencilActive' => 1])
            ->orderBy(['stencilName' => SORT_ASC])->all();

        $items = ArrayHelper::map($stensils, 'id', 'stencilName');
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
    public static function findByVerificationToken($token)
    {
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

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
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

    public function getPaymentStatus()
    {
        //if this user has client - use client status field for detect payment status

        $Client = Client::findOne(['id' => $this->clientid]);
        if ($Client) {
            if ($Client->status == self::STATUS_ACTIVE) {
                return 1;
            } else if ($Client->status == self::STATUS_INACTIVE or $Client->status == self::STATUS_CHURN ) {
                return 0;
            } else  // trial
            {
                if ($Client->expiry_date > time()) {
                    return 2;
                } else {
                    return -1;
                }
            }
        } else {
            if ($this->status == self::STATUS_ACTIVE) {
                return 1;
            } else if ($this->status == self::STATUS_INACTIVE or $this->status == self::STATUS_CHURN or $this->status == self::STATUS_ARCHIVE) {
                return 0;
            } else  // trial
            {
                if ($this->expiry_date > time()) {
                    return 2;
                } else {
                    return -1;
                }
            }
        }


    }

    public function setCountSavedFloorplans()
    {

         $Events =  \common\models\Event::findAll([
          'userid' => $this->id,
          ]);
         if ($Events) {
             $recCount = count($Events);
          }
          else {
            $recCount = 0;
          }

            $this->userSavedFloorplansCount =  $recCount;
            $this->save(false);

    }
    public function setLastLogin()
    {
        $this->user_templates = -1;
        $this->user_stencils = -1;

        $this->touch('last_login');
        //increase nRelease if its less 1000
        if ($this->nrelease < 1000) {
            $this->nrelease = $this->nrelease + 1;
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
            'password_hash' => 'Password',
            'email' => 'Email',
            'status' => 'Status',
            'userfullname' => 'Full Name',
            'userIsAdmin' => 'Admin',
            'userIsSupport' => 'Support',
            'userPayment' => 'Payment',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
            'last_login' => 'Last login',
            'clientid' => 'Client',
            'user_templates' => 'Template list',
            'maxSession' => 'Max Sessions',
            'totSession' => 'Total Sessions',
            'userStencilid' => 'Stencil',
            'company_admin' => 'Company Admin',
            'expiry_date' => 'Expiry Date',
            'firstname' => 'First Name',
            'surname' => 'Surname',
            'ShowMaxCapPlans' => 'Show Max Capacity Plans',
            'user_settings_meas_unit' => 'Measurement Units',
            'user_settings_localDir' => 'Local Directory',
            'userType' => 'Type',
            'AllowSaveCloud' => 'Allow EventDraw Cloud',
            'userSavedFloorplansCount' => 'Cloud Plans',
            'UserCompanyName' => 'Company Name',
            'UserCountry' => 'Country',
            'UserDoNotEmail' => 'Do Not Email',
            'siDate' => 'SI Date'

        ];
    }

    public function saveTemplates()
    {
        UserTemplates::setUserTmpl($this->id, $this->user_templates);
        return true;
    }

    public function saveStencils()
    {
      
        UserStencils::setUserStns($this->id, $this->user_stencils);
        return true;
    }

    public function saveSettings()
    {
        Usersettings::setUserSettings($this->id, $this->user_settings_meas_unit, $this->user_settings_localDir);
        return true;
    }

 public function addRecordToSpreadsheet($userInfo)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets and PHP');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig('credentials.json');
        $service = new \Google_Service_Sheets($client);  
        
        //$spreadsheetId = "1A8J93VX3H7z_qRJ383Da61QSXaSjuMwrppEu_uo6rZg"; 
        $spreadsheetId = "1Qcxu5GDZ1_IvKfJGqQAEAhKPGN-yI49ZON2uj9PUs8g";
        
        //insert new rows data on spreadsheet
        $update_range = "UserList!A1:S1";

    if ($userInfo['expiry_date'] != 0)
    {
        $expiry_date_good =  \Yii::$app->formatter->asDatetime($data['expiry_date'], "php:d-M-Y H:i:s");
    }
    else
    {
        $expiry_date_good = '';
    }

    if ($userInfo['last_login'] != 0)
    {
        $last_login_good =  \Yii::$app->formatter->asDatetime($userInfo['last_login'], "php:d-M-Y H:i:s");
    }
    else
    {
        $last_login_good = '';
    }
    

    if ($userInfo['created_at'] != 0)
    {
        $created_at_good =  \Yii::$app->formatter->asDatetime($userInfo['created_at'], "php:d-M-Y H:i:s");
    }
    else
    {
        $created_at_good = '';
    }

    if ($userInfo['updated_at'] != 0)
    {
        $updated_at_good =  \Yii::$app->formatter->asDatetime($userInfo['updated_at'], "php:d-M-Y H:i:s");
    }
    else
    {
        $updated_at_good = '';
    }

    if ($userInfo['userIsAdmin'] == 1)
    {
        $userIsAdmin_good =  'Yes';
    }
    else
    {
        $userIsAdmin_good = 'No';
    }

if ($userInfo['UserDoNotEmail'] == 1)
    {
        $UserDoNotEmail =  'Yes';
    }
    else
    {
        $UserDoNotEmail = 'No';
    }

    if ($userInfo['is_subscribed'] == 1)
    {
        $is_subscribed =  'Yes';
    }
    else
    {
        $is_subscribed = 'No';
    }
    

    if ($userInfo['userIsSupport'] == 1)
    {
        $userIsSupport_good =  'Yes';
    }
    else
    {
        $userIsSupport_good = 'No';
    }

    if ($userInfo['userPayment'] == 1)
    {
        $userPayment_good =  'Yes';
    }
    else
    {
        $userPayment_good = 'No';
    }

    if ($userInfo['company_admin'] == 1)
    {
        $company_admin_good =  'Yes';
    }
    else
    {
        $company_admin_good = 'No';
    }


    if ($userInfo->getClientName() != NULL)
    {
        $client_name_good = $userInfo->getClientName() ;
    }
    else
    {
        $client_name_good = '';
    }

    if ($userInfo->getStencilName() != NULL)
    {
        $stencil_name_good = $userInfo->getStencilName() ;
    }
    else
    {
        $stencil_name_good = '';
    }

    if ($userInfo->getStatusName() != NULL)
    {
        $status_name_good = $userInfo->getStatusName() ;
    }
    else
    {
        $status_name_good = '';
    }

    if ($userInfo->getCountryName() != NULL)
    {
        $country_name_good = $userInfo->getCountryName() ;
    }
    else
    {
        $country_name_good = '';
    }


    if ($userInfo->getTypeName() != NULL)
    {
        $type_name_good = $userInfo->getTypeName() ;
    }
    else
    {
        $type_name_good = '';
    }

    $values = [
                    [
                        $this->id,
                        $client_name_good,
                        $this->firstname,
                        $this->surname,
                        $this->email,
                        $this->username,
                        $last_login_good,
                        $this->totSession,
                        $stencil_name_good,
                        $status_name_good,
                        $expiry_date_good,
                        $userIsAdmin_good,
                        $userIsSupport_good,
                        $userPayment_good,
                        $company_admin_good,
                        $created_at_good,
                        $updated_at_good,
                        $this->maxSession,
                        $this->UserCompanyName,
                        $country_name_good,
                        $UserDoNotEmail,
                        $is_subscribed
                    ]
                  ]; 
        $params = ['valueInputOption' => 'RAW'];

        //var_dump($values);die();
        $body = new \Google_Service_Sheets_ValueRange(['values' => $values]);
        $update_sheet = $service->spreadsheets_values->append($spreadsheetId, $update_range, $body, $params);

    }
    
    public function afterSave($insert)
    {
        if ($insert) {
        $this->addRecordToSpreadsheet($this);

        //set special bulltin board messages for new user

            // $bulletBoardUser1 = new \common\models\BulletBoardUsers();
            // $bulletBoardUser1->user_id = $this->id;
            // $bulletBoardUser1->bullet_board_id =30;
            // $bulletBoardUser1->save();

            // $bulletBoardUser2 = new \common\models\BulletBoardUsers();
            // $bulletBoardUser2->user_id = $this->id;
            // $bulletBoardUser2->bullet_board_id =31;
            // $bulletBoardUser2->save();

            // $bulletBoardUser3 = new \common\models\BulletBoardUsers();
            // $bulletBoardUser3->user_id = $this->id;
            // $bulletBoardUser3->bullet_board_id =32;
            // $bulletBoardUser3->save();
        }
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->new_password) {
                $this->setPassword($this->new_password);
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
