<?php
/**
 * file		: PremiumCalculator.php
 * created	: 13 March 2012
 *
 * @package	: lig
 * @author	: Charles
 */

require ('../../config/config.php');
$calc = new PremiumCalculator();

switch ($_GET['get']) {
    
    case 'addcustomer':
        $calc->addNewCustomer();
        break;
    
    case 'calculate':
        $calc->setPremiInfo($_GET['gender_code'], $_GET['packages'], $_GET['job']);
        echo $calc->getPremiCost();
        break;
    
    case 'checkacceptance':
        $calc->setPremiInfo(0, 0, $_GET['job']);
        echo $calc->checkAcceptance();
        break;
    
    case 'feepolicy':
        echo $calc->getFeePolicy();
        break;
    
    case 'feestamp':
        echo $calc->getFeeStamp($_GET['total']);
        break;
    
    case 'feeadmin':
        echo $calc->getFeeAdmin();
        break;
}
?>