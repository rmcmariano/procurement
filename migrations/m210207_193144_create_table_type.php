<?php

use yii\db\Migration;

class m210207_193144_create_table_type extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%type}}',
            [
                'id' => $this->primaryKey(),
                'type_code' => $this->string(100)->notNull(),
                'description' => $this->string(100)->notNull(),
            ],
            $tableOptions
        );

        $this->createIndex('type_un', '{{%type}}', ['type_code'], true);
    }

    public function down()
    {
        $this->dropTable('{{%type}}');
    }
}
