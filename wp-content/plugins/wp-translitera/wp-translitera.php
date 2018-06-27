<?php
/*
Plugin Name: WP Translitera
Plugin URI: http://yur4enko.com/category/moi-proekty/wp-translitera
Description: Plug-in for transliteration permanent permalink records , pages, and tag
Version: 161114
Author: Evgen Yurchenko
Text Domain: wp_translitera
Domain Path: /languages/
Author URI: http://yur4enko.com/
*/

/*  Copyright 2015 Evgen Yurchenko  (email: evgen@yur4enko.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc.
*/

class wp_translitera {//wp=>3.0 php=>5.2.4
    
    //Создаем локализации
    protected static function createlocale() {//wp=>3.0 php=>5.2.4
        $loc = get_locale();
        $ret = array();
        if ($loc == 'ru_RU') {//Русская локализация
            $ret = array(
                'А'=>'A','а'=>'a','Б'=>'B','б'=>'b','В'=>'V','в'=>'v','Г'=>'G',
                'г'=>'g','Д'=>'D','д'=>'d','Е'=>'E','е'=>'e','Ё'=>'Jo','ё'=>'jo',
                'Ж'=>'Zh','ж'=>'zh','З'=>'Z','з'=>'z','И'=>'I','и'=>'i','Й'=>'J',
                'й'=>'j','К'=>'K','к'=>'k','Л'=>'L','л'=>'l','М'=>'M','м'=>'m',
                'Н'=>'N','н'=>'n','О'=>'O','о'=>'o','П'=>'P','п'=>'p','Р'=>'R',
                'р'=>'r','С'=>'S','с'=>'s','Т'=>'T','т'=>'t','У'=>'U','у'=>'u',
                'Ф'=>'F','ф'=>'f','Х'=>'H','х'=>'h','Ц'=>'C','ц'=>'c','Ч'=>'Ch',
                'ч'=>'ch','Ш'=>'Sh','ш'=>'sh','Щ'=>'Shh','щ'=>'shh','Ъ'=>'',
                'ъ'=>'','Ы'=>'Y','ы'=>'y','Ь'=>'','ь'=>'','Э'=>'Je','э'=>'je',
                'Ю'=>'Ju','ю'=>'ju','Я'=>'Ja','я'=>'ja'
            );
        } elseif ($loc == 'uk') {//Украинская локализация
            $ret = array(
                'А'=>'A','а'=>'a','Б'=>'B','б'=>'b','В'=>'V','в'=>'v','Г'=>'H',
                'г'=>'h','Ґ'=>'G','ґ'=>'g','Д'=>'D','д'=>'d','Е'=>'E','е'=>'e',
                'Є'=>'Ie','є'=>'ie','Ж'=>'Zh','ж'=>'zh','З'=>'Z','з'=>'z','И'=>'Y',
                'и'=>'y','І'=>'I','і'=>'i','Ї'=>'I','ї'=>'i','Й'=>'I','й'=>'i',
                'К'=>'K','к'=>'k','Л'=>'L','л'=>'l','М'=>'M','м'=>'m','Н'=>'N',
                'н'=>'n','О'=>'O','о'=>'o','П'=>'P','п'=>'p','Р'=>'R','р'=>'r',
                'С'=>'S','с'=>'s','Т'=>'T','т'=>'t','У'=>'U','у'=>'u','Ф'=>'F',
                'ф'=>'f','Х'=>'Kh','х'=>'kh','Ц'=>'Ts','ц'=>'ts','Ч'=>'Ch','ч'=>'ch',
                'Ш'=>'Sh','ш'=>'sh','Щ'=>'Shch','щ'=>'shch','Ь'=>'','ь'=>'','Ю'=>'Iu',
                'ю'=>'iu','Я'=>'Ia','я'=>'ia',"'"=>''
            );
        } 
        //Глобальная локализация
        $ret = $ret + array(
            'А'=>'A','а'=>'a','Б'=>'B','б'=>'b','В'=>'V','в'=>'v','Г'=>'G',
            'г'=>'g','Д'=>'D','д'=>'d','Е'=>'E','е'=>'e','Ё'=>'Jo','ё'=>'jo',
            'Ж'=>'Zh','ж'=>'zh','З'=>'Z','з'=>'z','И'=>'I','и'=>'i','Й'=>'J',
            'й'=>'j','К'=>'K','к'=>'k','Л'=>'L','л'=>'l','М'=>'M','м'=>'m',
            'Н'=>'N','н'=>'n','О'=>'O','о'=>'o','П'=>'P','п'=>'p','Р'=>'R',
            'р'=>'r','С'=>'S','с'=>'s','Т'=>'T','т'=>'t','У'=>'U','у'=>'u',
            'Ф'=>'F','ф'=>'f','Х'=>'H','х'=>'h','Ц'=>'C','ц'=>'c','Ч'=>'Ch',
            'ч'=>'ch','Ш'=>'Sh','ш'=>'sh','Щ'=>'Shh','щ'=>'shh','Ъ'=>'',
            'ъ'=>'','Ы'=>'Y','ы'=>'y','Ь'=>'','ь'=>'','Э'=>'Je','э'=>'je',
            'Ю'=>'Ju','ю'=>'ju','Я'=>'Ja','я'=>'ja','Ґ'=>'G','ґ'=>'g','Є'=>'Ie',
            'є'=>'ie','І'=>'I','і'=>'i','Ї'=>'I','ї'=>'i',"'"=>''
        );
        return $ret;
    }
        
