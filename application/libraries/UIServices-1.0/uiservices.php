<?php
    //$pathThemes= realpath(dirname(__FILE__)) . '/../../source/view/themes/';
    $pathThemes= PATHAPP . '/source/view/themes/';
    //$pathJavaScript= realpath(dirname(__FILE__)) . '/../../source/view/javascript/';
    $pathJavaScript= PATHAPP . '/source/view/javascript/';
    //$pathComponents= realpath(dirname(__FILE__)) . '/../../source/view/components/';
    $pathComponents= PATHAPP . '/source/view/components/';
    //$pathServerDefinition= realpath(dirname(__FILE__)) . '/../../source/content/ServerDefinition-2015-03-12.txt';
    $pathServerDefinition= PATHAPP . '/source/content/ServerDefinition-2015-03-12.txt';
    define('PATH_THEME', $pathThemes);
    define('PATH_JAVASCRIPT', $pathJavaScript);    
    define('PATH_COMPONENT', $pathComponents);
    define('SERVER_DEFINITION', FALSE);
    define('SERVER_DEFINITION_FILE', $pathServerDefinition);
    require 'ApiUi.php';
    require 'Tags.php';
?>
