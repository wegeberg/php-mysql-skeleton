<?php
// PDO wrapper class 2.6.4
// 2022-11-25 - copy_table, create_table, get_query
// 2022-09-14 - Generel oprydning
// 2022-08-30 - Linting
// 2021-10-24 - default parametre i increment()
// 2020-08-30 - indexField i get_rows_indexed
// 2020-08-06 - table_exists, copy_table, create_table (stub)
// 2020-07-13 - Escape realQueryværdier i insert
// 2020-05-13 - Vælg ikke en række hvis id og conditions mangler - get_row()
// 2020-05-08 - Fjernet LIMIT fra increment
// 2020-04-13 - Mulighed for null conditions i get_row_count_multi
// 2020-03-01 - realQuery i update rettet
// 2020-02-11 - realQuery i update()
// 2020-02-03 - realQuery
// 2019-10-25 - Rettet increment
// 2019-09-29 - Formattering af defines
// 2019-09-13 - get_result_from_query tilføjet
// 2019-08-10 - get_result
// 2019-03-19 - get_distinct, order part
// 2019-02-01 - get_row_count returnerer integer
// 2019-01-07 - function increment
// 2018-12-31 - update, Enten id eller conditions skal være angivet
// 2018-12-28 - && $orderField != $column tilføjet til get_distinct
// 2018-12-27 - Fejlrettelse, do_query
// 2018-12-24 - get_rows_join_multi, default order + joinType, returner resultat fra do_query hvis SELECT
// 2018-12-20 - get_row_count_multi tilføjet
// 2018-12-17 - do_query returnerer nu resultatet
// 2018-12-14 - get_rows_join_multi: default værdier for $select og $order
// 2018-09-01 - get_distinct: tilføj order-felter til select
// 2018-08-14 - tilføjet fields_is_integer (skifter '' ud med 0 for integers)
// 2017-08-29 - tilføjet get_values_indexed
// Martin Wegeberg, http://www.wegeberg.dk/systemudvikling/

// include("../includes/constants.inc.php");

