<?php

namespace common\models;

use Yii;

/**
 * Tracks the link between an EventDraw saved layout and a Momentus service order.
 *
 * @property int $id
 * @property int $eventdraw_event_id
 * @property int $momentus_event_id
 * @property int|null $momentus_function_id
 * @property int $momentus_order_number
 * @property string $org_code
 * @property string|null $price_list
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class MomentusServiceOrder extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%momentus_service_order}}';
    }

    public function rules()
    {
        return [
            [['eventdraw_event_id', 'momentus_event_id', 'momentus_order_number', 'org_code'], 'required'],
            [['eventdraw_event_id', 'momentus_event_id', 'momentus_function_id', 'momentus_order_number'], 'integer'],
            [['org_code'], 'string', 'max' => 50],
            [['price_list'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 20],
            [['status'], 'default', 'value' => 'active'],
            [
                ['eventdraw_event_id', 'org_code'],
                'unique',
                'targetAttribute' => ['eventdraw_event_id', 'org_code'],
                'message' => 'A service order already exists for this event and org.',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eventdraw_event_id' => 'EventDraw Event ID',
            'momentus_event_id' => 'Momentus Event ID',
            'momentus_function_id' => 'Momentus Function ID',
            'momentus_order_number' => 'Momentus Order Number',
            'org_code' => 'Org Code',
            'price_list' => 'Price List',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Find the active service order for an EventDraw event.
     */
    public static function findByEventDrawEvent($eventdrawEventId, $orgCode)
    {
        return static::find()
            ->where([
                'eventdraw_event_id' => (int) $eventdrawEventId,
                'org_code' => (string) $orgCode,
                'status' => 'active',
            ])
            ->one();
    }

    /**
     * Find mapping row for this event/org regardless of status (e.g. soft-deleted).
     * Needed because the DB unique key is on (eventdraw_event_id, org_code) only.
     */
    public static function findAnyByEventDrawEvent($eventdrawEventId, $orgCode)
    {
        return static::find()
            ->where([
                'eventdraw_event_id' => (int) $eventdrawEventId,
                'org_code' => (string) $orgCode,
            ])
            ->one();
    }
}
