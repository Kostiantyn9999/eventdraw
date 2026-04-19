<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "matterport_assign".
 *
 * @property int $id
 * @property int $matterport_id
 * @property int $layout_id
 */
class MatterportAssign extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'matterport_assign';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['matterport_id', 'layout_id'], 'required'],
            [['matterport_id', 'layout_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'matterport_id' => 'Matterport ID',
            'layout_id' => 'Layout ID',
        ];
    }
}
