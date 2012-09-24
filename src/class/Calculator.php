<?php

/**
 * Description of Calculator
 *
 * @author Charles
 */
class Calculator {
    private $feeStamp = array(9000, 12000);
    const feePolicy = 28000;
    const feeAdmin = 129298;
    private $customer;
    
    public function storeCustomerDataObject(Customer $customer) {
        $this->customer = $customer;
    }
    
    public function getPremium(Gender $gender, Job $job, Package $package) {
        $gender->setId($_GET['gender']);
        $job->setId($_GET['job']);
        $package->setId($_GET['package']);
        $jobDetail = $job->getJobDetails();
        
        $query = sprintf("SELECT premium_prices.price
                          FROM premium_prices
                          WHERE premium_prices.gender_code=%d
                          AND premium_prices.grade_id=%d
                          AND premium_prices.package_id=%d",
                          $gender->getId(),
                          $jobDetail['grade'],
                          $package->getId());
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data['price'];
    }
    
    public function getFeePolicy() {
        return self::feePolicy;
    }
    
    public function getFeeAdmin() {
        return self::feeAdmin;
    }
    
    public function getSubtotal() {
        $query = sprintf("SELECT SUM(policies.premium) AS subtotal
                          FROM policies
                          WHERE policies.customer_id=%d",
                          $this->customer->getId());
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data['subtotal'];
    }
    
    public function getDiscount(PolicyType $policyType) {
        $customerData = $this->customer->getCustomerDetails();
        $policyType->setId($customerData['package_id']);
        $discount = $policyType->getDiscount();
        if ($discount > 0)
            return ($discount / 100) * self::getSubtotal();
        return 0;
    }
    
    public function getFeeStamp() {
        if (self::getTotal() <= 1000000)
            return $this->feeStamp[0];
        return $this->feeStamp[1];
    }
    
    public function getTotal() {
        return self::getSubtotal() - self::getDiscount(new PolicyType());
    }
    
    public function getGrandTotal() {
        return self::getTotal() + self::getFeeStamp() + (self::getFeeAdmin() + self::getFeePolicy());
    }
    
    public function generateTotal(Customer $customer, PolicyType $policyType) {
        $discount = array(
                          'persen' => 0,
                          'total' => 0
                         );
        $customer->setId($_GET['custId']);
        $customerData = $customer->getCustomerDetails();
        $policyType->setId($customerData['package_id']);
        $discount['persen'] = $policyType->getDiscount();
        $discount['total'] = $customerData['discount'];
        echo '
            <table width="100%" class="premiumTotal">
                <tr>
                    <td width="80%"></td><td colspan="2" width="20%"></td>
                </tr>
                <tr>
                    <td align="right">Subtotal</td><td>:</td><td align="right">'. number_format($customerData['subtotal_premium'], 0, '.', ',') .'</td>
                </tr>
                <tr>
                    <td align="right">Discount '. $discount['persen'] .'%</td><td>:</td><td align="right">'. number_format($discount['total'], 0, '.', ',') .'
                </tr>
                <tr>
                    <td align="right">Total</td><td>:</td><td align="right">'. number_format($customerData['total_premium'], 0, '.', ',') .'</td>
                </tr>
                <tr>
                    <td align="center" colspan="3"><hr /></td>
                </tr>
                <tr>
                    <td align="right">Policy Cost</td><td>:</td><td align="right">'. number_format($customerData['policy_cost'], 0, '.', ',') .'</td>
                </tr>
                <tr>
                    <td align="right">Meterai</td><td>:</td><td align="right">'. number_format($customerData['stamp_duty'], 0, '.', ',') .'</td>
                </tr>
                <tr>
                    <td align="right">Admin Cost</td><td>:</td><td align="right">'. number_format($customerData['admin_fee'], 0, '.', ',') .'</td>
                </tr>
                <tr>
                    <td align="right">Grand Total</td><td>:</td><td align="right">'. number_format($customerData['total_installment'], 0, '.', ',') .'</td>
                </tr>
            </table>
        ';
    }
    
    public function generateInstallment(Customer $customer) {
        $customer->setId($_REQUEST['custId']);
        $dataCustomer = $customer->getCustomerDetails();
        $installment = array(1, 3, 6, 12);
        echo '
            <form name="installmentForm" id="installmentForm">
            <table width="100%" class="premiumInstallment" border="1">
                <tr>
                    <th width="5%">&nbsp;</th>
                    <th width="5%">Installment</th>
                    <th width="45%">First Installment</th>
                    <th width="45%">Next Installment</th>
                </tr>
        ';
        
        foreach ($installment as $value) {
            $firstInstallment = ($dataCustomer['total_premium'] / $value) + ($dataCustomer['policy_cost'] + $dataCustomer['stamp_duty'] + $dataCustomer['admin_fee']);
            $nextInstallment = ($value === 1) ? 0 : $dataCustomer['total_premium'] / $value;
            $checked = ($dataCustomer['installment'] == $value) ? ' checked="checked"' : '';
            echo '
                <tr>
                    <td align="center"><input type="radio" name="installment" value="'. $value .'" '. $checked .' onclick="Customer.updateInstallment(this.value)" /></td>
                    <td align="center">'. $value .'x</td>
                    <td align="center">'. number_format($firstInstallment, 0, '.', ',') .'</td>
                    <td align="center">'. number_format($nextInstallment, 0, '.', ',') .'</td>
                </tr>
            ';
        }
        
        echo '
            </table>
            </form>
        ';
    }
}

?>