if (!defined ("PDO")) {
    define ("PDO","Database included");
    if (!defined ("DBCHARSET")) {
        define ("DBCHARSET", "utf8");
    }

    class db {
        public string $sql = "";
        public string $realQuery = "";
        private PDO $dbh;
        public string $error;
        private string $dsn;
        private PDOStatement $stmt;
        private array $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "'.DBCHARSET.'"'
        ];

        public function __construct($dbName = null, $dbUser = null, $dbPass = null, $dbHost = null) {
            $this->dsn = "mysql:host="
                .($dbHost ?: DB_HOST)
                .";dbname=".($dbName ?: DB_NAME)
                .";charset=".DBCHARSET;
            try {
                $this->dbh = new PDO($this->dsn, ($dbUser ?: DB_USER), ($dbPass ?: DB_PASS), $this->options);
            }
            catch (PDOException $e) {
                $this->error = $e->getMessage();
            }
        }

        public function get_query(): string
        {
            return $this->stmt->queryString ?? "";
        }

        public function fields($table): array
        {
            $fields = [];
            $query = "SHOW COLUMNS FROM {$table}";
            $this->query($query);
            return ($this->resultset());
        }

        public function field_names($table): array
        {
            $fields = $this->fields($table);
            $fieldNames = [];
            foreach ($fields as $field) {
                $fieldNames[] = $field["Field"];
            }
            return ($fieldNames);
        }

        public function numeric_fields($table): array
        {
            $fields = $this->fields($table);
            $fieldsByName = [];
            foreach ($fields as $field) {
                $isInteger = !(stripos ($field["Type"], "int") === false);
                $isDecimal = !(stripos ($field["Type"], "decimal") === false);
                $fieldsByName[$field["Field"]] = $isInteger || $isDecimal;
            }
            return $fieldsByName;
        }

        public function fields_is_integer($table): array
        {
            $fields = $this->fields($table);
            $fieldsByName = [];
            foreach ($fields as $field) {
                $isInteger = !(stripos ($field["Type"], "int") === false);
                $fieldsByName[$field["Field"]] = $isInteger;
            }
            return $fieldsByName;
        }
        public function table_exists($table): bool
        {
            $query = "SELECT 1 FROM {$table} LIMIT 1";
            try {
                $this->dbh->query($query);
            } catch (Exception $e) {
                return false;
            }
            return true;
        }

        public function create_table($table, $columns, $primaryKey = "id"): bool
        {
            if (!$table || !$columns) {
                $this->error = "table and column parameters must be defined";
                return false;

            }
            if ($this->table_exists($table)) {
                $this->error = "Table already exists";
                return false;
            }
            $columnLines = [];
            if (!in_array ($primaryKey, array_keys ($columns))) {
                $columns = array_merge ([ "id" => [] ], $columns);
            }

            foreach ($columns as $columnName => $column) {
                // COLUMN_NAME => [ "type" => COLUMN_TYPE, "extras" => COLUMN_EXTRAS, "default" => COLUMN_DEFAULT ]
                $type = $column["type"] ?? "int(8) UNSIGNED";
                $extras = $column["extras"] ?? "NULL";
                $default = $column["default"] ?? (stripos ($type, 'int') !== false ? "DEFAULT '0'" : "DEFAULT NULL");
                if (stripos ($default, "DEFAULT") === false) {
                    $default = "DEFAULT {$default}";
                }
                $columnLines[] = $columnName === $primaryKey
                    ?   "{$columnName} {$type} NOT NULL AUTO_INCREMENT"
                    :   "{$columnName} {$type} ${extras} {$default}";
            }

            $columnsString = implode (",\n", $columnLines);
            $query = <<<RETURN
CREATE TABLE {$table} (
    {$columnsString},
    PRIMARY KEY ({$primaryKey})
) ENGINE = InnoDB
RETURN;
            $this->query($query);
            try {
                $this->execute();
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                return false;
            }
            return true;
        }

        public function copy_table($table, $new_table_name): bool
        {
            if (!$this->table_exists($table)) {
                $this->error = "Source table does not exist";
                return false;
            }
            if ($this->table_exists($new_table_name)) {
                $this->error = "Target table already exists";
                return false;
            }
            $this->query("CREATE TABLE {$new_table_name} LIKE {$table}");
            $this->execute();
            return true;
        }

        public function insert($table, $row, $indexKey = "id", $debug = false): bool
        {
            if (!is_array ($row)) {
                $this->error = "Row should be an array.";
                return false;
            }
            $fieldNames = $this->field_names($table);
            if (empty ($fieldNames)) {
                $this->error = "Couldn't fetch the columns for the table {$table}";
                return false;
            }
            $numericFields = $this->numeric_fields($table);
            $keys = $values = $cleanedRow = [];
            if ($debug) {
                $realValues = [];
            }
            foreach ($row as $key=>$val) {
                if ($key != $indexKey && in_array ($key, $fieldNames)) {
                    $keys[] = "`{$key}`";
                    $values[] = ":{$key}";
                    if ($debug) {
                        if (!$val) {
                            $realValues[] = $numericFields[$key] ? "0" : "NULL";
                        } else {
                            $realValues[] = "'".addslashes ($val)."'";
                        }
                    }
                    if (!$val) {
                        $cleanedRow[$key] = $numericFields[$key] ? 0 : NULL;
                    } else {
                        $cleanedRow[$key] = $val;
                    }
                }
            }
            if (empty ($keys) || empty ($values)) {
                $this->error = "Either no keys or no values.";
                return false;
            }
            $keysString = implode (", ", $keys);
            $valuesString = implode (", ", $values);
            if ($debug) {
                $realValuesString = implode (", ", $realValues);
            }
            $query = "INSERT INTO {$table} ({$keysString}) VALUES ({$valuesString})";
            if ($debug) {
                $this->realQuery = "INSERT INTO {$table} ({$keysString}) VALUES ({$realValuesString})";
            }
            $this->query($query);
            $this->bindArrayValues($cleanedRow);
            $this->execute();
            return true;
        }

        public function update($table, $id, $row, $conditions = null, $indexKey = "id", $debug = false): bool
        {
            if (!$id && !$conditions) {
                // Enten id eller conditions skal være angivet
                return false;
            }
            if (!is_array ($row)) {
                $this->error = "Row should be an array.";
                return false;
            }
            $fieldNames = $this->field_names($table);
            if (empty ($fieldNames)) {
                $this->error = "Couldn't fetch the columns for the table {$table}";
                return false;
            }
            if ($id > 0) {
                $conditionsString = "id = ".intval ($id);
            } else {
                $conditionsString = $this->make_conditions($conditions);
            }
            $numericFields = $this->numeric_fields($table);
            if ($debug) {
                // $realValues = [];
                $realUpdates = [];
            }
            $updates = $cleanedRow = [];
            foreach ($row as $key=>$val) {
                if ($key != $indexKey && in_array ($key, $fieldNames)) {
                    if ($debug) {
                        if (!$val) {
                            if ($numericFields[$key]) {
                                $realUpdates[] = "`{$key}` = 0";
                            } else {
                                $realUpdates[] = "`{$key}` = NULL";
                            }
                        } else {
                            $realUpdates[] = "`{$key}` = '{$val}'";
                        }
                    }
                    $updates[] = "`{$key}` = :{$key}";
                    if (!$val) {
                        $cleanedRow[$key] = $numericFields[$key]
                            ? 0
                            : NULL;
                    } else {
                        $cleanedRow[$key] = $val;
                    }
                }
            }
            if (empty ($updates)) {
                $this->error = "Intet at opdatere";
            }
            $query = "UPDATE {$table} SET ".implode (', ', $updates)." WHERE {$conditionsString}"
            ;
            if ($debug) {
                $this->realQuery = "UPDATE {$table} SET ".implode (", ", $realUpdates)." WHERE {$conditionsString}";
            }
            $this->query($query);
            $this->bindArrayValues($cleanedRow);
            $this->execute();
            return true;
        }

        public function delete($table, $id = 0, $conditions = null): bool
        {
            if (!$id && !$conditions) {
                $this->error = "No criteria given";
                return false;
            }
            $id = (int) $id;
            if ($id > 0) {
                $conditionsString = "id = {$id}";
            } else {
                $conditionsString = $this->make_conditions($conditions);
            }
            $query = "DELETE FROM {$table} WHERE {$conditionsString}";
            $this->query($query);
            $this->execute();
            return true;
        }

        public function get_value($table, $id = 0, $column = "id", $order = null, $conditions = null) {
            $id = (int) $id;
            if (!$id && !$conditions) {
                return null;
            }
            if ($id > 0) {
                $conditionsString = "id = {$id}";
            } else {
                $conditionsString = $this->make_conditions($conditions);
            }
            $query = "SELECT {$column} FROM {$table} WHERE {$conditionsString} ";
            if ($order) $query .= "ORDER BY {$order} ";
            $this->query($query);
            return $this->single(PDO::FETCH_COLUMN);
        }

        public function get_sum($table, $column, $conditions = null): int
        {
            $conditionsString = $this->make_conditions($conditions);
            $query = "SELECT SUM({$column}) FROM {$table} WHERE {$conditionsString}";
            $this->query($query);
            return intval ($this->single(PDO::FETCH_COLUMN));
        }

        public function get_max($table, $field, $conditions = null) {
            $conditionsString = ($conditions
                ? $this->make_conditions($conditions)
                : "1"
            );
            $query = "SELECT MAX({$field}) FROM {$table} WHERE {$conditionsString} LIMIT 1";
            $this->query($query);
            return $this->single(PDO::FETCH_COLUMN);
        }

        public function get_min($table, $field, $conditions = null) {
            $conditionsString = $conditions ? $this->make_conditions($conditions) : "1";
            $query = "SELECT min({$field}) FROM {$table} WHERE {$conditionsString} LIMIT 1";
            $this->query($query);
            return $this->single(PDO::FETCH_COLUMN);
        }

        public function get_row($table, $id = 0, $conditions = null, $order = null, $select = "*")
        {
            $id = intval ($id);
            if (!$id && !$conditions) return null;
            $selectString = $this->make_select($select);
            if ($id > 0) {
                $conditionsString = "id = {$id}";
            } else if ($conditions) {
                $conditionsString = $this->make_conditions($conditions);
            } else {
                $conditionsString = "1";
            }
            $query = "SELECT {$selectString} FROM {$table} WHERE {$conditionsString}";
            if ($order) {
                $query .= " ORDER BY {$order} ";
            }
            $this->query($query);
            return $this->single();
        }

        public function get_distinct($table, $column = "id", $order = null, $conditions = null, $limit = null)
        {
            $conditionsString = $this->make_conditions($conditions);

            $orderBy = $order ?? $column;
            $query = "SELECT DISTINCT( {$column} ) FROM {$table} WHERE {$conditionsString} ORDER BY {$orderBy}";
            if ($limit) $query .= " LIMIT {$limit} ";
            $this->query($query);
            return $this->resultset(PDO::FETCH_COLUMN);
        }

        public function get_row_count($table, $conditions = null, $column = "id"): int
        {
            $conditionsString = $this->make_conditions($conditions);
            $query = "SELECT COUNT( {$column} ) FROM `{$table}` WHERE {$conditionsString}";
            $this->query($query);
            return intval ($this->single(PDO::FETCH_COLUMN));
        }

        public function get_distinct_row_count($table, $conditions = null, $column = "id"): int
        {
            return $this->get_row_count($table, $conditions, $column);
        }

        public function exists($table, $id, $conditions, $column = "id"): bool
        {
            if (intval ($id) > 0) {
                $conditions = "id = ".intval ($id);
            }
            return $this->get_row_count($table, $conditions, $column) > 0;
        }

        public function get_result($table, $order = null, $conditions = null, $select = "*", $limit = null): object
        {
            $selectString = $this->make_select($select);
            $conditionsString = $this->make_conditions($conditions);
            $query = "SELECT {$selectString} FROM {$table} WHERE {$conditionsString}";
            if ($order) $query .= " ORDER BY {$order} ";
            if ($limit) $query .= " LIMIT {$limit} ";
            $this->sql = $query;
            return $this->dbh->query($query);
        }

        public function get_result_from_query($query)
        {
            $this->sql = $query;
            return $this->dbh->query($query);
        }

        public function get_rows($table, $order = null, $conditions = null, $select = "*", $limit = null): array
        {
            $selectString = $this->make_select($select);
            $conditionsString = $this->make_conditions($conditions);
            $query = "SELECT {$selectString} FROM {$table} WHERE {$conditionsString}";
            if ($order) $query .= " ORDER BY {$order}";
            if ($limit) $query .= " LIMIT {$limit}";
            $this->query($query);
            return $this->resultset();
        }

        public function increment($table, $id, $field, $conditions = null, $increment = 1): ?bool
        {
            $id = intval ($id);
            if ($id > 0) {
                $conditionsString = "id = {$id}";
            } else if ($conditions) {
                $conditionsString = $this->make_conditions($conditions);
            } else {
                return false;
            }
            $query = "UPDATE {$table} SET {$field} = {$field} + {$increment} WHERE {$conditionsString}";
            $this->query($query);
            return $this->execute();
        }

        public function do_query($query): bool
        {
            $this->query($query);
            if (stripos ($this->sql, "SELECT") !== false) {
                return $this->resultset();
            }
            return $this->execute();
        }

        public function get_values_indexed($table, $valueField, $conditions = null, $order = null, $limit = null, $indexField = "id"): array
        {
            $rows = $this->get_rows($table, $order, $conditions, "{$indexField}, {$valueField}", $limit);
            if (!$rows) return [];
            $returnRows = [];
            foreach ($rows as $row) {
                $returnRows[$row[$indexField]] = $row[$valueField];
            }
            return $returnRows;
        }

        public function get_rows_indexed($table, $order = null, $conditions = null, $select = "*", $limit = null, $indexField = "id"): array
        {
            $rows = $this->get_rows(
                $table,
                $order,
                $conditions,
                $select,
                $limit
            );
            if (!$rows) return [];
            $returnRows = [];
            foreach ($rows as $row) {
                $returnRows[$row[$indexField]] = $row;
            }
            return $returnRows;
        }

        public function get_distinct_join($table1, $table2, $table1Idfield, $table2Idfield, $conditions, $column, $order, $limit = null): array
        {
            $conditionsString = $this->make_conditions($conditions);
            if (!stristr ($column, ".")) { // no table in $column
                $column = "{$table1}.{$column}";
            }
            $query = <<<QUERY
SELECT DISTINCT( {$column} ) FROM {$table1} INNER JOIN {$table2} ON {$table1}.{$table1Idfield} = {$table2}.{$table2Idfield} WHERE {$conditions}
QUERY;
            if ($order) $query .= " ORDER BY {$order} ";
            if ($limit) $query .= " LIMIT {$limit} ";
            $this->query($query);
            return $this->resultset(PDO::FETCH_COLUMN);
        }

        public function get_rows_join($table1, $table2, $table1Idfield, $table2Idfield, $conditions, $select, $order, $limit = null): array
        {
            return $this->get_rows_join_multi([$table1, $table2], [$table1Idfield, $table2Idfield], $conditions, $select, $order, $limit);
        }

        public function get_row_count_multi($tables, $idFields, $conditions = null, $select = null): int
        {
            if (!is_array ($tables) || !is_array ($idFields)) {
                $this->error = "The first two parameters MUST be arrays!";
                return 0;
            }
            if (empty ($tables) || empty ($idFields) || count($tables) != count($idFields)) {
                $this->error = "The two arrays need to be of equal length and not empty!";
                return 0;
            }
            $conditionsString = $this->make_conditions($conditions);
            $selectString = $select ?? "{$tables[0]}.id";
            $joinString = "";
            for ($i = 1; $i < count ($tables); $i++) {

                $joinString .= <<<JOINSTRING
INNER JOIN `{$tables[$i]}` ON `{$tables[0]}`.`{$idFields[0]}` = `{$tables[$i]}`.`{$idFields[$i]}` 
JOINSTRING;
            }
            $query = "SELECT COUNT( {$selectString} ) FROM `{$tables[0]}` {$joinString} WHERE {$conditionsString} ";
            $this->query($query);
            return intval ($this->single(PDO::FETCH_COLUMN));
        }

        public function get_rows_join_multi($tables, $idFields, $conditions, $select = null, $order = null, $limit = null, $joinType = "INNER"): ?array
        {
            if (!is_array ($tables) || !is_array ($idFields)) {
                $this->error = "The first two parameters MUST be arrays!";
                return null;
            }
            if (empty ($tables) || empty ($idFields) || count($tables) != count($idFields)) {
                $this->error = "The two arrays need to be of equal length and not empty!";
                return null;
            }
            if (!$select) {
                $select = "{$tables[0]}.*";
            }
            if (!$order) {
                $order = "{$tables[0]}.id ASC";
            }
            $selectString = $this->make_select($select);
            $conditionsString = $this->make_conditions($conditions);
            $joinString = "";
            for ($i = 1; $i < count ($tables); $i++) {
                $joinString .= "{$joinType} JOIN `{$tables[$i]}` ON `{$tables[0]}`.`{$idFields[0]}` = `{$tables[$i]}`.`{$idFields[$i]}` ";
            }
            $query = "SELECT {$selectString} FROM `{$tables[0]}` {$joinString} WHERE {$conditionsString} ";
            if ($order) $query .= "ORDER BY {$order} ";
            if ($limit) $query .= "LIMIT {$limit} ";
            $this->query($query);
            return $this->resultset();
        }

        public function get_ids($table, $order = null, $conditions = null, $limit = null, $field = "id")
        {
            $conditionsString = $this->make_conditions($conditions);
            $query = "SELECT {$field} FROM {$table} WHERE {$conditionsString} ";
            if ($order) $query .= "ORDER BY {$order} ";
            if ($limit) $query .= "LIMIT {$limit} ";
            $this->query($query);
            return ($this->resultset(PDO::FETCH_COLUMN));
        }

        private function bindArrayValues($array): void
        {
            foreach ($array as $key => $value) {
                if (is_array ($key) || is_array ($value)) {
                    $this->error = "Array forsøgt sendt til bind!";
                }
                $this->bind(":$key",$value);
            }
        }

        public function lastid(): int
        {
            return $this->lastInsertId();
        }

        private function make_conditions($conditions): string {
            if (!$conditions) {
                $conditionsString = "1";
            } else {
                $conditionsString = is_array ($conditions)
                    ? implode (" AND ", $conditions)
                    : $conditions;
            }
            return $conditionsString;
        }

        function make_select($select) {
            return is_array ($select) ? implode (", ", $select) : $select;
        }

        public function bind($param, $value, $type = null): void
        {
            if (is_null($type)) {
                $type = match (true) {
                    is_int($value) => PDO::PARAM_INT,
                    is_bool($value) => PDO::PARAM_BOOL,
                    is_null($value) => PDO::PARAM_NULL,
                    default => PDO::PARAM_STR,
                };
            }
            $this->stmt->bindValue($param, $value, $type);
        }

        public function query($query): ?PDOStatement
        {
            $this->sql = $query;
            try {
                return $this->stmt = $this->dbh->prepare($query);
            } catch (PDOException $e) {
                return null;
            }
        }

        private function execute(): bool
        {
            try {
                return $this->stmt->execute();
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
            }
            return false;
        }

        private function resultset($modifier = PDO::FETCH_ASSOC) {
            $this->execute();
            return $this->stmt->fetchAll($modifier);
        }

        private function single($modifier = PDO::FETCH_ASSOC) {
            $this->execute();
            return $this->stmt->fetch($modifier );
        }
        public function lastInsertId(): int
        {
            return $this->dbh->lastInsertId();
        }

        public function beginTransaction(): bool
        {
            return $this->dbh->beginTransaction();
        }

        public function endTransaction(): bool
        {
            return $this->dbh->commit();
        }

        public function cancelTransaction(): bool
        {
            return $this->dbh->rollBack();
        }

        public function debugDumpParams(): ?bool
        {
            return $this->stmt->debugDumpParams();
        }
    } // class db
} // if (!defined ('DB'))
