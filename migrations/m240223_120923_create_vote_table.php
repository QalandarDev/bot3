<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vote}}`.
 */
class m240223_120923_create_vote_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vote}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%vote}}');
    }
}
