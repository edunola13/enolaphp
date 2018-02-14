<?php
namespace UiServices;
/** Description of Tags */
class Tags {
    /** @var ApiUi */
    public $api;
    
    protected $currentLevel= 0;
    protected $levelData= array();
    
    public function __construct($project = NULL) {
        $this->api= ApiUi::getInstance();
        if($project != NULL){
            $this->api->project= $project;
        }
    }
    
    public function setProject($name){
        $this->api->project= $name;
    }
    public function setServerUrl($url){
        $this->api->serverUrl= $url;
    }
    /* Temas y JavaScripts */
    public function theme($name = 'base'){
        return $this->api->theme($name);               
    }
    public function javaScript($name = 'base'){ 
        return $this->api->javaScript($name);
    }
    /* DEFINICION DE TODOS LOS COMPONENTES */
    public function columnParsed($tableId, $title, $units){
        $valores= array('config.tableId' => $tableId, 'config.title' => $title, 'config.units' => $units);
        $this->api->component('column-parsed', $valores);
    }
    public function dateCountdown($id, $date, $width, $height){
        $valores= array('config.id' => $id, 'config.date' => $date, 'config.width' => $width, 'config.height' => $height);
        $this->api->component('date_countdown', $valores);
    }    
    /* Estaticos */        
    public function address($name, $dir, $locale, $tel){
        $valores= array('config.nombre' => $name, 'config.direccion' => $dir, 'config.localidad' => $locale, 'config.telefono' => $tel);
        $this->api->component('address', $valores);
    }    
    public function alertMessage($type, $message, $strong = NULL){
        if($message != NULL){
            $valores= array('config.type' => $type, 'config.strong' => $strong, 'config.message' => $message);
            $this->api->component('alert_message', $valores);
        }
    }    
    public function badge($label, $href, $badge = NULL){
        $valores= array('config.label' => $label, 'config.href' => $href, 'config.badge' => $badge);
        $this->api->component('badge', $valores);
    }
    public function buttonBadge($label, $badge = NULL, $id = NULL, $onClick = NULL, $type = 'button', $style = 'primary', $size = 'md'){
        $valores= array('config.label' => $label, 'config.type' => $type, 'config.badge' => $badge, 'config.style' => $style, 'config.size' => $size, 'config.id' => $id, 'config.onclick' => $onClick);
        $this->api->component('button_badge', $valores);
    }
    public function blockquote($text, $source){
        $valores= array('config.texto' => $text, 'config.fuente' => $source);
        $this->api->component('blockquote', $valores);
    }    
    public function fixedFooter(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('fixed_footer', $valores);
    }    
    public function endFixedFooter(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('fixed_footer', $valores);
    }    
    public function formSearch($name, $label, $button_id, $onClick, $placeholder, $value = NULL){
        $valores= array('config.input.name' => $name, 'config.input.placeholder' => $placeholder, 'config.label' => $label,
            'config.id' => $button_id, 'config.onclick' => $onClick, 'config.value_input' => $value);
        $this->api->component('form_search', $valores);
    }    
    public function iframe($src, $ratio = NULL){
        $valores= array('config.src' => $src, 'config.ratio' => $ratio);
        $this->api->component('iframe', $valores);
    }    
    public function image($alt, $src, $type = NULL){
        $valores= array('config.alt' => $alt, 'config.src' => $src, 'config.type' => $type);
        $this->api->component('image', $valores);
    }    
    public function jumbotron($title, $content, $href, $label, $buttonStyle = 'default', $buttonSize = 'md'){
        $valores= array('config.titulo' => $title, 'config.contenido' => $content, 'config.href' => $href, 'config.label' => $label, 'config.buttonStyle' => $buttonStyle, 'config.buttonSize' => $buttonSize);
        $this->api->component('jumbotron', $valores);
    }
    public function link($id, $href, $label, $button=FALSE, $style = 'default', $size = 'md'){
        $valores= array('config.id' => $id, 'config.href' => $href, 'config.label' => $label, 'config.style' => $style, 'config.size' => $size);
        $valores['config.button']='no';
        if($button)$valores['config.button']='si';
        $this->api->component('link', $valores);
    } 
    public function simplePager($preState, $preHref, $preLabel, $nextState, $nextHref, $nextLabel){
        $valores= array('config.previous.state' => $preState, 'config.previous.href' => $preHref, 'config.previous.label' => $preLabel,
                        'config.next.state' => $nextState, 'config.next.href' => $nextHref, 'config.next.label' => $nextLabel);
        $this->api->component('simple_pager', $valores);
    }    
    public function progressBar($percentage, $striped = FALSE){
        $valores= array('config.porcentaje' => $percentage, 'config.striped' => ($striped ? 'si' : 'no'));
        $this->api->component('progress_bar', $valores);
    }    
    public function simpleFooter(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('simple_footer', $valores);
    }    
    public function endSimpleFooter(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('simple_footer', $valores);
    }    
    public function simpleHeader($primary, $secondary = NULL){
        $valores= array('config.primario' => $primary, 'config.secundario' => $secondary);
        $this->api->component('simple_header', $valores);
    }    
    public function thumbnail($title, $content, $href, $label, $src = NULL, $alt = NULL,  $buttonStyle = 'default', $buttonSize = 'md'){
        $valores= array('config.titulo' => $title, 'config.contenido' => $content, 'config.href' => $href, 'config.label' => $label, 'config.src' => $src, 'config.alt' => $alt,
            'config.buttonStyle' => $buttonStyle, 'config.buttonSize' => $buttonSize);
        $this->api->component('thumbnail', $valores);
    }    
    public function title($title){
        $valores= array('config.title' => $title);
        $this->api->component('title', $valores);
    }    
    public function well($content){
        $valores= array('config.contenido' => $content);
        $this->api->component('well', $valores);
    }    
    /*
     * Formulario
     */   
    public function form($id, $method, $action, $enctype = NULL, $label = NULL){
        $valores= array('config.seccion' => 'cabecera', 'config.id' => $id, 'config.method' => $method, 'config.action' => $action, 'config.label' => $label,
            'config.enctype' => $enctype);
        $this->api->component('form', $valores);
    }    
    public function endForm(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('form', $valores);
    }
    public function formInline($id, $method, $action, $enctype = NULL){
        $valores= array('config.seccion' => 'cabecera', 'config.id' => $id, 'config.method' => $method, 'config.action' => $action, 'config.enctype' => $enctype);
        $this->api->component('form_inline', $valores);
    }    
    public function endFormInline(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('form_inline', $valores);
    }
    public function boxButton(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('keypad', $valores);
    }    
    public function endBoxButton(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('keypad', $valores);
    }    
    public function button($label, $id = NULL, $type = 'button', $onClick = NULL, $style = 'default', $size = 'md'){
        $valores= array('config.type' => $type, 'config.label' => $label, 'config.style' => $style, 'config.size' => $size, 'config.id' => $id, 'config.onclick' => $onClick);
        $this->api->component('button', $valores);
    }    
    public function input($label, $id, $name, $type, $placeholder = NULL, $value = NULL, $message = NULL, $typeError= NULL, $size = 'md'){
        $valores= array('config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.type' => $type, 'config.placeholder' => $placeholder, 
            'config.value' => $value, 'config.message' => $message, 'config.typeError' => $typeError, 'config.size' => $size);
        $this->api->component('input', $valores);
    }
    public function inputInline($label, $id, $name, $type, $placeholder = NULL, $value = NULL, $typeError= NULL, $size = 'md'){
        $valores= array('config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.type' => $type, 
            'config.placeholder' => $placeholder, 'config.value' => $value, 'config.typeError' => $typeError, 'config.size' => $size);
        $this->api->component('input_inline', $valores);
    }
    public function inputButton($inputId, $name, $inputType, $placeholder, $labelButton, $onclick = NULL, $value = NULL, $buttonAfter = TRUE, $buttonId = NULL, $buttonStyle = 'default', $size = 'md'){
        $valores= array('config.inputId' => $inputId, 'config.name' => $name, 'config.inputType' => $inputType, 'config.placeholder' => $placeholder, 'config.labelButton' => $labelButton, 
            'config.onclick' => $onclick, 'config.value' => $value, 'config.size' => $size, 'config.buttonId' => $buttonId, 'config.buttonStyle' => $buttonStyle);
        if($buttonAfter){
            $valores['config.buttonAfter']= 'si';
        } else{
            $valores['config.buttonAfter']= 'no';
        }
        $this->api->component('input_button', $valores);
    }
    public function textarea($label, $id, $name, $rows, $placeholder = NULL, $value = NULL, $message = NULL, $typeError= NULL, $size = 'md'){
        $valores= array('config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.rows' => $rows, 'config.placeholder' => $placeholder,
            'config.value' => $value, 'config.message' => $message, 'config.typeError' => $typeError, 'config.size' => $size);
        $this->api->component('textarea', $valores);
    }
    public function booleanCheckbox($label, $id, $name, $value, $typeError = NULL, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Imprimo lo que seria el Head
        $this->api->component('checkbox', $valores);
        //Imprimo los hijos
        $valores_option= array('config.label' => '', 'config.name' => $name, 'config.inline' => 'si', 'config.id' => $id, 'config.checked' => ($value ? 'si' : 'no'), 'config.value' => '1');       
        $this->api->component('checkbox_option', $valores_option);
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        $this->api->component('checkbox', $valores);
    }
    public function checkboxFull($label, $id, $name, $value, $options, $varLabel=0,$varValue=1,$inline = FALSE, $typeError = NULL, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Imprimo lo que seria el Head
        $this->api->component('checkbox', $valores);
        //Imprimo los hijos
        $can= 0;
        foreach ($options as $option) {
            $optionLab= "";
            $optionVal= "";
            if(is_object($option)){
                $reflection= new Reflection($option);
                $optionLab= $reflection->getProperty($varLabel);
                $optionVal= $reflection->getProperty($varValue);
            }else{
                if(is_array($option)){
                    $optionLab= $option[$varLabel];
                    $optionVal= $option[$varValue];
                }else{
                    $optionLab= $option;
                    $optionVal= $option;
                }                
            }
            $valores_option= array('config.label' => $optionLab, 'config.name' => $name, 'config.id' => $id . $can,'config.value' => $optionVal);
            $checked= FALSE;
            if(is_array($value)){
               if(in_array($optionVal, $value)){
                    $checked= TRUE;
               }
            }
            else{
                if($optionVal == $value){
                    $checked= TRUE;
                }
            }                    
            if($checked == TRUE){
                $valores_option['config.checked']= 'si';
            }
            else{
                $valores_option['config.checked']= 'no';
            }        
            if($inline == TRUE){
                $valores_option['config.inline']= 'si';
            }
            else{
                $valores_option['config.inline']= 'no';
            }
            $this->api->component('checkbox_option', $valores_option);
            $can++;
        }        
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        $this->api->component('checkbox', $valores);
    }
    public function checkbox($label, $id, $name, $value, $inline = FALSE, $typeError = NULL, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Guardo los value para los option
        //Actualizo el nivel
        $this->currentLevel++;
        $this->levelData[$this->currentLevel]['check_name']= $name;
        $this->levelData[$this->currentLevel]['check_value']= $value;
        $this->levelData[$this->currentLevel]['check_id']= $id;
        $this->levelData[$this->currentLevel]['check_num']= 0;
        $this->levelData[$this->currentLevel]['check_inline']= $inline;
        $this->api->component('checkbox', $valores);
    }    
    public function endCheckbox(){
        $valores= array('config.seccion' => 'pie');
        //Elimino los datos
        unset($this->levelData[$this->currentLevel]);
        $this->currentLevel--;
        $this->api->component('checkbox', $valores);
    }    
    public function checkboxOption($label, $value){
        $levelData= &$this->levelData[$this->currentLevel];
        $valores= array('config.label' => $label, 'config.value' => $value, 'config.name' => $levelData['check_name']);     
        $checked= FALSE;
        if(isset($levelData['check_value'])){
            $var= $levelData['check_value'];
            if(is_array($var)){
                if(in_array($value, $var)){
                    $checked= TRUE;
                }
            }
            else{
                if($value == $var){
                    $checked= TRUE;
                }
            }
        }        
        if($checked == TRUE){
            $valores['config.checked']= 'si';
        }
        else{
            $valores['config.checked']= 'no';
        }        
        if($levelData['check_inline'] == TRUE){
            $valores['config.inline']= 'si';
        }
        else{
            $valores['config.inline']= 'no';
        }        
        $valores['config.id']= $levelData['check_id'] . $levelData['check_num'];
        $levelData['check_num']++;
        $this->api->component('checkbox_option', $valores);
    }
    public function radioFull($label, $id, $name, $value, $options, $varLabel=0, $varValue=1, $inline = FALSE, $typeError = NULL, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Imprimo lo que seria el Head
        $this->api->component('radio', $valores);
        //Imprimo los hijos
        $can= 0;
        foreach ($options as $option) {
            $optionLab= "";
            $optionVal= "";
            if(is_object($option)){
                $reflection= new Reflection($option);
                $optionLab= $reflection->getProperty($varLabel);
                $optionVal= $reflection->getProperty($varValue);
            }else{
                if(is_array($option)){
                    $optionLab= $option[$varLabel];
                    $optionVal= $option[$varValue];
                }else{
                    $optionLab= $option;
                    $optionVal= $option;
                }                
            }
            $valores_option= array('config.label' => $optionLab, 'config.name' => $name, 'config.id' => $id . $can,'config.value' => $optionVal);
            if($optionVal == $value){
                $valores_option['config.checked']= 'si';
            }
            else{
                $valores_option['config.checked']= 'no';
            }      
            if($inline == TRUE){
                $valores_option['config.inline']= 'si';
            }
            else{
                $valores_option['config.inline']= 'no';
            }
            $this->api->component('radio_option', $valores_option);
            $can++;
        }        
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        $this->api->component('radio', $valores);
    }
    public function radio($label, $id, $name, $value, $inline = FALSE, $typeError = NULL, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Guardo los value para los option
        //Actualizo el nviel
        $this->currentLevel++;
        $this->levelData[$this->currentLevel]['radio_name']= $name;
        $this->levelData[$this->currentLevel]['radio_value']= $value;
        $this->levelData[$this->currentLevel]['radio_id']= $id;
        $this->levelData[$this->currentLevel]['radio_num']= 0;
        $this->levelData[$this->currentLevel]['radio_inline']= $inline;
        $this->api->component('radio', $valores);
    }    
    public function endRadio(){
        $valores= array('config.seccion' => 'pie');
        //Elimino los datos del nivel
        unset($this->levelData[$this->currentLevel]);
        $this->currentLevel--;
        $this->api->component('radio', $valores);
    }    
    public function radioOption($label, $value){
        $levelData= &$this->levelData[$this->currentLevel];
        $valores= array('config.label' => $label, 'config.value' => $value, 'config.name' => $levelData['radio_name']);
        if(isset($levelData['radio_value'])){
            if($value == $levelData['radio_value']){
                $valores['config.checked']= 'si';
            }
            else{
                $valores['config.checked']= 'no';
            }
        }        
        if($levelData['radio_inline'] == TRUE){
            $valores['config.inline']= 'si';
        }
        else{
            $valores['config.inline']= 'no';
        }        
        $valores['config.id']= $levelData['radio_id'] . $levelData['radio_num'];
        $levelData['radio_num']++;
        $this->api->component('radio_option', $valores);
    }
    public function selectFull($simple, $label, $id, $name, $value, $options, $varLabel = 0, $varValue = 1,$defaultLabel=NULL,$defaultValue=NULL,$onchange = NULL, $multiple = FALSE, $message = NULL, $typeError = NULL, $size = 'md'){
        $form= 'select';
        if($simple){$form='select_simple';}
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.onchange' => $onchange, 
            'config.multiple' => ($multiple ? 'si':'no'), 'config.typeError' => $typeError, 'config.size' => $size);
        if(! $simple){$valores['config.message']= $message;};
        //Imprimo lo que seria el Head
        $this->api->component($form, $valores);
        //Imprimo los hijos  
        if($defaultLabel != NULL){
            $valores_option= array('config.label' => $defaultLabel, 'config.value' => $defaultValue, 'config.checked' => 'no');
            $this->api->component('select_option', $valores_option);
        }
        foreach ($options as $option) {
            $optionLab= "";
            $optionVal= "";
            if(is_object($option)){
                $reflection= new Reflection($option);
                $optionLab= $reflection->getProperty($varLabel);
                $optionVal= $reflection->getProperty($varValue);
            }else{
                if(is_array($option)){
                    $optionLab= $option[$varLabel];
                    $optionVal= $option[$varValue];
                }else{
                    $optionLab= $option;
                    $optionVal= $option;
                }                
            }            
            $valores_option= array('config.label' => $optionLab, 'config.value' => $optionVal);
            $checked= FALSE;
            if(is_array($value)){
               if(in_array($optionVal, $value)){
                    $checked= TRUE;
               }
            }
            else{
                if($optionVal == $value){
                    $checked= TRUE;
                }
            }                    
            if($checked == TRUE){
                $valores_option['config.checked']= 'si';
            }
            else{
                $valores_option['config.checked']= 'no';
            }
            $this->api->component('select_option', $valores_option);
        }        
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        if(! $simple){$valores['config.message']= $message;};
        $this->api->component($form, $valores);
    }
    public function select($label, $id, $name, $value, $onchange = NULL, $multiple = FALSE, $message = NULL, $typeError = NULL, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.onchange' => $onchange, 
            'config.multiple' => ($multiple ? 'si':'no'), 'config.message' => $message, 'config.typeError' => $typeError, 'config.size' => $size);
        //Guardo los value para los option
        //Actualizo el nivel
        $this->currentLevel++;
        $this->levelData[$this->currentLevel]['select_value']= $value;
        $this->levelData[$this->currentLevel]['select_message']= $message;
        $this->api->component('select', $valores);
    }    
    public function endSelect(){
        $message= '';
        $levelData= $this->levelData[$this->currentLevel];
        if(isset($levelData['select_message']))$message=$levelData['select_message'];
        $valores= array('config.seccion' => 'pie', 'config.message' => $message);
        //Elimino los datos
        unset($this->levelData[$this->currentLevel]);
        $this->currentLevel--;
        $this->api->component('select', $valores);
    }
    public function selectSimple($id, $name, $value, $onchange = NULL, $multiple = FALSE, $typeError = NULL, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.id' => $id, 'config.name' => $name, 'config.onchange' => $onchange, 
            'config.multiple' => ($multiple ? 'si':'no'), 'config.typeError' => $typeError, 'config.size' => $size);
        //Guardo los value para los option
        //Actualizo el nivel
        $this->currentLevel++;
        $this->levelData[$this->currentLevel]['select_value']= $value;
        $this->api->component('select_simple', $valores);
    }    
    public function endSelectSimple(){
        $valores= array('config.seccion' => 'pie');
        //Elimino los datos
        unset($this->levelData[$this->currentLevel]);
        $this->currentLevel--;
        $this->api->component('select_simple', $valores);
    }
    public function selectOption($label, $value){
        $levelData= $this->levelData[$this->currentLevel];
        $valores= array('config.label' => $label, 'config.value' => $value);
        $checked= FALSE;
        if(isset($levelData['select_value'])){
            $var= $levelData['select_value'];
            if(is_array($var)){
                if(in_array($value, $var)){
                    $checked= TRUE;
                }
            }
            else{
                if($value == $var){
                    $checked= TRUE;
                }
            }
        }        
        if($checked == TRUE){
            $valores['config.checked']= 'si';
        }
        else{
            $valores['config.checked']= 'no';
        }        
        $this->api->component('select_option', $valores);
    }
    public function login($method, $action, $title, $userName, $userPlaceholder, $passName, $passPlaceholder, $checkName, $checkLabel, $checkValue, $labelButton, $userValue = NULL){
        $valores= array('config.method' => $method, 'config.action' => $action, 'config.title' => $title, 
                        'config.email.name' => $userName, 'config.email.placeholder' => $userPlaceholder,
                        'config.pass.name' => $passName, 'config.pass.placeholder' => $passPlaceholder,
                        'config.check.name' => $checkName, 'config.check.value' => $checkValue, 'config.check.label' => $checkLabel,
                        'config.labelButton' => $labelButton, 'config.email.value' => $userValue);
        $this->api->component('login', $valores);
    }
    /* Navegacion y Menu  */    
    public function dropDownMenu($label, $style = 'default', $size = 'md', $right = FALSE){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.style' => $style, 'config.size' => $size, 'config.right' => ($right ? 'pull-right': ''));
        $this->api->component('drop_down_menu', $valores);
    }    
    public function endDropDownMenu(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('drop_down_menu', $valores);
    }    
    public function menuItem($type, $label = NULL, $href = NULL, $disabled = NULL){
        $valores= array('config.type' => $type, 'config.label' => $label, 'config.disabled' => ($disabled ? 'si' : 'no'), 'config.href' => $href);
        $this->api->component('menu_item', $valores);
    }    
    public function navBarForm($action, $method, $inputName, $inputPlaceholder, $labelButton, $inputValue = NULL){
        $valores= array('config.action' => $action, 'config.method' => $method, 'config.input_name' => $inputName,
                        'config.placeholder' => $inputPlaceholder, 'config.label' => $labelButton, 'config.value_input' => $inputValue);
        $this->api->component('nav_bar_form', $valores);
    }    
    public function navBarLeft(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('nav_bar_left', $valores);
    }    
    public function endNavBarLeft(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('nav_bar_left', $valores);
    }
    public function navBarRight(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('nav_bar_right', $valores);
    }    
    public function endNavBarRight(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('nav_bar_right', $valores);
    }    
    public function navItem($label, $href, $state = NULL){
        $valores= array('config.label' => $label, 'config.href' => $href, 'config.state' => $state);
        $this->api->component('nav_item', $valores);
    }    
    public function navItemDropDown($label, $right = FALSE, $state = NULL){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.right' => ($right ? 'pull-right': ''), 'config.state' => $state);
        $this->api->component('nav_item_drop_down', $valores);
    }    
    public function endNavItemDropDown(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('nav_item_drop_down', $valores);
    }    
    public function navigationBar($logo, $href, $containerFluid = TRUE, $position='', $inverse=FALSE){
        $valores= array('config.seccion' => 'cabecera', 'config.logo' => $logo, 'config.href' => $href, 'config.position' => $position);
        $valores['config.containerFluid']= 'si';
        if(!$containerFluid)$valores['config.containerFluid']='no';
        $valores['config.inverse']= '';
        if($inverse)$valores['config.inverse']='navbar-inverse';
        $this->api->component('navigation_bar', $valores);
    }    
    public function endNavigationBar(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('navigation_bar', $valores);
    }    
    public function navigationMenu($type, $justified = FALSE, $stacked = FALSE){
        $valores= array('config.seccion' => 'cabecera', 'config.type' => $type, 'config.justified' => ($justified ? 'si' : 'no'), 'config.stacked' => ($stacked ? 'si' : 'no'));
        $this->api->component('navigation_menu', $valores);
    }    
    public function endNavigationMenu(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('navigation_menu', $valores);
    }    
    /* Componentes Varios */    
    public function breadcrumb(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('breadcrumb', $valores);
    }    
    public function endBreadcrumb(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('breadcrumb', $valores);
    }
    public function buttonGroup($label, $vertical = FALSE, $size = 'md'){
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size);
        if($vertical){
            $valores['config.vertical']= 'si';
        } else {
            $valores['config.vertical']= 'no';
        }
        $this->api->component('button_group', $valores);
    }    
    public function endButtonGroup(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('button_group', $valores);
    }
    public function buttonToolbar(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('button_toolbar', $valores);
    }    
    public function endButtonToolbar(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('button_toolbar', $valores);
    }
    public function carousel(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('carousel', $valores);
    }   
    public function endCarousel($labelPrevious="", $labelNext=""){
        $valores= array('config.seccion' => 'pie', 'config.labelPrevious' => $labelPrevious, 'config.labelNext' => $labelNext);
        $this->api->component('carousel', $valores);
    }
    public function carouselItem($src, $alt, $state=''){
        $valores= array('config.seccion' => 'cabecera', 'config.src'=>$src,'config.alt'=>$alt,'config.state'=>$state);
        $this->api->component('carousel_item', $valores);
    }    
    public function endCarouselItem(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('carousel_item', $valores);
    }
    public function em($value){
        $valores= array('config.value' => $value);
        $this->api->component('em', $valores);
    }    
    public function li($label, $active = NULL, $badge = NULL){
        $valores= array('config.type' => 'lista', 'config.label' => $label, 'config.badge' => $badge, 'config.active' => ($active ? 'active': ''));
        $this->api->component('li', $valores);
    }    
    public function liLink($label, $href, $active = NULL, $badge = NULL){
        $valores= array('config.type' => 'lista_a', 'config.label' => $label, 'config.href' => $href, 'config.badge' => $badge, 'config.active' => ($active ? 'active': ''));
        $this->api->component('li', $valores);
    }    
    public function mediaObject($href, $alt, $src, $title, $content){
        $valores= array('config.seccion' => 'cabecera', 'config.href' => $href, 'config.alt' => $alt,
                        'congif.src' => $src, 'config.titulo' => $title, 'config.contenido' => $content);
        $this->api->component('media_object', $valores);
    }    
    public function endMediaObject(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('media_object', $valores);
    }    
    public function pagerItem($label, $href, $state = NULL){
        $valores= array('config.first' => 'no', 'config.last' => 'no', 'config.label' => $label, 'config.href' => $href, 'config.state' => $state);
        $this->api->component('pager_item', $valores);
    }    
    public function pagerItemFirst($href, $state = NULL){
        $valores= array('config.first' => 'si', 'config.href' => $href, 'config.state' => $state);
        $this->api->component('pager_item', $valores);
    }    
    public function pagerItemLast($href, $state = NULL){
        $valores= array('config.last' => 'si', 'config.first' => 'no', 'config.href' => $href, 'config.state' => $state);
        $this->api->component('pager_item', $valores);
    }    
    public function pager(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('pager', $valores);
    }    
    public function endPager(){
        
        $valores= array('config.seccion' => 'pie');
        $this->api->component('pager', $valores);
    }    
    public function panel($title = NULL){
        $valores= array('config.seccion' => 'cabecera', 'config.titulo' => $title);
        $this->api->component('panel', $valores);
    }
    public function endPanel($fot = NULL){
        $valores= array('config.seccion' => 'pie', 'config.pie' => $fot);
        $this->api->component('panel', $valores); 
    }
    public function paragraph($align = NULL, $lead = NULL){
        $valores= array('config.seccion' => 'cabecera', 'config.align' => $align, 'config.lead' => $lead);
        $this->api->component('paragraph', $valores);
    }    
    public function endParagraph(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('paragraph', $valores);
    }    
    public function small($value){
        $valores= array('config.value' => $value);
        $this->api->component('small', $valores);
    }    
    public function strong($value){
        $valores= array('config.value' => $value);
        $this->api->component('strong', $valores);
    }    
    public function text($value){
        $valores= array('config.value' => $value);
        $this->api->component('text', $valores);
    }    
    public function ul(){
        $valores= array('config.type' => 'lista', 'config.seccion' => 'cabecera');
        $this->api->component('ul', $valores);
    }    
    public function endUl(){
        $valores= array('config.type' => 'lista', 'config.seccion' => 'pie');
        $this->api->component('ul', $valores);
    }    
    public function ulLink(){
        $valores= array('config.type' => 'lista_a', 'config.seccion' => 'cabecera');
        $this->api->component('ul', $valores);
    }    
    public function endUlLink(){
        $valores= array('config.type' => 'lista_a', 'config.seccion' => 'pie');
        $this->api->component('ul', $valores);
    }    
    /* Table */    
    public function table(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('table', $valores);
    }    
    public function endTable(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('table', $valores);
    }    
    public function tableField($value = NULL){
        $valores= array('config.seccion' => 'cabecera', 'config.value' => $value);
        $this->api->component('table_field', $valores);
    }    
    public function endTableField(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('table_field', $valores);
    }    
    public function tableHead(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('table_head', $valores);
    }    
    public function endTableHead(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('table_head', $valores);
    }    
    public function tableHeadField($value = NULL){
        $valores= array('config.seccion' => 'cabecera', 'config.value' => $value);
        $this->api->component('table_head_field', $valores);
    }    
    public function endTableHeadField(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('table_head_field', $valores);
    }    
    public function tableRow(){
        $valores= array('config.seccion' => 'cabecera');
        $this->api->component('table_row', $valores);
    }    
    public function endTableRow(){
        $valores= array('config.seccion' => 'pie');
        $this->api->component('table_row', $valores);
    }
}

