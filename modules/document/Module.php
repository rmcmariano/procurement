<?php

namespace app\modules\document;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\document\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = '/main.php';
        parent::init();

        // custom initialization code goes here
    }
}
