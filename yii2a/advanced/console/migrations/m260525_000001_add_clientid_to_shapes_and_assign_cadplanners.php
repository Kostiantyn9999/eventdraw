<?php

use yii\db\Migration;

/**
 * Adds clientid to shapes and assigns all existing shapes/templates to CadPlanners Pty (EventDraw).
 */
class m260525_000001_add_clientid_to_shapes_and_assign_cadplanners extends Migration
{
    private function resolveCadPlannersClientId()
    {
        $id = (new \yii\db\Query())
            ->select('id')
            ->from('client')
            ->where(['clientName' => 'CadPlanners Pty (EventDraw)'])
            ->scalar();

        if ($id) {
            return (int) $id;
        }

        $id = (new \yii\db\Query())
            ->select('id')
            ->from('client')
            ->where(['like', 'clientName', 'CadPlanners'])
            ->scalar();

        if ($id) {
            return (int) $id;
        }

        echo "    > CadPlanners Pty (EventDraw) client not found — skipping data assignment.\n";
        return null;
    }

    public function safeUp()
    {
        $tableSchema = $this->db->getTableSchema('shapes');
        if ($tableSchema === null) {
            echo "    > table shapes does not exist, skipping.\n";
            return;
        }

        if (!isset($tableSchema->columns['clientid'])) {
            $this->addColumn('shapes', 'clientid', $this->integer()->null()->comment('Owning client'));
            $this->createIndex('idx_shapes_clientid', 'shapes', 'clientid');
        } else {
            echo "    > column clientid already exists in table shapes, skipping add.\n";
        }

        $cadPlannersId = $this->resolveCadPlannersClientId();
        if ($cadPlannersId === null) {
            return;
        }

        $this->update('shapes', ['clientid' => $cadPlannersId]);

        $templateSchema = $this->db->getTableSchema('xmltemplate');
        if ($templateSchema !== null && isset($templateSchema->columns['clientid'])) {
            $this->update('xmltemplate', ['clientid' => $cadPlannersId]);
        }
    }

    public function safeDown()
    {
        $tableSchema = $this->db->getTableSchema('shapes');
        if ($tableSchema !== null && isset($tableSchema->columns['clientid'])) {
            $this->dropIndex('idx_shapes_clientid', 'shapes');
            $this->dropColumn('shapes', 'clientid');
        }
    }
}
