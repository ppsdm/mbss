<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TaoResult]].
 *
 * @see TaoResult
 */
class TaoResultQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TaoResult[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TaoResult|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
