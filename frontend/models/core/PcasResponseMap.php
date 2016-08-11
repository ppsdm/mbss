<?php

namespace app\models\core;

use Yii;

/**
 * This is the model class for table "pcas_response_map".
 *
 * @property string $item
 * @property string $choice_1
 * @property string $choice_2
 */
class PcasResponseMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pcas_response_map';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('coredb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item'], 'required'],
            [['item', 'choice_1', 'choice_2'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item' => Yii::t('app', 'Item'),
            'choice_1' => Yii::t('app', 'Choice 1'),
            'choice_2' => Yii::t('app', 'Choice 2'),
        ];
    }

    /**
     * @inheritdoc
     * @return PcasResponseMapQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PcasResponseMapQuery(get_called_class());
    }
}
