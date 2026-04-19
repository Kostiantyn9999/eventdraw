<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "client_template_folders".
 *
 * @property int $id
 * @property int $clientid
 * @property int $templateid
 * @property string $folderName
 * @property int $parentid
 */
class ClientTemplateFolders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_template_folders';
    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clientid', 'templateid', 'folderName', 'parentid'], 'required'],
            [['clientid', 'templateid', 'parentid'], 'integer'],
            [['folderName'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clientid' => 'Client',
            'templateid' => 'Template',
            'folderName' => 'Folder Name',
            'parentid' => 'Parent',
        ];
    }
}
