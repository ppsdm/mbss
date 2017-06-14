<?php

namespace app\models\core;

use Yii;

/**
 * This is the model class for table "pcas_range_ref".
 *
 * @property string $grafik
 * @property string $group
 * @property double $dmin
 * @property double $dmax
 * @property double $imin
 * @property double $imax
 * @property double $smin
 * @property double $smax
 * @property double $cmin
 * @property double $cmax
 */
class PcasRangeMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pcas_range_ref';
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
            [['dmin', 'dmax', 'imin', 'imax', 'smin', 'smax', 'cmin', 'cmax'], 'number'],
            [['grafik', 'group'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grafik' => Yii::t('app', 'Grafik'),
            'group' => Yii::t('app', 'Group'),
            'dmin' => Yii::t('app', 'Dmin'),
            'dmax' => Yii::t('app', 'Dmax'),
            'imin' => Yii::t('app', 'Imin'),
            'imax' => Yii::t('app', 'Imax'),
            'smin' => Yii::t('app', 'Smin'),
            'smax' => Yii::t('app', 'Smax'),
            'cmin' => Yii::t('app', 'Cmin'),
            'cmax' => Yii::t('app', 'Cmax'),
        ];
    }

    /**
     * @inheritdoc
     * @return PcasRangeMapQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PcasRangeMapQuery(get_called_class());
    }
}
