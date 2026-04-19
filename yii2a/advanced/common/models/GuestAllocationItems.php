<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "guestallocationitems".
 *
 * @property int $id
 * @property int $guestallocationid
 * @property string $guestname
 * @property int $tablenumber
 * @property int $chairnumber
 * @property int $diet
 * @property int $dietcolor
 * @property int $seating
 * @property int $gift
 */
class GuestAllocationItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guestallocationitems';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['guestallocationid'], 'required'],
            [['guestallocationid', 'tablenumber', 'chairnumber', 'gift'], 'integer'],
            [['guestname','diet', 'seating','dietcolor'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'guestallocationid' => 'Guest Allocation ID',
            'guestname' => 'Guest',
            'tablenumber' => 'Table number',
            'chairnumber' => 'Chair number',
            'diet' => 'Diet',
            'dietcolor' => 'Diet color',
            'seating' => 'Seating',
            'gift' => 'Gift',
        ];
    }
}
