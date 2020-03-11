<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%group_user}}`.
 */
class m191010_210455_create_group_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%group_user}}', [
            'id'           => $this->primaryKey(),
            'user_id'      => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->addForeignKey('fk_user_group_user_id', '{{%group_user}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_user_group_group_id', '{{%group_user}}', 'group_id', '{{%group}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%group_user}}');
    }
}