/**
 * Esta clase se encarga de acceder a las propiedades de los distintos objetos y clases.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category \UiServices
 */
class Reflection{
    protected $object;
    /** @var \ReflectionObject */
    protected $reflection;
    /**
     * Cosntructor - Se le pasa el objeto a tratar
     * @param type $object
     */
    public function __construct($object) {
        $this->setObject($object);
    }
    public function setObject($object){
        $this->object= $object;
        //Crea un Reflection para acceder a las caracteristicas del objeto
        $this->reflection= new \ReflectionObject($object); 
    }
    /**
     * Lee una propiedad de un objeto. Para poder la propiedad debe exister el metodo get public y/o ser una variable
     * de acceso public. Primero intenta con el metodo get.
     * @param string $property
     * @return type
     */
    public function getProperty($property){        
        //Primero Busco por get
        $getMethod= 'get' . strtoupper($property[0]) . substr($property, 1);                     
        if($this->reflection->hasMethod($getMethod)){
            $reflectionMethod= $this->reflection->getMethod($getMethod);
            //Si existe el metodo set y es public lo seteo
            if($reflectionMethod->isPublic()){
                return $this->object->$getMethod();
            }            
        }else if($this->reflection->hasProperty($property)){
            //Si existe la propiedad y es public la seteo
            $reflectionProperty= $this->reflection->getProperty($property);
            if($reflectionProperty->isPublic()){
                return $this->object->$property;
            }
        }
        return NULL;
    }
    /**
     * Retorna un array con todos los valores de las propiedades.
     * Para leer cada propiedad llama al metodo getProperty.
     * @param array $properties
     * @return array - propertyName => value
     */
    public function getProperties($properties){
        $values= array();
        foreach ($properties as $key => $value) {
            $values[$key]= $this->setProperty($key, $value);
        }
        return $values;
    }
}