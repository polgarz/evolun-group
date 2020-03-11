<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%group_coordinator}}`.
 */
class m191010_210436_create_group_coordinator_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%group_coordinator}}', [
            'id'           => $this->primaryKey(),
            'group_id' => $this->integer()->notNull(),
            'user_id'      => $this->integer()->notNull(),
            ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->addForeignKey('fk_group_coordinator_user_id', '{{%group_coordinator}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_group_coordinator_group_id', '{{%group_coordinator}}', 'group_id', '{{%group}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%group_coordinator}}');
    }
}
