<?php
/**
 * author zhepama
 */
namespace migration\controllers;

use migration\AppUtility;
use migration\models\MigrationUtility;
use Yii;
use yii\base\BaseObject;
use yii\helpers\FileHelper;
use yii\web\Controller;

use backend\controllers\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class DefaultController extends BaseController
{

    /**
     * @todo 没有添加事务
     *
     * @return string
     */
    public function actionIndex()
    {
        set_time_limit(0);
        $model = new MigrationUtility();
        $upStr = new OutputString();
        $downStr = new OutputString();
        
        if ($model->load(\Yii::$app->getRequest()
            ->post())) {
            
            if (! empty($model->tableSchemas)) {
                list ($up, $down) = $this->generalTableSchemas($model->tableSchemas, $model->tableOption);
                $upStr->outputStringArray = array_merge($upStr->outputStringArray, $up->outputStringArray);
                $downStr->outputStringArray = array_merge($downStr->outputStringArray, $down->outputStringArray);
            }
            
            if (! empty($model->tableDatas)) {
                list ($up, $down) = $this->generalTableDatas($model->tableDatas);
                $upStr->outputStringArray = array_merge($upStr->outputStringArray, $up->outputStringArray);
                $downStr->outputStringArray = array_merge($downStr->outputStringArray, $down->outputStringArray);
            }
            
            $path = Yii::getAlias($model->migrationPath);
            if (! is_dir($path)) {
                FileHelper::createDirectory($path);
            }
            
            $name = 'm' . gmdate('ymd_His') . '_' . $model->migrationName;
            $file = $path . DIRECTORY_SEPARATOR . $name . '.php';
            
            $content = $this->renderFile(Yii::getAlias("@backend/modules/migration/views/migration.php"), [
                'className' => $name,
                'up' => $upStr->output(),
                'down' => $downStr->output()
            ]);
            file_put_contents($file, $content);
            Yii::$app->session->setFlash("success", "迁移成功，保存在" . $file);
        }
        
        if ($model->migrationPath == null) {
            $model->migrationPath = $this->module->migrationPath;
        }
        
        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function getTableName($name)
    {
        $prefix = \Yii::$app->db->tablePrefix;

        return str_replace($prefix, '', $name);
    }
    public function getTableComment ($name)
    {
//        print_r("<pre>");print_r(\Yii::$app->db);die;
        $sql = "SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_NAME = '$name' AND TABLE_SCHEMA = 'xwom_all2020'";//获取 xwom_all2020 数据库中 $name 表 的注释。
//        $sql = "show table status";//可以获得数据库所有表的信息，包括COMMENT注释信息
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        $table_comment =  $res[0]['TABLE_COMMENT'];
        return $table_comment;
    }

    public function generalTableSchemas($tables, $tableOption)
    {
        //$tableOption = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';//$tableOption 并没有从模型里获得
        $initialTabLevel = 0;
        $upStr = new OutputString([
            'tabLevel' => $initialTabLevel
        ]);
        $upStr->addStr('$this->execute(\'SET foreign_key_checks = 0\');');
        $upStr->addStr(' ');
        foreach ($tables as $table) {
            $upStr->tabLevel = $initialTabLevel;
            
            $tablePrepared = $this->getTableName($table);
            // 添加表结构以及字段的注释
            $upStr->addStr('$this->createTable(\'{{%' . $tablePrepared . '}}\', [');
            $upStr->tabLevel ++;
            $tableSchema = \Yii::$app->db->getTableSchema($table);
            foreach ($tableSchema->columns as $column) {
                $appUtility = new AppUtility($column);//构造数据表结构
                $upStr->addStr($appUtility->string . "',");                
            }
            if (! empty($tableSchema->primaryKey)) {
                $upStr->addStr("'PRIMARY KEY (`" . implode("`,`", $tableSchema->primaryKey) . "`)'");
            }
            
            $upStr->tabLevel --;
            //添加  $tableOption、表名注释 由于添加表注释后，无法迁移入库，就在暂时不添加了
            $tableComment = $this->getTableComment($table);
            $upStr->addStr('], "' . $tableOption . '");');//$upStr->addStr('], "' . $tableOption . '");');//$upStr->addStr('], "' . $tableOption);
            //$upStr->addStr('], "' . $tableOption . "  COMMENT = '" . $tableComment ."'" . '");');//添加上表注释 migrate/up 报错，估计需要修改migrate/up
            
            // 添加索引
            $tableIndexes = Yii::$app->db->createCommand('SHOW INDEX FROM `' . $table . '`')->queryAll();
            $indexs = [];
            foreach ($tableIndexes as $item) {
                if ($item['Key_name'] == 'PRIMARY') {
                    continue;
                }
                if (! isset($indexs[$item["Key_name"]])) {
                    $indexs[$item["Key_name"]] = [];
                    $indexs[$item["Key_name"]]["unique"] = ($item['Non_unique']) ? 0 : 1;
                }
                $indexs[$item["Key_name"]]["columns"][] = $item['Column_name'];
            }
            
            if (! empty($indexs)) {
                $upStr->addStr(' ');
            }
            
            foreach ($indexs as $index => $item) {
                $str = '$this->createIndex(\'' . $index . '\',\'{{%' . $tablePrepared . '}}\',\'' . implode(', ', $item['columns']) . '\',' . $item['unique'] . ');';
                $upStr->addStr($str);
            }
            
            $upStr->addStr(' ');
        }
        
        //添加外键
        $sql = "SELECT tb1.CONSTRAINT_NAME, tb1.TABLE_NAME, tb1.COLUMN_NAME,
            tb1.REFERENCED_TABLE_NAME, tb1.REFERENCED_COLUMN_NAME, tb2.MATCH_OPTION,
        
            tb2.UPDATE_RULE, tb2.DELETE_RULE
        
            FROM information_schema.KEY_COLUMN_USAGE AS tb1
            INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS AS tb2 ON
            tb1.CONSTRAINT_NAME = tb2.CONSTRAINT_NAME AND tb1.CONSTRAINT_SCHEMA = tb2.CONSTRAINT_SCHEMA
            WHERE TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_SCHEMA = DATABASE() AND REFERENCED_COLUMN_NAME IS NOT NULL";
        $foreignKeys = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($foreignKeys as $fk)
        {
            $str = '$this->addForeignKey(';
            $str .= '\'' . $fk['CONSTRAINT_NAME'] . '\', ';
            $str .= '\'{{%' . $this->getTableName($fk['TABLE_NAME']) . '}}\', ';
            $str .= '\'' . $fk['COLUMN_NAME'] . '\', ';
            $str .= '\'{{%' . $this->getTableName($fk['REFERENCED_TABLE_NAME'])  . '}}\', ';
            $str .= '\'' . $fk['REFERENCED_COLUMN_NAME'] . '\', ';
            $str .= '\'' . $fk['DELETE_RULE'] . '\', ';
            $str .= '\'' . $fk['UPDATE_RULE'] . '\' ';
            $str .= ');';
            $upStr->addStr($str);
        }

        
        $upStr->addStr(' ');
        $upStr->addStr('$this->execute(\'SET foreign_key_checks = 1;\');');
        
        $downStr = new OutputString();
        /* DROP TABLE */
        $downStr->addStr('$this->execute(\'SET foreign_key_checks = 0\');');
        foreach ($tables as $table) {
            if (! empty($table)) {
                $downStr->addStr('$this->dropTable(\'{{%' . $tablePrepared . '}}\');');
            }
        }
        $downStr->addStr('$this->execute(\'SET foreign_key_checks = 1;\');');
        
        return [
            $upStr,
            $downStr
        ];
    }

    public function generalTableDatas($tables)
    {
        $initialTabLevel = 0;
        $upStr = new OutputString([
            'tabLevel' => $initialTabLevel
        ]);
        $upStr->addStr('$this->execute(\'SET foreign_key_checks = 0\');');
        $upStr->addStr(' ');
        foreach ($tables as $table) {
            
            $tablePrepared = $this->getTableName($table);
            $upStr->addStr('/* Table ' . $table . ' */');
            $tableSchema = \Yii::$app->db->getTableSchema($table);
            $data = Yii::$app->db->createCommand('SELECT * FROM `' . $table . '`')->queryAll();
            $out = '$this->batchInsert(\'{{%' . $tablePrepared . '}}\',[';
            foreach ($tableSchema->columns as $column) {
                $out .= "'" . $column->name . "',";
            }
            $out = rtrim($out, ',') . '],[';
            foreach ($data as $row) {
                $out .= '[';
                foreach ($row as $field) {
                    if ($field === null) {
                        $out .= "null,";
                    } else {
                        $out .= "'" . addcslashes($field, "'") . "',";
                    }
                }
                $out = rtrim($out, ',') . "],\n";
            }
            $out = rtrim($out, ',') . ']);';
            $upStr->addStr($out);
            $upStr->addStr(' ');
        }
        $upStr->addStr('$this->execute(\'SET foreign_key_checks = 1;\');');
        $downStr = new OutputString();
        return [
            $upStr,
            $downStr
        ];
    }
}

/**
 * Class OutputString
 *
 * @author Nils Lindentals <nils@dfworks.lv>
 *        
 * @package c006\utility\migration\controllers
 */
class OutputString extends BaseObject
{

    /**
     *
     * @var string
     */
    public $nw = "\n";

    /**
     *
     * @var string
     */
    public $tab = "\t";

    /**
     *
     * @var string
     */
    public $outputStringArray = array();

    /**
     *
     * @var int
     */
    public $tabLevel = 0;

    /**
     * Adds string to output string array with "tab" prefix
     *
     * @var string $str
     */
    public function addStr($str)
    {
        $str = str_replace($this->tab, '', $str);
        $this->outputStringArray[] = str_repeat($this->tab, $this->tabLevel) . $str;
    }

    /**
     * Returns string output
     */
    public function output()
    {
        return implode($this->nw, $this->outputStringArray);
    }
}
