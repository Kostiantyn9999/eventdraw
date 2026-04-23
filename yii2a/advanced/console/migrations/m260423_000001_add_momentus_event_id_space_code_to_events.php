<?php

use yii\db\Migration;

class m260423_000001_add_momentus_event_id_space_code_to_events extends Migration
{
    public function safeUp()
    {
        $this->addColumn('events', 'momentus_event_id', $this->integer()->null()->defaultValue(null)->comment('Momentus EventID linked to this saved layout'));
        $this->addColumn('events', 'momentus_space_code', $this->string(50)->null()->defaultValue(null)->comment('Momentus SpaceCode linked to this saved layout'));
    }

    public function safeDown()
    {
        $this->dropColumn('events', 'momentus_space_code');
        $this->dropColumn('events', 'momentus_event_id');
    }
}
