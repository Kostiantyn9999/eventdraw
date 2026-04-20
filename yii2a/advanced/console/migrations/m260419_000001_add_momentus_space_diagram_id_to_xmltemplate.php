<?php

use yii\db\Migration;

class m260419_000001_add_momentus_space_diagram_id_to_xmltemplate extends Migration
{
    public function safeUp()
    {
        $this->addColumn('xmltemplate', 'momentusEventSpaceDiagramId', $this->integer()->null()->defaultValue(null)->comment('Default Momentus EventSpaceDiagram ID for this template'));
    }

    public function safeDown()
    {
        $this->dropColumn('xmltemplate', 'momentusEventSpaceDiagramId');
    }
}
