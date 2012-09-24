<?php

class PremiumCalculator {
    
    private $gender = null;
    private $package = null;
    private $jobType = null;
    
    private $feeStamp = array(9000, 12000);
    private $feePolicy = 28000;
    private $feeAdmin = 129298;
    
    public function setPremiInfo($gender, $package, $jobType) {
        $this->gender = $gender;
        $this->package = $package;
        $this->jobType = $jobType;
    }
    
    private function getComboGender() {
        $query = sprintf("SELECT list_values.list_code,
                          list_values.list_data
                          FROM list_values
                          JOIN list_groups ON list_groups.id=list_values.group_id
                          WHERE list_groups.group_name='GENDER'
                          ORDER BY list_values.sort_index ASC");
        $options = '<option value="">--</option>';
        MySQL::setQuery($query);
        foreach (MySQL::fetchRows() as $key => $value) {
            $options .= '<option value="'. $value['list_code'] .'">'. $value['list_data'] .'</option>';
        }
        return $options;
    }
    
    private function getComboJobs() {
        $query = sprintf("SELECT job_grades.id,
                          job_grades.job_type
                          FROM job_grades
                          ORDER BY job_grades.job_type ASC");
        $options = '<option value="">--</option>';
        MySQL::setQuery($query);
        foreach (MySQL::fetchRows() as $key => $value) {
            $options .= '<option value="'. $value['id'] .'">'. ucwords($value['job_type']) .'</option>';
        }
        return $options;
    }
    
    private function getComboPackages() {
        $query = sprintf("SELECT packages.id,
                          packages.package_name
                          FROM packages
                          WHERE packages.is_enabled=1
                          ORDER BY packages.sort_order ASC");
        $options = '<option value="">--</option>';
        MySQL::setQuery($query);
        foreach (MySQL::fetchRows() as $key => $value) {
            $options .= '<option value="'. $value['id'] .'">'. $value['package_name'] .'</option>';
        }
        return $options;
    }
    
    private function getComboPremiumRelationship() {
        $query = sprintf("SELECT pr.*
                          FROM premium_relationship pr
                          WHERE is_enabled=1
                          ORDER BY sort_order ASC");
        MySQL::setQuery($query);
        foreach (MySQL::fetchRows() as $key => $value) {
            $options .= '<option value="'. $value['relationship_discount'] .'">'. $value['relationship_name'] .'</option>';
        }
        return $options;
    }
    
    public function showCalculatorForm() {
        echo '
        <div id="comments-dialog"></div>
        <form name="premiumcalculator" id="premiumcalculator">
            <div id="list-customers">
                <div id="customer-head">
                    <div id="add-customer">
                        <span id="new-customer"><img src="'. __TPL_IMG__ .'/icon-add.gif" align="absmiddle" /> Add Customer</span>
                    </div>
                    <div id="customer-relationship">
                        Relationship : <select name="discount" onchange="PremiumCalculator.calculateTotal()">'. self::getComboPremiumRelationship() .'</select>
                    </div>
                </div>
                <table class="customers" border="1" width="100%">
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Gender</th>
                        <th width="45%">Job Type</th>
                        <th width="10%">Package</th>
                        <th width="20%">Premium (Rp.)</th>
                        <th width="5%">Acc.</th>
                        <th width="5%">Del.</th>
                    </tr>
                </table>
                <table id="customer1" border="1" width="100%">
                    <tr>
                        <td width="5%" align="center"><input type="text" name="displaynumber[]" id="calc-text" style="text-align: center" value="1." readonly="readonly" size="1" /></td>
                        <td width="10%"><select name="gender_code[]" onchange="PremiumCalculator.calculate(1, 1)" class="input-text">'. self::getComboGender() .'</select></td>
                        <td width="45%"><select name="job[]" onchange="PremiumCalculator.calculate(1, 1)" class="input-text" style="width: 98%">'. self::getComboJobs() .'</select></td>
                        <td width="10%"><select name="packages[]" onchange="PremiumCalculator.calculate(1, 1)" class="input-text" style="width: 98%">'. self::getComboPackages() .'</select></td>
                        <td width="20%" align="right"><span id="hiddenNum1" style="display: none">1</span><input type="text" name="premiumCost[]" id="calc-text" style="text-align: right" value="0" readonly="readonly" /></td>
                        <td width="5%" align="center"><span id="iconCustomer1"></span></td>
                        <td width="5%" align="center"></td>
                    </tr>
                </table>
            </div>
            <div id="totalpremium">
                <table border="1" class="customers" width="100%">
                    <tr>
                        <th width="70%" colspan="4" align="right">Subtotal</th>
                        <th width="20%" align="right"><span id="subtotal">0</span></th>
                        <th width="10%" colspan="2"></th>
                    </tr>
                    <tr>
                        <th width="70%" colspan="4" align="right">Disc <span id="displaydiscount">0</span>%</th>
                        <th width="20%" align="right"><span id="discount">0</span></th>
                        <th width="10%" colspan="2"></th>
                    </tr>
                    <tr>
                        <th width="70%" colspan="4" align="right">Total</th>
                        <th width="20%" align="right"><span id="total">0</span></th>
                        <th width="10%" colspan="2"></th>
                    </tr>
                    <tr>
                        <th width="100%" colspan="7">&nbsp;</th>
                    </tr>
                    <tr>
                        <th width="70%" colspan="4" align="right">Biaya Polis</th>
                        <th width="20%" align="right"><span id="feepolicy">0</span></th>
                        <th width="10%" colspan="2"></th>
                    </tr>
                    <tr>
                        <th width="70%" colspan="4" align="right">Biaya Meterai</th>
                        <th width="20%" align="right"><span id="feestamp">0</span></th>
                        <th width="10%" colspan="2"></th>
                    </tr>
                    <tr>
                        <th width="70%" colspan="4" align="right">Biaya Admin</th>
                        <th width="20%" align="right"><span id="feeadmin">0</span></th>
                        <th width="10%" colspan="2"></th>
                    </tr>
                    <tr>
                        <th width="70%" colspan="4" align="right">Grand Total</th>
                        <th width="20%" align="right"><span id="grandtotal">0</span></th>
                        <th width="10%" colspan="2"></th>
                    </tr>
                </table>
                <table id="installment" border="1" width="80%">
                    <thead>
                        <tr>
                            <th>Installment</th>
                            <th>First Installment</th>
                            <th>Next Installment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>1x</th>
                            <td><span id="firstInstallment1"></span></td>
                            <td><span id="nextInstallment1"></span></td>
                        </tr>
                        <tr>
                            <th>3x</th>
                            <td><span id="firstInstallment3"></span></td>
                            <td><span id="nextInstallment3"></span></td>
                        </tr>
                        <tr>
                            <th>6x</th>
                            <td><span id="firstInstallment6"></span></td>
                            <td><span id="nextInstallment6"></span></td>
                        </tr>
                        <tr>
                            <th>12x</th>
                            <td><span id="firstInstallment12"></span></td>
                            <td><span id="nextInstallment12"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
        ';
    }
    
    public function addNewCustomer() {
        echo '
                <table id="customer'. $_GET['num'] .'" border="1" width="100%">
                    <tr>
                        <td width="5%" align="center"><input type="text" name="displaynumber[]" id="calc-text" style="text-align: center" value="'. $_GET['displaynum'] .'." readonly="readonly" size="1" /></span></td>
                        <td width="10%"><select name="gender_code[]" onchange="PremiumCalculator.calculate(document.getElementById(\'hiddenNum'. $_GET['num'] .'\').innerHTML, '. $_GET['num'] .')" class="input-text">'. self::getComboGender() .'</select></td>
                        <td width="45%"><select name="job[]" onchange="PremiumCalculator.calculate(document.getElementById(\'hiddenNum'. $_GET['num'] .'\').innerHTML, '. $_GET['num'] .')" class="input-text" style="width: 98%">'. self::getComboJobs() .'</select></td>
                        <td width="10%"><select name="packages[]" onchange="PremiumCalculator.calculate(document.getElementById(\'hiddenNum'. $_GET['num'] .'\').innerHTML, '. $_GET['num'] .')" class="input-text" style="width: 98%">'. self::getComboPackages() .'</select></td>
                        <td width="20%" align="right"><span id="hiddenNum'. $_GET['num'] .'" style="display: none"></span><input type="text" name="premiumCost[]" id="calc-text" style="text-align: right" value="0" readonly="readonly" /></td>
                        <td width="5%" align="center"><span id="iconCustomer'. $_GET['num'] .'"></span></td>
                        <td width="5%" align="center"><span id="remove" onclick="PremiumCalculator.removeCustomer(\'#customer'. $_GET['num'] .'\')"><img src="'. __TPL_IMG__ .'/icon-delete.gif" align="absmiddle" /></span></td>
                    </tr>
                </table>
        ';
    }
    
    public function getPremiCost() {
        $query = sprintf("SELECT pp.price
                          FROM premium_prices pp
                          JOIN job_grades job ON job.grade=pp.grade_id
                          WHERE pp.gender_code=%d
                          AND pp.package_id=%d
                          AND job.id=%d
                          AND job.acceptance='A'
                          LIMIT 1",
                          $this->gender,
                          $this->package,
                          $this->jobType);
        MySQL::setQuery($query);
        $result = MySQL::fetchRow();
        return (MySQL::getNumRows()) ? $result['price'] : 0;
    }
    
    public function checkAcceptance() {
        $query = sprintf("SELECT job.acceptance
                          FROM job_grades job
                          WHERE job.id=%d",
                          $this->jobType);
        MySQL::setQuery($query);
        $result = MySQL::fetchRow();
        return $result['acceptance'];
    }
    
    public function getFeePolicy() {
        return $this->feePolicy;
    }
    
    public function getFeeStamp($total) {
        if ($total <= 1000000) {
            return $this->feeStamp[0];
        }
        return $this->feeStamp[1];
    }
    
    public function getFeeAdmin() {
        return $this->feeAdmin;
    }
}
?>