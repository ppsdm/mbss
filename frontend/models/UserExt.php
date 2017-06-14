<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_ext".
 *
 * @property string $username
 * @property string $firstname
 * @property string $lastname
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $jabatan_dilamar
 * @property string $pendidikan_terakhir
 * @property string $tujuan_pemeriksaan
 * @property string $tempat
 * @property string $tanggal_test
 */
class UserExt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ext';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['tanggal_lahir', 'tanggal_test'], 'safe'],
            [['tujuan_pemeriksaan'], 'string'],
            [['username', 'firstname', 'lastname', 'tempat_lahir', 'jabatan_dilamar', 'pendidikan_terakhir', 'tempat'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'firstname' => Yii::t('app', 'Firstname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'tempat_lahir' => Yii::t('app', 'Tempat Lahir'),
            'tanggal_lahir' => Yii::t('app', 'Tanggal Lahir'),
            'jabatan_dilamar' => Yii::t('app', 'Jabatan Dilamar'),
            'pendidikan_terakhir' => Yii::t('app', 'Pendidikan Terakhir'),
            'tujuan_pemeriksaan' => Yii::t('app', 'Tujuan Pemeriksaan'),
            'tempat' => Yii::t('app', 'Tempat'),
            'tanggal_test' => Yii::t('app', 'Tanggal Test'),
        ];
    }

    /**
     * @inheritdoc
     * @return UserExtQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserExtQuery(get_called_class());
    }
}
