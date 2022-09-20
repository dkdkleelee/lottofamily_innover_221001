<?
/*  */
class DB {
	private $conn;
	public $db,$result;

	function __construct($host, $user, $pass, $db, $report=false) {
		$this->db = $db;
		$this->conn = mysqli_connect($host, $user, $pass, $db);
		if(!$this->conn) die("Connection failed error#".mysqli_errno($this->conn)." : ".mysqli_error($this->conn));
		mysqli_select_db($this->conn, $db) || die("Database selection failed error#".mysqli_errno($this->conn)." : ".mysqli_error($this->conn));

		$this->result = array();
	}

	private function checkType($type) {
		return true;
	}

	public function query($q, $idx=0, $pre=false) {
		if($pre == false) {
			$this->result[$idx] = @mysqli_query($this->conn, $q);
			if(!$this->result[$idx]) {
				echo "Query failed error#".mysqli_errno($this->conn)." : ".mysqli_error($this->conn)."(".$q.")";
				exit;
			}
		}
	}

	public function numRows($idx=0) {
		return mysqli_num_rows($this->result[$idx]);
	}

	public function result() {
		return true;
	}

	public function insertId() {
		return mysqli_insert_id($this->conn);
	}

	public function fetch($idx=0) {
		return ($this->result[$idx]) ? mysqli_fetch_array($this->result[$idx]) : false;
	}

	public function fetchAssoc($idx=0) {
		return ($this->result[$idx]) ? mysqli_fetch_assoc($this->result[$idx]) : false;
	}

	public function fetchFields($idx=0) {
		return mysqli_fetch_field($this->result[$idx]);
	}

	public function numFields($idx=0) {
		return mysqli_num_fields($this->result[$idx]);
	}

	public function fetchRow($q, $idx='99999') {
		$this->query($q,  $idx);
		return ($this->result[$idx]) ? mysqli_fetch_array($this->result[$idx]) : false;
	}

	public function serverInfo() {
		return mysqli_get_server_info($this->conn);
	}

	public function listTables($db, $idx=0) {
		$this->result[$idx] = mysqli_list_tables($db, $this->conn);
		return $idx;
	}

	public function listFields($table) {
		$fields = mysqli_list_fields($this->db, $table, $this->conn);
		$columns = mysqli_num_fields($fields);
		for($i=0; $i<$columns; $i++) {
			$field_name[] = mysqli_field_name($fields, $i);
		}
		return $field_name;
	}

	public function innoDB() {
		if(!mysqli_query($this->conn, "SET autocommit=false")) {
			echo "Setting autocommit to false failed error#".mysqli_errno()." : ".mysqli_error();
			exit;
		}
	}

	public function commit() {
		if(!mysqli_query( $this->conn, "commit")) {
			echo "Commit failed error#".mysqli_errno()." : ".mysqli_error();
			exit;
		}
	}

	public function rollback() {
		if(!mysqli_query($this->conn, "rollback")) {
			echo "Rollback failed error#".mysqli_errno()." : ".mysqli_error();
			exit;
		}
	}

	public function bind() {
		return true;
	}

	public function execute() {
		if(!mysqli_query($this->conn, "execute")) {
			echo "Execute failed error#".mysqli_errno()." : ".mysqli_error();
			exit;
		}
	}

	public function sClose() {
		return true;
	}

	public function close() {
		mysqli_close($this->conn);
	}
}
// end class
?>