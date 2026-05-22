<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "matterport".
 *
 * @property int $id
 * @property string|null $mat
 * @property string|null $name
 * @property float|null $minX
 * @property float|null $maxX
 * @property float|null $minY
 * @property float|null $maxY
 * @property int|null $axis
 * @property string|null $creation_date
 * @property int|null $reg_man
 * @property string|null $updated_date
 * @property float $baseElevation
 * @property int $rotation
 * @property string|null $sid
 * @property string|null $floor
 */
class Matterport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'matterport';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['minX', 'maxX', 'minY', 'maxY', 'baseElevation'], 'number'],
            [['axis', 'reg_man','rotation'], 'integer'],
            [['creation_date', 'updated_date'], 'safe'],
            [['mat', 'name'], 'string', 'max' => 45],
            [['sid', 'floor'], 'string', 'max' => 20],
            [['mat'], 'unique'],
        ];
    }


    public static function getMatterportList()
    {
        $mttps = \common\models\Matterport::find()
            ->select(['id','name'])
            ->orderBy(['name' => SORT_ASC])->all();


        $items = ArrayHelper::map($mttps, 'id', 'name');
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mat' => 'Mat',
            'name' => 'Name',
            'minX' => 'Min X',
            'maxX' => 'Max X',
            'minY' => 'Min Y',
            'maxY' => 'Max Y',
            'axis' => 'Axis',
            'creation_date' => 'Creation Date',
            'reg_man' => 'Reg Man',
            'updated_date' => 'Updated Date',
            'baseElevation' => 'Base Elevation',
            'rotation' => 'Rotation angle',
            'sid' => 'Sid',
            'floor' => 'Floor',
        ];
    }
}
