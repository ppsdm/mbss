<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "adjustment".
 *
 * @property string $test_id
 * @property string $key
 * @property string $value
 */
class Adjustment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adjustment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id', 'key'], 'required'],
            [['test_id', 'key', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'test_id' => Yii::t('app', 'Test ID'),
            'key' => Yii::t('app', 'Key'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * @inheritdoc
     * @return AdjustmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AdjustmentQuery(get_called_class());
    }
}
