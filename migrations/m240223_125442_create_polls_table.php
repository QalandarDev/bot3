<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%polls}}`.
 */
class m240223_125442_create_polls_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%polls}}', [
            'id' => $this->primaryKey(),
            'button_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'vote_id' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%polls}}');
    }
}
