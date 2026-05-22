<?php

namespace backend\services\users;

use common\models\UserTemplates;
use yii\db\ActiveRecord;

/**
 * Class UserTemplatesService
 */
class UserTemplatesService
{
    /**
     * @param $id
     * @return array|ActiveRecord[]
     */
    public function findUserTemplates($id) {
        return UserTemplates::find()->where(['userid' => $id])->all();
    }
}
