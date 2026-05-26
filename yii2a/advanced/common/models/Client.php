<?php

namespace common\models;

use common\models\ClientTemplates;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string $clientName
 * @property string $clientEmail
 * @property int $clientActive
 * @property int $clientPayment
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $ShowMaxCapPlans
 * @property integer $status
 * @property integer $expiry_date
 * @property int $AllowSaveCloud
 * @property int $AllowFavouriteStencils
 * @property integer $last_client_login
 * @property integer $last_client_email
 * @property string $clientCountry
 * @property int $AllowShare
 * @property int $siDate
 * @property int $ClientVenueType
 * @property int $AllowMomentus
 * @property string $MomentusAPIKey
 * @property string $MomentusSecretKey
 * @property string $clientNotes
 * @property string $MomentusAPIUrlAuth
 * @property string $MomentusAPIUrl
 * @property string|null $momentusOrgCode
 * @property int $AllowSaveFolder
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * Hostnames where Momentus Enterprise API credentials are read from the logged-in user's client row.
     */
    public static function hostsUsingPerClientMomentusConfig()
    {
        return [
            'momentusproduction.eventdrawus.com',
            // 'yii2a'
        ];
    }

    /**
     * Client admin hosts where shapes/templates are scoped to the logged-in client.
     */
    public static function hostsUsingClientResourceScoping()
    {
        return [
            'clientmomentusproduction.eventdrawus.com',
        ];
    }

    /**
     * Whether the current (or given) host uses per-client shape/template scoping.
     */
    public static function usesClientResourceScoping($hostName = null)
    {
        if ($hostName === null) {
            if (!isset(\Yii::$app->request)) {
                return false;
            }
            $hostName = \Yii::$app->request->hostName;
        }

        return in_array(
            strtolower((string) $hostName),
            array_map('strtolower', static::hostsUsingClientResourceScoping()),
            true
        );
    }

    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_TRIAL = 11;
    const STATUS_CHURN = 12;
    public $client_templates = [];
    public $client_stencils = [];
    public $client_settings_meas_unit;
    public $client_max_sessions = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function getLastLoginList()
    {
        $info = array('Last week','Within  30 days','Over 30 days','Not Set');

        return $info;
    }

    public static function getClientStencils($id)
    {
        $stencils = \common\models\ClientStencils::findAll([
            'clientid' => $id,
        ]);
        $items = ArrayHelper::getColumn($stencils, 'stencilid');
        return $items;
    }

    public static function getClientTemplates($id)
    {
        $tmpls = \common\models\ClientTemplates::findAll([
            'clientid' => $id,
        ]);
        $items = ArrayHelper::getColumn($tmpls, 'templateid');
        return $items;
    }

    public function getCountryName()
    {
        $Country = \common\models\Country::findOne(['COUNTRY_ISO3' => $this->clientCountry]);
        if ($Country) {
            return $Country->NAME;
        } else {
            return null;
        }
    }

    public function getVenueTypeName()
    {
        $VenueType = \common\models\VenueType::findOne(['id' => $this->ClientVenueType]);
        if ($VenueType) {
            return $VenueType->venueTypeName;
        } else {
            return null;
        }
    }


