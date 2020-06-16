<?php
namespace Services;

use Silex\Application;

class MySQLKeeper

{
    private $db;

    /**
     * Path to sql folder (with last slash)
     *
     * @var string
     */
    private $path_to_sql;

    /**
     * Database Name (Required)
     *
     * @var string
     */
    private $dbname;

    /*
     * Application $app
     */
    private $app;

    public $retry_update = false;
    protected $retry_count = 0;

    const RETRY_MAX = 5;

    public function __construct(Application $app = null)
    {
        $this->app = $app;
        $this->dbname = $this->app['db.options']['dbname'];
        $this->path_to_sql = $this->app['mysqlkeep.sqlpath'];

        if (!is_dir($this->path_to_sql)) {
            mkdir($this->path_to_sql, 0777, true);
        }

        $this->db = $this->app['db'];
    }

    public function Query($sql, $params = array())
    {
        /** @var \Doctrine\DBAL\Driver\PDOStatement $handler */
        $handler = $this->db->prepare($sql);
        $handler->execute($params);
        return $handler;
    }

    public function FetchAll($result)
    {
        /** @var \Doctrine\DBAL\Driver\PDOStatement $result */
        return $result->fetchAll();
    }

    public function FetchRow($result)
    {
        /** @var \Doctrine\DBAL\Driver\PDOStatement $result */
        return $result->fetch();
    }

    /**
     * Filter table or column name.
     *
     * @param string $name Table or Colunm Name
     * @return string Filtered name
     */
    public function prepareColname($name)
    {
        return '`' . preg_replace('~[^a-z0-9_\$]~i', '', $name) . '`';
    }

    /**
     * Make query and return value of first column of first row of query or <b>null</b> if result is empty.
     *
     * @return mixed first value
     */
    public function QuerySingle()
    {
        $array = $this->FetchRow(call_user_func_array(array($this, 'Query'), func_get_args()));
        if (empty($array)) {
            return null;
        }
        return reset($array);
    }

    /**
     * Make query and return an array containing all rows in the result set
     *
     * @return array Array rows in the result set
     */
    public function QueryAssoc()
    {
        return $this->FetchAll(call_user_func_array(array($this, 'Query'), func_get_args()));
    }

    /**
     * Check if table exists
     *
     * @param string $table Table name
     * @return boolean <b>true</b> if table exists
     */
    public function CheckTable($table)
    {
        return $this->QuerySingle("SHOW TABLES LIKE ?", array($table)) ? true : false;
    }

    /**
     * Check if field exists
     *
     * @param string $table table name
     * @param string $field field name
     * @return boolean <b>true</b> if table and field exists
     */
    public function CheckField($table, $field)
    {
        if (!$this->CheckTable($table)) {
            return false;
        }
        $fields = $this->QueryAssoc("DESCRIBE " . $this->prepareColname($table));
        foreach ($fields as $f) {
            if ($f['Field'] == $field) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if field type correct<br/>
     * Type examples:<br/>
     * <ul>
     * <li>bigint(20) unsigned
     * <li>varchar(255)
     * <li>enum('true','false')
     * </ul>
     *
     * @param string $table table name
     * @param string $field field name
     * @param string $type field type
     * @param null|string $null
     * @param bool|string $default
     * @return boolean true if type completely correct
     */
    public function CheckFieldType($table, $field, $type, $null = null, $default = false)
    {
        if (!$this->CheckTable($table)) {
            return false;
        }
        $fields = $this->QueryAssoc("DESCRIBE " . $this->prepareColname($table));
        foreach ($fields as $f) {
            if ($f['Field'] == $field) {
                $ok_null = isset($null) ? $f['Null'] == ($null ? 'YES' : 'NO') : true;
                $ok_default =
                    $default !== false ?
                        (is_null($default) && is_null($f['Default'])) ||
                        (!is_null($default) && $f['Default'] == $default) :
                        true;
                return $f['Type'] == $type && $ok_null && $ok_default;
            }
        }
        return false;
    }

    /**
     * Checks if the specified field in the table may be assigned that value
     *
     * @param string $table table name
     * @param string $field field name
     * @param string $value test value
     * @return boolean true if table and field exist and value could be assigned
     */
    public function CheckFieldValue($table, $field, $value)
    {
        if (!$this->CheckTable($table)) {
            return false;
        }
        $fields = $this->QueryAssoc("SHOW FIELDS FROM " . $this->prepareColname($table) . " like ?", array($field));
        if (empty($fields)) {
            return false;
        }
        $type = $fields[0]['Type'];
        preg_match('~^([^\(]+)\(([^\)]+)\)~', $type, $m);
        if (!$m || count($m) < 3) {
            return false;
        }
        switch ($m[1]) {
            case "tinyint":
            case "smallint":
            case "mediumint":
            case "int":
            case "bigint":
                return $value == (string)(int)$value;
            case "decimal":
            case "float":
            case "double":
            case "real":
                return $value == (string)(float)$value;
            case "enum":
            case "set":
                return in_array("'" . $value . "'", explode(',', $m[2]));
            case "date":
                return preg_match('~^\d(4)-\d{2}-\d{2}$', $value) ? true : false;
            case "datetime":
                return preg_match('~^\d(4)-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}$', $value) ? true : false;
            case "char":
            case "varchar":
                return strlen($value) <= (int)$m[2];
        }
        return true;
    }

    public function CheckIndex($table, $index, $columns = array(), $nonunique = null)
    {
        if (!$this->CheckTable($table)) {
            return false;
        }
        $indexes = $this->QueryAssoc("SHOW INDEX FROM " . $this->prepareColname($table));
        $okcols = 0;
        $found = false;
        foreach ($indexes as $i) {
            if ($i['Key_name'] == $index) {
                $found = true;
                if (isset($nonunique) && $i['Non_unique'] != $nonunique) {
                    return false;
                }
                if (!empty($columns)) {
                    $k = array_search($i['Column_name'], $columns);
                    if ($k === false) {
                        return false;
                    }
                    if ($k === $i['Seq_in_index'] - 1) {
                        $okcols++;
                    }
                }
            }
        }
        return $found && $okcols == count($columns);
    }

    public function CheckForeign($table, $fkname, $field = null, $ref_table = null, $ref_field = null, $on_delete = null, $on_update = null)
    {
        if (!$this->CheckTable($table)) {
            return false;
        }
        $keys = $this->QueryAssoc("
            SELECT `CONSTRAINT_NAME`,`REFERENCED_TABLE_NAME`,`DELETE_RULE`,`UPDATE_RULE`
            FROM `INFORMATION_SCHEMA`.`REFERENTIAL_CONSTRAINTS`
            WHERE
                CONSTRAINT_SCHEMA = :dbname AND
                TABLE_NAME = :tablename
            ", array(
                'dbname' => $this->dbname,
                'tablename' => $table
            )
        );
        $key_row = null;
        foreach ($keys as $row) {
            if ($row['CONSTRAINT_NAME'] == $fkname) {
                $key_row = $row;
                break;
            }
        }
        if (!$key_row) {
            return false;
        }

        if (isset($field)) {
            $ref_column_name = $this->QuerySingle("
                SELECT `REFERENCED_COLUMN_NAME`
                FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
                WHERE
                    CONSTRAINT_SCHEMA = :dbname AND
                    TABLE_NAME = :tablename AND
                    COLUMN_NAME = :field
                ", array(
                    'dbname' => $this->dbname,
                    'tablename' => $table,
                    'field' => $field
                )
            );
            if (!$ref_column_name) {
                return false;
            }
            if (isset($ref_table) && isset($ref_field)) {
                if ($ref_table != $key_row['REFERENCED_TABLE_NAME'] || $ref_field != $ref_column_name) {
                    return false;
                }
            }
        }

        if (isset($on_delete) && $key_row['DELETE_RULE'] != $on_delete) {
            return false;
        }
        if (isset($on_update) && $key_row['UPDATE_RULE'] != $on_update) {
            return false;
        }
        return true;
    }

