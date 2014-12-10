<?php
    //$pathThemes= realpath(dirname(__FILE__)) . '/../../source/view/themes/';
    $pathThemes= PATHAPP . '/source/view/themes/';
    //$pathJavaScript= realpath(dirname(__FILE__)) . '/../../source/view/javascript/';
    $pathJavaScript= PATHAPP . '/source/view/javascript/';
    //$pathComponents= realpath(dirname(__FILE__)) . '/../../source/view/components/';
    $pathComponents= PATHAPP . '/source/view/components/';
    
    define('PATH_THEME', $pathThemes);
    define('PATH_JAVASCRIPT', $pathJavaScript);    
    define('PATH_COMPONENT', $pathComponents);
    require 'ApiUi.php';
    require 'Tags.php';
?>
