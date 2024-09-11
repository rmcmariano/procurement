<?php

use yii\db\Migration;

class m210207_193055_create_table_service extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%service}}',
            [
                'id' => $this->primaryKey(),
                'fund' => $this->integer()->notNull(),
                'service_code' => $this->string(100)->notNull(),
                'uacs' => $this->string(100)->notNull(),
                'subject_code' => $this->string(100)->notNull(),
                'uacs_description' => $this->string(100)->notNull(),
                'title' => $this->string(100)->notNull(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'NewTable_FK',
            '{{%service}}',
            ['fund'],
            '{{%fund_cluster}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%service}}');
    }
}
