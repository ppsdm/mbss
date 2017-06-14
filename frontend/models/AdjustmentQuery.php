<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Adjustment]].
 *
 * @see Adjustment
 */
class AdjustmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Adjustment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Adjustment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
