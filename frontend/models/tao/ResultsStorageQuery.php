<?php

namespace app\models\tao;

/**
 * This is the ActiveQuery class for [[ResultsStorage]].
 *
 * @see ResultsStorage
 */
class ResultsStorageQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ResultsStorage[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ResultsStorage|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
