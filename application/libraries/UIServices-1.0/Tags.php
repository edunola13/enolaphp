<?php
/** Description of Tags */
class Tags {
    public static function theme($nombre = 'base'){
        $api= ApiUi::getInstance();    
        return $api->theme($nombre);               
    }
    public static function javaScript($nombre = 'base'){
        $api= ApiUi::getInstance();    
        return $api->javaScript($nombre);
    }
    public static function setProyecto($nombre){
        ApiUi::$proyecto= $nombre;
    }
    /* DEFINICION DE TODOS LOS COMPONENTES */
    public static function columnParsed($tableId, $title, $units){
        $api= ApiUi::getInstance();
        $valores= array('config.tableId' => $tableId, 'config.title' => $title, 'config.units' => $units);
        $api->componente('column-parsed', $valores);
    }
    public static function dateCountdown($id, $date, $width, $height){
        $api= ApiUi::getInstance();
        $valores= array('config.id' => $id, 'config.date' => $date, 'config.width' => $width, 'config.height' => $height);
        $api->componente('date_countdown', $valores);
    }    
    /* Estaticos */        
    public static function address($name, $dir, $locale, $tel){
        $api= ApiUi::getInstance();
        $valores= array('config.nombre' => $name, 'config.direccion' => $dir, 'config.localidad' => $locale, 'config.telefono' => $tel);
        $api->componente('address', $valores);
    }    
    public static function alertMessage($type, $message, $strong = NULL){
        if($message != NULL){
            $api= ApiUi::getInstance();
            $valores= array('config.type' => $type, 'config.strong' => $strong, 'config.message' => $message);
            $api->componente('alert_message', $valores);
        }
    }    
    public static function badge($label, $href, $badge = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'config.href' => $href, 'config.badge' => $badge);
        $api->componente('badge', $valores);
    }
    public static function buttonBadge($label, $badge = NULL, $id = NULL, $onClick = NULL, $type = 'button', $style = 'primary', $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'config.type' => $type, 'config.badge' => $badge, 'config.style' => $style, 'config.size' => $size, 'config.id' => $id, 'config.onclick' => $onClick);
        $api->componente('button_badge', $valores);
    }
    public static function blockquote($text, $source){
        $api= ApiUi::getInstance();
        $valores= array('config.texto' => $text, 'config.fuente' => $source);
        $api->componente('blockquote', $valores);
    }    
    public static function fixedFooter(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('fixed_footer', $valores);
    }    
    public static function endFixedFooter(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('fixed_footer', $valores);
    }    
    public static function formSearch($name, $label, $button_id, $onClick, $placeholder, $value = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.input.name' => $name, 'config.input.placeholder' => $placeholder, 'config.label' => $label,
            'config.id' => $button_id, 'config.onclick' => $onClick, 'datos.value_input' => $value);
        $api->componente('form_search', $valores);
    }    
    public static function iframe($src, $ratio = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.src' => $src, 'config.ratio' => $ratio);
        $api->componente('iframe', $valores);
    }    
    public static function image($alt, $src, $type = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.alt' => $alt, 'config.src' => $src, 'config.type' => $type);
        $api->componente('image', $valores);
    }    
    public static function jumbotron($title, $content, $href, $label, $buttonStyle = 'default', $buttonSize = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.titulo' => $title, 'config.contenido' => $content, 'config.href' => $href, 'config.label' => $label, 'config.buttonStyle' => $buttonStyle, 'config.buttonSize' => $buttonSize);
        $api->componente('jumbotron', $valores);
    }
    public static function link($id, $href, $label, $button=FALSE, $style = 'default', $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.id' => $id, 'config.href' => $href, 'config.label' => $label, 'config.style' => $style, 'config.size' => $size);
        $valores['config.button']='no';
        if($button)$valores['config.button']='si';
        $api->componente('link', $valores);
    } 
    public static function paginadorSimple($preState, $preHref, $preLabel, $nextState, $nextHref, $nextLabel){
        $api= ApiUi::getInstance();
        $valores= array('config.previous.state' => $preState, 'config.previous.href' => $preHref, 'config.previous.label' => $preLabel,
                        'config.next.state' => $nextState, 'config.next.href' => $nextHref, 'config.next.label' => $nextLabel);
        $api->componente('paginador_simple', $valores);
    }    
    public static function progressBar($percentage, $striped = FALSE){
        $api= ApiUi::getInstance();
        $valores= array('config.porcentaje' => $percentage, 'config.striped' => ($striped ? 'si' : 'no'));
        $api->componente('progress_bar', $valores);
    }    
    public static function simpleFooter(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('simple_footer', $valores);
    }    
    public static function endSimpleFooter(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('simple_footer', $valores);
    }    
    public static function simpleHeader($primary, $secondary = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.primario' => $primary, 'config.secundario' => $secondary);
        $api->componente('simple_header', $valores);
    }    
    public static function thumbnail($title, $content, $href, $label, $src = NULL, $alt = NULL,  $buttonStyle = 'default', $buttonSize = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.titulo' => $title, 'config.contenido' => $content, 'config.href' => $href, 'config.label' => $label, 'config.src' => $src, 'config.alt' => $alt,
            'config.buttonStyle' => $buttonStyle, 'config.buttonSize' => $buttonSize);
        $api->componente('thumbnail', $valores);
    }    
    public static function title($title){
        $api= ApiUi::getInstance();
        $valores= array('config.title' => $title);
        $api->componente('title', $valores);
    }    
    public static function well($content){
        $api= ApiUi::getInstance();
        $valores= array('config.contenido' => $content);
        $api->componente('well', $valores);
    }    
    /*
     * Formulario
     */   
    public static function form($id, $method, $action, $enctype = NULL, $label = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.id' => $id, 'config.method' => $method, 'config.action' => $action, 'config.label' => $label,
            'config.enctype' => $enctype);
        $api->componente('formulario', $valores);
    }    
    public static function endForm(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('formulario', $valores);
    }
    public static function formInline($id, $method, $action, $enctype = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.id' => $id, 'config.method' => $method, 'config.action' => $action, 'config.enctype' => $enctype);
        $api->componente('form_inline', $valores);
    }    
    public static function endFormInline(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('form_inline', $valores);
    }
    public static function botonera(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('botonera', $valores);
    }    
    public static function endBotonera(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('botonera', $valores);
    }    
    public static function button($label, $id = NULL, $type = 'button', $onClick = NULL, $style = 'default', $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => $type, 'config.label' => $label, 'config.style' => $style, 'config.size' => $size, 'config.id' => $id, 'config.onclick' => $onClick);
        $api->componente('button', $valores);
    }    
    public static function input($label, $id, $name, $type, $placeholder = NULL, $value = NULL, $message = NULL, $typeError= NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.type' => $type, 'config.placeholder' => $placeholder, 
            'datos.value' => $value, 'config.message' => $message, 'config.typeError' => $typeError, 'config.size' => $size);
        $api->componente('input', $valores);
    }
    public static function inputInline($label, $id, $name, $type, $placeholder = NULL, $value = NULL, $typeError= NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.type' => $type, 
            'config.placeholder' => $placeholder, 'datos.value' => $value, 'config.typeError' => $typeError, 'config.size' => $size);
        $api->componente('input_inline', $valores);
    }
    public static function inputButton($inputId, $name, $inputType, $placeholder, $labelButton, $onclick = NULL, $value = NULL, $buttonAfter = TRUE, $buttonId = NULL, $buttonStyle = 'default', $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.inputId' => $inputId, 'config.name' => $name, 'config.inputType' => $inputType, 'config.placeholder' => $placeholder, 'config.labelButton' => $labelButton, 
            'config.onclick' => $onclick, 'datos.value' => $value, 'config.size' => $size, 'config.buttonId' => $buttonId, 'config.buttonStyle' => $buttonStyle);
        if($buttonAfter){
            $valores['config.buttonAfter']= 'si';
        } else{
            $valores['config.buttonAfter']= 'no';
        }
        $api->componente('input_button', $valores);
    }
    public static function textarea($label, $id, $name, $rows, $placeholder = NULL, $value = NULL, $message = NULL, $typeError= NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.rows' => $rows, 'config.placeholder' => $placeholder,
            'datos.value' => $value, 'config.message' => $message, 'config.typeError' => $typeError, 'config.size' => $size);
        $api->componente('textarea', $valores);
    }
    public static function booleanCheckbox($label, $id, $name, $value, $typeError = NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Imprimo lo que seria el Head
        $api->componente('checkbox', $valores);
        //Imprimo los hijos
        $valores_option= array('config.label' => '', 'config.name' => $name, 'config.inline' => 'si', 'config.id' => $id, 'config.checked' => ($value ? 'si' : 'no'), 'datos.value' => '1');       
        $api->componente('checkbox_option', $valores_option);
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        $api->componente('checkbox', $valores);
    }
    public static function checkboxFull($label, $id, $name, $value, $options, $varLabel=NULL,$varValue=NULL,$inline = FALSE, $typeError = NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Imprimo lo que seria el Head
        $api->componente('checkbox', $valores);
        //Imprimo los hijos
        $can= 0;
        foreach ($options as $option) {
            $optionLab= "";
            $optionVal= "";
            if(is_object($option)){
                $optionLab= $option->$varLabel;
                $optionVal= $option->$varValue;
            }else{
                $optionLab= $option[0];
                $optionVal= $option[1];                
            }
            $valores_option= array('config.label' => $optionLab, 'config.name' => $name, 'config.id' => $id . $can,'datos.value' => $optionVal);
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
            $api->componente('checkbox_option', $valores_option);
            $can++;
        }        
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        $api->componente('checkbox', $valores);
    }
    public static function checkbox($label, $id, $name, $value, $inline = FALSE, $typeError = NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Guardo los value para los option
        $GLOBALS['check_name']= $name;
        $GLOBALS['check_value']= $value;
        $GLOBALS['check_id']= $id;
        $GLOBALS['check_num']= 0;
        $GLOBALS['check_inline']= $inline;
        $api->componente('checkbox', $valores);
    }    
    public static function endCheckbox(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        //Elimino los datos
        unset($GLOBALS['check_name']);
        unset($GLOBALS['check_value']);
        unset($GLOBALS['check_id']);
        unset($GLOBALS['check_num']);
        unset($GLOBALS['check_inline']);
        $api->componente('checkbox', $valores);
    }    
    public static function checkboxOption($label, $value){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'datos.value' => $value, 'config.name' => $GLOBALS['check_name']);     
        $checked= FALSE;
        if(isset($GLOBALS['check_value'])){
            $var= $GLOBALS['check_value'];
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
        if($GLOBALS['check_inline'] == TRUE){
            $valores['config.inline']= 'si';
        }
        else{
            $valores['config.inline']= 'no';
        }        
        $valores['config.id']= $GLOBALS['check_id'] . $GLOBALS['check_num'];
        $GLOBALS['check_num']++;
        $api->componente('checkbox_option', $valores);
    }
    public static function radioFull($label, $id, $name, $value, $options, $varLabel = NULL, $varValue=NULL, $inline = FALSE, $typeError = NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Imprimo lo que seria el Head
        $api->componente('radio', $valores);
        //Imprimo los hijos
        $can= 0;
        foreach ($options as $option) {
            $optionLab= "";
            $optionVal= "";
            if(is_object($option)){
                $optionLab= $option->$varLabel;
                $optionVal= $option->$varValue;
            }else{
                $optionLab= $option[0];
                $optionVal= $option[1];                
            }
            $valores_option= array('config.label' => $optionLab, 'config.name' => $name, 'config.id' => $id . $can,'datos.value' => $optionVal);
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
            $api->componente('radio_option', $valores_option);
            $can++;
        }        
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        $api->componente('radio', $valores);
    }
    public static function radio($label, $id, $name, $value, $inline = FALSE, $typeError = NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size, 'config.typeError' => $typeError);
        //Guardo los value para los option
        $GLOBALS['radio_name']= $name;
        $GLOBALS['radio_value']= $value;
        $GLOBALS['radio_id']= $id;
        $GLOBALS['radio_num']= 0;
        $GLOBALS['radio_inline']= $inline;
        $api->componente('radio', $valores);
    }    
    public static function endRadio(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        //Elimino los datos
        unset($GLOBALS['radio_name']);
        unset($GLOBALS['radio_value']);
        unset($GLOBALS['radio_id']);
        unset($GLOBALS['radio_num']);
        unset($GLOBALS['radio_inline']);
        $api->componente('radio', $valores);
    }    
    public static function radioOption($label, $value){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'datos.value' => $value, 'config.name' => $GLOBALS['radio_name']);
        if(isset($GLOBALS['radio_value'])){
            if($value == $GLOBALS['radio_value']){
                $valores['config.checked']= 'si';
            }
            else{
                $valores['config.checked']= 'no';
            }
        }        
        if($GLOBALS['radio_inline'] == TRUE){
            $valores['config.inline']= 'si';
        }
        else{
            $valores['config.inline']= 'no';
        }        
        $valores['config.id']= $GLOBALS['radio_id'] . $GLOBALS['radio_num'];
        $GLOBALS['radio_num']++;
        $api->componente('radio_option', $valores);
    }
    public static function selectFull($simple, $label, $id, $name, $value, $options, $varLabel = 0, $varValue = 1,$defaultLabel=NULL,$defaultValue=NULL,$onchange = NULL, $multiple = FALSE, $message = NULL, $typeError = NULL, $size = 'md'){
        $form= 'select';
        if($simple){$form='select_simple';}
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.onchange' => $onchange, 
            'config.multiple' => ($multiple ? 'si':'no'), 'config.typeError' => $typeError, 'config.size' => $size);
        if(! $simple){$valores['config.message']= $message;};
        //Imprimo lo que seria el Head
        $api->componente($form, $valores);
        //Imprimo los hijos
        $can= 0;        
        if($defaultLabel != NULL){
            $valores_option= array('config.label' => $defaultLabel, 'datos.value' => $defaultValue, 'config.checked' => 'no');
            $api->componente('select_option', $valores_option);
        }
        foreach ($options as $option) {
            $optionLab= "";
            $optionVal= "";
            if(is_object($option)){
                $optionLab= $option->$varLabel;
                $optionVal= $option->$varValue;
            }else{
                $optionLab= $option[$varLabel];
                $optionVal= $option[$varValue];
            }            
            $valores_option= array('config.label' => $optionLab, 'datos.value' => $optionVal);
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
            $api->componente('select_option', $valores_option);
        }        
        //Armo el Pie
        $valores= array('config.seccion' => 'pie');
        if(! $simple){$valores['config.message']= $message;};
        $api->componente($form, $valores);
    }
    public static function select($label, $id, $name, $value, $onchange = NULL, $multiple = FALSE, $message = NULL, $typeError = NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.id' => $id, 'config.name' => $name, 'config.onchange' => $onchange, 
            'config.multiple' => ($multiple ? 'si':'no'), 'config.message' => $message, 'config.typeError' => $typeError, 'config.size' => $size);
        //Guardo los value para los option
        $GLOBALS['select_value']= $value;
        $GLOBALS['select_message']= $message;
        $api->componente('select', $valores);
    }    
    public static function endSelect(){
        $api= ApiUi::getInstance();
        $message= '';
        if(isset($GLOBALS['select_message']))$message=$GLOBALS['select_message'];
        $valores= array('config.seccion' => 'pie', 'config.message' => $message);
        //Elimino los datos
        unset($GLOBALS['select_value']);
        unset($GLOBALS['select_message']);
        $api->componente('select', $valores);
    }
    public static function selectSimple($id, $name, $value, $onchange = NULL, $multiple = FALSE, $typeError = NULL, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.id' => $id, 'config.name' => $name, 'config.onchange' => $onchange, 
            'config.multiple' => ($multiple ? 'si':'no'), 'config.typeError' => $typeError, 'config.size' => $size);
        //Guardo los value para los option
        $GLOBALS['select_value']= $value;
        $api->componente('select_simple', $valores);
    }    
    public static function endSelectSimple(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        //Elimino los datos
        unset($GLOBALS['select_value']);
        $api->componente('select_simple', $valores);
    }
    public static function selectOption($label, $value){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'datos.value' => $value);
        $checked= FALSE;
        if(isset($GLOBALS['select_value'])){
            $var= $GLOBALS['select_value'];
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
        $api->componente('select_option', $valores);
    }
    public static function login($method, $action, $title, $userName, $userPlaceholder, $passName, $passPlaceholder, $checkName, $checkLabel, $checkValue, $labelButton, $userValue = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.method' => $method, 'config.action' => $action, 'config.title' => $title, 
                        'datos.email.name' => $userName, 'datos.email.placeholder' => $userPlaceholder,
                        'datos.pass.name' => $passName, 'datos.pass.placeholder' => $passPlaceholder,
                        'datos.check.name' => $checkName, 'datos.check.value' => $checkValue, 'datos.check.label' => $checkLabel,
                        'config.labelButton' => $labelButton, 'datos.email.value' => $userValue);
        $api->componente('login', $valores);
    }
    /* Navegacion y Menu  */    
    public static function dropDownMenu($label, $style = 'default', $size = 'md', $right = FALSE){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.style' => $style, 'config.size' => $size, 'config.right' => ($right ? 'pull-right': ''));
        $api->componente('drop_down_menu', $valores);
    }    
    public static function endDropDownMenu(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('drop_down_menu', $valores);
    }    
    public static function menuItem($type, $label = NULL, $href = NULL, $disabled = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => $type, 'config.label' => $label, 'config.disabled' => ($disabled ? 'si' : 'no'), 'config.href' => $href);
        $api->componente('menu_item', $valores);
    }    
    public static function navBarForm($action, $method, $inputName, $inputPlaceholder, $labelButton, $inputValue = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.action' => $action, 'config.method' => $method, 'config.input_name' => $inputName,
                        'config.placeholder' => $inputPlaceholder, 'config.label' => $labelButton, 'config.value_input' => $inputValue);
        $api->componente('nav_bar_form', $valores);
    }    
    public static function navBarLeft(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('nav_bar_left', $valores);
    }    
    public static function endNavBarLeft(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('nav_bar_left', $valores);
    }
    public static function navBarRight(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('nav_bar_right', $valores);
    }    
    public static function endNavBarRight(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('nav_bar_right', $valores);
    }    
    public static function navItem($label, $href, $state = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.label' => $label, 'config.href' => $href, 'config.state' => $state);
        $api->componente('nav_item', $valores);
    }    
    public static function navItemDropDown($label, $right = FALSE, $state = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.right' => ($right ? 'pull-right': ''), 'config.state' => $state);
        $api->componente('nav_item_drop_down', $valores);
    }    
    public static function endNavItemDropDown(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('nav_item_drop_down', $valores);
    }    
    public static function navigationBar($logo, $href, $containerFluid = TRUE, $position='', $inverse=FALSE){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.logo' => $logo, 'config.href' => $href, 'config.position' => $position);
        $valores['config.containerFluid']= 'si';
        if(!$containerFluid)$valores['config.containerFluid']='no';
        $valores['config.inverse']= '';
        if($inverse)$valores['config.inverse']='navbar-inverse';
        $api->componente('navigation_bar', $valores);
    }    
    public static function endNavigationBar(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('navigation_bar', $valores);
    }    
    public static function navigationMenu($type, $justified = FALSE, $stacked = FALSE){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.type' => $type, 'config.justified' => ($justified ? 'si' : 'no'), 'config.stacked' => ($stacked ? 'si' : 'no'));
        $api->componente('navigation_menu', $valores);
    }    
    public static function endNavigationMenu(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('navigation_menu', $valores);
    }    
    /* Componentes Varios */    
    public static function breadcrumb(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('breadcrumb', $valores);
    }    
    public static function endBreadcrumb(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('breadcrumb', $valores);
    }
    public static function buttonGroup($label, $vertical = FALSE, $size = 'md'){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.label' => $label, 'config.size' => $size);
        if($vertical){
            $valores['config.vertical']= 'si';
        } else {
            $valores['config.vertical']= 'no';
        }
        $api->componente('button_group', $valores);
    }    
    public static function endButtonGroup(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('button_group', $valores);
    }
    public static function buttonToolbar(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('button_toolbar', $valores);
    }    
    public static function endButtonToolbar(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('button_toolbar', $valores);
    }
    public static function carousel(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('carousel', $valores);
    }   
    public static function endCarousel($labelPrevious="", $labelNext=""){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie', 'config.labelPrevious' => $labelPrevious, 'config.labelNext' => $labelNext);
        $api->componente('carousel', $valores);
    }
    public static function carouselItem($src, $alt, $state=''){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.src'=>$src,'config.alt'=>$alt,'config.state'=>$state);
        $api->componente('carousel_item', $valores);
    }    
    public static function endCarouselItem(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('carousel_item', $valores);
    }
    public static function em($value){
        $api= ApiUi::getInstance();
        $valores= array('config.value' => $value);
        $api->componente('em', $valores);
    }    
    public static function li($label, $active = NULL, $badge = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => 'lista', 'config.label' => $label, 'config.badge' => $badge, 'config.active' => ($active ? 'active': ''));
        $api->componente('li', $valores);
    }    
    public static function liLink($label, $href, $active = NULL, $badge = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => 'lista_a', 'config.label' => $label, 'config.href' => $href, 'config.badge' => $badge, 'config.active' => ($active ? 'active': ''));
        $api->componente('li', $valores);
    }    
    public static function mediaObject($href, $alt, $src, $title, $content){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.href' => $href, 'config.alt' => $alt,
                        'congif.src' => $src, 'config.titulo' => $title, 'config.contenido' => $content);
        $api->componente('media_object', $valores);
    }    
    public static function endMediaObject(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('media_object', $valores);
    }    
    public static function page($label, $href, $state = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.first' => 'no', 'config.last' => 'no', 'config.label' => $label, 'config.href' => $href, 'config.state' => $state);
        $api->componente('pagina', $valores);
    }    
    public static function pageFirst($href, $state = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.first' => 'si', 'config.href' => $href, 'config.state' => $state);
        $api->componente('pagina', $valores);
    }    
    public static function pageLast($href, $state = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.last' => 'si', 'config.first' => 'no', 'config.href' => $href, 'config.state' => $state);
        $api->componente('pagina', $valores);
    }    
    public static function paginator(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('paginador', $valores);
    }    
    public static function endPaginator(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('paginador', $valores);
    }    
    public static function panel($title = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.titulo' => $title);
        $api->componente('panel', $valores);
    }
    public static function endPanel($fot = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie', 'config.pie' => $fot);
        $api->componente('panel', $valores); 
    }
    public static function paragraph($align = NULL, $lead = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.align' => $align, 'config.lead' => $lead);
        $api->componente('parrafo', $valores);
    }    
    public static function endParagraph(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('parrafo', $valores);
    }    
    public static function small($value){
        $api= ApiUi::getInstance();
        $valores= array('config.value' => $value);
        $api->componente('small', $valores);
    }    
    public static function strong($value){
        $api= ApiUi::getInstance();
        $valores= array('config.value' => $value);
        $api->componente('strong', $valores);
    }    
    public static function text($value){
        $api= ApiUi::getInstance();
        $valores= array('config.value' => $value);
        $api->componente('text', $valores);
    }    
    public static function ul(){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => 'lista', 'config.seccion' => 'cabecera');
        $api->componente('ul', $valores);
    }    
    public static function endUl(){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => 'lista', 'config.seccion' => 'pie');
        $api->componente('ul', $valores);
    }    
    public static function ulLink(){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => 'lista_a', 'config.seccion' => 'cabecera');
        $api->componente('ul', $valores);
    }    
    public static function endUlLink(){
        $api= ApiUi::getInstance();
        $valores= array('config.type' => 'lista_a', 'config.seccion' => 'pie');
        $api->componente('ul', $valores);
    }    
    /* Table */    
    public static function table(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('table', $valores);
    }    
    public static function endTable(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('table', $valores);
    }    
    public static function tableField($value = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.value' => $value);
        $api->componente('table_field', $valores);
    }    
    public static function endTableField(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('table_field', $valores);
    }    
    public static function tableHead(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('table_head', $valores);
    }    
    public static function endTableHead(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('table_head', $valores);
    }    
    public static function tableHeadField($value = NULL){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera', 'config.value' => $value);
        $api->componente('table_head_field', $valores);
    }    
    public static function endTableHeadField(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('table_head_field', $valores);
    }    
    public static function tableRow(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'cabecera');
        $api->componente('table_row', $valores);
    }    
    public static function endTableRow(){
        $api= ApiUi::getInstance();
        $valores= array('config.seccion' => 'pie');
        $api->componente('table_row', $valores);
    }
}