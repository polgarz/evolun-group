<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%group_link}}`.
 */
class m191010_210501_create_group_link_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%group_link}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
            'group_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_group_link_group_id', '{{%group_link}}', 'group_id', '{{%group}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%group_link}}');
    }
}
