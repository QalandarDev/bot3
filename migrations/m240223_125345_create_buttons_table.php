<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%buttons}}`.
 */
class m240223_125345_create_buttons_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%buttons}}', [
            'id' => $this->primaryKey(),
            'vote_id' => $this->integer()->notNull(),
            'text' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%buttons}}');
    }
}
