<?php
/**
 * Theme
 * @param type $nombre
 * @return type 
 */
function ui_theme($nombre = 'base'){
    echo Tags::theme($nombre);              
}    
/**
 * Java Script
 * @param type $nombre
 * @return type 
 */
function ui_javaScript($nombre = 'base'){
    echo Tags::javaScript($nombre);
}    
/*
 * DEFINICION DE TODOS LOS COMPONENTES 
 */    
/*
 * Charts
 */    
function ui_column_parsed($tableId, $title, $units){
    echo Tags::column_parsed($tableId, $title, $units);
}   
/*
 * Estaticos
 */        
function ui_address($name, $dir, $locale, $tel){
    echo Tags::address($name, $dir, $locale, $tel);
}    
function ui_alert_message($type, $message, $strong = NULL){
    echo Tags::alert_message($type, $message, $strong);
}    
function ui_badge($label, $badge = NULL){
    echo Tags::badge($label, $badge);
}    
function ui_blockquote($text, $source){
    echo Tags::blockquote($text, $source);
}    
function ui_fixed_footer(){
    echo Tags::fixed_footer();
}    
function ui_end_fixed_footer(){
    echo Tags::end_fixed_footer();
}    
function ui_form_search($label, $name, $placeholder = NULL, $value = NULL){
    echo Tags::form_search($label, $name, $placeholder, $value);
}    
function ui_iframe($src, $ratio = NULL){
    echo Tags::iframe($src, $ratio);
}    
function ui_image($alt, $src, $type = NULL){
    echo Tags::image($alt, $src, $type);
}    
function ui_jumbotron($title, $content, $href, $label){
    echo Tags::jumbotron($title, $content, $href, $label);
}    
function ui_paginador_simple($preState, $preHref, $preLabel, $nextState, $nextHref, $nextLabel){
    echo Tags::paginador_simple($preState, $preHref, $preLabel, $nextState, $nextHref, $nextLabel);
}    
function ui_progress_bar($percentage, $striped = FALSE){
    echo Tags::progress_bar($percentage, $striped);
}    
function ui_simple_footer(){
    echo Tags::simple_footer();
}    
function ui_end_simple_footer(){
    echo Tags::end_simple_footer();
}    
function ui_simple_header($primary, $secondary = NULL){
    echo Tags::simple_header($primary, $secondary);
}    
function ui_thumbnail($title, $content, $href, $label, $src = NULL, $alt = NULL){
    echo Tags::thumbnail($title, $content, $href, $label, $src, $alt);
}    
function ui_title($title){
    echo Tags::title($title);
}    
function ui_well($content){
    echo Tags::well($content);
}    
/*
 * Formulario
 */    
function ui_formulario($method, $action, $label = NULL){
    echo Tags::formulario($method, $action, $label);
}    
function ui_end_formulario(){
    echo Tags::end_formulario();
}    
function ui_botonera(){
    echo Tags::botonera();
}    
function ui_end_botonera(){
    echo Tags::end_botonera();
}    
function ui_button($type, $label){
    echo Tags::button($type, $label);
}    
function ui_boolean_checkbox($label, $name, $value){
    echo Tags::boolean_checkbox($label, $name, $value);
}
function ui_checkbox($label, $name, $value, $inline = FALSE){
    echo Tags::checkbox($label, $name, $value, $inline);
}    
function ui_end_checkbox(){
    echo Tags::end_checkbox();
}    
function ui_checkbox_option($label, $value){
    echo Tags::checkbox_option($label, $value);
}    
function ui_file_button($label, $name){
    echo Tags::file_button($label, $name);
}    
function ui_input($label, $name, $type, $placeholder = NULL, $value = NULL){
    echo Tags::input($label, $name, $type, $placeholder, $value);
}    
function ui_login($method, $action, $title, $userName, $userPlaceholder, $passName, $passPlaceholder, $checkName, $checkLabel, $checkValue, $labelButton, $userValue = NULL){
    echo Tags::login($method, $action, $title, $userName, $userPlaceholder, $passName, $passPlaceholder, $checkName, $checkLabel, $checkValue, $labelButton, $userValue);
}    
function ui_radio($label, $name, $value, $inline = FALSE){
    echo Tags::radio($label, $name, $value, $inline);
}    
function ui_end_radio(){
    echo Tags::end_radio();
}    
function ui_radio_option($label, $value){
    echo Tags::radio_option($label, $value);
}    
function ui_select($label, $name, $value, $multiple = FALSE){
    echo Tags::select($label, $name, $value, $multiple);
}    
function ui_end_select(){
    echo Tags::end_select();
}    
function ui_select_option($label, $value){
    echo Tags::select_option($label, $value);
}    
function ui_textarea($label, $name, $rows, $placeholder = NULL, $value = NULL){
    echo Tags::textarea($label, $name, $rows, $placeholder, $value);
}    
/*
 * Navegacion y Menu 
 */    
