<?php
/**
 * file		: MySQL.php
 * created	: 09 March 2012
 *
 * @package	: lig
 * @author	: Charles
 */

abstract class MySQL implements Database {
    
    private static $_host = null;
    private static $_port = null;
    private static $_user = null;
    private static $_pass = null;
    private static $_db = null;
    
    private static $currentConnection = null;
    private static $link = null; // Connection Link
    private static $query = null; // SQL Query
    
    /**
     * Define current connection (dev, beta, prod)
     * @param String $connection = Connection name (Default as dev)
     */
    public static function setConnection($connection = 'dev') {
        self::$currentConnection = $connection;
        switch ($connection) {
            case 'dev':
                self::$_host = '127.0.0.1';
                self::$_port = 3306;
                self::$_user = 'root';
                self::$_pass = 'marlboro';
                self::$_db = 'ts_lig';
                break;
            
            case 'beta':
                self::$_host = '';
                self::$_port = 3306;
                self::$_user = '';
                self::$_pass = '';
                self::$_db = '';
                break;
            
            case 'prod':
                self::$_host = '';
                self::$_port = 3306;
                self::$_user = '';
                self::$_pass = '';
                self::$_db = '';
                break;
            
            default:
                self::$_host = '127.0.0.1';
                self::$_port = 3306;
                self::$_user = 'root';
                self::$_pass = 'marlboro';
                self::$_db = 'ts_lig';
                break;
        }
    }
    
    /**
     * Method for get current Connection
     * @return String Connection name
     */
    public static function getConnection() {
        return self::$currentConnection;
    }
    
    /**
     * Method for open connection to Database
     * @return Object Link
     */
    public static function connect() {
        self::$link = mysqli_connect(self::$_host,
                                     self::$_user,
                                     self::$_pass,
                                     self::$_db,
                                     self::$_port);
        if (!self::$link) {
            die(
                'Failed while connect to Database '. self::$_db .' at '. self::$_host .':'. self::$_port .'.<br />
                Current connection is : '. self::getConnection() .'<br />
                Message : '. self::showErrors()
            );
        }
        return self::$link;
    }
    
    /**
     * Method for set SQL Query
     * @param string $query = SQL Query
     */
    public static function setQuery($query) {
        self::$query = $query;
    }
    
    /**
     * Method for get SQL Query
     * @param boolean $html_view Set true if return html view, replace \n to <br />
     * @return String = SQL Query
     */
    public static function getQuery($html_view = false) {
        if ($html_view) {
            return str_replace("\n", '<br />', self::$query);
        }
        return self::$query;
    }
    
    /**
     * Method for execute Query
     */
    public static function execute() {
        return mysqli_query(self::connect(), self::$query, MYSQLI_STORE_RESULT);
    }
    
    /**
     * Method for get query Data
     * @return array = Array of Data
     */
    public static function fetchRows() {
        $data = array();
        $exec = self::execute();
        while ($row = mysqli_fetch_assoc($exec)) {
            $data[] = $row;
        }
        return $data;
    }
    
    /**
     * Method for get query data (single row)
     * @return array = Array of Data
     */
    public static function fetchRow() {
        return mysqli_fetch_assoc(self::execute());
    }
    
    /* Method for return number of rows from SQL Query
     * @return int = Number of Rows
     */
    public static function getLastId() {
        return mysqli_insert_id(self::connect());
    }
    
    /* Method for return Last Insert ID
     * @return int = Last Insert ID
     */
    public static function getNumRows() {
        return mysqli_num_rows(self::execute());
    }
    
    /* Method for Show SQL Last Error
     */
    public static function showErrors() {
        
    }
    
    /* Method for disconnect from SQL Server
     */
    public static function disconnect() {
        return mysqli_close(self::connect());
    }
}
?>