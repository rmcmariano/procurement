<?php

use yii\db\Migration;

class m210207_193031_create_table_order_of_payment_logs extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%order_of_payment_logs}}',
            [
                'id' => $this->primaryKey(),
                'order_of_payment' => $this->integer()->notNull(),
                'type' => $this->string()->notNull(),
                'timestamp' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'user' => $this->integer()->notNull(),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'order_of_payment_logs_FK',
            '{{%order_of_payment_logs}}',
            ['order_of_payment'],
            '{{%order_of_payment}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%order_of_payment_logs}}');
    }
}
