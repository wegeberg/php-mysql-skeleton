<?php
	// PDO wrapper class 2.5.2
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

	if(!defined("PDO")) {
		define("PDO","Database included");
		if(!defined("DB_DEBUG")) {
			define("DB_DEBUG", false);
		}
		if(!defined("ERROR_LOGFILE")) {
			define("ERROR_LOGFILE", "pdo_error.log");
		}
		if(!defined("DEBUG_LOGFILE")) {
			define("DEBUG_LOGFILE", "pdo_debug.log");
		}
		if(!defined("DISPLAY_DEBUG")) {
			define("DISPLAY_DEBUG", false);
		}
		if(!defined("DBCHARSET")) {
			define("DBCHARSET", "utf8");
		}

		class db {
			public $sql;
			public $realQuery;
			private $dbh;
			public $error;
			private $dsn;
			private $stmt;
			private $options = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "'.DBCHARSET.'"'
			);

			public function __construct($dbName = null, $dbUser = null, $dbPass = null, $dbHost = null) {
				$this->dsn = "mysql:host="
					.($dbHost ? $dbHost : DB_HOST)
					.";dbname=".($dbName ? $dbName : DB_NAME)
					.";charset=".DBCHARSET;
				try {
					$this->dbh = new PDO($this->dsn, ($dbUser ? $dbUser : DB_USER), ($dbPass ? $dbPass : DB_PASS), $this->options);
				}
				catch(PDOException $e) {
					$this->error = $e->getMessage();
					$this->log_db_error($this->error, "Fatal");
				}
			}

			public function fields($table) {
				$fields = [];
				$query = "SHOW COLUMNS FROM {$table}";
				$this->query($query);
				return($this->resultset());
			}

			public function field_names($table) {
				$fields = $this->fields($table);
				$fieldNames = [];
				foreach($fields as $field) {
					$fieldNames[] = $field["Field"];
				}
				return($fieldNames);
			}

			public function numeric_fields($table) {
				$fields = $this->fields($table);
				$fieldsByName = [];
				foreach($fields as $field) {
					$isInteger = stripos($field["Type"], "int") === false ? false : true;
					$isDecimal = stripos($field["Type"], "decimal") === false ? false : true;
					$fieldsByName[$field["Field"]] = $isInteger || $isDecimal;
				}
				return $fieldsByName;
			}

			public function fields_is_integer($table) {
				$fields = $this->fields($table);
				$fieldsByName = [];
				foreach($fields as $field) {
					$isInteger = stripos($field["Type"], "int") === false ? false : true;
					$fieldsByName[$field["Field"]] = $isInteger;
				}
				return $fieldsByName;
			}

			function insert($table, $row, $indexKey = "id", $debug = false) {
				if(!is_array($row)) {
					$this->error = "Row should be an array.";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				$fieldNames = $this->field_names($table);
				if(empty($fieldNames)) {
					$this->error = "Couldn't fetch the columns for the table {$table}";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				$numericFields = $this->numeric_fields($table);
				$keys = $values = $cleanedRow = [];
				if($debug) {
					$realValues = [];
				}
				foreach($row as $key=>$val) {
					if($key != $indexKey && in_array($key, $fieldNames)) {
						$keys[] = "`{$key}`";
						$values[] = ":{$key}";
						if($debug) {
							if(!$val) {
								$realValues[] = $numericFields[$key]
									? "0"
									: "NULL";
							} else {
								$realValues[] = "'{$val}'";
							}
						}
						if(!$val) {
							$cleanedRow[$key] = $numericFields[$key]
								? 0
								: NULL;
						} else {
							$cleanedRow[$key] = $val;
						}
					}
				}
				if(empty($keys) || empty($values)) {
					$this->error = "Either no keys or no values.";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				$keysString = implode(", ", $keys);
				$valuesString = implode(", ", $values);
				if($debug) {
					$realValuesString = implode(", ", $realValues);
				}
				$query =
					"INSERT INTO {$table}
					({$keysString}) VALUES ({$valuesString})";
				if($debug) {
					$this->realQuery =
						"INSERT INTO {$table}
						({$keysString}) VALUES ({$realValuesString})";
					// echo "\n\n{$query}";
					// echo "\n\n{$realQuery}";
					// exit;
				}
				$this->query($query);
				$this->bindArrayValues($cleanedRow);
				$this->execute();
				return(true);
			}

			public function update($table, $id, $row, $conditions = null, $indexKey = "id", $debug = false) {
				if(!$id && !$conditions) {
					// Enten id eller conditions skal være angivet
					return false;
				}
				if(!is_array($row)) {
					$this->error = "Row should be an array.";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				$fieldNames = $this->field_names($table);
				if(empty($fieldNames)) {
					$this->error = "Couldn't fetch the columns for the table {$table}";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				if($id > 0) {
					$conditionsString = "id = ".intval($id);
				} else {
					$conditionsString = $this->make_conditions($conditions);
				}
				$numericFields = $this->numeric_fields($table);
				if($debug) {
					$realValues = [];
				}
				$updates = $cleanedRow = [];
//				$realValues = [];
				foreach($row as $key=>$val) {
					if($key != $indexKey && in_array($key, $fieldNames)) {
						$updates[] = "`{$key}` = :{$key}";
						if($debug) {
							if(!$val) {
								$realValues[] = $numericFields[$key]
									? "0"
									: "NULL";
							} else {
								$realValues[] = "'{$val}'";
							}
						}
						if(!$val) {
							$cleanedRow[$key] = $numericFields[$key]
								? 0
								: NULL;
						} else {
							$cleanedRow[$key] = $val;
						}
					}
				}
				if(empty($updates)) {
					$this->error = "Intet at opdatere";
					$this->log_db_error($this->error,"Warning");
				}
				$query =
					"UPDATE {$table}
					SET ".implode(', ', $updates).
					" WHERE {$conditionsString}"
				;
				if($debug) {
					$this->realQuery =
						"UPDATE {$table}
						SET ".implode(", ", $realValues).
						" WHERE {$conditionsString}"
					;
				}
				$this->query($query);
				$this->bindArrayValues($cleanedRow);
				$this->execute();
				return(true);
			}

			public function delete($table, $id = 0, $conditions = null) {
				if(!$id && !$conditions) {
					$this->error = "No criteria given";
					$this->log_db_error($this->error, "Fatal");
					return(0);
				}
				$id = (int) $id;
				if($id > 0) {
					$conditionsString = "id = {$id}";
				} else {
					$conditionsString = $this->make_conditions($conditions);
				}
				$query =
					"DELETE FROM {$table}
					WHERE {$conditionsString}"
				;
				$this->query($query);
				$this->execute();
			}

			public function get_value($table, $id = 0, $column = "id", $order = null, $conditions = null) {
				$id = (int) $id;
				if($id > 0) {
					$conditionsString = "id = {$id}";
				} else {
					$conditionsString = $this->make_conditions($conditions);
				}
				$query =
					"SELECT {$column}
					FROM {$table}
					WHERE {$conditionsString} ";
				if($order) $query .= "ORDER BY {$order} ";
				$this->query($query);
				return($this->single(PDO::FETCH_COLUMN));
			}

			public function get_sum($table, $column, $conditions = null) {
				$conditionsString = $this->make_conditions($conditions);
				$query =
					"SELECT SUM({$column})
					FROM {$table}
					WHERE {$conditionsString}";
				$this->query($query);
				return($this->single(PDO::FETCH_COLUMN));
			}

			public function get_max($table, $field, $conditions = null) {
				$conditionsString = ($conditions
					? $this->make_conditions($conditions)
					: "1"
				);
				$query =
					"SELECT MAX({$field})
					FROM {$table}
					WHERE {$conditionsString}
					LIMIT 1 ";
					$this->query($query);
					return($this->single(PDO::FETCH_COLUMN));
			}

			public function get_min($table, $field, $conditions = null) {
				$conditionsString = ($conditions
					? $this->make_conditions($conditions)
					: "1"
				);
				$query =
					"SELECT min({$field})
					FROM {$table}
					WHERE {$conditionsString}
					LIMIT 1 ";
					$this->query($query);
					return($this->single(PDO::FETCH_COLUMN));
			}

			public function get_row($table, $id = 0, $conditions = null, $order = null, $select = "*") {
				$id = (int) $id;
				$selectString = $this->make_select($select);
				if($id > 0) {
					$conditionsString = "id = {$id}";
				} else if($conditions) {
					$conditionsString = $this->make_conditions($conditions);
				} else {
					$conditionsString = "1";
				}
				$query =
					"SELECT {$selectString}
					FROM {$table}
					WHERE {$conditionsString} ";
				if($order) {
					$query .= "ORDER BY {$order} ";
				}
				$this->query($query);
				return($this->single());
			}

			public function get_distinct($table, $column = "id", $order = null, $conditions = null, $limit = null) {
				$conditionsString = $this->make_conditions($conditions);
				// echo $conditionsString;
				// exit;
				if(!$order) {
					$query =
						"SELECT DISTINCT($column)
						FROM {$table}
						WHERE {$conditionsString} ";
						"ORDER BY {$column} ";
						if($limit) $query .= "LIMIT {$limit} ";
						// echo $query;
						// exit;
				} else {
					// tilføj felter i order til selected fields
					$orderFieldsArray = explode(" ", $order);
					$extraFields = [];
					foreach($orderFieldsArray as $orderField) {
						if(!in_array(strtoupper($orderField), ["ASC", "DESC"]) && $orderField != $column) {
							$extraFields[] = $orderField;
						}
					}
					$query =
						"SELECT DISTINCT($column)".(!empty($extraFields) ? ", ".implode(", ", $extraFields)." " : "").
						"FROM {$table}
						WHERE {$conditionsString}
						ORDER BY {$order} ";
						if($limit) $query .= "LIMIT {$limit} ";
					// echo $query;
					// exit;
				}
				$this->query($query);
				return($this->resultset(PDO::FETCH_COLUMN));
			}

			public function get_row_count($table, $conditions = null, $column = "id") {
				$conditionsString = $this->make_conditions($conditions);
				$query =
					"SELECT COUNT({$column}) FROM `{$table}` WHERE {$conditionsString}";
					$this->query($query);
					return(intval($this->single(PDO::FETCH_COLUMN)));
			}

			public function get_distinct_row_count($table, $conditions = null, $column = "id") {
				return($this->get_row_count($table, $conditions, $column));
			}

			public function exists($table, $id, $conditions, $column = "id") {
				if(intval($id) > 0) {
					$conditions = "id = ".intval($id);
				}
				if(intval($this->get_row_count($table, $conditions, $column)) > 0) {
					return true;
				}
				return false;
			}

			public function get_result($table, $order = null, $conditions = null, $select = "*", $limit = null) {
				$selectString = $this->make_select($select);
				$conditionsString = $this->make_conditions($conditions);
				$query =
					"SELECT {$selectString}
					FROM {$table}
					WHERE {$conditionsString}
					";
				// echo $query."<br />";
				if($order) $query .= "ORDER BY {$order} ";
				if($limit) $query .= "LIMIT {$limit} ";
				$this->sql = $query;
				$result = $this->dbh->query($query);
				return $result;
			}

			public function get_result_from_query($query) {
				$this->sql = $query;
				$result = $this->dbh->query($query);
				return $result;
			}

			public function get_rows($table, $order = null, $conditions = null, $select = "*", $limit = null) {
				$selectString = $this->make_select($select);
				$conditionsString = $this->make_conditions($conditions);
				$query =
					"SELECT {$selectString}
					FROM {$table}
					WHERE {$conditionsString}
					";
				// echo $query."<br />";
				if($order) $query .= "ORDER BY {$order} ";
				if($limit) $query .= "LIMIT {$limit} ";
				$this->query($query);
				return($this->resultset());
			}

			public function increment($table, $id = 0, $field, $conditions = null, $increment = 1) {
				$id = intval($id);
				if($id > 0) {
					$conditionsString = "id = {$id}";
				} else if($conditions) {
					$conditionsString = $this->make_conditions($conditions);
				} else {
					return false;
				}
				$query = "UPDATE {$table} SET {$field} = {$field} + {$increment} WHERE {$conditionsString} LIMIT 1";
				$this->query($query);
				return($this->execute());
			}

			public function do_query($query) {
				$this->query($query);
				if(stripos($this->sql, "SELECT") !== false) {
					// echo "Her: |".stripos($this->sql, "SELECT")."|";
					return($this->resultset());
				}
				$this->execute();
			}

			public function get_rows_group_by($table, $groupBy = null, $conditions = null, $select = "*", $order = null, $limit = null) {
				$selectString = $this->make_select($select);
				$conditionsString = $this->make_conditions($conditions);
				$query =
					"SELECT {$selectString}
					FROM {$table}
					WHERE {$conditionsString}
					";
				if($groupBy) $query .= "GROUP BY {$groupBy} ";
				if($order) $query .= "ORDER BY {$order} ";
				if($limit) $query .= "LIMIT {$limit} ";
			}

			public function get_values_indexed($table, $valueField, $conditions = null, $order = null, $limit = null, $indexField = "id") {
				$rows = $this->get_rows($table, $order, $conditions, "{$indexField}, {$valueField}", $limit);
				if(empty($rows)) {
					return([]);
				}
				$returnRows = [];
				foreach($rows as $row) {
					$returnRows[$row[$indexField]] = $row[$valueField];
				}
				return($returnRows);
			}

			public function get_rows_indexed($table, $order = null, $conditions = null, $select = "*", $limit = null) {
				$rows = $this->get_rows($table, $order, $conditions, $select, $limit);
				if(empty($rows)) {
					return([]);
				}
				$returnRows = [];
				foreach($rows as $row) {
					$returnRows[$row['id']] = $row;
				}
				return($returnRows);
			}

			public function get_distinct_join($table1, $table2, $table1Idfield, $table2Idfield, $conditions, $column, $order, $limit = null) {
				$conditionsString = $this->make_conditions($conditions);
				if(!stristr($column, ".")) { // no table in $column
					$column = $table1.".".$column;
				}
				$query =
					"SELECT DISTINCT($column)
					FROM {$table1}
					INNER JOIN {$table2} ON {$table1}.{$table1Idfield} = {$table2}.{$table2Idfield}
					WHERE {$conditions} ";
				if($order) $query .= "ORDER BY {$order} ";
				if($limit) $query .= "LIMIT {$limit} ";
				$this->query($query);
				return($this->resultset(PDO::FETCH_COLUMN));
			}

			public function get_group_by_join($table1, $table2, $table1Idfield, $table2Idfield, $conditions, $groupBy, $select, $order, $limit = null) {
				$conditionsString = $this->make_conditions($conditions);
				$query =
					"SELECT {$select}
					FROM {$table1}
					INNER JOIN {$table2} ON {$table1}.{$table1Idfield} = {$table2}.{$table2Idfield}
					WHERE {$conditions}
					GROUP BY {$groupBy} ";
				if($order) $query .= "ORDER BY {$order} ";
				if($limit) $query .= "LIMIT {$limit} ";
				$this->query($query);
				return($this->resultset());
			}


			public function get_rows_join($table1, $table2, $table1Idfield, $table2Idfield, $conditions, $select, $order, $limit = null) {
				return(
					$this->get_rows_join_multi([$table1, $table2], [$table1Idfield, $table2Idfield], $conditions, $select, $order, $limit)
				);
			}

			public function get_row_count_multi($tables, $idFields, $conditions, $select = null) {
				if(!is_array($tables) || !is_array($idFields)) {
					$this->error = "The first two parameters MUST be arrays!";
					$this->log_db_error($this->error, "Warning");
					return(0);
				}
				if(empty($tables) || empty($idFields) || count($tables) != count($idFields)) {
					$this->error = "The two arrays need to be of equal length and not empty!";
					$this->log_db_error($this->error, "Warning");
					return(0);
				}
				if(!$select) {
					$select = "{$tables[0]}.*";
				}
				$selectString = $this->make_select($select);
				$conditionsString = $this->make_conditions($conditions);
				$joinString = "";
				for($i = 1; $i < count($tables); $i++) {
					$joinString .= "INNER JOIN `".$tables[$i]."` ON `".$tables[0]."`.`{$idFields[0]}` = `".$tables[$i]."`.`".$idFields[$i]."` ";
				}
				$query =
					"SELECT COUNT({$selectString})
					FROM `{$tables[0]}`
					{$joinString}
					WHERE {$conditionsString} ";
				$this->query($query);
				return($this->single(PDO::FETCH_COLUMN));
			}
			
			public function get_row_multi($tables, $idFields, $id = 0, $select = null, $conditions = null, $order = null, $joinType = "INNER") {
				if(!is_array($tables) || !is_array($idFields)) {
					$this->error = "The first two parameters MUST be arrays!";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				if(empty($tables) || empty($idFields) || count($tables) != count($idFields)) {
					$this->error = "The two arrays need to be of equal length and not empty!";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				if(!$select) {
					$select = "{$tables[0]}.*";
				}
				$selectString = $this->make_select($select);
				if($id > 0) {
					$conditionsString = "{$tables[0]}.id = {$id}";
				} else if($conditions) {
					$conditionsString = $this->make_conditions($conditions);
				} else {
					$conditionsString = "1";
				}
				$joinString = "";
				for($i = 1; $i < count($tables); $i++) {
					$joinString .= "{$joinType} JOIN `".$tables[$i]."` ON `".$tables[0]."`.`{$idFields[0]}` = `".$tables[$i]."`.`".$idFields[$i]."` ";
				}
				$query =
					"SELECT {$selectString}
					FROM `{$tables[0]}`
					{$joinString}
					WHERE {$conditionsString} ";
				if($order) $query .= "ORDER BY {$order} ";
				$this->query($query);
				return($this->single());
			}
			
			public function get_rows_join_multi($tables, $idFields, $conditions, $select = null, $order = null, $limit = null, $joinType = "INNER") {
				if(!is_array($tables) || !is_array($idFields)) {
					$this->error = "The first two parameters MUST be arrays!";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				if(empty($tables) || empty($idFields) || count($tables) != count($idFields)) {
					$this->error = "The two arrays need to be of equal length and not empty!";
					$this->log_db_error($this->error, "Warning");
					return(false);
				}
				if(!$select) {
					$select = "{$tables[0]}.*";
				}
				if(!$order) {
					$order = "{$tables[0]}.id ASC";
				}
				$selectString = $this->make_select($select);
				$conditionsString = $this->make_conditions($conditions);
				$joinString = "";
				for($i = 1; $i < count($tables); $i++) {
					$joinString .= "{$joinType} JOIN `".$tables[$i]."` ON `".$tables[0]."`.`{$idFields[0]}` = `".$tables[$i]."`.`".$idFields[$i]."` ";
				}
				$query =
					"SELECT {$selectString}
					FROM `{$tables[0]}`
					{$joinString}
					WHERE {$conditionsString} ";
				if($order) $query .= "ORDER BY {$order} ";
				if($limit) $query .= "LIMIT {$limit} ";
				$this->query($query);
				return($this->resultset());
			}

			public function get_ids($table, $order = null, $conditions = null, $limit = null, $field="id") {
				$conditionsString = $this->make_conditions($conditions);
				$query =
					"SELECT {$field}
					FROM {$table}
					WHERE {$conditionsString}
					";
				if($order) $query .= "ORDER BY {$order} ";
				if($limit) $query .= "LIMIT {$limit} ";
				$this->query($query);
				return($this->resultset(PDO::FETCH_COLUMN));
			}

			private function bindArrayValues($array) {
				foreach($array as $key => $value) {
					if(is_array($key) || is_array($value)) {
						$this->error = "Array forsøgt sendt til bind!";
						$this->log_db_error($this->error, "Fatal");
					}
					$this->bind(":$key",$value);
				}
			}

			public function lastid() {
				return($this->lastInsertId());
			}

			private function make_conditions($conditions) {
				if(!$conditions) {
					$conditionsString = "1";
				} else {
					$conditionsString = is_array($conditions)
						? implode(" AND ",$conditions)
						: $conditions;
				}
				return($conditionsString);
			}

			function make_select($select) {
				return(is_array($select)
					? implode(", ",$select)
					: $select);
			}

			private function log_db_error($error, $class) {
				$debugstring = date("Y-m-d H:i:s").' '
					.$class."\n".(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "")
					."\n".$error."\n"
					.$this->sql."\n"
					.(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "")."\n\n";
				if(defined(DISPLAY_DEBUG) && DISPLAY_DEBUG) {
					echo str_replace("\n", "<br />", $debugstring);
				} elseif(defined(ERROR_LOGFILE)) {
					// echo "logging...<br />";
					$fh = fopen(ERROR_LOGFILE, "a");
					fwrite($fh, $debugstring);
					fclose($fh);
				}
			}

			function log_debug($class = "Log") {
				$debugstring = date("Y-m-d H:i:s").' '.$class."\n".(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "")
					."\n".$this->sql."\n".(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "")
					."\n\n";
				if(DEBUG_LOGFILE) {
					$fh = fopen(DEBUG_LOGFILE,"a");
					fwrite($fh, $debugstring);
					fclose($fh);
				}
			}

			public function bind($param, $value, $type = null) {
				if (is_null($type)) {
					switch (true) {
						case is_int($value):
							$type = PDO::PARAM_INT;
							break;
						case is_bool($value):
							$type = PDO::PARAM_BOOL;
							break;
						case is_null($value):
							$type = PDO::PARAM_NULL;
							break;
						default:
							$type = PDO::PARAM_STR;
					}
				}
				$this->stmt->bindValue($param, $value, $type);
			}

			public function query($query){
				$this->sql = $query;
				try {
					if(DB_DEBUG) {
						$this->log_debug("Log");
					}
					return $this->stmt = $this->dbh->prepare($query);
				} catch(PDOException $e) {
					$this->log_db_error($e->getMessage(), "Fatal");
				}
			}

			private function execute(){
				try {
					if(DB_DEBUG) {
						$this->log_debug("Log");
					}
					return $this->stmt->execute();
				} catch(PDOException $e) {
					$this->log_db_error($e->getMessage(), "Fatal");
				}
			}

			private function resultset($modifier = PDO::FETCH_ASSOC) {
				$this->execute();
				return $this->stmt->fetchAll($modifier);
			}

			private function single($modifier =  PDO::FETCH_ASSOC){
				$this->execute();
				return $this->stmt->fetch($modifier );
			}

			public function lastInsertId(){
					return $this->dbh->lastInsertId();
			}

			public function beginTransaction(){
				return $this->dbh->beginTransaction();
			}

			public function endTransaction(){
				return $this->dbh->commit();
			}

			public function cancelTransaction(){
				return $this->dbh->rollBack();
			}

			public function debugDumpParams(){
				return $this->stmt->debugDumpParams();
			}
		} // class db
	} // if(!defined('DB'))
