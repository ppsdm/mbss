<?php

namespace app\models\tao;

use Yii;

/**
 * This is the model class for table "variables_storage".
 *
 * @property integer $variable_id
 * @property string $results_result_id
 * @property string $call_id_test
 * @property string $call_id_item
 * @property string $test
 * @property string $item
 * @property string $value
 * @property string $identifier
 */
class VariablesStorage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'variables_storage';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('taodb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['results_result_id'], 'required'],
            [['value'], 'string'],
            [['results_result_id', 'call_id_test', 'call_id_item', 'test', 'item', 'identifier'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'variable_id' => Yii::t('app', 'Variable ID'),
            'results_result_id' => Yii::t('app', 'Results Result ID'),
            'call_id_test' => Yii::t('app', 'Call Id Test'),
            'call_id_item' => Yii::t('app', 'Call Id Item'),
            'test' => Yii::t('app', 'Test'),
            'item' => Yii::t('app', 'Item'),
            'value' => Yii::t('app', 'Value'),
            'identifier' => Yii::t('app', 'Identifier'),
        ];
    }

    /**
     * @inheritdoc
     * @return VariablesStorageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VariablesStorageQuery(get_called_class());
    }
}
