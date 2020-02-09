<?php

namespace evolun\group\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * A felhasználók kereséséhez szükséges model (grouppal kiegészítve)
 */
class UserSearch extends Model
{
    /**
     * Kereső kifejezés
     * @var string
     */
    public $searchString;

    /**
     * Csoport azonositó (numerikus id, vagy other)
     * @var integer|string
     */
    public $group;

    /**
     * Az egyéb csoportok azonosítója
     */
    const GROUP_OTHER = 'other';

    /**
     * A csoportok küszöbe: az ez alatti felhasználóval rendelkező
     * csoportok az egyébbe kerülnek
     */
    const GROUP_THRESHOLD = 10;

    /**
     * Belső cache: csoportok listája
     * @var array
     */
    private $_groupList;

    /**
     * Belső cache: az egyéb csoportok listája
     * @var array
     */
    private $_otherGroups;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['searchString', 'group'], 'safe'],
            [['group'], 'in', 'range' => array_keys($this->groupList)]
        ];
    }

    /**
     * Keresés
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $userModel = Yii::createObject(Yii::$app->user->identityClass);
        $query = $userModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 50,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->orFilterWhere(['like', 'user.name', $this->searchString])
            ->orFilterWhere(['like', 'user.nickname', $this->searchString])
            ->orFilterWhere(['like', 'user.email', $this->searchString])
            ->orFilterWhere(['like', 'user.skype', $this->searchString])
            ->orFilterWhere(['like', 'user.phone', $this->searchString]);

        if ($this->group) {
            $query->leftJoin(GroupUser::tableName(), 'user_id = user.id');

            if ($this->group != self::GROUP_OTHER) {
                $query->andFilterWhere(['group_id' => $this->group]);
            } else {
                $query->andFilterWhere(['in', 'group_id', array_keys($this->otherGroups)]);
            }
        }

        return $dataProvider;
    }

    /**
     * A csoportok listája, bizonyos küszöbbel, a küszöbb alatt groupolva egyébként
     * @return array
     */
    public function getGroupList()
    {
        if (isset($this->_groupList)) {
            return $this->_groupList;
        }

        $groups = [];

        // eloszor lekerjuk azokat, amikben tobben vannak, mint a kuszob
        $groups = Group::find()
            ->select([Group::tableName() . '.name', Group::tableName() . '.id'])
            ->joinWith('groupUsers', false)
            ->groupBy('group_id')
            ->having(['>=', 'COUNT(*)', self::GROUP_THRESHOLD])
            ->asArray()
            ->indexBy('id')
            ->column();

        // majd hozzaadjuk az egyebet is
        $groups[self::GROUP_OTHER] = 'Egyéb';

        $this->_groupList = $groups;

        return $groups;
    }

    /**
     * Visszaadja azokat a csoportokat, amik nem érték el a küszöböt
     * @return array
     */
    public function getOtherGroups()
    {
        if (isset($this->_otherGroups)) {
            return $this->_otherGroups;
        }

        $this->_otherGroups = Group::find()
            ->select([Group::tableName() . '.name', Group::tableName() . '.id'])
            ->joinWith('groupUsers', false)
            ->groupBy('group_id')
            ->having(['<=', 'COUNT(*)', self::GROUP_THRESHOLD])
            ->asArray()
            ->indexBy('id')
            ->column();

        return $this->_otherGroups;
    }
}
