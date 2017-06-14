<?php

namespace app\models\core;

/**
 * This is the ActiveQuery class for [[PcasResponseMap]].
 *
 * @see PcasResponseMap
 */
class PcasResponseMapQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PcasResponseMap[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PcasResponseMap|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
