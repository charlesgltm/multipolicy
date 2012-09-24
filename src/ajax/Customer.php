<?php
require ('../../config/main.php');

$customer = new Customer();
$customer->setId($_REQUEST['custId']);

switch ($_GET['get']) {
    case 'customerDetails':
        echo json_encode($customer->getCustomerDetails());
        break;
}

switch ($_POST['act']) {
    case 'updatePolicyType':
        $customer->updatePolicyType(new PolicyType());
        break;
    
    case 'updateCost':
        $customer->updateCost(new Calculator());
        break;
    
    case 'updateInstallment':
        $customer->updateInstallment($_POST['installment']);
        break;
}
?>
