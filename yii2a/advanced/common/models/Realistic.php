<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "realistic".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property float|null $startx
 * @property float|null $starty
 * @property float|null $widthx
 * @property float|null $widthy
 * @property string|null $screenshot
 * @property string|null $model
 * @property float|null $posx
 * @property float|null $posy
 * @property float|null $posh
 */
class Realistic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'realistic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['startx', 'starty', 'widthx', 'widthy','posx','posy','posh'], 'number'],
            [['name', 'description', 'screenshot','model'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'startx' => 'Startx',
            'starty' => 'Starty',
            'widthx' => 'Widthx',
            'widthy' => 'Widthy',
            'screenshot' => 'Screenshot',
            'model' => 'Model',
            'posx' => 'Posx',
            'posy' => 'Posy',
            'posh'=> 'Posh',
        ];
    }
}
