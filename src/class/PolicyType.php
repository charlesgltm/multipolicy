<?php

/**
 * Description of PolicyType
 *
 * @author Charles
 */
class PolicyType {
    private $id = null;
    private $type = null;
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return (!is_null($this->id)) ? $this->id : 'Not set';
    }
    
    public function getPolicyTypeDetails() {
        $query = sprintf("SELECT pr.*
                          FROM premium_relationship pr
                          WHERE is_enabled=1
                          AND pr.id=%d",
                          $this->id);
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data;
    }
    
    public function getOptions() {
        $query = sprintf("SELECT pr.id,
                          pr.relationship_name
                          FROM premium_relationship pr
                          WHERE is_enabled=1
                          ORDER BY sort_order ASC");
        MySQL::setQuery($query);
        $options = '<option value="">--</option>';
        foreach (MySQL::fetchRows() as $key => $value) {
            $selected = ($this->id == $value['id']) ? ' selected="selected"' : '';
            $options .= '<option value="'. $value['id'] .'"'. $selected .'>'. $value['relationship_name'] .'</option>';
        }
        return $options;
    }
    
    public function getDiscount() {
        $query = sprintf("SELECT relationship_discount AS discount
                          FROM premium_relationship
                          WHERE id=%d",
                          $this->id);
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data['discount'];
    }
    
    public function getRules() {
        $info = array();
        switch ($this->id) {
            case 1:
                $info[] = 'Hanya boleh memiliki 1 tertanggung.';
                break;
            
            case 2:
                $info[] = 'Tertanggung merupakan pasangan suami istri.';
                $info[] = 'Jenis kelamin harus berbeda.';
                break;
            
            case 3:
                $info[] = 'Tiap polis mempunyai hubungan keluarga.';
                $info[] = 'Minimal 3 polis.';
                $info[] = 'Jenis kelamin terdiri dari Pria dan Wanita.';
                break;
        }
        return $info;
    }
    
    public function validate(Customer $customer) {
        $customer->setId($_REQUEST['custId']);
        $info = '';
        switch ($this->getId()) {
            case 1: // Single
                $query = sprintf("SELECT COUNT(policies.id) as count
                                  FROM policies
                                  WHERE policies.customer_id=%d",
                                  $customer->getId());
                MySQL::setQuery($query);
                $num = MySQL::fetchRow();
                if ($num['count'] >= 1) {
                    $info = 'Policy Type Single hanya boleh memiliki 1 tertanggung.<br />Harap hapus sebagian tertanggung yang telah terdaftar.';
                    break;
                }
                break;
            
            case 2: // Couple
                $query = sprintf("SELECT COUNT(policies.id) as count
                                  FROM policies
                                  WHERE policies.customer_id=%d",
                                  $customer->getId());
                MySQL::setQuery($query);
                $num = MySQL::fetchRow();
                
                $query = sprintf("SELECT DISTINCT(policies.gender)
                                  FROM policies
                                  WHERE policies.customer_id=%d",
                                  $customer->getId());
                MySQL::setQuery($query);
                $gender = MySQL::getNumRows();
                if (($num['count'] > 2) || ($gender == 1 && $num['count'] > 2)) {
                    $info = 'Policy Type Couple harus terdiri dari 1 Pria dan 1 Wanita.<br />Harap hapus sebagian tertanggung yang telah terdaftar.';
                    break;
                }
                break;
        }
        return $info;
    }
}

?>
