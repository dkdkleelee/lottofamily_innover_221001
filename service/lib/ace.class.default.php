<?

	class AceSolution {

		var $solution_root;
		var $modules_arr;

		var $db;
		var $tables;

		function AceSolution() {

			//$this->solution_root = $root;
			$full_path = getcwd();
			$document_root = $_SERVER['DOCUMENT_ROOT'];
			$tmp_path = explode("ace_solution/", $full_path);
			$this->solution_root = $tmp_path[0]."/ace_solution/";
			$this->solution_root = $document_root."/ace_solution/";		// 솔루션 루트를 document root로 고정

			include_once($this->solution_root."/lib/ace.lib.inc.php");

			$this->getModules();

			//register autoload
			spl_autoload_register(array($this, 'autoLoaderCommon'));
		}

		function getModules() {
			$fp = dir($this->solution_root."/modules");
			while (false !== ($entry = $fp->read())) {
				if($entry != "." && $entry != "..") {
					$this->modules_arr[$entry] = array(
														"path" => $this->solution_root."modules/".$entry,
														"config" => parse_ini_file($this->solution_root."modules/".$entry."/config.php", true)
											);

					$this->tables[$entry] = $this->modules_arr[$entry]['config']['table'];
				}
			}
			
		}

		function getAdminPage($module_name, $page_name) {
			global $_url;
			include_once($this->solution_root."/".$module_name."/admin/".$page_name);
		}

		function getUserPage($page_name) {
			//include 
		}

		function autoLoaderCommon($class) {
			
			include_once $this->solution_root . "lib/" . $class . '.class.php';
		}
	}