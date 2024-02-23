<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "buttons".
 *
 * @property int $id
 * @property int $vote_id
 * @property string $text
 */
class Buttons extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'buttons';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vote_id', 'text'], 'required'],
            [['vote_id'], 'default', 'value' => null],
            [['vote_id'], 'integer'],
            [['text'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vote_id' => 'Vote ID',
            'text' => 'Text',
        ];
    }
}
