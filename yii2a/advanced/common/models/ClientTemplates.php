<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clienttemplates".
 *
 * @property int $id
 * @property int $clientid
 * @property int $templateid
 */
class ClientTemplates extends \yii\db\ActiveRecord
{
    public $templateName = '';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clienttemplates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clientid', 'templateid'], 'required'],
            [['clientid', 'templateid'], 'integer'],
            ['templateName', 'trim'],
        ];
    }

    public static function setClientTmpl($clientid, $tmpls)
    {
        //clear all user templates
        self::deleteAll(['clientid' => $clientid]);
        //add new records for all selected templates

        if ($tmpls) {
            foreach ($tmpls as $v) {
                $usertmpl = new ClientTemplates();
                $usertmpl->clientid = $clientid;
                $usertmpl->templateid = $v;
                $usertmpl->save();
            }
        }
    }

    public static function getTemplateList($clientid)
    {
        //return list of templates for this client
        $tmpls = \common\models\ClientTemplates::findAll([
            'clientid' => $clientid,
        ]);

        $tmplResult = ArrayHelper::getColumn($tmpls, function ($element) {
            return $element['templateid'];
        });

        return $tmplResult;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clientid' => 'Clientid',
            'templateid' => 'Templateid',
        ];
    }
    public function getTemplateName()
    {
        $Template=Template::findOne(['id' => $this->templateid]);
        if ($Template){
            return $Template->templateName;
        }
        else{
            return null;
        }
    }

}
