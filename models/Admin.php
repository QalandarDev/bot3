<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "admin".
 *
 * @property int $user_id
 * @property int|null $step
 * @property string|null $text
 * @property string|null $keyboard
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['step'], 'default', 'value' => null],
            [['step'], 'integer'],
            [['text', 'keyboard'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'step' => 'Step',
            'text' => 'Text',
            'keyboard' => 'Keyboard',
        ];
    }
}
