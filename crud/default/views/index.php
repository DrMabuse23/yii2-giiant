<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var <?= ltrim($generator->searchModelClass, '\\') ?> $searchModel
 */

$this->title = '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

	<?= "<?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>

	<div class="clearfix">
        <p class="pull-left">
            <?= "<?= " ?>Html::a('New', ['create'], ['class' => 'btn btn-info']) ?>
        </p>
        <p class="pull-right">
            <?php foreach($generator->getModelRelations() AS $relation): ?>
                <?php
                $controller = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $generator->pathPrefix.StringHelper::basename($relation->modelClass)));
                ?>
                <?= "<?= " ?>Html::a('<?= Inflector::camel2words(StringHelper::basename($relation->modelClass)) ?>', ['<?= $controller ?>/index'], ['class' => 'btn btn-default']) ?>
            <?php endforeach; ?>
        </p>
    </div>

<?php if ($generator->indexWidgetType === 'grid'): ?>
	<?= "<?php " ?>echo GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
<?php
$count = 0;
foreach ($generator->getTableSchema()->columns as $column) {
	$format = $generator->generateColumnFormat($column);
	if (++$count < 6) {
		echo "\t\t\t'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
	} else {
		echo "\t\t\t// '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
	}
}
?>

			['class' => '<?= $generator->actionButtonClass ?>'],
		],
	]); ?>
<?php else: ?>
	<?= "<?php " ?>echo ListView::widget([
		'dataProvider' => $dataProvider,
		'itemOptions' => ['class' => 'item'],
		'itemView' => function ($model, $key, $index, $widget) {
			return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
		},
	]); ?>
<?php endif; ?>

</div>
