<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "new_user_broadcast_email_templates".
 *
 * @property int $id
 * @property string|null $heading
 * @property string|null $template_image
 * @property string|null $link_heading
 * @property string|null $link
 * @property string|null $button_link
 * @property int $hours
 * @property int $order
 */
class NewUserBroadcastEmailTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'new_user_broadcast_email_templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['template_image', 'hours', 'order'], 'required'],
            [['template_image'], 'string'],
            [['heading', 'link_heading', 'link', 'button_link'], 'string', 'max' => 255],
            [['hours', 'order'],'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'heading' => 'Heading',
            'template_image' => 'Template Image',
            'link_heading' => 'Link Heading',
            'link' => 'Link',
            'button_link' => 'Button Link',
            'hours' => 'Hours',
            'order' => 'Order',
        ];
    }
}
