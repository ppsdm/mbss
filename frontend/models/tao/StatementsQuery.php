<?php

namespace app\models\tao;

/**
 * This is the ActiveQuery class for [[Statements]].
 *
 * @see Statements
 */
class StatementsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Statements[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Statements|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
