<?php

/**
 * Description of Customer
 *
 * @author Charles
 */
class Customer {
    private $id = null;
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getCustomerDetails() {
        $query = sprintf("SELECT customers.*
                          FROM customers
                          WHERE customers.id=%d",
                          $this->getId());
        MySQL::setQuery($query);
        return MySQL::fetchRow();
    }
    
    public function customerForm(Gender $gender) {
        $customer = $this->getCustomerDetails();
        $gender->setGender($customer['gender']);
        return '
            <div id="customerInformation"></div>
            <div id="customerForm">
                <form name="updateCustomerForm" id="updateCustomerForm">
                    <input type="hidden" name="act" value="update" />
                    <input type="hidden" name="custId" value="'. $this->getId() .'" />
                    <table>
                        <tr>
                            <td>Name</td><td>:</td><td> '. $customer['customer'] .'</td>
                        </tr>
                        <tr>
                            <td>Gender</td><td>:</td><td> <select name="customer_gender" class="input-text">'. $gender->getOptions('data') .'</select></td>
                        </tr>
                        <tr>
                            <td>Handphone</td><td>:</td><td> '. $customer['handphone1'] .'</td>
                        </tr>
                        <tr>
                            <td>Office Phone</td><td>:</td><td><input type="text" class="input-text" name="customer_officephone1" value="'. $customer['officephone1'] .'" /></td>
                        </tr>
                        <tr>
                            <td>Address</td><td>:</td><td><textarea class="input-text" name="customer_address" onkeyup="Functions.textToUpper(this)">'. $customer['address'] .'</textarea></td>
                        </tr>
                        <tr>
                            <td>City</td><td>:</td><td> <input type="text" class="input-text" name="customer_city" value="'. $customer['city'] .'" onkeyup="Functions.textToUpper(this)" /></td>
                        </tr>
                        <tr>
                            <td>Post Code</td><td>:</td><td> <input type="text" class="input-text" name="customer_postcode" value="'. $customer['postcode'] .'" /></td>
                        </tr>
                        <tr>
                            <td colspan="3" align="center">
                                <hr />
                                <input type="button" class="button" value="Save" onclick="Customer.update()" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        ';
    }
    
    public function update() {
        $query = sprintf("UPDATE customers
                          SET
                            gender='%s',
                            officephone1='%s',
                            address='%s',
                            city='%s',
                            postcode='%s'
                          WHERE id=%d",
                          $_POST['customer_gender'],
                          MySQL::escapeString($_POST['customer_officephone1']),
                          MySQL::escapeString($_POST['customer_address']),
                          MySQL::escapeString($_POST['customer_city']),
                          MySQL::escapeString($_POST['customer_postcode']),
                          MySQL::escapeString($_POST['custId']));
        MySQL::setQuery($query);
        if (MySQL::execute())
            return true;
        return false;
    }
    
    public function updatePolicyType(PolicyType $policyType) {
        $policyTypeId = ($_POST['policyTypeId'] == '') ? 'null' : $_POST['policyTypeId'];
        $policyType->setId($policyTypeId);
        $policyTypeData = $policyType->getPolicyTypeDetails();
        $query = sprintf("UPDATE customers
                          SET 
                            package_id=$policyTypeId,
                            package='%s'
                          WHERE id=%d",
                         $policyTypeData['relationship_name'],
                         $this->getId());
        MySQL::setQuery($query);
        if (MySQL::execute())
            return true;
        return false;
    }
    
    public function updateSubtotal() {
        $query = sprintf("UPDATE customers
                          SET customers.subtotal_premium=%d
                          WHERE customers.id=%d",
                          self::getSubtotalPremium(),
                          $this->getId());
        MySQL::setQuery($query);
        if (MySQL::execute())
            return true;
        return false;
    }
    
    public function updateCost(Calculator $calculator) {
        $calculator->storeCustomerDataObject($this);
        $query = sprintf("UPDATE customers
                          SET
                            subtotal_premium=%d,
                            discount=%d,
                            total_premium=%d,
                            stamp_duty=%d,
                            policy_cost=%d,
                            admin_fee=%d,
                            total_installment=%d
                          WHERE id=%d",
                          $calculator->getSubtotal(),
                          $calculator->getDiscount(new PolicyType()),
                          $calculator->getTotal(),
                          $calculator->getFeeStamp(),
                          $calculator->getFeePolicy(),
                          $calculator->getFeeAdmin(),
                          $calculator->getGrandTotal(),
                          $this->getId());
        MySQL::setQuery($query);
        if (MySQL::execute())
            return true;
        return false;
    }
    
    public function updateInstallment($installment) {
        $customer = self::getCustomerDetails();
        $first = ($customer['total_premium'] / $installment) + ($customer['policy_cost'] + $customer['stamp_duty'] + $customer['admin_fee']);
        $next = ($installment == 1) ? 0 : $customer['total_premium'] / $installment;
        $query = sprintf("UPDATE customers
                          SET
                            installment=%d,
                            first_installment=%d,
                            next_installment=%d
                          WHERE id=%d",
                          $installment,
                          $first,
                          $next,
                          self::getId());
        MySQL::setQuery($query);
        if (MySQL::execute())
            return true;
        return false;
    }
}

?>
