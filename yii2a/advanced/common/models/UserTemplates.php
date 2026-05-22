<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "usertemplates".
 *
 * @property int $id
 * @property int $userid
 * @property int $templateid
 */
class UserTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usertemplates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'templateid'], 'required'],
            [['userid', 'templateid'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public static function setUserTmpl($userid, $tmpls)
    {
        //clear all user templates
        self::deleteAll(['userid' => $userid]);
        //add new records for all selected templates

        if ($tmpls) {
            foreach ($tmpls as $v) {
                $usertmpl = new UserTemplates();
                $usertmpl->userid = $userid;
                $usertmpl->templateid = $v;
                $usertmpl->save();
            }
        }
    }

    public static function getTemplateList($userid)
    {
        //return list of templates for this user
       $tmpls = \common\models\UserTemplates::findAll([
            'userid' => $userid,
        ]);

        $tmplResult = ArrayHelper::getColumn($tmpls, function ($element) {
            return $element['templateid'];
        });

        return $tmplResult;
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'User',
            'templateid' => 'Template',
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
