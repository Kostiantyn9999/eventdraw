<?php

namespace common\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "front_template".
 *
 * @property int $id
 * @property int $template_id
 * @property int $client_id
 * @property string $template_name
 * @property string $created_at
 * @property string old_template_name
 *
 */
class FrontTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'templates_changes_bulletin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['template_id','client_id','template_name','created_at'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
            'client_id'=>'Client ID',
            'template_name'=>'Template Name',
            'old_template_name'=>'Old Template Name',
            'show_to_user'=>'Show to User'
        ];
    }
}
