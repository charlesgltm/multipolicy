<?php
require ('./config/main.php');

$template = new Template();

$template->getCssFiles(array('datatables/default.css',
                             'jquery-ui/south-street/jquery-ui-1.8.18.custom.css'));

$template->getJsFiles(array('jquery/jquery-1.7.2.min.js',
                            'jquery-ui/jquery-ui-1.8.18.custom.min.js',
                            'prototype/prototype-1.7.js',
                            'datatables/script.js'));
$template->getJsObject(array('Template.js',
                             'Functions.js',
                             'Customer.js',
                             'Policy.js',
                             'Job.js',
                             'PolicyType.js',
                             'Calculator.js'));

$script = '
    <script type="text/javascript">
        Functions = new Functions;
        Template = new Template;
        Customer = new Customer;
        Policy = new Policy;
        var windowId = "main-window";
        var tabId = "main-tab";
        
        Customer.setId('. $_GET['custId'] .');
        jQuery(document).ready(function($) {
            Template.initMainWindow(windowId, "Customer Information");
            jQuery("#" + tabId).tabs();
        });
    </script>
';

$template->getJs($script);

$customer = new Customer;
$customer->setId($_GET['custId']);

$template->show($customer);
?>