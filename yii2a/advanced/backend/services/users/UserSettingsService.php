<?php

namespace backend\services\users;

use common\models\Usersettings;

/**
 * Class UserSettingsService
 */
class UserSettingsService
{
    /**
     * @param $id
     * @return Usersettings
     */
    public function findUserSettings($id) {
        $userSetting = Usersettings::findOne(['userid' => $id]);

        if ($id != -1) {
            if (!$userSetting) {
                $userSetting = new Usersettings();
                $userSetting->userid = $id;
                $userSetting->meas_unit = 'M';
                $userSetting->copy_dist = 1;
                $userSetting->save(false);
            }
        }

        return $userSetting;
    }
}
