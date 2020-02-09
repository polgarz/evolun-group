<?php
namespace evolun\group\widgets;

use Yii;
use evolun\user\widgets\UserWidgetInterface;
use evolun\group\models\Group;

/**
 * Egy adott felhasználó csoportjai
 */
class UserGroup extends \yii\base\Widget implements UserWidgetInterface
{
    /**
     * A felhasználó, akinek az adatait meg kell jeleníteni. Ha nincs megadva,
     * akkor az aktuális felhasználó adatait használja
     * @var User
     */
    public $user = false;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (!$this->user) {
            $this->user = Yii::$app->user->identity;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!Yii::$app->user->can('showGroups')) {
            return null;
        }

        $groups = Group::find()
            ->joinWith('groupUsers', false)
            ->where(['user_id' => $this->user->id])
            ->all();

        return $this->render('user-group', [
            'user' => $this->getUser(),
            'groups' => $groups,
            ]);
    }
}
