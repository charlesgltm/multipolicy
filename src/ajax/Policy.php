<?php
require ('../../config/main.php');

$policy = new Policy();
if ($_REQUEST['policyId']) {
    $policy->setId($_REQUEST['policyId']);
}

switch ($_GET['get']) {
    case 'policyDetails':
        echo json_encode($policy->getPolicyDetails());
        break;
    
    case 'activatingTab':
        $policy->showTab(new PolicyType(), new Customer());
        break;
    
    case 'listPolicy':
        echo $policy->listPolicy(new Customer());
        break;
    
    case 'totalPolicy':
        echo $policy->getTotalPolicy(new Customer());
        break;
    
    case 'loadPolicyForm':
        $policy->addForm(new Customer(), new Gender(), new Job(), new Package());
        break;
    
    case 'loadUpdateForm':
        $policy->updateForm(new Gender(), new Job(), new Package());
        break;
}

switch ($_POST['act']) {
    case 'add':
        if ($policy->add(new Customer(), new Gender(), new Job(), new Package())) {
            echo 'true';
        }
        else {
            echo 'false';
        }
        die();
        break;
        
    case 'update':
        if ($policy->update(new Customer(), new Gender(), new Job(), new Package())) {
            echo 'true';
        }
        else {
            echo 'false';
        }
        die();
        break;
        
    case 'delete':
        if ($policy->delete()) {
            echo 'true';
        }
        else {
            echo 'false';
        }
        break;
}
?>
