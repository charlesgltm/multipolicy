<?php

require ('../../config/main.php');

$calculator = new Calculator;

switch ($_GET['get']) {
    case 'getPremium':
        echo $calculator->getPremium(new Gender(), new Job(), new Package());
        break;
    
    case 'generateTotal':
        echo $calculator->generateTotal(new Customer(), new PolicyType());
        break;
    
    case 'generateInstallment':
        echo $calculator->generateInstallment(new Customer());
        break;
}

?>
