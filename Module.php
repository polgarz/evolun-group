<?php

namespace evolun\group;

use yii;

class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'evolun\group\controllers';

    /**
     * A csoport főtípusok listája (pl.: szakmai csoport,
     * fenntartásért felelős csoport, stb..)
     * kulcs -> elnevezés párok
     * @var array
     */
    public $typeList = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!class_exists(Yii::$app->user->identityClass)) {
            throw new \yii\base\InvalidConfigException('Nem található a felhasználó modul, ami elengedhetetlen a csoport modulhoz');
        }
    }
}
