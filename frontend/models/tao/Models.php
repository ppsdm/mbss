<?php

namespace app\models\tao;

use Yii;

/**
 * This is the model class for table "models".
 *
 * @property integer $modelid
 * @property string $modeluri
 */
class Models extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'models';
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
            [['modeluri'], 'required'],
            [['modeluri'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'modelid' => Yii::t('app', 'Modelid'),
            'modeluri' => Yii::t('app', 'Modeluri'),
        ];
    }

    /**
     * @inheritdoc
     * @return ModelsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ModelsQuery(get_called_class());
    }
}
