<?php

namespace common\models;

use FontLib\Table\Type\name;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\data\SqlDataProvider;


/**
 * This is the model class for table "xmltemplate".
 *
 * @property int $id
 * @property string $templateName
 * @property string $xmlCode
 * @property int $templateActive
 * @property integer $created_at
 * @property integer $updated_at
 * @property int $templateDefault
 * @property int $clientid
 * @property int $shadow
 * @property string $image
 * @property string $templateSize
 * @property int $matterportid
 * @property int $created_by
 */
class Emailtemplate extends \yii\db\ActiveRecord
{
    public $imageFile;
    public $subtemplate;
    public $template_version;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_templates';
    }


    public function rules()
    {
        return [
            [['template_name', 'email_subject','content','template_type'], 'required'],
            [['bulletin_heading'],'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_name' => 'Template Name',
            'email_subject' => 'Email Subject',
            'template_type'=>'Template Type',
            'content' => 'Message',
            'bulletin_heading'=>'Bulletin Heading'
        ];
    }
}
