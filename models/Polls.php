<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "polls".
 *
 * @property int $id
 * @property int $button_id
 * @property int $user_id
 * @property int $vote_id
 */
class Polls extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'polls';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['button_id', 'user_id', 'vote_id'], 'required'],
            [['button_id', 'user_id', 'vote_id'], 'default', 'value' => null],
            [['button_id', 'user_id', 'vote_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'button_id' => 'Button ID',
            'user_id' => 'User ID',
            'vote_id' => 'Vote ID',
        ];
    }
}
