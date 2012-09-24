<?php
/**
 * file		: Functions.php
 * created	: 02 April 2012
 *
 * @package	: equity
 * @author	: Charles
 */

class Functions {
    
    public static function getWeather() {
        $currentHour = date('H');
        switch ($currentHour) {
            case $currentHour <= 11:
                return 'Pagi';
                break;
            
            case $currentHour < 15:
                return 'Siang';
                break;
            
            case $currentHour >= 15:
                return 'Sore';
                break;
            
            default:
                return 'Pagi/Siang/Malam';
                break;
        }
    }
    
    /**
     * getAge 
     * Method to calculate the age
     * @param date $dob 
     * @return int Age
     */
    public static function getAge($dob) {
        $date1 = new DateTime($dob);
        $date2 = new DateTime(date('Y-m-d'));
        return $date1->diff($date2)->format('%y');
    }
    
    /**
     * getListValues
     * Method to get list values from db
     * @param int $groupId Group ID
     * @return array of values
     */
    public static function getListValues($groupId, $listCode = NULL) {
        $query = sprintf("SELECT list_values.list_code,
                          list_values.list_data
                          FROM list_values
                          WHERE list_values.group_id=%d
                          AND list_values.is_enabled=1",
                          $groupId);
        if ($listCode !== null)
            $query .= sprintf(" AND list_values.list_code=%d", $listCode);
        $query .= sprintf(" ORDER BY list_values.sort_index ASC");
        MySQL::setQuery($query);
        return MySQL::fetchRows();
    }
    
    /**
     * getAutocompleteProvince
     * Method to get Province with JSON Encode 
     */
    public static function getAutocompleteProvince() {
        $query = sprintf("SELECT list_values.list_data
                          FROM list_values
                          WHERE list_values.group_id=17
                          AND list_values.is_enabled=1
                          AND list_values.list_data LIKE ('%%%s%%')
                          ORDER BY list_values.list_data ASC
                          LIMIT 5",
                          MySQL::escapeString($_GET['term']));
        MySQL::setQuery($query);
        if (MySQL::getNumRows()) {
            $tags = array();
            foreach (MySQL::fetchRows() as $key => $value) {
                $tags[] = $value['list_data'];
            }
            return json_encode($tags);
        }
        return;
    }
}
?>