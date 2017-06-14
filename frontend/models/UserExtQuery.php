<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[UserExt]].
 *
 * @see UserExt
 */
class UserExtQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserExt[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserExt|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
