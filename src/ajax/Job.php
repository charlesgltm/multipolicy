<?php
require ('../../config/main.php');

$job = new Job();
$job->setId($_REQUEST['jobId']);

switch ($_GET['get']) {
    case 'acceptance':
        echo $job->isAccepted();
        break;
}
?>
