<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass;
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>

    <div class="form-group">

        <?php echo "<?php \$this->beginBlock('main'); ?>"; ?>
        <p>
        <?php foreach ($safeAttributes as $attribute) {
            echo "\t\t<?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
        } ?>
        </p>
        <?php echo "<?php \$this->endBlock(); ?>"; ?>

        <?php
        $label = substr(strrchr($model::className(), "\\"), 1);;

        $items = <<<EOS
[
    'label'   => '$label',
    'content' => \$this->blocks['main'],
    'active'  => true,
],
EOS;
        ?>

        <?php foreach ($generator->getModelRelations() as $name => $relation) {

            if (!$relation->multiple) {
                continue;
            }

            echo "<?php \$this->beginBlock('$name'); ?>\n";

            echo "<h3>\n";
            echo "    <?= \\yii\\helpers\\Html::a('$name', ['" . $generator->generateRelationTo($relation) . "/index']) ?>\n";
            echo "</h3>\n";

            # TODO
            echo "<?php echo " . $generator->generateRelationField([$relation, $name]) . " ?>";

            echo "<?php \$this->endBlock(); ?>";

            $items .= <<<EOS
[
    'label'   => '$name',
    'content' => \$this->blocks['$name'],
    'active'  => false,
],
EOS;
        } ?>

        <?=
        "<?=
    \yii\bootstrap\Tabs::widget(
                 [
                     'items' => [ $items ]
                 ]
    );
    ?>";
        ?>

        <hr/>

        <div class="form-group">
            <?= "<?= " ?>Html::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => $model->isNewRecord ?
            'btn btn-primary' : 'btn btn-primary']) ?>
        </div>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>
