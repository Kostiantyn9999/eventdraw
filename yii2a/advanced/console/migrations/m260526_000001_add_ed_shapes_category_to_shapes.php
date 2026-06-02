<?php

use yii\db\Migration;

/**
 * ED stencil library name (e.g. "Te Pae - Event Sets") for shapes imported from XML.
 */
class m260526_000001_add_ed_shapes_category_to_shapes extends Migration
{
    public function safeUp()
    {
        $tableSchema = $this->db->getTableSchema('shapes');
        if ($tableSchema === null) {
            echo "    > table shapes does not exist, skipping.\n";
            return;
        }

        if (!isset($tableSchema->columns['ed_shapes_category'])) {
            $this->addColumn(
                'shapes',
                'ed_shapes_category',
                $this->string(255)->null()->comment('ED stencil library / XML heading')
            );
            $this->createIndex('idx_shapes_ed_shapes_category', 'shapes', 'ed_shapes_category');
        }
    }

    public function safeDown()
    {
        $tableSchema = $this->db->getTableSchema('shapes');
        if ($tableSchema !== null && isset($tableSchema->columns['ed_shapes_category'])) {
            $this->dropIndex('idx_shapes_ed_shapes_category', 'shapes');
            $this->dropColumn('shapes', 'ed_shapes_category');
        }
    }
}
