<?php

use yii\db\Migration;

class m260420_000001_add_momentus_org_code_to_client extends Migration
{
    public function safeUp()
    {
        $this->addColumn('client', 'momentusOrgCode', $this->string(50)->null()->defaultValue(null)->comment('Momentus Organisation Code for this client'));
    }

    public function safeDown()
    {
        $this->dropColumn('client', 'momentusOrgCode');
    }
}