    //Проставляем галочки в чебоксах
    protected static function getchebox($name){//wp=>0.0.0 php=>5.2.4
        $value = wp_translitera::getset($name);
        return (empty($value))?'':' checked';
    }
    
    //Форма админки
    protected static function GetForm() {//wp=>0.0.0 php=>5.2.4
        $noparsevar = wp_translitera::getset('fileext');
        $extforform = '';
        foreach ($noparsevar as $value) {
            $extforform = $extforform.$value.',';
        }
        if (!empty($extforform)){
            $extforform = substr($extforform, 0, -1);
        }
        $ret = '<h2>'.__('Convert existing','wp_translitera').':</h2></br>'
                . '<form method=POST> '
                . '<input type="checkbox" name="r1" value="1">'.__('Pages and posts','wp_translitera').'</br>'
                . '<input type="checkbox" name="r2" value="1">'.__('Headings, tags etc...','wp_translitera').'</br>'
                . '<input type="submit" value="'.__('Transliterate','wp_translitera').'" name="transliterate">'
                . '</form>'
                . '<p><h2>'.__('Settings','wp_translitera').':</h2></br>'
                . '<form method=POST> '
                . '<input type="checkbox" name="use_force_transliterations" value="1"'.wp_translitera::getchebox("use_force_transliterations").'>'.__('Use forces transliteration for title','wp_translitera').'</br>'
                . '<input type="checkbox" name="tranliterate_uploads_file" value="1"'.wp_translitera::getchebox("tranliterate_uploads_file").'>'.__('Transliterate names of uploads files','wp_translitera').'</br>'
                . '<input type="checkbox" name="tranliterate_404" value="1"'.wp_translitera::getchebox("tranliterate_404").'>'.__('Transliterate 404 url','wp_translitera').'</br>'
                . __('File extensions, separated by commas , titles that do not need to transliterate','wp_translitera').'<input type="text" size="80" name="typefiles" value="'.$extforform.'"></br>'
                . '<input type="submit" value="'.__('Apply','wp_translitera').'" name="apply">'
                . '</form>';
        return $ret;                
    }
    
    //Транслитерация в БД
    protected static function do_transliterte($table,$id,$name) {//wp=>3.0 php=>5.2.4
        global $wpdb;
        $rez = $wpdb->get_results("SELECT {$id}, {$name} FROM {$table} WHERE 1",ARRAY_A);
        foreach ($rez as $value) {
            $tmp_name = wp_translitera::transliterate(urldecode($value[$name]));
            if ($tmp_name != $value[$name]) {
                $wpdb->update($table,array($name=>$tmp_name),array($id=>$value[$id]));
            }
        }
    }
    
    //Получаем настройки
    protected static function getoptions() {//wp=>3.0 php=>5.2.4
        $set = get_option('wp_translitera');
        if (gettype($set) != 'array') {
            $set = array();
        }
        return $set;
    }

    //Получаем значение настройки
    protected static function getset($name) {//wp=>0.0.0 php=>5.2.4
        $set = wp_translitera::getoptions();
        return (array_key_exists($name,$set))?$set[$name]:NULL;
    }
    
    //Записываем настройки
    protected static function updset($name,$value) {//wp=>3.0 php=>5.2.4
        $set = wp_translitera::getoptions();
        $set[$name] = $value;
        update_option('wp_translitera',$set);
    }
    
