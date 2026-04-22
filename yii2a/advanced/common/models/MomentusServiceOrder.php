<?php

namespace common\models;

use Yii;

/**
 * Tracks the link between an EventDraw Room Diagram (EventSpaceDiagram) and a
 * Momentus service order.  One service order is stored per space diagram so
 * that each Room Diagram within the same Momentus event can have its own
 * independent order.
 *
 * @property int         $id
 * @property int|null    $eventdraw_event_id          Legacy / reference only (nullable).
 * @property int|null    $eventdraw_space_diagram_id  Primary local key (EventSpaceDiagramID).
 * @property int         $momentus_event_id
 * @property int|null    $momentus_function_id
 * @property int         $momentus_order_number
 * @property string      $org_code
 * @property string|null $price_list
 * @property string      $status
 * @property string      $created_at
 * @property string      $updated_at
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
            [['momentus_event_id', 'momentus_order_number', 'org_code'], 'required'],
            [['eventdraw_event_id', 'eventdraw_space_diagram_id', 'momentus_event_id', 'momentus_function_id', 'momentus_order_number'], 'integer'],
            [['org_code'], 'string', 'max' => 50],
            [['price_list'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 20],
            [['status'], 'default', 'value' => 'active'],
            [
                ['eventdraw_space_diagram_id', 'org_code'],
                'unique',
                'targetAttribute' => ['eventdraw_space_diagram_id', 'org_code'],
                'message' => 'A service order already exists for this room diagram and org.',
                'when' => function ($model) {
                    return $model->eventdraw_space_diagram_id !== null;
                },
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                         => 'ID',
            'eventdraw_event_id'         => 'EventDraw Event ID',
            'eventdraw_space_diagram_id' => 'EventDraw Space Diagram ID',
            'momentus_event_id'          => 'Momentus Event ID',
            'momentus_function_id'       => 'Momentus Function ID',
            'momentus_order_number'      => 'Momentus Order Number',
            'org_code'                   => 'Org Code',
            'price_list'                 => 'Price List',
            'status'                     => 'Status',
            'created_at'                 => 'Created At',
            'updated_at'                 => 'Updated At',
        ];
    }

    // -------------------------------------------------------------------------
    // Space-diagram finders (preferred — one SO per Room Diagram)
    // -------------------------------------------------------------------------

    /**
     * Find the active service order for an EventDraw space diagram.
     */
    public static function findBySpaceDiagram($spaceDiagramId, $orgCode)
    {
        return static::find()
            ->where([
                'eventdraw_space_diagram_id' => (int) $spaceDiagramId,
                'org_code'                   => (string) $orgCode,
                'status'                     => 'active',
            ])
            ->one();
    }

    /**
     * Find mapping row for this space diagram/org regardless of status.
     */
    public static function findAnyBySpaceDiagram($spaceDiagramId, $orgCode)
    {
        return static::find()
            ->where([
                'eventdraw_space_diagram_id' => (int) $spaceDiagramId,
                'org_code'                   => (string) $orgCode,
            ])
            ->one();
    }

    // -------------------------------------------------------------------------
    // Legacy event-level finders (kept for backwards compatibility)
    // -------------------------------------------------------------------------

    /**
     * Find the active service order for an EventDraw event (legacy).
     */
    public static function findByEventDrawEvent($eventdrawEventId, $orgCode)
    {
        return static::find()
            ->where([
                'eventdraw_event_id' => (int) $eventdrawEventId,
                'org_code'           => (string) $orgCode,
                'status'             => 'active',
            ])
            ->one();
    }

    /**
     * Find mapping row for this event/org regardless of status (legacy).
     */
    public static function findAnyByEventDrawEvent($eventdrawEventId, $orgCode)
    {
        return static::find()
            ->where([
                'eventdraw_event_id' => (int) $eventdrawEventId,
                'org_code'           => (string) $orgCode,
            ])
            ->one();
    }
}