public function getTypeName()
    {
        if ($this->clientType == 1) {
            return 'Venue';
        } elseif ($this->clientType == 2) {
            return 'Event Organiser';
        } else {
            return 'Not Set';
        }
    }
    public static function getTemplateList()
    {
//        $tmpls = \common\models\Template::findAll([
//            'templateActive' => 1,
//        ]);

        $tmpls = \common\models\Template::find()
            ->select(['id', 'clientid', 'templateName'])
            ->where(['templateActive' => 1])
            ->orderBy(['templateName' => SORT_ASC])->all();


        $items = ArrayHelper::map($tmpls, 'id', 'templateName');
        return $items;
    }

    public function rules()
    {
        return [
            ['clientName', 'trim'],
            ['clientName', 'required'],
            ['clientName', 'unique', 'targetClass' => '\common\models\Client', 'message' => 'This client has already been taken.'],
            ['clientName', 'string', 'min' => 2, 'max' => 255],
            ['client_templates', 'each', 'rule' => ['integer']],
            ['client_stencils', 'each', 'rule' => ['integer']],
            [['ClientVenueType'], 'number', 'min' => 0],
            /*['clientEmail', 'trim'],
            ['clientEmail', 'required'],
            ['clientEmail', 'email'],
            ['clientEmail', 'string', 'max' => 255],
            ['clientEmail', 'unique', 'targetClass' => '\common\models\Client', 'message' => 'This email address has already been taken.'],*/
            ['client_settings_meas_unit', 'string', 'max' => 50],
            [['ShowMaxCapPlans'], 'number', 'min' => 0],
            [['client_max_sessions'], 'number', 'min' => 0],
            [['last_client_login'], 'number'],
            [['last_client_email'], 'number'],
            [['siDate'], 'number'],
            ['ShowMaxCapPlans', 'default', 'value' => 0],
            [['clientType'], 'number', 'min' => 0],
            ['clientType', 'default', 'value' => 0],
            [['AllowSaveCloud'], 'number', 'min' => 0],
            ['AllowSaveCloud', 'default', 'value' => 0],
            [['Allow3D'], 'number', 'min' => 0],
            ['Allow3D', 'default', 'value' => 0],
            [['AllowImportPdf'], 'number', 'min' => 0],
            ['AllowImportPdf', 'default', 'value' => 0],
            [['AllowFavouriteStencils'], 'number', 'min' => 0],
            ['AllowFavouriteStencils', 'default', 'value' => 0],
            [['AllowShare'], 'number', 'min' => 0],
            ['AllowShare', 'default', 'value' => 0],
             [['AllowMomentus'], 'number', 'min' => 0],
            ['AllowMomentus', 'default', 'value' => 0],
            [['clientActive', 'clientPayment'], 'boolean'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED, self::STATUS_TRIAL, self::STATUS_CHURN]],
            ['status', 'validateStatus'],
            ['expiry_date', 'date', 'timestampAttribute' => 'expiry_date'],
            [['expiry_date'], 'default', 'value' => null],
            ['clientCountry', 'string', 'max' => 3],
             [['MomentusAPIKey'], 'default', 'value' => null],
            ['MomentusAPIKey', 'string', 'max' => 100],
             [['MomentusAPIKey'], 'default', 'value' => null],
            ['MomentusSecretKey', 'string', 'max' => 100],
             [['MomentusSecretKey'], 'default', 'value' => null],
            ['clientNotes', 'string', 'max' => 255],
             [['clientNotes'], 'default', 'value' => null],
            ['MomentusAPIUrlAuth', 'string', 'max' => 255],
            [['MomentusAPIUrlAuth'], 'default', 'value' => null],
            ['MomentusAPIUrl', 'string', 'max' => 255],
            [['MomentusAPIUrl'], 'default', 'value' => null],
            ['momentusOrgCode', 'string', 'max' => 50],
            [['momentusOrgCode'], 'default', 'value' => null],
            [['AllowSaveFolder'], 'number', 'min' => 0],
            ['AllowSaveFolder', 'default', 'value' => 0]
             
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validateStatus($attribute, $params)
    {
        if ($this->$attribute == self::STATUS_TRIAL) {
            if (!$this->expiry_date) {
                $this->addError($attribute, 'Please set Expiry date for Custom trial status');
            }
        }
    }

    public function getStatusName()
    {
        if ($this->status == 9) {
            return 'Trial';
        } elseif ($this->status == 10) {
            return 'Full version';
        } elseif ($this->status == 11) {
            return 'Custom trial';    
        } elseif ($this->status == 12) {
            return 'Churn';        
        } else {
            return 'No Access';
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
    
    public function getPaymentStatus()
    {
        if ($this->status == self::STATUS_ACTIVE) {
            return 1;
        } else if ($this->status == self::STATUS_INACTIVE) {
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

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clientName' => 'Client Name',
            'clientEmail' => 'Client Email',
            'clientActive' => 'Client Active',
            'clientPayment' => 'Client Payment',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
            'client_templates' => 'Template list',
            'client_stencils' => 'Stencil list',
            'ShowMaxCapPlans' => 'Show Max Capacity Plans',
            'AllowSaveCloud' => 'Allow EventDraw Cloud',
            'client_settings_meas_unit' => 'Measurement Units',
            'client_max_sessions' => 'Max Sessions',
            'AllowFavouriteStencils' => 'Allow Favourite Stencils',
            'Allow3D' => 'Allow 3D',
            'AllowImportPdf' => 'Allow Import PDF',
            'clientType' => 'Type',
            'last_client_login' => 'Last Login',
            'last_client_email' => 'Last Email Sent',
            'clientCountry' => 'Country',
            'AllowShare'=> 'Allow Share Edit Plan',
            'siDate' => 'SI Date',
            'ClientVenueType' => 'Venue Type',
            'AllowMomentus' => 'Momentus Enterprise',
            'MomentusAPIKey' => 'Momentus API Token',
            'MomentusSecretKey' => 'Momentus Subscription Key',
            'clientNotes' => '........Notes..........',
            'MomentusAPIUrlAuth' => 'Momentus Elite Auth URL (Elite only)',
            'MomentusAPIUrl' => 'Momentus Enterprise API Base URL',
            'momentusOrgCode' => 'Momentus Organisation Code',
            'AllowSaveFolder'=> 'Allow Save to Folder'
        ];
    }

    /**
     * Returns this client's Momentus Account Code (OrgCode).
     * Falls back to the global params value if not set on the client.
     */
    public function getMomentusOrgCode()
    {
        $code = trim((string) $this->momentusOrgCode);
        if ($code !== '') {
            return $code;
        }
        return trim((string) (\Yii::$app->params['momentus']['orgCode'] ?? ''));
    }

    public function saveTemplates()
    {
        ClientTemplates::setClientTmpl($this->id, $this->client_templates);
        return true;
    }

    public function saveStencils()
    {
        ClientStencils::setClientStns($this->id, $this->client_stencils);
        return true;
    }

    public function saveSettings()
    {
        //get list of user for this client
        $ClientUsers = \common\models\User::findAll([
            'clientid' => $this->id,
        ]);
        //now for all user set selected properties
        $arrlength = count($ClientUsers);
        for ($x = 0; $x < $arrlength; $x++) {
            Usersettings::setUserSettings($ClientUsers[$x]->id, $this->client_settings_meas_unit);
        }


        return true;
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            $old_expiry_date = parent::getOldAttributes()["expiry_date"];
            $new_expiry_date = $this->expiry_date;
            
            $old_country = parent::getOldAttributes()["clientCountry"];
            $new_country = $this->clientCountry;

         //if ($new_expiry_date != $old_expiry_date)
         if (1==1)
         {
             $ClientUsers = \common\models\User::findAll([
            'clientid' => $this->id,
            ]);
            //now for all user set new expiry date
            $arrlength = count($ClientUsers);
            for ($x = 0; $x < $arrlength; $x++) {
                
                if ($ClientUsers[$x]->status != 0)
                {
                    $ClientUsers[$x]->status =   $this->status;
                }
                    $ClientUsers[$x]->userType =   $this->clientType;
                    $ClientUsers[$x]->expiry_date =   $this->expiry_date;
                    $ClientUsers[$x]->UserCountry =  $new_country;
                    $ClientUsers[$x]->siDate = $this->siDate;
                    $ClientUsers[$x]->save(false);
                
                
               
            }
         }
         

         

        }

        if (parent::beforeSave($insert)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Enterprise Connect API settings stored on the client row (TEST/PROD per client).
     *
     * Field mapping (repurposed from Elite OAuth columns):
     * - MomentusAPIUrl      -> baseUrl
     * - MomentusAPIKey      -> apiToken
     * - MomentusSecretKey   -> subscriptionKey
     * - momentusOrgCode     -> orgCode
     *
     * @return array<string, string>
     */
    public function getMomentusEnterpriseConfig()
    {
        $config = [];

        $baseUrl = trim((string) $this->MomentusAPIUrl);
        if ($baseUrl !== '') {
            $config['baseUrl'] = rtrim($baseUrl, '/');
        }

        $apiToken = trim((string) $this->MomentusAPIKey);
        if ($apiToken !== '') {
            $config['apiToken'] = $apiToken;
        }

        $subscriptionKey = trim((string) $this->MomentusSecretKey);
        if ($subscriptionKey !== '') {
            $config['subscriptionKey'] = $subscriptionKey;
        }

        $orgCode = trim((string) $this->momentusOrgCode);
        if ($orgCode !== '') {
            $config['orgCode'] = $orgCode;
        }

        return $config;
    }

    public function hasMomentusEnterpriseConfig()
    {
        if ((int) $this->AllowMomentus !== 1) {
            return false;
        }

        $config = $this->getMomentusEnterpriseConfig();

        return isset($config['baseUrl'], $config['apiToken'], $config['subscriptionKey']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['clientid' => 'id']);
    }
}
