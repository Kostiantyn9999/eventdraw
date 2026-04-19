<?php

use yii\db\Migration;

/**
 * Tracks the mapping between EventDraw saved layouts and Momentus service orders.
 * One service order per EventDraw EventID. Supports updating an existing order
 * when the floor plan changes.
 */
class m260318_000007_create_momentus_service_order_table extends Migration
{
    public function init()
    {
        parent::init();
        $this->db = \Yii::$app->db;
    }

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%momentus_service_order}}', [
            'id' => $this->primaryKey(),
            'eventdraw_event_id' => $this->integer()->notNull(),
            'momentus_event_id' => $this->integer()->notNull(),
            'momentus_function_id' => $this->integer()->null(),
            'momentus_order_number' => $this->integer()->notNull(),
            'org_code' => $this->string(50)->notNull(),
            'price_list' => $this->string(100)->null(),
            'status' => $this->string(20)->notNull()->defaultValue('active'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'uq-momentus_service_order-eventdraw_event',
            '{{%momentus_service_order}}',
            ['eventdraw_event_id', 'org_code'],
            true
        );

        $this->createIndex(
            'idx-momentus_service_order-order_number',
            '{{%momentus_service_order}}',
            ['momentus_order_number', 'org_code']
        );

        $this->createIndex(
            'idx-momentus_service_order-momentus_event',
            '{{%momentus_service_order}}',
            'momentus_event_id'
        );
    }

    public function down()
    {
        $this->dropTable('{{%momentus_service_order}}');
    }
}
