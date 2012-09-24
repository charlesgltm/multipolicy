<?php

define ('__BASE_URI__', $_SERVER['DOCUMENT_ROOT'] .'/LIG/Telemarketing/multipolicy');
define ('__BASE_URL__', 'http://'. $_SERVER['SERVER_NAME'] .'/LIG/Telemarketing/multipolicy');
define ('__BASE_SRC__', __BASE_URI__ .'/src');
define ('__BASE_LIB_URI__', __BASE_URI__ .'/lib');
define ('__BASE_LIB_URL__', __BASE_URL__ .'/lib');
define ('__BASE_LOG__', __BASE_URI__ .'/log');
define ('__BASE_TPL__', __BASE_URL__ .'/tpl');
define ('__BASE_CLASS__', __BASE_SRC__ .'/class');
define ('__BASE_JS__', __BASE_URL__ .'/src/js');

define ('__TPL_CSS__', __BASE_TPL__ .'/css');
define ('__TPL_IMG__', __BASE_TPL__ .'/img');

date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL ^E_NOTICE|E_WARNING);

// if app running in development server, show errors
$development = (stristr($_SERVER['HTTP_HOST'], 'local')) ? true : false;
$show_error = ($development) ? 1 : 0;
ini_set('display_errors', $show_error);

// create error handler
function my_error_handler($e_number, $e_message, $e_file, $e_line, $e_vars) {
    global $development;
    // create error message
    $message = "Error found in file $e_file at line $e_line {NL}";
    $message .= "DateTime : ". date('d M Y, H:i:s') ."{NL}";
    $message .= "Messages :{NL}$e_message{NL}";
    
    if (($e_number != E_NOTICE) && ($e_number < 2048)) {
        // show errors
        if ($development) { // show error on browser if running in development server
            echo '<p class="error">'. str_replace("{NL}", "<br />", $message) .'</p>';
        }
        else { // log error to file
            $fopen = fopen(__BASE_LOG__ .'/errors.log', 'a+');
            $message .= '{NL}***************************************************{NL}{NL}';
            fwrite($fopen, str_replace("{NL}", "\n", $message));
            echo '<p class="error">An error in the system has occurred. We apologize for this error.</p>';
        }
    }
}
set_error_handler('my_error_handler');

function __autoload($class) {
    require_once (__BASE_CLASS__ .'/'. $class .'.php');
}
MySQL::setConnection('dev');
?>
