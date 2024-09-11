<?php

use yii\db\Migration;

class m210207_193012_create_table_order_of_payment_collection extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%order_of_payment_collection}}',
            [
                'id' => $this->primaryKey(),
                'collection' => $this->integer()->notNull(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'order_of_payment_collection_FK',
            '{{%order_of_payment_collection}}',
            ['id'],
            '{{%order_of_payment}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'order_of_payment_collection_FK_1',
            '{{%order_of_payment_collection}}',
            ['collection'],
            '{{%collection_type}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%order_of_payment_collection}}');
    }
}
