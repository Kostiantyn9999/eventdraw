<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "realistic_templates".
 *
 * @property int $id
 * @property int $realistic_id
 * @property int $template_id
 */
class RealisticTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'realistic_templates';
    }


    public function getRealisticName()
    {
        $Realistic=\common\models\Realistic::findOne(['id' => $this->realistic_id ]);
        if ($Realistic){
            return $Realistic->name;
        }
        else{
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['realistic_id', 'template_id'], 'required'],
            [['realistic_id', 'template_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realistic_id' => 'Realistic ID',
            'template_id' => 'Template ID',
        ];
    }
}
