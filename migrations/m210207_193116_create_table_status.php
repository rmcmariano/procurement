<?php

use yii\db\Migration;

class m210207_193116_create_table_status extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%status}}',
            [
                'id' => $this->primaryKey(),
                'code' => $this->string(100)->notNull(),
                'description' => $this->string(100)->notNull(),
            ],
            $tableOptions
        );

        $this->createIndex('status_un', '{{%status}}', ['code'], true);
    }

    public function down()
    {
        $this->dropTable('{{%status}}');
    }
}
