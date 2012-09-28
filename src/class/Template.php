<?php
/**
 * file		: Template.php (Copied & Modified from hasnur.stockpile >> lig.info)
 * created	: 12 March 2012
 *
 * @package	: lig
 * @author	: Charles
 */

class Template {
    
    private $title = null;
    private $css = array(); // require CSS files
    private $js = array(); // require JS files
    private $js_object = array(); // require Javascript Object files
    private $em_css = null; // embedded CSS
    private $em_js = null; // embedded JS
    private $default_style = 'style.css'; // default style
    
    /**
     * Method for set Title Page
     * @param string $title = Current Page Title
     */
    public function __construct($title = 'Policy') {
        $this->title = $title;
    }
    
    /**
     * Method for get Require CSS files
     * @param array $css = CSS filename
     */
    public function getCssFiles($css = array()) {
        $this->css = $css;
    }
    
    /**
     * Method for get Require Javascript files
     * @param array $js = Javascript filename
     */
    public function getJsFiles($js = array()) {
        $this->js = $js;
    }
    
    /**
     * Method for get Require Javascript files
     * @param array $js = Javascript filename
     */
    public function getJsObject($js) {
        $this->js_object = $js;
    }
    
    /**
     * Method for set CSS files
     */
    private function setCssFiles() {
        if (count($this->css)) {
            foreach ($this->css as $filename) {
                echo '
    <link rel="stylesheet" type="text/css" href="'. __TPL_CSS__ .'/'. $filename .'" />
                ';
            }
        }
    }
    
    /**
     * Method for set JS files
     */
    private function setJsFiles() {
        if (count($this->js)) {
            foreach ($this->js as $filename) {
                echo '
    <script type="text/javascript" src="'. __BASE_LIB_URL__ .'/js/'. $filename .'" /></script>
                ';
            }
        }
    }
    
    /**
     * Method for set JS files
     */
    private function setJsObject() {
        if (count($this->js_object)) {
            foreach ($this->js_object as $filename) {
                echo '
    <script type="text/javascript" src="'. __BASE_JS__ .'/'. $filename .'" /></script>
                ';
            }
        }
    }
    
    /**
     * Method for set Embedded Style Sheet
     * @param string $css = Embedded Style Sheet
     */
    public function getCss($css = null) {
        $this->em_css = $css;
    }
    
    /**
     * Method for set Embedded Javascript
     * @param string $js = Embedded Javascript
     */
    public function getJs($js = null) {
        $this->em_js = $js;
    }
    
    /**
     * Method for load embedded CSS
     */
    private function loadCss() {
        if ($this->em_css) {
            echo '
    '. $this->em_css .'
            ';
        }
    }
    
    /**
     * Method for load embedded Javascript
     */
    private function loadJs() {
        if ($this->em_js) {
            echo '
    '. $this->em_js .'
            ';
        }
    }
    
    /**
     * Method for show Header page
     */
    public function showHeader() {
        echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <title>'. $this->title .'</title>
    <link rel="stylesheet" type="text/css" href="'. __TPL_CSS__ .'/default/'. $this->default_style .'" />
        ';
        
        // load embedded CSS and CSS files
        self::setCssFiles();
        self::loadCss();
        
        // load embedded Javascript and JS files
        self::setJsFiles();
        self::setJsObject();
        self::loadJs();
        
        echo '
</head>
<body>
    <div id="header"></div>
    <div id="wrapper">
        <div id="contents">
        ';
    }
    
    public function show(Customer $customer) {
        $this->showHeader();
        echo '
            <div id="main-window">
                <div id="main-tab">
                    <ul>
                        <li><a href="#tabCustomer">Customer</a></li>
                        <li><a href="#tabPolicy" onclick="Policy.activatingTab();Policy.activatingTab()">Policy</a></li>
                    </ul>
                    <div id="tabCustomer">'. $customer->customerForm(new Gender()) .'</div>
                    <div id="tabPolicy"></div>
                </div>
            </div>
        ';
        $this->showFooter();
    }
    
    /**
     * Method for show Footer page
     */
    public function showFooter() {
        echo '
        </div>
    </div>
    <div id="footer">
        <!--&copy; Copyright 2012 - <a href="http://www.jsm.co.id" target="_blank">Jaring Synergi Mandiri</a>-->
    </div>
</body>
</html>
        ';
    }
    
    public function headerHTML() {
        echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
    <title>'. $this->title .'</title>
    <link rel="stylesheet" type="text/css" href="'. __TPL_CSS__ .'/default/login.css" />
    ';
    
        // load embedded CSS and CSS files
        self::setCssFiles();
        self::loadCss();
        
        // load embedded Javascript and JS files
        self::setJsFiles();
        self::setJsObject();
        self::loadJs();
        
    echo '
</head>
<body>
    <div id="contents">
        ';
    }
    
    public function footerHTML() {
        echo '
    </div>
</body>
</html>
        ';
    }
}
?>