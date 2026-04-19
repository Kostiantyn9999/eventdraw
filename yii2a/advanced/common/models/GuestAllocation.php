<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "guestallocation".
 *
 * @property int $id
 * @property int $userid
 * @property string $allocationname
 * @property int $expirydate
 * @property int $created_at
 * @property int $updated_at
 * @property string $password
 * @property string $comments
 * @property string $xmlcode
 * @property string $email
 * @property string $allocationlink
 */
class GuestAllocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guestallocation';
    }

    public static function findLink($allocationlink)
    {
        return static::findOne(['allocationlink' => $allocationlink]);
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
    public function rules()
    {
        return [
            [['userid', 'allocationname'], 'required'],
            [['userid', 'created_at','updated_at'], 'integer'],
            [['comments', 'xmlcode'], 'string'],
            [['allocationname', 'password', 'email','expirydate','allocationlink'], 'string', 'max' => 255],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'User',
            'allocationname' => 'Allocation Name',
            'expirydate' => 'Expiry date',
            'created_at' => 'Created',
            'updated_at'=> 'Updated',
            'password' => 'Password',
            'comments' => 'Comments',
            'xmlcode' => 'Xml code',
            'email' => 'Email',
            'allocationlink' => 'Link',
        ];
    }
}
