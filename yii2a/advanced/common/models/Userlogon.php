<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "userlogon".
 *
 * @property int $id
 * @property int $userid
 * @property string $guid
 * @property string $xml
 * @property string $ip_addr
 * @property integer $created_at
 * @property integer $updated_at
 */
class Userlogon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userlogon';
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

    public static function findByGUID($guid)
    {
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        return static::findOne(['guid' => $guid]);
    }

    public function rules()
    {
        return [
            [['userid', 'guid'], 'required'],
            [['userid'], 'integer'],
            [['xml'], 'string'],
            [['guid'], 'string', 'max' => 50],
            [['ip_addr'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'guid' => 'Guid',
            'xml' => 'Xml',
            'ip_addr' => 'Ip Addr',
            'created_at'=> 'Created',
            'updated_at'=> 'Updated',
        ];
    }
}
