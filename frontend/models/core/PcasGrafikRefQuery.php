<?php

namespace app\models\core;

/**
 * This is the ActiveQuery class for [[PcasGrafikRef]].
 *
 * @see PcasGrafikRef
 */
class PcasGrafikRefQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PcasGrafikRef[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PcasGrafikRef|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
