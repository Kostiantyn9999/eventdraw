<?php

namespace common\models;

use common\models\ClientTemplates;
use Yii;
use yii\behaviors\TimestampBehavior;
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
 * @property int $AllowSaveFolder
 * @property string|null $momentusOrgCode
 */
class Client extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const STATUS_TRIAL = 11;
    public $client_templates =[];
    public $client_settings_meas_unit;
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

    public static function getClientTemplates($id)
    {
        $tmpls = \common\models\ClientTemplates::findAll([
            'clientid' => $id,
        ]);
        $items =  ArrayHelper::getColumn($tmpls, 'templateid');
        return $items;
    }

    public static function getTemplateList()
    {
//        $tmpls = \common\models\Template::findAll([
//            'templateActive' => 1,
//        ]);

        $tmpls = \common\models\Template::find()
            ->select(['id', 'clientid','templateName'])
            ->where(['templateActive' => 1])
            ->orderBy(['templateName' => SORT_ASC])->all();


        $items = ArrayHelper::map($tmpls,'id','templateName');
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
            ['clientEmail', 'trim'],
            ['clientEmail', 'required'],
            ['clientEmail', 'email'],
            ['clientEmail', 'string', 'max' => 255],
            ['clientEmail', 'unique', 'targetClass' => '\common\models\Client', 'message' => 'This email address has already been taken.'],
            ['client_settings_meas_unit', 'string', 'max' => 50],
            [['ShowMaxCapPlans'], 'number', 'min' => 0],
            ['ShowMaxCapPlans', 'default', 'value' => 0],
            [['AllowSaveCloud'], 'number', 'min' => 0],
            ['AllowSaveCloud', 'default', 'value' => 0],
            [['AllowFavouriteStencils'], 'number', 'min' => 0],
            ['AllowFavouriteStencils', 'default', 'value' => 0],
            [['clientActive','clientPayment'],'boolean'],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED, self::STATUS_TRIAL]],
            ['status', 'validateStatus'],
            ['expiry_date', 'date', 'timestampAttribute' => 'expiry_date'],
            [['expiry_date'], 'default', 'value' => null],
            [['AllowSaveFolder'], 'number', 'min' => 0],
            ['AllowSaveFolder', 'default', 'value' => 0],
            ['momentusOrgCode', 'string', 'max' => 50],
            ['momentusOrgCode', 'default', 'value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public  function validateStatus($attribute, $params)
    {
        if ($this->$attribute == self::STATUS_TRIAL) {
            if (!$this->expiry_date) {
                $this->addError($attribute, 'Please set Expiry date for Custom trial status');
            }
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clientName' => 'Client Name',
            'clientEmail' => 'Client Email',
            'clientActive' => 'Client Active',
            'clientPayment' => 'Client Payment',
            'created_at'=> 'Created',
            'updated_at'=> 'Updated',
            'client_templates' => 'Template list',
            'ShowMaxCapPlans' => 'Show Max Capacity Plans',
            'AllowSaveCloud' => 'Allow EventDraw Cloud',
            'client_settings_meas_unit'=> 'Measurement Units',
            'AllowFavouriteStencils' => 'Allow Favourite Stencils',
            'AllowSaveFolder' => 'Allow Save Folder',
            'momentusOrgCode' => 'Account Code',
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

    public function saveSettings()
    {
        //get list of user for this client
        $ClientUsers = \common\models\User ::findAll([
            'clientid' => $this->id,
        ]);
       //now for all user set selected properties
        $arrlength = count($ClientUsers);
        for($x = 0; $x < $arrlength; $x++) {
            Usersettings::setUserSettings($ClientUsers[$x]->id, $this->client_settings_meas_unit);
        }


        return true;
    }
        public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        } else {
            return false;
        }

    }
}
