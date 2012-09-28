<?php

/**
 * Description of Gender
 *
 * @author Charles
 */
class Gender {
    private $id = null;
    private $gender = null;
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return (!is_null($this->id)) ? $this->id : 'Not set';
    }
    
    public function setGender($gender) {
        $this->gender = $gender;
    }
    
    public function getGender() {
        return $this->gender;
    }
    
    public function getGenderDetails() {
        $query = sprintf("SELECT list_values.*
                          FROM list_values
                          JOIN list_groups ON list_groups.id=list_values.group_id
                          WHERE list_groups.group_name='GENDER'
                          AND list_values.list_code=%d",
                          $this->id);
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data;
    }
    
    public function getOptions($data = 'code') {
        $data = ($data == 'code') ? 'list_code' : 'list_data';
        $query = sprintf("SELECT list_values.list_code,
                          list_values.list_data
                          FROM list_values
                          JOIN list_groups ON list_groups.id=list_values.group_id
                          WHERE list_groups.group_name='GENDER'
                          ORDER BY list_values.sort_index ASC");
        $options = '<option value="">--</option>';
        MySQL::setQuery($query);
        foreach (MySQL::fetchRows() as $key => $value) {
            $selected = ($this->gender == $value['list_data']) ? ' selected="selected"' : '';
            $options .= '<option value="'. $value[$data] .'" '. $selected .'>'. $value['list_data'] .'</option>';
        }
        return $options;
    }
}

?>
