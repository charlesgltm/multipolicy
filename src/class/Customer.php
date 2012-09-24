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
                          $this->id);
        MySQL::setQuery($query);
        return MySQL::fetchRow();
    }
    
    public function customerForm() {
        $customer = $this->getCustomerDetails();
        return '
            <div id="customer-form">
                <form name="customerform" id="customerform">
                    <table>
                        <tr>
                            <td>Name</td><td>:</td><td> '. $customer['customer'] .'</td>
                        </tr>
                    </table>
                </form>
            </div>
        ';
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
