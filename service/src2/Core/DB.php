<?php namespace Acesoft\Core;

use \MysqliDb;
use \dbObject;

Class DB
{
	private static $dbInstance = NULL;
    private static $dbObject = NULL;
	public static $host;
	public static $username;
	public static $password;
	public static $database;

	public function __construct() { }
	public function __clone()  { }

	public static function getInstance()
	{
		if (self::$dbInstance === NULL)
		{
			self::$dbInstance = new self;
			self::connect();
		}
		return self::$dbInstance;
	}

	public static function getNewInstance() {
		$config = require(dirname(__FILE__)."/../../config/config.php");

		
		$db_tmp = new MysqliDb ($config['connections']['db']['host'],
            $config['connections']['db']['username'], $config['connections']['db']['password'], $config['connections']['db']['database']);
		dbObject::autoload("models");
		return $db_tmp;
	}

	private static function connect()
	{
		$config = require(dirname(__FILE__)."/../../config/config.php");

		
		self::$dbInstance = new MysqliDb ($config['connections']['db']['host'],
            $config['connections']['db']['username'], $config['connections']['db']['password'], $config['connections']['db']['database']);
        dbObject::autoload("models");
	}

    public static function getModel($table)
    {

        self::$dbObject = dbObject::table($table);

        return self::$dbObject;
    }

}