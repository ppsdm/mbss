<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tao_result".
 *
 * @property int $id
 * @property string|null $delivery_id
 * @property string|null $result_id
 * @property string|null $email
 * @property int|null $score
 * @property string|null $status
 */
class TaoResult extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tao_result';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('portaldb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['score'], 'integer'],
            [['delivery_id', 'result_id', 'email', 'status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'delivery_id' => Yii::t('app', 'Delivery ID'),
            'result_id' => Yii::t('app', 'Result ID'),
            'email' => Yii::t('app', 'Email'),
            'score' => Yii::t('app', 'Score'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TaoResultQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaoResultQuery(get_called_class());
    }
}
