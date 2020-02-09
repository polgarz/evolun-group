<?php

namespace evolun\group\models;

use Yii;

/**
 * This is the model class for table "group_link".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $group_id
 *
 * @property Group $group
 */
class GroupLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id'], 'integer'],
            [['url'], 'url'],
            [['name', 'url'], 'string', 'max' => 255],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('group', 'Name'),
            'url' => Yii::t('group', 'Url'),
            'group_id' => Yii::t('group', 'Group'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }
}
