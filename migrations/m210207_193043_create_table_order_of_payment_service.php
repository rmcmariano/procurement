<?php

use yii\db\Migration;

class m210207_193043_create_table_order_of_payment_service extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%order_of_payment_service}}',
            [
                'id' => $this->primaryKey(),
                'service' => $this->integer()->notNull(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'order_of_payment_service_FK',
            '{{%order_of_payment_service}}',
            ['id'],
            '{{%order_of_payment}}',
            ['id'],
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'order_of_payment_service_FK_1',
            '{{%order_of_payment_service}}',
            ['service'],
            '{{%service}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%order_of_payment_service}}');
    }
}
