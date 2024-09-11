<?php

use yii\db\Migration;

class m210207_192704_create_table_fund_cluster extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%fund_cluster}}',
            [
                'id' => $this->primaryKey(),
                'fund_code' => $this->string(100)->notNull(),
                'fund_name' => $this->string(100)->notNull(),
                'description' => $this->string(100),
            ],
            $tableOptions
        );

        $this->createIndex('fund_cluster_un', '{{%fund_cluster}}', ['fund_code'], true);
    }

    public function down()
    {
        $this->dropTable('{{%fund_cluster}}');
    }
}
