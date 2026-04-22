<?php

use yii\db\Migration;

class m260420_000001_add_momentus_org_code_to_client extends Migration
{
    public function safeUp()
    {
        $tableSchema = $this->db->getTableSchema('client');
        if ($tableSchema === null || !isset($tableSchema->columns['momentusOrgCode'])) {
            $this->addColumn('client', 'momentusOrgCode', $this->string(50)->null()->defaultValue(null)->comment('Momentus Organisation Code for this client'));
        } else {
            echo "    > column momentusOrgCode already exists in table client, skipping.\n";
        }
    }

    public function safeDown()
    {
        $tableSchema = $this->db->getTableSchema('client');
        if ($tableSchema !== null && isset($tableSchema->columns['momentusOrgCode'])) {
            $this->dropColumn('client', 'momentusOrgCode');
        }
    }
}
