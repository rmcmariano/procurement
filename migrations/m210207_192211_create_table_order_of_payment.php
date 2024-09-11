<?php

use yii\db\Migration;

class m210207_192211_create_table_order_of_payment extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%order_of_payment}}',
            [
                'id' => $this->primaryKey(),
                'transaction_number' => $this->string(100)->notNull(),
                'type' => $this->integer()->notNull(),
                'customer_id' => $this->integer()->notNull(),
                'division_id' => $this->integer()->notNull(),
                'fund_cluster' => $this->integer()->notNull(),
                'amount' => $this->double()->notNull(),
                'balance' => $this->double()->notNull(),
                'status' => $this->integer()->notNull(),
                'created_timestamp' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'service_id' => $this->integer()->notNull(),
            ],
            $tableOptions
        );

        $this->createIndex('service_un', '{{%order_of_payment}}', ['service_id'], true);
        $this->createIndex('transaction_number_un', '{{%order_of_payment}}', ['transaction_number'], true);

        $this->addForeignKey(
            'fund_cluster_FK',
            '{{%order_of_payment}}',
            ['fund_cluster'],
            '{{%fund_cluster}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'status_FK',
            '{{%order_of_payment}}',
            ['status'],
            '{{%status}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
        $this->addForeignKey(
            'type_FK',
            '{{%order_of_payment}}',
            ['type'],
            '{{%type}}',
            ['id'],
            'RESTRICT',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%order_of_payment}}');
    }
}
