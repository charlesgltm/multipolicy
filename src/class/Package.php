<?php

/**
 * Description of Package
 *
 * @author Charles
 */
class Package {
    private $id = null;
    private $package = null;
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return (!is_null($this->id)) ? $this->id : 'Not set';
    }
    
    public function getPackageDetails() {
        $query = sprintf("SELECT packages.*
                          FROM packages
                          WHERE packages.is_enabled=1
                          AND packages.id=%d",
                          $this->id);
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data;
    }
    
    public function getOptions() {
        $query = sprintf("SELECT packages.id,
                          packages.package_name
                          FROM packages
                          WHERE packages.is_enabled=1
                          ORDER BY packages.sort_order ASC");
        $options = '<option value="">--</option>';
        MySQL::setQuery($query);
        foreach (MySQL::fetchRows() as $key => $value) {
            $selected = ($this->id == $value['id']) ? ' selected="selected"' : '';
            $options .= '<option value="'. $value['id'] .'" '. $selected .'>'. $value['package_name'] .'</option>';
        }
        return $options;
    }
}

?>
