<?php

use yii\db\Migration;

/**
 * Changes the momentus_service_order mapping key from eventdraw_event_id to
 * eventdraw_space_diagram_id so that each Room Diagram can have its own
 * independent service order rather than sharing one per Event.
 *
 * - Adds eventdraw_space_diagram_id (nullable int) column.
 * - Makes eventdraw_event_id nullable (kept for reference / backwards compat).
 * - Drops the old unique index on (eventdraw_event_id, org_code).
 * - Adds a new unique index on (eventdraw_space_diagram_id, org_code).
 * - Adds a non-unique index on eventdraw_space_diagram_id for fast lookups.
 */
class m260421_000001_add_space_diagram_to_service_order extends Migration
{
    public function init()
    {
        parent::init();
        $this->db = \Yii::$app->db;
    }

    public function up()
    {
        // Add the new space-diagram column (nullable so existing rows are unaffected)
        $this->addColumn(
            '{{%momentus_service_order}}',
            'eventdraw_space_diagram_id',
            $this->integer()->null()->after('eventdraw_event_id')
        );

        // Make eventdraw_event_id nullable (existing rows keep their value)
        $this->alterColumn(
            '{{%momentus_service_order}}',
            'eventdraw_event_id',
            $this->integer()->null()
        );

        // Drop the old unique constraint on (eventdraw_event_id, org_code)
        $this->dropIndex(
            'uq-momentus_service_order-eventdraw_event',
            '{{%momentus_service_order}}'
        );

        // New unique constraint: one active service order per space diagram + org
        $this->createIndex(
            'uq-momentus_service_order-space_diagram',
            '{{%momentus_service_order}}',
            ['eventdraw_space_diagram_id', 'org_code'],
            true
        );

        // Non-unique index for fast lookups by space diagram id alone
        $this->createIndex(
            'idx-momentus_service_order-space_diagram_id',
            '{{%momentus_service_order}}',
            'eventdraw_space_diagram_id'
        );
    }

    public function down()
    {
        $this->dropIndex('idx-momentus_service_order-space_diagram_id', '{{%momentus_service_order}}');
        $this->dropIndex('uq-momentus_service_order-space_diagram', '{{%momentus_service_order}}');

        // Restore old unique index (may fail if data is inconsistent after up() was used)
        $this->createIndex(
            'uq-momentus_service_order-eventdraw_event',
            '{{%momentus_service_order}}',
            ['eventdraw_event_id', 'org_code'],
            true
        );

        // Restore eventdraw_event_id as not-null
        $this->alterColumn(
            '{{%momentus_service_order}}',
            'eventdraw_event_id',
            $this->integer()->notNull()
        );

        $this->dropColumn('{{%momentus_service_order}}', 'eventdraw_space_diagram_id');
    }
}
