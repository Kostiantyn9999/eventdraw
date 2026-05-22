<?php

/**
 * Class UserSettings
 */
class UserSettings
{
    public function findUserSettings() {

        $userSetting = \common\models\Usersettings::findOne(['userid' => $id]);
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
