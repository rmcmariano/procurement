<?php

use yii\db\Migration;

class m210207_192640_create_table_collection_type extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%collection_type}}',
            [
                'id' => $this->primaryKey(),
                'fund' => $this->integer()->notNull(),
                'collection_code' => $this->string(100)->notNull(),
                'uacs' => $this->string(100)->notNull(),
                'subject_code' => $this->string(100)->notNull(),
                'uacs_description' => $this->string(100)->notNull(),
                'collection_name' => $this->string(100)->notNull(),
            ],
            $tableOptions
        );

        $this->createIndex('collection_type_un', '{{%collection_type}}', ['collection_code'], true);

        $this->addForeignKey(
            'collection_type_FK',
            '{{%collection_type}}',
            ['fund'],
            '{{%fund_cluster}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%collection_type}}');
    }
}
