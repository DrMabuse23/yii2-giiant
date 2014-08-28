<?php

use yii\helpers\StringHelper;

/**
 * This is the template for generating a CRUD controller class file.
 *
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass.'Search';
}

$pks = $generator->getTableSchema()->primaryKey;
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;


/**
* <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
*/
class <?= $controllerClass ?> extends ActiveController {

    /**
    * @var string
    */
    public $modelClass = '<?= ltrim($generator->modelClass, '\\') ?>';

    /**
    * @var array
    */
    public $serializer = [
        'class'              => 'yii\rest\Serializer',
        'collectionEnvelope' => 'models',
    ];

    /**
    * @var array
    */
    public function behaviors()
    {
        $behaviors                                                     = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        /**$behaviors['authenticator']                                    = [
            'class' => HttpBasicAuth::className(),
        ];*/
        return $behaviors;
    }
}