<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shapemodels".
 *
 * @property int $id
 * @property string $shapeName
 * @property string $modelFileName
 * @property string $materialFileName
 * @property string $scaleMultiplier
 * @property string $scaleX
 * @property string $scaleY
 * @property string $scaleZ
 * @property string $offsetX
 * @property string $offsetY
 * @property string $offsetZ
 * @property string $rotationX
 * @property string $rotationY
 * @property string $rotationZ
 * @property int $flipWidthHeight
 * @property int $scaleHeight
 * @property int $baseElevation
 */
class ShapeModels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shapemodels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shapeName', 'modelFileName'], 'required'],
            [['scaleMultiplier', 'scaleX', 'scaleY', 'scaleZ', 'offsetX', 'offsetY','offsetZ', 'rotationX', 'rotationY', 'rotationZ'], 'number'],
            [['flipWidthHeight', 'scaleHeight', 'baseElevation'], 'integer'],
            [['shapeName'], 'string', 'max' => 100],
            [['modelFileName', 'materialFileName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shapeName' => 'Shape Name',
            'modelFileName' => 'Model File Name',
            'materialFileName' => 'Material File Name',
            'scaleMultiplier' => 'Scale Multiplier',
            'scaleX' => 'Scale X',
            'scaleY' => 'Scale Y',
            'scaleZ' => 'Scale Z',
            'offsetX' => 'Offset X',
            'offsetY' => 'Offset Y',
            'offsetZ' => 'Offset Z',
            'rotationX' => 'Rotation X',
            'rotationY' => 'Rotation Y',
            'rotationZ' => 'Rotation Z',
            'flipWidthHeight' => 'Flip Width Height',
            'scaleHeight' => 'Scale Height',
            'baseElevation' => 'Base Elevation',
        ];
    }
}
