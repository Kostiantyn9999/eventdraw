<?php

namespace backend\services\users;

use common\models\UserStencils;
use yii\db\ActiveRecord;

/**
 * Class UserStencilsService
 */
class UserStencilsService
{
    /**
     * @param $id
     * @return array|ActiveRecord[]
     */
    public function findUserStencils($id) {
        return UserStencils::find()->where(['userid' => $id])->all();
    }
}
