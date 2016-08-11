<?php

namespace app\models\core;

use Yii;

/**
 * This is the model class for table "pcas_grafik_ref".
 *
 * @property string $grafik
 * @property string $di
 * @property string $ds
 * @property string $dc
 * @property string $is
 * @property string $ic
 * @property string $sc
 * @property integer $d-pos
 * @property integer $i-pos
 * @property integer $s-pos
 * @property integer $c-pos
 */
class PcasGrafikRef extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pcas_grafik_ref';
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
            [['grafik'], 'required'],
            [['d-pos', 'i-pos', 's-pos', 'c-pos'], 'integer'],
            [['grafik', 'di', 'ds', 'dc', 'is', 'ic', 'sc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grafik' => Yii::t('app', 'Grafik'),
            'di' => Yii::t('app', 'Di'),
            'ds' => Yii::t('app', 'Ds'),
            'dc' => Yii::t('app', 'Dc'),
            'is' => Yii::t('app', 'Is'),
            'ic' => Yii::t('app', 'Ic'),
            'sc' => Yii::t('app', 'Sc'),
            'd-pos' => Yii::t('app', 'D Pos'),
            'i-pos' => Yii::t('app', 'I Pos'),
            's-pos' => Yii::t('app', 'S Pos'),
            'c-pos' => Yii::t('app', 'C Pos'),
        ];
    }

    /**
     * @inheritdoc
     * @return PcasGrafikRefQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PcasGrafikRefQuery(get_called_class());
    }
}
