<?php

/**
 * Description of Policy
 *
 * @author Charles
 */
class Policy {
    private $id = null;
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }
    
    private function addNewButton() {
        echo '
            <span id="new-policy-button" class="button">
                <img align="absmiddle" src="'. __TPL_IMG__ .'/icon-add.gif" /> Add Policy
            </span>
        ';
    }
    
    public function getPolicy() {
        $query = sprintf("SELECT policies.*
                          FROM policies
                          WHERE policies.id=%d",
                          $this->getId());
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data;
    }
    
    public function getCustomerPolicy($custId) {
        $query = sprintf("SELECT policies.*
                          FROM policies
                          WHERE policies.customer_id=%d",
                          $custId);
        MySQL::setQuery($query);
        if (!MySQL::getNumRows()) {
            return false;
        }
        return MySQL::fetchRows();
    }
    
    public function showTab(PolicyType $policyType, Customer $customer) {
        $customer->setId($_GET['custId']);
        $dataCustomer = $customer->getCustomerDetails();
        $policyType->setId($dataCustomer['package_id']);
        
        self::addNewButton();
        echo '
            <hr />
            <div id="policyDialog"></div>
            <div id="addPolicyFormDialog"></div>
            <div id="updatePolicyFormDialog"></div>
            <form name="policyTabForm" id="policyTabForm">
                <div id="policyTypeOption" style="margin-bottom: 10px">
                    Policy Type : <select name="policyType" class="input-text" onchange="PolicyType.setId(this.value);Customer.updatePolicyType()" onkeyup="PolicyType.setId(this.value);PolicyType.getRules();Customer.updatePolicyType()">'. $policyType->getOptions() .'</select>
                </div>
                <div id="policyInformation"></div>
                <div id="listPolicy"></div>
                <div id="premiumTotal"></div>
                <div id="premiumInstallment"></div>
                <div id="dialogDeletePolicy" class="dialogBox">
                    Are you sure want to delete this Policy ?<br />
                    <span style="color: #FF1437">Warning: This data will be delete permanently.</span>
                </div>
            </form>
        ';
    }
    
    public function getTotalPolicy(Customer $customer) {
        $customer->setId($_REQUEST['custId']);
        $query = sprintf("SELECT COUNT(policies.id) as count
                          FROM policies
                          WHERE policies.customer_id=%d",
                          $customer->getId());
        MySQL::setQuery($query);
        $num = MySQL::fetchRow();
        return $num['count'];
    }
    
    public function listPolicy(Customer $customer) {
        $customer->setId($_GET['custId']);
        $policy = self::getCustomerPolicy($customer->getId());
        if ($policy) {
            $table = '';
            $table .= '
                <table id="policyDataTable" class="display">
                    <thead>
                        <tr>
                            <th width="5%">No.</th>
                            <th width="30%">Name</th>
                            <th width="10%">Gender</th>
                            <th width="10%">Package</th>
                            <th width="15%">Premium</th>
                            <th width="5%">Edit</th>
                            <th width="5%">Del.</th>
                        </tr>
                    </thead>
            ';
            
            $table .= '
                    <tbody>
            ';
            
            foreach ($policy as $key => $value) {
                static $i = 1;
                $table .= '
                        <tr>
                            <td align="center">'. $i .'.</td>
                            <td align="left">'. $value['customer'] .'</td>
                            <td align="center">'. $value['gender'] .'</td>
                            <td align="center">'. $value['package'] .'</td>
                            <td align="right">'. number_format($value['premium'], 0, '.', ',') .'</td>
                            <td align="center"><a href="#updatePolicy" onclick="Policy.setId('. $value['id'] .');Policy.openUpdatePolicyForm()"><img src="'. __TPL_IMG__ .'/icon-edit.gif" align="absmiddle" /></a></td>
                            <td align="center"><a href="#deletePolicy" onclick="Policy.setId('. $value['id'] .');Functions.openDialog(\'dialogDeletePolicy\')"><img src="'. __TPL_IMG__ .'/icon-delete.gif" align="absmiddle" /></a></td>
                        </tr>
                ';
                ++$i;
            }
            
            $table .= '
                    </tbody>
                </table>
            ';
            
            return $table;
        }
        else {
            return '
                <p class="error" align="center">No Policy found for this customer</p>
            ';
        }
    }
    
    public function addForm(Customer $customer, Gender $gender, Job $job, Package $package) {
        $customer->setId($_GET['custId']);
        
        $optRelationship = '<option value="">--</option>';
        foreach (Functions::getListValues(7) as $key => $value) {
            $optRelationship .= '<option value="'. $value['list_code'] .'">'. $value['list_data'] .'</option>';
        }
        echo '
            <div id="addPolicyInformation"></div>
            <form name="addPolicyForm" id="addPolicyForm">
                <input type="hidden" name="act" value="add" />
                <input type="hidden" name="custId" value="'. $customer->getId() .'" />
                <table align="center" width="100%">
                    <tr>
                        <th align="right">Name</th><td>:</td><td><input type="text" class="input-text" name="policy_name" size="32" /></td>
                    </tr>
                    <tr>
                        <th align="right">Date of Birth</th><td>:</td><td><input type="text" class="input-text" name="policy_dob" id="newDob" /></td>
                    </tr>
                    <tr>
                        <th align="right">Relationship</th><td>:</td><td><select name="policy_relationship" class="input-text" style="width: 215px">'. $optRelationship .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Gender</th><td>:</td><td><select name="policy_gender" class="input-text" style="width: 215px" onchange="Calculator.getPremium(\'add\')" onkeyup="Calculator.getPremium(\'add\')">'. $gender->getOptions() .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Job</th><td>:</td><td><select name="policy_job" class="input-text" style="width: 215px" onchange="Job.setId(this.value);Calculator.getPremium(\'add\')" onkeyup="Job.setId(this.value);Calculator.getPremium(\'add\')">'. $job->getOptions() .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Package</th><td>:</td><td><select name="policy_package" class="input-text" style="width: 215px" onchange="Calculator.getPremium(\'add\')" onkeyup="Calculator.getPremium(\'add\')">'. $package->getOptions() .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Handphone</th><td>:</td><td><input type="text" class="input-text" name="policy_handphone1" size="32" /><td>
                    </tr>
                    <tr>
                        <th align="right">Home Phone</th><td>:</td><td><input type="text" class="input-text" name="policy_homephone1" size="32" /><td>
                    </tr>
                    <tr>
                        <th align="right">Office Phone</th><td>:</td><td><input type="text" class="input-text" name="policy_officephone1" size="32" /><td>
                    </tr>
                    <tr>
                        <th align="right" valign="top">Address</th><td valign="top">:</td><td><textarea cols="29" rows="4" class="input-text" name="policy_address"></textarea><td>
                    </tr>
                    <tr>
                        <th align="right">City</th><td>:</td><td><input type="text" class="input-text" name="policy_city" size="32" /></td>
                    </tr>
                    <tr>
                        <th align="right">Postcode</th><td>:</td><td><input type="text" class="input-text" name="policy_postcode" size="32" /></td>
                    </tr>
                    <tr>
                        <th align="right">Premium</th><td>:</td><td><input type="hidden" name="policy_premium" value="0" /><span id="policy_premium"><span>
                    </tr>
                    <tr>
                        <td colspan="3" align="center">
                            <hr />
                            <input type="button" class="button" value="Add" onclick="Policy.validateAdd()" />
                            <input type="button" class="button" value="Cancel" onclick="Functions.closeDialog(\'addPolicyFormDialog\')" />
                        </td>
                    </tr>
                <table>
            </form>
        ';
    }
    
    public function add(Customer $customer, Gender $gender, Job $job, Package $package) {
        $customer->setId($_POST['custId']);
        $dob = ($_POST['policy_dob']) ? "'$_POST[policy_dob]'" : "null";
        $gender->setId($_POST['policy_gender']);
        $dataGender = $gender->getGenderDetails();
        
        $job->setId($_POST['policy_job']);
        $dataJob = $job->getJobDetails();
        
        $package->setId($_POST['policy_package']);
        $dataPackage = $package->getPackageDetails();
        
        $relationship = Functions::getListValues(7, $_POST['policy_relationship']);
        
        $query = sprintf("INSERT INTO policies
                          (
                            customer_id,
                            customer,
                            birth_date,
                            relationship_code,
                            relationship,
                            gender,
                            package_id,
                            package,
                            job_id,
                            job_category,
                            job_grade,
                            premium,
                            handphone1,
                            homephone,
                            officephone1,
                            address,
                            city,
                            postcode
                          )
                          VALUES
                          (
                            %d,
                            '%s',
                            $dob,
                            '%s',
                            '%s',
                            '%s',
                            %d,
                            '%s',
                            %d,
                            '%s',
                            '%s',
                            %d,
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                          )",
                          $customer->getId(),
                          $_POST['policy_name'],
                          $relationship[0]['list_code'],
                          $relationship[0]['list_data'],
                          $dataGender['list_data'],
                          $package->getId(),
                          $dataPackage['package_name'],
                          $job->getId(),
                          $dataJob['job_category'],
                          $dataJob['grade'],
                          $_POST['policy_premium'],
                          $_POST['policy_handphone1'],
                          $_POST['policy_homephone1'],
                          $_POST['policy_officephone1'],
                          $_POST['policy_address'],
                          $_POST['policy_city'],
                          $_POST['policy_postcode']);
        MySQL::setQuery($query);
        if (MySQL::execute()) {
            return true;
        }
        echo MySQL::getQuery();
        return false;
    }
    
    public function updateForm(Gender $gender, Job $job, Package $package) {
        $policy = self::getPolicy();
        $gender->setGender($policy['gender']);
        $job->setId($policy['job_id']);
        $package->setId($policy['package_id']);
        
        $optRelationship = '<option value="">--</option>';
        foreach (Functions::getListValues(7) as $key => $value) {
            $selected = ($policy['relationship_code'] == $value['list_code']) ? ' selected="selected"' : '';
            $optRelationship .= '<option value="'. $value['list_code'] .'" '. $selected .'>'. $value['list_data'] .'</option>';
        }
        
        echo '
            <div id="updatePolicyInformation"></div>
            <form name="updatePolicyForm" id="updatePolicyForm">
                <input type="hidden" name="act" value="update" />
                <input type="hidden" name="policyId" value="'. $policy['id'] .'" />
                <input type="hidden" name="custId" value="'. $policy['customer_id'] .'" />
                <table align="center" width="100%">
                    <tr>
                        <th align="right">Name</th><td>:</td><td><input type="text" class="input-text" name="policy_name" size="32" value="'. $policy['customer'] .'" /></td>
                    </tr>
                    <tr>
                        <th align="right">Date of Birth</th><td>:</td><td><input type="text" class="input-text" name="policy_dob" id="updateDob" value="'. $policy['birth_date'] .'" /></td>
                    </tr>
                    <tr>
                        <th align="right">Relationship</th><td>:</td><td><select name="policy_relationship" class="input-text" style="width: 215px">'. $optRelationship .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Gender</th><td>:</td><td><select name="policy_gender" class="input-text" style="width: 215px" onchange="Calculator.getPremium(\'update\')" onkeyup="Calculator.getPremium(\'update\')">'. $gender->getOptions() .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Job</th><td>:</td><td><select name="policy_job" class="input-text" style="width: 215px" onchange="Job.setId(this.value);Calculator.getPremium(\'update\')" onkeyup="Job.setId(this.value);Calculator.getPremium(\'update\')">'. $job->getOptions() .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Package</th><td>:</td><td><select name="policy_package" class="input-text" style="width: 215px" onchange="Calculator.getPremium(\'update\')" onkeyup="Calculator.getPremium(\'update\')">'. $package->getOptions() .'</select></td>
                    </tr>
                    <tr>
                        <th align="right">Handphone</th><td>:</td><td><input type="text" class="input-text" name="policy_handphone1" size="32" value="'. $policy['handphone1'] .'" /><td>
                    </tr>
                    <tr>
                        <th align="right">Home Phone</th><td>:</td><td><input type="text" class="input-text" name="policy_homephone1" size="32" value="'. $policy['homephone'] .'" /><td>
                    </tr>
                    <tr>
                        <th align="right">Office Phone</th><td>:</td><td><input type="text" class="input-text" name="policy_officephone1" size="32" value="'. $policy['officephone1'] .'" /><td>
                    </tr>
                    <tr>
                        <th align="right" valign="top">Address</th><td valign="top">:</td><td><textarea cols="29" rows="4" class="input-text" name="policy_address">'. $policy['address'] .'</textarea><td>
                    </tr>
                    <tr>
                        <th align="right">City</th><td>:</td><td><input type="text" class="input-text" name="policy_city" size="32" value="'. $policy['city'] .'" /></td>
                    </tr>
                    <tr>
                        <th align="right">Postcode</th><td>:</td><td><input type="text" class="input-text" name="policy_postcode" size="32" value="'. $policy['postcode'] .'" /></td>
                    </tr>
                    <tr>
                        <th align="right">Premium</th><td>:</td><td><input type="hidden" name="policy_premium" value="'. $policy['premium'] .'" /><span id="policy_premium">Rp. '. number_format($policy['premium'], 0, '', ',') .'<span>
                    </tr>
                    <tr>
                        <td colspan="3" align="center">
                            <hr />
                            <input type="button" class="button" value="Update" onclick="Policy.validateUpdate()" />
                            <input type="button" class="button" value="Cancel" onclick="Functions.closeDialog(\'updatePolicyFormDialog\')" />
                        </td>
                    </tr>
                <table>
            </form>
        ';
    }
    
    public function update(Customer $customer, Gender $gender, Job $job, Package $package) {
        $customer->setId($_POST['custId']);
        $dob = ($_POST['policy_dob']) ? "'$_POST[policy_dob]'" : "null";
        $gender->setId($_POST['policy_gender']);
        $dataGender = $gender->getGenderDetails();
        
        $job->setId($_POST['policy_job']);
        $dataJob = $job->getJobDetails();
        
        $package->setId($_POST['policy_package']);
        $dataPackage = $package->getPackageDetails();
        
        $relationship = Functions::getListValues(7, $_POST['policy_relationship']);
        
        $query = sprintf("UPDATE policies
                          SET
                            customer='%s',
                            birth_date=$dob,
                            relationship_code=%d,
                            relationship='%s',
                            gender='%s',
                            package_id=%d,
                            package='%s',
                            job_id=%d,
                            job_category='%s',
                            job_grade=%d,
                            premium=%d,
                            handphone1='%s',
                            homephone='%s',
                            officephone1='%s',
                            address='%s',
                            city='%s',
                            postcode='%s'
                          WHERE policies.id=%d",
                          $_POST['policy_name'],
                          $relationship[0]['list_code'],
                          $relationship[0]['list_data'],
                          $dataGender['list_data'],
                          $package->getId(),
                          $dataPackage['package_name'],
                          $job->getId(),
                          $dataJob['job_category'],
                          $dataJob['grade'],
                          $_POST['policy_premium'],
                          $_POST['policy_handphone1'],
                          $_POST['policy_homephone1'],
                          $_POST['policy_officephone1'],
                          $_POST['policy_address'],
                          $_POST['policy_city'],
                          $_POST['policy_postcode'],
                          $_POST['policyId']);
        MySQL::setQuery($query);
        if (MySQL::execute()) {
            return true;
        }
        return false;
    }
    
    public function delete() {
        $query = sprintf("DELETE FROM policies
                          WHERE id=%d",
                          self::getId());
        MySQL::setQuery($query);
        if (MySQL::execute()) {
            return true;
        }
        return false;
    }
}

?>
