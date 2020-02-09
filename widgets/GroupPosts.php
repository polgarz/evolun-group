<?php
namespace evolun\group\widgets;

use Yii;
use yii\data\ActiveDataProvider;
use evolun\group\models\{GroupPost, GroupUser};

/**
 * A felhasználó csoportjai, és azok frissítései (dashboard widget)
 */
class GroupPosts extends \yii\base\Widget
{
    /**
     * @var string
     */
    public $groupModuleId = 'group';

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
        Yii::$app->getModule($this->groupModuleId);

        if (!$this->user) {
            $this->user = Yii::$app->user->identity;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!Yii::$app->user->can('showGroups')) {
            return null;
        }

        $postsDataProvider = new ActiveDataProvider([
            'query' => GroupPost::find()
                ->joinWith('group.groupUsers', false)
                ->where(['user_id' => $this->user->id])
                ->limit(2),
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);

        $hasGroups = GroupUser::find()->where(['user_id' => $this->user->id])->exists();

        return $this->render('group-posts', [
            'user' => $this->user,
            'postsDataProvider' => $postsDataProvider,
            'hasGroups' => $hasGroups,
            'groupModuleId' => $this->groupModuleId,
        ]);
    }
}
