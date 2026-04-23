<?php

use yii\db\Migration;

class m260422_000001_add_momentus_fields_to_events extends Migration
{
    public function safeUp()
    {
        $this->addColumn('events', 'momentus_space_diagram_id', $this->integer()->null()->defaultValue(null)->comment('Momentus EventSpaceDiagramID linked to this saved layout'));
        $this->addColumn('events', 'momentus_org_code', $this->string(50)->null()->defaultValue(null)->comment('Momentus OrgCode linked to this saved layout'));
        $this->createIndex('idx_events_momentus_space_diagram', 'events', ['momentus_space_diagram_id', 'momentus_org_code']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_events_momentus_space_diagram', 'events');
        $this->dropColumn('events', 'momentus_org_code');
        $this->dropColumn('events', 'momentus_space_diagram_id');
    }
}
