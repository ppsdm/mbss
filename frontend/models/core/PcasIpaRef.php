<?php

namespace app\models\core;

use Yii;

/**
 * This is the model class for table "pcas_ipa_ref".
 *
 * @property string $code
 * @property integer $trait_1
 * @property integer $trait_2
 * @property integer $trait_3
 * @property integer $trait_4
 * @property integer $trait_5
 * @property integer $trait_6
 * @property integer $trait_7
 * @property integer $trait_8
 * @property integer $trait_9
 */
class PcasIpaRef extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pcas_ipa_ref';
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
            [['code'], 'required'],
            [['trait_1', 'trait_2', 'trait_3', 'trait_4', 'trait_5', 'trait_6', 'trait_7', 'trait_8', 'trait_9'], 'integer'],
            [['code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app', 'Code'),
            'trait_1' => Yii::t('app', 'Trait 1'),
            'trait_2' => Yii::t('app', 'Trait 2'),
            'trait_3' => Yii::t('app', 'Trait 3'),
            'trait_4' => Yii::t('app', 'Trait 4'),
            'trait_5' => Yii::t('app', 'Trait 5'),
            'trait_6' => Yii::t('app', 'Trait 6'),
            'trait_7' => Yii::t('app', 'Trait 7'),
            'trait_8' => Yii::t('app', 'Trait 8'),
            'trait_9' => Yii::t('app', 'Trait 9'),
        ];
    }

    /**
     * @inheritdoc
     * @return PcasIpaRefQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PcasIpaRefQuery(get_called_class());
    }
}
