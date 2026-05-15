<?php

namespace common\models;

use Yii;

/**
 * Maps EventDraw shapes to Momentus resources per org_code.
 *
 * @property int $id
 * @property int $shape_id
 * @property string $org_code
 * @property string $momentus_resource_code
 * @property string|null $momentus_resource_description
 * @property string|null $momentus_resource_type
 * @property int $sequence
 * @property string $created_at
 * @property string $updated_at
 *
 * @property MomentusShape $shape
 */
class ShapeMomentusMapping extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%shape_momentus_mapping}}';
    }

    public function rules()
    {
        return [
            [['shape_id', 'org_code'], 'required'],
            [['shape_id', 'sequence'], 'integer'],
            [['org_code'], 'string', 'max' => 50],
            [['momentus_resource_code'], 'string', 'max' => 100],
            [['momentus_resource_description', 'momentus_resource_type'], 'string', 'max' => 255],
            [['sequence'], 'default', 'value' => 1],
            [
                ['shape_id', 'org_code', 'momentus_resource_code'],
                'unique',
                'targetAttribute' => ['shape_id', 'org_code', 'momentus_resource_code'],
                'message' => 'This shape+org+resource combination already exists.',
                'when' => function ($model) {
                    return $model->momentus_resource_code !== null && $model->momentus_resource_code !== '';
                },
            ],
            [['shape_id'], 'exist', 'targetClass' => MomentusShape::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shape_id' => 'Shape',
            'org_code' => 'Org Code',
            'momentus_resource_code' => 'Resource Code',
            'momentus_resource_description' => 'Resource Description',
            'momentus_resource_type' => 'Resource Type',
            'sequence' => 'Sequence',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShape()
    {
        return $this->hasOne(MomentusShape::class, ['id' => 'shape_id']);
    }

    /**
     * Returns the highest-priority mapping (lowest sequence) for a given shape + org.
     */
    public static function findPrimaryMapping($shapeId, $orgCode)
    {
        return static::find()
            ->where(['shape_id' => $shapeId, 'org_code' => $orgCode])
            ->orderBy(['sequence' => SORT_ASC])
            ->limit(1)
            ->one();
    }
}
