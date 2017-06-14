<?php

namespace app\models\core;

/**
 * This is the ActiveQuery class for [[ScaleRef]].
 *
 * @see ScaleRef
 */
class ScaleRefQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ScaleRef[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ScaleRef|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
