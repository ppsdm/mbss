<?php

namespace app\models\core;

/**
 * This is the ActiveQuery class for [[PcasRangeMap]].
 *
 * @see PcasRangeMap
 */
class PcasRangeMapQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PcasRangeMap[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PcasRangeMap|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
