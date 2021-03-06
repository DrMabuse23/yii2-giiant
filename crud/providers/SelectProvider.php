<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 14.03.14
 * Time: 10:21
 */

namespace schmunk42\giiant\crud\providers;

use schmunk42\giiant\base\Provider;
use yii\base\Object;
use yii\log\Logger;

class SelectProvider extends Provider
{
    public function generateActiveField($attribute)
    {
        $column = $this->generator->getTableSchema()->columns[$attribute];
        switch (true) {
            case (in_array($column->name, $this->columnNames)):
                $this->generator->requires[] = '2amigos/yii2-selectize-widget';
                return <<<EOS
\dosamigos\selectize\Selectize::widget([
    'model' => \$model,
    'attribute' => '$column->name',
    'clientOptions' => [
        'delimiter' => ',',
        'plugins' => ['remove_button'],
        'persist' => false,
        'create' => new \yii\web\JsExpression('function(input){
            return {value: input, text: input};
        }'),
    ]
])
EOS;
            default:
                return null;
        }
    }
} 