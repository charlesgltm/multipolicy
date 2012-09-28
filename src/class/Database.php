<?php
/**
 * file		: Database.php
 * created	: 13 July 2011
 *
 * @package	: 
 * @author	: Charles
 */

interface Database {
    
    /**
     * Method for set Connection
     * @param String $connection = Connection name (Default as dev)
     */
    public static function setConnection($connection = 'dev');
    
    /**
     * Method for get current Connection
     * @return String Connection name
     */
    public static function getConnection();
    
    /**
     * Method for connecting to SQL
     */
    public static function connect();
    
    /**
     * Method for set SQL Query
     * @param string $query = SQL Query
     */
    public static function setQuery($query);
    
    /**
     * Method for get SQL Query
     * @return String = SQL Query
     */
    public static function getQuery();
    
    /**
     * Method for escapes special character from Query
     * @param string Query
     */
    public static function escapeString($string);
    
    /**
     * Method for execute Query
     */
    public static function execute();
    
    /**
     * Method for get query Data
     * @return array = Array of Data
     */
    public static function fetchRows();
    
    /**
     * Method for get query data (single row)
     * @return array = Array of Data
     */
    public static function fetchRow();
    
    /**
     * Method for return Last Insert ID
     * @return int = Last Insert ID
     */
    public static function getLastId();
    
    /**
     * Method for return number of rows from SQL Query
     * @return int = Number of Rows
     */
    public static function getNumRows();
    
    /* Method for Show SQL Last Error
     */
    public static function showErrors();
    
    /**
     * Method for disconnect from SQL Server
     */
    public static function disconnect();
}
?>