<?php

use yii\db\Migration;

/**
 * Stores Momentus ResourceTypeDescription (or similar) next to code/description for shape mapping UI.
 */
class m260514_000001_add_momentus_resource_type_to_shape_momentus_mapping extends Migration
{
    public function safeUp()
    {
        $table = '{{%shape_momentus_mapping}}';
        $schema = $this->db->getTableSchema($table, true);
        if ($schema === null) {
            echo "    > table shape_momentus_mapping not found, skipping.\n";

            return;
        }
        if (!isset($schema->columns['momentus_resource_type'])) {
            $this->addColumn(
                $table,
                'momentus_resource_type',
                $this->string(255)->null()->defaultValue(null)->comment('Momentus resource type description (ResourceTypeDescription)')
            );
        } else {
            echo "    > column momentus_resource_type already exists, skipping.\n";
        }
    }

    public function safeDown()
    {
        $table = '{{%shape_momentus_mapping}}';
        $schema = $this->db->getTableSchema($table, true);
        if ($schema !== null && isset($schema->columns['momentus_resource_type'])) {
            $this->dropColumn($table, 'momentus_resource_type');
        }
    }
}
