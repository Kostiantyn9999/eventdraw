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
class TemplateChangeView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template_change_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id','template_id','user_id','is_seen'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id'=>'Client ID',
            'template_id'=>'Template ID',
            'user_id'=>'User ID',
            'is_seen'=>'IS Seen'
        ];
    }
}
