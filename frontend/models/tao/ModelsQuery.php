<?php

namespace app\models\tao;

/**
 * This is the ActiveQuery class for [[Models]].
 *
 * @see Models
 */
class ModelsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Models[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Models|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
