<?php
/**
 * @link http://www.phundament.com
 * @copyright Copyright (c) 2014 herzog kommunikation GmbH
 * @license http://www.phundament.com/license/
 */

namespace schmunk42\giiant\model;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 * @since 0.1
 */
class Generator extends \yii\gii\generators\model\Generator
{
    public $generateModelClass = false;
    public $tablePrefix = null;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Giiant Model';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class and base class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['generateModelClass'], 'boolean'],
                [['tablePrefix'], 'safe'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'generateModelClass' => 'Generate Model Class',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'generateModelClass' => 'This indicates whether the generator should generate the model class, this should usually be done only once. The model-base class is always generated.',
                'tablePrefix'        => 'Custom table prefix, eg <code>app_</code>.<br/><b>Note!</b> overrides <code>yii\db\Connection</code> prefix!',

            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'model-extended.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files     = [];
        $relations = $this->generateRelations();
        $db        = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            $className   = $this->generateClassName(str_replace($this->tablePrefix, '', $tableName));
            $tableSchema = $db->getTableSchema($tableName);
            $params      = [
                'tableName'   => $tableName,
                'className'   => $className,
                'tableSchema' => $tableSchema,
                'labels'      => $this->generateLabels($tableSchema),
                'rules'       => $this->generateRules($tableSchema),
                'relations'   => isset($relations[$className]) ? $relations[$className] : [],
            ];

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . 'Base.php',
                $this->render('model.php', $params)
            );

            if ($this->generateModelClass) {
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php',
                    $this->render('model-extended.php', $params)
                );
            }
        }
        return $files;
    }

    private $_tableNames;
    private $_classNames;

    /**
     * Generates a class name from the specified table name.
     *
     * @param string $tableName the table name (which may contain schema prefix)
     *
     * @return string the generated class name
     */
    protected function generateClassName($tableName)
    {
        if (isset($this->_classNames[$tableName])) {
            return $this->_classNames[$tableName];
        }

        if (($pos = strrpos($tableName, '.')) !== false) {
            $tableName = substr($tableName, $pos + 1);
        }

        $db       = $this->getDbConnection();
        $patterns = [];
        # TODO - review ordering
        $patterns[] = "/^{$this->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$this->tablePrefix}$/";
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }
        #var_dump($className);
        return $this->_classNames[$tableName] = Inflector::id2camel($className, '_');
    }
}
