<?php

namespace app\models\core;

/**
 * This is the ActiveQuery class for [[PcasIpaRef]].
 *
 * @see PcasIpaRef
 */
class PcasIpaRefQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PcasIpaRef[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PcasIpaRef|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