    public function listReferences($table)
    {
        return $this->QueryAssoc("
            SELECT `REFERENCED_TABLE_NAME` AS 'table',`REFERENCED_COLUMN_NAME` AS 'field'
            FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
            WHERE
                CONSTRAINT_SCHEMA = :dbname AND
                TABLE_NAME = :tablename AND
                REFERENCED_COLUMN_NAME IS NOT NULL
            ", array(
                'dbname' => $this->dbname,
                'tablename' => $table
            )
        );

    }

    public function QueryEcho($sql)
    {
        echo $sql . "\n";
        $this->Query($sql);
    }

    public function ExecuteEchoShort($sql)
    {
        $echo = explode("\n", trim($sql));
        echo count($echo) > 1 ? trim($echo[0]) . "..." : trim($echo[0]) . "\n";
        $this->Query($sql);
    }

    public function SqlInclude($file)
    {
        include $file;
    }

    public function prepareSkippingCondition($keys)
    {
        if (empty($keys)) {
            return "";
        }
        $check = implode(" || ", array_map(function ($row) {
            return '!$db->CheckField("' . $row['table'] . '","' . $row['field'] . '")';
        }, $keys));
        return <<<EOT
if ($check) {
    \$db->retry_update = true;
    return;
}
EOT;
    }

    public function TableUpdater()
    {
        echo '<pre>';
        do {
            if ($handle = opendir($this->path_to_sql)) {
                $this->retry_update = false;
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' ||
                        $file == '..' ||
                        $file[0] == '$' ||
                        substr($file, -4) !== '.php' ||
                        !is_file($this->path_to_sql . $file)
                    ) {
                        continue;
                    }
                    $this->SqlInclude($this->path_to_sql . $file);
                }
                $this->retry_count++;
            }
        } while ($this->retry_update && $this->retry_count < self::RETRY_MAX);
    }

    public function TableDumper()
    {
        echo '<pre>';
        $tables = $this->QueryAssoc("SHOW TABLES");
        //$class = __CLASS__;
        foreach ($tables as $table) {
            $tablename = $table["Tables_in_{$this->dbname}"];
            $filename = $this->path_to_sql . $tablename . ".sql.php";
            if (!is_file($filename)) {
                $tablecreate = $this->QueryAssoc("SHOW CREATE TABLE `" . $tablename . "`");
                $tablecreate = preg_replace('~AUTO_INCREMENT=\d+~', '', $tablecreate[0]['Create Table']);
                $tablecreate = preg_replace('~\n~', "\n      ", $tablecreate);

                $foreignskip = $this->prepareSkippingCondition($this->listReferences($tablename));

                $str = <<<EOT
<?php
\$db = \\App::_('mysqlkeep');
$foreignskip
if (!\$db->CheckTable("$tablename")) {
    \$db->QueryEcho("$tablecreate");
}
EOT;
                file_put_contents($filename, $str);
                echo $filename . " created \n";
            }
        }
    }
}