    //Вызываемые дочерние функции
    //Модуль формы админки
    public static function main_settings() {//wp=>3.0 php=>5.2.4
        global $wpdb;
        $act = filter_input(INPUT_POST,'transliterate');
        if (!empty($act)) {
            $r1 = filter_input(INPUT_POST, 'r1');
            $r2 = filter_input(INPUT_POST, 'r2');
            if (!empty($r1)) {
                wp_translitera::do_transliterte($wpdb->posts, 'ID', 'post_name');
            }
            if (!empty($r2)){
                wp_translitera::do_transliterte($wpdb->terms, 'term_id', 'slug');
            }
            
        }
        $setupd = filter_input(INPUT_POST, 'apply');
        if (!empty($setupd)){
            wp_translitera::updset('tranliterate_uploads_file', filter_input(INPUT_POST,'tranliterate_uploads_file'));
            wp_translitera::updset('tranliterate_404', filter_input(INPUT_POST,'tranliterate_404'));
            wp_translitera::updset('fileext', explode(',', filter_input(INPUT_POST, 'typefiles')));
            wp_translitera::updset('use_force_transliterations', filter_input(INPUT_POST, 'use_force_transliterations'));  
        }
        echo wp_translitera::GetForm();
    }
    
    //Вызываемые функции
    //Процедура преобразования символов
    public static function transliterate($title) {//wp=>3.0.0 php=>5.2.4
        $type = substr(filter_input(INPUT_POST, 'name'),-3);
        if (!empty($type)) {
            if (in_array($type, wp_translitera::getset('fileext'))) {
                return $title;
            }
        }
        return strtr($title, wp_translitera::createlocale());
    }
    
    //Процедура преобразования символов форсированный режим
    public static function transliterate_force($title, $raw_title) {//wp=>3.0.0 php=>5.2.4
        $type = substr(filter_input(INPUT_POST, 'name'),-3);
        if (!empty($type)) {
            if (in_array($type, wp_translitera::getset('fileext'))) {
                return $title;
            }
        }
        return sanitize_title_with_dashes(strtr($raw_title, wp_translitera::createlocale()));
    }
    
    //Добавляем раздел в админку
    public static function add_menu(){//wp=>3.0 php=>5.2.4
        add_options_page('WP Translitera', 'Translitera', 'activate_plugins', __FILE__, array('wp_translitera','main_settings'));
    }
        
    //Попытка транслитерировать урл
    public static function init404(){//wp=>1.5.1 php=>5.2.4
        if (is_404()){
            if (wp_translitera::getset('tranliterate_404')){
                $thisurl = filter_input(INPUT_SERVER, 'REQUEST_URI');
                $thisurl = urldecode($thisurl); 
                $trurl = wp_translitera::transliterate($thisurl);
                if ($thisurl != $trurl) {
                    wp_redirect($trurl,301);
                }
            }
        }
    }
    
    //Обработка файлов загружаемых из форм
    public static function rename_uploads_additional($value, $filename_raw) {//wp=>0.0.0 php=>5.2.4
        if (wp_translitera::getset('tranliterate_uploads_file')){
            $value = wp_translitera::transliterate($value);
        }
        return $value; 
    }
    
    //Чистка при удалении
    static function tr_delete(){//wp=>3.0 php=>5.2.4
        delete_option('wp_translitera');
    }
    
    //Инициализация ядра
    static function init() {//wp=>3.0 php=>5.2.4
        //инициализация языка
        load_plugin_textdomain('wp_translitera', false, dirname(plugin_basename(__FILE__)).'/languages');
        
        //провека необходимости обновить БД
        $from = wp_translitera::getset('version');
        $startfrom = $from;
        
        if (empty($from)) {
            $from = 160819;
        }
        if ($from == 160819) {
            if (wp_translitera::getset('fileext') == NULL) {
                wp_translitera::updset('fileext', array());
            }
            $from = 161011;
        }
        
        if ($startfrom != $from) {
            wp_translitera::updset('version', $from);
        }
        
        //инициализация метода транслитерации
        if (wp_translitera::getset('use_force_transliterations') === '1') {
            add_filter('sanitize_title', array('wp_translitera','transliterate_force'), 25, 2);
        } else {
            add_filter('sanitize_title', array('wp_translitera','transliterate'), 0);
        }
    }
}

//wp=>3.0 php=>5.2.4
//Добавляем админ меню
add_action('admin_menu', array('wp_translitera', 'add_menu'));
//Инициализация ядра
add_action('init', array('wp_translitera', 'init'));
//Редирект 404
add_action('wp',array('wp_translitera','init404'));
//Переименовываение загружаемых файлов
add_filter('sanitize_file_name',array('wp_translitera', 'rename_uploads_additional'),10,2);
//Обработка удаления
register_uninstall_hook(__FILE__,array('wp_translitera','tr_delete'));