function ui_drop_down_menu($label){
    echo Tags::drop_down_menu($label);
}    
function ui_end_drop_down_menu(){
    echo Tags::end_drop_down_menu();
}    
function ui_menu_item($type, $label = NULL, $href = NULL, $disabled = NULL){
    echo Tags::menu_item($type, $label, $href, $disabled);
}    
function ui_nav_bar_form($action, $method, $inputName, $inputPlaceholder, $labelButton, $inputValue = NULL){
    echo Tags::nav_bar_form($action, $method, $inputName, $inputPlaceholder, $labelButton, $inputValue);
}    
function ui_nav_bar_left(){
    echo Tags::nav_bar_left();
}    
function ui_end_nav_bar_left(){
    echo Tags::end_nav_bar_left();
}    
function ui_nav_bar_right(){
    echo Tags::nav_bar_right();
}    
function ui_end_nav_bar_right(){
    echo Tags::end_nav_bar_right();
}    
function ui_nav_item($label, $href, $state = NULL){
    echo Tags::nav_item($label, $href, $state);
}    
function ui_nav_item_drop_down($label){
    echo Tags::nav_item_drop_down($label);
}   
function ui_end_nav_item_drop_down(){
    echo Tags::end_nav_item_drop_down();
}    
function ui_nav_item_list($type, $label = NULL, $href = NULL, $disabled = NULL){
    echo Tags::nav_item_list($type, $label, $href, $disabled);
}    
function ui_navigation_bar($logo, $href){
    echo Tags::navigation_bar($logo, $href);
}    
function ui_end_navigation_bar(){
    echo Tags::end_navigation_bar();
}    
function ui_navigation_list(){
    echo Tags::navigation_list();
}    
function ui_end_navigation_list(){
    echo Tags::end_navigation_list();
}    
function ui_navigation_menu($type, $justified = NULL, $stacked = NULL){
    echo Tags::navigation_menu($type, $justified, $stacked);
}    
function ui_end_navigation_menu(){
    echo Tags::end_navigation_menu();
}    
/*
 * Componentes Varios 
 */    
function ui_breadcrumb(){
    echo Tags::breadcrumb();
}    
function ui_end_breadcrumb(){
    echo Tags::end_breadcrumb();
}    
function ui_em($value){
    echo Tags::em($value);
}    
function ui_li($label, $active = NULL, $badge = NULL){
    echo Tags::li($label, $active, $badge);
}    
function ui_li_a($label, $href, $active = NULL, $badge = NULL){
    echo Tags::li_a($label, $href, $active, $badge);
}    
function ui_media_object($href, $alt, $src, $title, $content){
    echo Tags::media_object($href, $alt, $src, $title, $content);
}    
function ui_end_media_object(){
    echo Tags::end_media_object();
}    
function ui_page($label, $href, $state = NULL){
    echo Tags::page($label, $href, $state);
}    
function ui_page_first($href, $state = NULL){
    echo Tags::page_first($href, $state);
}    
function ui_page_last($href, $state = NULL){
    echo Tags::page_last($href, $state);
}    
function ui_paginator(){
    echo Tags::paginator();
}    
function ui_end_paginator(){
    echo Tags::end_paginator();
}    
function ui_panel($content, $title = NULL, $fot = NULL){
    echo Tags::panel($content, $title, $fot);
}    
function ui_paragraph($align = NULL, $lead = NULL){
    echo Tags::paragraph($align, $lead);
}    
function ui_end_paragraph(){
    echo Tags::end_paragraph();
}    
function ui_small($value){
    echo Tags::small($value);
}    
function ui_strong($value){
    echo Tags::strong($value);
}    
function ui_text($value){
    echo Tags::text($value);
}    
function ui_ul(){
    echo Tags::ul();
}    
function ui_end_ul(){
    echo Tags::end_ul();
}    
function ui_ul_a(){
    echo Tags::ul_a();
}    
function ui_end_ul_a(){
    echo Tags::end_ul_a();
}    
/*
 * Table
 */    
function ui_table(){
    echo Tags::table();
}    
function ui_end_table(){
    echo Tags::end_table();
}    
function ui_table_field($value = NULL){
    echo Tags::table_field($value);
}    
function ui_end_table_field(){
    echo Tags::end_table_field();
}    
function ui_table_head(){
    echo Tags::table_head();
}    
function ui_end_table_head(){
    echo Tags::end_table_head();
}    
function ui_table_head_field($value = NULL){
    echo Tags::table_head_field($value);
}    
function ui_end_table_head_field(){
    echo Tags::end_table_head_field();
}    
function ui_table_row(){
    echo Tags::table_row();
}    
function ui_end_table_row(){
    echo Tags::end_table_row();
}
?>