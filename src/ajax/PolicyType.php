<?php
require ('../../config/main.php');

$policyType = new PolicyType();

$policyType->setId($_REQUEST['policyTypeId']);

switch ($_GET['get']) {
    
    case 'rules':
        echo 'Rules :<br />';
        echo '<ul>';
        foreach ($policyType->getRules() as $value) {
            echo '<li>'. $value .'</li>';
        }
        echo '</ul>';
        break;
        
    case 'validate':
        echo $policyType->validate(new Customer());
        break;
    
    case 'validateGender':
        echo $policyType->validateGender(new Customer(), new Policy(), new Gender());
        break;
}
?>
