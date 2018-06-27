<?php
if (! defined ( 'PE_FILE' )){
	exit ( "Error, wrong way to file." );
}

function price_excel_save_table(){
	global $wpdb;
	$table_price_excel = $wpdb->prefix.price_excel;
	$price_excel_price_list = $wpdb->get_results("SELECT `data`,`header` FROM `".$table_price_excel."` WHERE `id` = ".intval($_POST['price_excel_id']));
	$price_excel_str=$price_excel_price_list[0]->data;
	$price_excel_data_new = unserialize($price_excel_str);

	foreach ($_POST as $key => $value) {
		if(preg_match('/^([0-9]{1,7})_([0-9]{1,3})$/', $key)){
			$m=explode('_', $key);
			$value=sanitization_string($value);
			$price_excel_data_new[$m[0]][$m[1]]=$value;
		}
	}
	price_excel_update($price_excel_data_new);
}

/*  Работа со столбцами прайсов  */

function price_excel_stolbec(){
	$func_stolbec=$_POST['func_stolbec'];
	if($func_stolbec=='none'){
		echo 'не указана операция';
	}
	if($func_stolbec=='del'){
		$nomer_stolbec=$_POST['nomer_stolbec'];
		if($nomer_stolbec=='none'){
			echo 'не указан столбец';
		}else{
			global $wpdb;
			$table_price_excel = $wpdb->prefix.price_excel;
			$price_excel_price_list = $wpdb->get_results("SELECT `data`,`header` FROM `".$table_price_excel."` WHERE `id` = ".intval($_POST['price_excel_id']));
			$price_excel_str=$price_excel_price_list[0]->data;
			$price_excel_data_new = unserialize($price_excel_str);
			$nomer_stolbec--;
			foreach ($price_excel_data_new as $key => $value) {
				$m=array();
				foreach ($value as $k => $v) {
					if($k!=$nomer_stolbec){
						$m[]=$v;
					}
				}
				$price_excel_data_new[$key]=$m;
			}
			price_excel_update($price_excel_data_new);
		}
	}
	if($func_stolbec=='add'){
		$position_stolbec=sanitization_string($_POST['position_stolbec']);
		$nomer_stolbec=sanitization_string($_POST['nomer_stolbec']);
		if(($position_stolbec=='none')||($nomer_stolbec=='none')){
			echo 'указаны не все параметры операции';
		}else{
			if($position_stolbec=='prev'){
				$nomer_stolbec--;
			}
			global $wpdb;
			$table_price_excel = $wpdb->prefix.price_excel;
			$price_excel_price_list = $wpdb->get_results("SELECT `data`,`header` FROM `".$table_price_excel."` WHERE `id` = ".intval($_POST['price_excel_id']));
			$price_excel_str=$price_excel_price_list[0]->data;
			$price_excel_data_new = unserialize($price_excel_str);
			foreach ($price_excel_data_new as $key => $value) {
				$m=array();
				foreach ($value as $k => $v) {
					if($k<$nomer_stolbec){
						$m[$k]=$v;
					}
					if($k==$nomer_stolbec){
						$m[$k]='';
						$m[$k+1]=$v;
					}
					if($k>$nomer_stolbec){
						$m[$k+1]=$v;
					}
				}
				if($nomer_stolbec>$k){
					$m[$k+1]='';
				}
				$price_excel_data_new[$key]=$m;
			}
			price_excel_update($price_excel_data_new);
		}
	}
}

/*записываем в базу число строк занимающих шапку*/
function price_excel_header(){
	global $wpdb;
	$table_price_excel = $wpdb->prefix.price_excel;
	$wpdb->update
				(
					$table_price_excel,  
					array('header' => intval($_POST['price_excel_header_nomer'])),
					array('id' => intval($_POST['price_excel_id'])),
					array('%s'),
					array('%s')
				);
}

function price_excel_update($data){
	$price_excel_str=serialize($data);
	global $wpdb;
	$table_price_excel = $wpdb->prefix.price_excel;	
	$wpdb->update
				(
					$table_price_excel,  
					array('data' => $price_excel_str),
					array('id' => intval($_POST['price_excel_id'])),
					array('%s'),
					array('%d')
				);
}

/*удаляем прайс*/
function price_excel_delet(){
	global $wpdb;
	$table_price_excel = $wpdb->prefix.price_excel;
	$wpdb->query("DELETE FROM `".$table_price_excel."` WHERE `id` = ".intval($_POST['price_excel_id']));
}
/*очищаем прайс*/
function price_excel_clear(){
	global $wpdb;
	$table_price_excel = $wpdb->prefix.price_excel;	
	$wpdb->update
				(
					$table_price_excel,  
					array('data' => ''),
					array('id' => intval($_POST['price_excel_id'])),
					array('%s'),
					array('%d')
				);
}
/*формируем выпадающий список прайсов*/
function price_excel_show_list_price(){
	global $wpdb;
	$table_price_excel_view = $wpdb->prefix.price_excel;
	$price_excel_price_list_id = $wpdb->get_results("SELECT `id`, `name` FROM `".$table_price_excel_view."`");
	echo '<select name="price_excel_id" id="price_excel_name_list"><option value="0">Выберите таблицу</option>';
	foreach ($price_excel_price_list_id as $item){
		echo '<option value="'.$item->id.'">'.$item->name.'</option>';
	}
	echo '</select>';	
}
/*создание, добавление, перезапись прайсов*/
function price_excel_insert($price_excel_data){
	if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die ( _e('Hacker?', 'price_excel') );
	if (function_exists ('check_admin_referer') ){
		check_admin_referer('price_excel_base_setup_form');
	}
	if(isset($_POST['name_price'])){
		global $wpdb;
		$table_price_excel = $wpdb->prefix.price_excel;
		if($_POST['name_price']=='price_new'){
			$price_excel_name_new=sanitization_string($_POST['price_excel_name_new']);
			if($price_excel_name_new!=''){
				$price_excel_str=serialize($price_excel_data);
				$wpdb->insert
						(
							$table_price_excel,  
							array( 'name' => $price_excel_name_new, 'data' => $price_excel_str , 'header' => '0'),  
							array( '%s', '%s', '%d')
						);
				echo '<p class="price_excel_valid">таблица успешно добавлена</p>';
			}else{
				echo '<p class="price_excel_novalid">ошибка: не указано имя таблицы</p>';
			}
		}else if($_POST['name_price']=='price_add'){
			if($_POST['price_excel_id']!=0){
				$price_excel_price_list = $wpdb->get_results("SELECT `data` FROM `".$table_price_excel."` WHERE `id` = ".intval($_POST['price_excel_id']));
				$price_excel_str=$price_excel_price_list[0]->data;
				$price_excel_data_update = unserialize($price_excel_str);
				foreach ($price_excel_data as $value) {
					$price_excel_data_update[]=$value;
				}
				$price_excel_str_new=serialize($price_excel_data_update);
				$wpdb->update
						(
							$table_price_excel,  
							array('data' => $price_excel_str_new),
							array('id' => intval($_POST['price_excel_id'])),
							array('%s'),
							array('%d')
						);
				echo '<p class="price_excel_valid">информация успешно добавлена в таблицу</p>';
			}else{
				echo '<p class="price_excel_novalid">ошибка: не указано имя таблицы</p>';
			}
		}else if($_POST['name_price']=='price_upd'){
			if($_POST['price_excel_id']!=0){
				$price_excel_str_new=serialize($price_excel_data);
				$wpdb->update
						(
							$table_price_excel,  
							array('data' => $price_excel_str_new),
							array('id' => intval($_POST['price_excel_id'])),
							array('%s'),
							array('%d')
						);
				echo '<p class="price_excel_valid">таблица успешно перезаписана</p>';
			}else{
				echo '<p class="price_excel_novalid">ошибка: не указано имя таблицы</p>';
			}
		}
	}else{
		echo '<p class="price_excel_novalid">ошибка: не указано имя таблицы</p>';
	}
}

/*обработка и парсинг загруженного экселевского документа*/
function price_excel_upload_file(){
	if(isset($_POST['price_excel_import'])){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die ( _e('Hacker?', 'price_excel') );

		if (function_exists ('check_admin_referer') ){
			check_admin_referer('price_excel_base_setup_form');
		}

		switch ($_FILES['filename']['error']){
			case '4': echo '<p class="price_excel_novalid">файл не выбран</p>'; break;
		}

		$price_excel_start = 1;
		/*инфа начинается с 1 строчки*/
		/*загрузка файла на сервер*/  
    	$arUploadDir=wp_upload_dir();
		$path=$arUploadDir['basedir'].'/price_excel/';
		$uploadfile = $_FILES['filename']['name'];
		$b = substr($uploadfile, -4);

		if($_FILES['filename']['error']==0){
			if($_FILES["filename"]["size"] > 1024*1024*3){
				echo '<p class="price_excel_novalid">error: file size over 3Mb (размер файла превышает 3 мегабайта)</p>';
			}else if($b == 'xlsx'){
			$name_xls='';
			$name_xls=uniqid();
			$uploadfile = $name_xls.'.zip';
			move_uploaded_file($_FILES['filename']['tmp_name'],$path.$uploadfile);
			if(file_exists ($path.$uploadfile)){
				echo '<p class="price_excel_valid">load completed (загрузка выполнена): '.$uploadfile.'</p>';
				//////////////////////////////////////////////////////////////////
				/*считывание документа*/
				/*извлечение архива*/
				$zip = new ZipArchive;
				$zip->open($path.$uploadfile);
				$zip->extractTo($path.$name_xls);
				/*чтение извлеченного*/
				/*собираем данные из sharedStrings.xml*/
				$xml = simplexml_load_file($path.$name_xls.'/xl/sharedStrings.xml');
				$sharedStringsArr = array();
				foreach ($xml->children() as $item) {
					$sharedStringsArr[] = (string)$item->t;
				}
				/*парсим инфу*/
				$handle = @opendir($path.$name_xls.'/xl/worksheets');
				$out=array();
				$stolbec = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
				while ($file = @readdir($handle)) {
					//проходим по всем файлам из директории /xl/worksheets/
					if ($file != "." && $file != ".." && $file != '_rels') {
						$xml = simplexml_load_file($path.$name_xls.'/xl/worksheets/' . $file);
						//по каждой строке
						$row = 0;
						foreach ($xml->sheetData->row as $price_excel_item) {
							$out[$file][$row] = array();
							//по каждой ячейке строки
							$price_excel_cell = 0;
							$stroka = ($row*1+$price_excel_start*1);
							$price_excel_back = 'A'.$stroka;
							$price_excel_next = '';
							//$out[$file][$row][0]='[символьный код]';
							foreach ($price_excel_item as $price_excel_child) {
								$price_excel_attr = $price_excel_child->attributes();
								$price_excel_next = $price_excel_attr['r'];
								/*если между ячейками в строке есть пустая ячейка, то забиваем пустой элемент в массив*/
								$k=0;
								preg_match_all('/[0-9]+/', $price_excel_next, $match);
								while($stolbec[$price_excel_cell].$match[0][0]!=$price_excel_next){
									$out[$file][$row][$price_excel_cell] = '';
									$price_excel_cell++;
									$k++;
									if($k>=50){break;}
								}
								/*------------------------------------------------------------------------------------*/
								if(isset($price_excel_child->v)){
									$price_excel_value=sanitization_string($price_excel_child->v);
								}else{
									$price_excel_value='';
								}
								$price_excel_value = isset($price_excel_child->v)? (string)$price_excel_child->v:false;
								$out[$file][$row][$price_excel_cell] = $price_excel_attr['t']=='s' ? $sharedStringsArr[$price_excel_value] : $price_excel_value;
								$price_excel_cell++;
								$price_excel_back=$price_excel_attr['r'];
							}
							$row++;
						}
					}
				}
				echo '<p class="price_excel_valid">файл прочитан</p>';
				//////////////////////////////////////////////////////////////////
				/*запись данных в инфоблок*/
				price_excel_insert($out['sheet1.xml']);
				//////////////////////////////////////////////////////////////////
				/*закрываем архив*/
				$zip->close();
				/*удаляем архив*/
				unlink($path.$uploadfile);
				unlink($path.$name_xls.'/_rels/.rels');
				/*удаляем извлеченные из архива файлы и папки*/
				function removeDirectory($dir){
					if ($objs = glob($dir."/*")){
						foreach($objs as $obj){
							is_dir($obj) ? removeDirectory($obj) : unlink($obj);
						}
					}
					@rmdir($dir);
				}
				removeDirectory($path.$name_xls);
				//////////////////////////////////////////////////////////////////
				echo '<p class="price_excel_valid">временные файлы удалены</p>';
			}else{
				echo '<p class="price_excel_novalid">load error (ошибка при сохранении файла на сайте):'.$uploadfile.'</p>';
			}
			}else{
				echo '<p class="price_excel_novalid">error: impossible file format (неправильный формат файла)</p>';
			}
		}


	}
}


function price_excel_add(){
	wp_enqueue_style( 'price_excel_style');
	price_excel_upload_file();
	echo '<form action="'.$_SERVER['PHP_SELF'].'?page=price_excel" method="post" enctype="multipart/form-data">';
	if (function_exists ('wp_nonce_field') ){
		wp_nonce_field('price_excel_base_setup_form'); 
	}
  	echo '<input type="file" name="filename" class="">
  	<p>Для загрузки доступны документы только с расширением ".xlsx".</p>';
  	echo '<div><input type="radio" name="name_price" value="price_new">Создать новую<br/>
   			<input type="radio" name="name_price" value="price_add">Добавить в существующую<br/>
   			<input type="radio" name="name_price" value="price_upd">Перезаписать существующую</div>';
   	echo '<div id="price_excel_input_name" ><div>Укажите имя таблицы</div>';
   	echo '<input id="price_excel_name_new" name="price_excel_name_new" type="text">';
   	price_excel_show_list_price();
  	echo '</div><input type="submit" value="Загрузить" class="button action" name="price_excel_import"></form>';
}

/*--------------------- Формирование таблицы прайса и функционала-----------------------*/
function price_excel_view_table(){
	wp_enqueue_style( 'price_excel_style');
	global $wpdb;
	$table_price_excel_view = $wpdb->prefix.price_excel;
	$price_excel_price_list = $wpdb->get_results("SELECT `data`,`header` FROM `".$table_price_excel_view."` WHERE `id` = ".intval($_POST['price_excel_id']));
	$price_excel_str=$price_excel_price_list[0]->data;
	$price_excel_header=$price_excel_price_list[0]->header;
	$price_excel_data_new = unserialize($price_excel_str);
	echo '<hr/>';
	echo '<form action="'.$_SERVER['PHP_SELF'].'?page=price_excel_view" method="post" enctype="multipart/form-data">';
		if (function_exists ('wp_nonce_field') ){
			wp_nonce_field('price_excel_base_setup_form'); 
		}
		echo 'Количество строк: '.(count($price_excel_data_new)-$price_excel_header).'<hr/><input type="hidden" value="'.intval($_POST['price_excel_id']).'" name="price_excel_id">';
		echo '<input type="submit" value="Удалить таблицу" name="price_excel_delet">';
  		echo '<input type="submit" value="Очистить таблицу" name="price_excel_clear">';
  	echo '</form>';
  	echo '<hr/>';
	echo '<form action="'.$_SERVER['PHP_SELF'].'?page=price_excel_view" method="post" enctype="multipart/form-data">';
		if (function_exists ('wp_nonce_field') ){
			wp_nonce_field('price_excel_base_setup_form'); 
		}
		echo 'Количество строк занимающих шапку<input type="text" name="price_excel_header_nomer">';
		echo '<input type="hidden" value="'.intval($_POST['price_excel_id']).'" name="price_excel_id">';
  		echo '<input type="submit" value="Задать" name="price_excel_header">';
	echo '</form>';
  	
  					$str='';
					$cols=0;
					$rows=0;
					$kol_cols=0;
					foreach ($price_excel_data_new as $value) {
						$rows=0;
						if($price_excel_header==0){
							$str.= '<tr><th>'.($cols-$price_excel_price_list[0]->header+1).'</th>';
						}else{
							$str.= '<tr><th></th>';
						}
						//echo $price_excel_yacheyka1.'<b style="color:red;">X</b>'.$price_excel_yacheyka2;
						foreach ($value as $v) {
							if($price_excel_header<=0){
								$str.= '<td id="'.$cols.'_'.$rows.'" class="red">';
							}else{
								$str.= '<th id="'.$cols.'_'.$rows.'" class="red">';
							}
							$str.= $v;
							if($price_excel_header<=0){
								$str.= '</td>';
							}else{
								$str.= '</th>';
							}
							$rows++;
						}
						if($rows>$kol_cols){
							$kol_cols=$rows;
						}
						$str.= '</tr>';
						$cols++;
						if($price_excel_header!=0){
							$price_excel_header--;
						}
					}
					$str.= '</table>';

	echo '<hr />';
	echo '<form action="'.$_SERVER['PHP_SELF'].'?page=price_excel_view" method="post" enctype="multipart/form-data">';
		if (function_exists ('wp_nonce_field') ){
			wp_nonce_field('price_excel_base_setup_form'); 
		}
		echo '<input type="hidden" value="'.$_POST['price_excel_id'].'" name="price_excel_id">';
  		echo '<select name="func_stolbec" id="func_stolbec"><option value="none">Укажите команду</option>
  					<option value="add">Добавить столбец</option>
  					<option value="del">Удалить столбец</option></select>';
  		echo '<select name="position_stolbec" id="position_stolbec"><option value="none">Укажите позицию</option>
  					<option value="prev">Перед</option>
					<option value="next">После</option></select>';
		echo '<select name="nomer_stolbec" id="nomer_stolbec"><option value="none">Укажите столбец</option>';
			for ($i=1; $i <= $kol_cols; $i++) { 
				echo '<option value="'.$i.'">'.$i.' столбец</option>';
			}
			echo '</select>';
		echo '<input type="submit" value="Выполнить" name="price_excel_stolbec">';
	echo '</form>';
	echo '<hr />';
	echo '<form action="'.$_SERVER['PHP_SELF'].'?page=price_excel_view" method="post" enctype="multipart/form-data">';
		if (function_exists ('wp_nonce_field') ){
			wp_nonce_field('price_excel_base_setup_form'); 
		}
		echo '<input type="hidden" value="'.intval($_POST['price_excel_id']).'" name="price_excel_id">';
		echo '<div id="price_excel_data_update"></div>';
		echo '<input type="submit" value="Сохранить изменения" name="price_excel_save" id="price_excel_save">';
	echo '</form>';
	$str_nom='';
	for ($i=0; $i <= $kol_cols; $i++) { 
		if($i==0){
			$str_nom.='<td class="nored"></td>';	
		}else{
			$str_nom.='<td class="nored">'.$i.'</td>';
		}
	}

	$str= '<table id="price_excel_table">'.$str_nom.$str;
	echo $str;
}

function price_excel_view(){
  	if(isset($_POST['price_excel_delet'])){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die ( _e('Hacker?', 'price_excel_view') );

		if (function_exists ('check_admin_referer') ){
			check_admin_referer('price_excel_base_setup_form');
		}
		price_excel_delet();
  	}
	if(isset($_POST['price_excel_clear'])){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die ( _e('Hacker?', 'price_excel_view') );

		if (function_exists ('check_admin_referer') ){
			check_admin_referer('price_excel_base_setup_form');
		}
		price_excel_clear();
  	}
	echo '<form action="'.$_SERVER['PHP_SELF'].'?page=price_excel_view" method="post" enctype="multipart/form-data">';
		if (function_exists ('wp_nonce_field') ){
			wp_nonce_field('price_excel_base_setup_form'); 
		}
		price_excel_show_list_price();
  		echo '<input type="submit" value="Показать" name="price_excel_view">';
  	echo '</form>';
	if(isset($_POST['price_excel_header'])){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die ( _e('Hacker?', 'price_excel_view') );

		if (function_exists ('check_admin_referer') ){
			check_admin_referer('price_excel_base_setup_form');
		}
		price_excel_header();
		price_excel_view_table();
  	}  	
	if(isset($_POST['price_excel_stolbec'])){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die ( _e('Hacker?', 'price_excel_view') );

		if (function_exists ('check_admin_referer') ){
			check_admin_referer('price_excel_base_setup_form');
		}
		price_excel_stolbec();
		price_excel_view_table();
  	}  	
	if(isset($_POST['price_excel_save'])){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die ( _e('Hacker?', 'price_excel_view') );

		if (function_exists ('check_admin_referer') ){
			check_admin_referer('price_excel_base_setup_form');
		}
		price_excel_save_table();
		price_excel_view_table();
  	}  	
  	/*показываем прайс*/
	if(isset($_POST['price_excel_view'])){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die ( _e('Hacker?', 'price_excel_view') );

		if (function_exists ('check_admin_referer') ){
			check_admin_referer('price_excel_base_setup_form');
		}
		price_excel_view_table();
	}
}

function price_excel_options_page(){
	echo '<h1>Импорт таблиц</h1>';
	if(wd_admin_notice()){
		echo '<div class="price_excel_options_page">';
		price_excel_add();
		echo '</div>';
	}else{
		echo 'No access';
	}
}

function price_excel_view_options_page(){
	echo '<h1>Обзор таблиц</h1>';
	if(wd_admin_notice()){
		echo '<div class="price_excel_view_options_page">';
		price_excel_view();
		echo '</div>';
	}else{
		echo 'No access';
	}
}

function price_excel_view_add_admin_page(){
    add_options_page('Обзор таблиц', 'Обзор таблиц', 8, 'price_excel_view', 'price_excel_view_options_page');
}

function price_excel_add_admin_page() {
    add_options_page('Импорт таблиц', 'Импорт таблиц', 8, 'price_excel', 'price_excel_options_page');
}

function price_excel_install(){
    global $wpdb;
	$table_price_excel = $wpdb->prefix.price_excel;
	$sql1 =	
	"
		CREATE TABLE  `".$table_price_excel."` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`name` VARCHAR( 40 ) NOT NULL ,
			`data` LONGTEXT NOT NULL ,
			`header` INT NOT NULL,
			PRIMARY KEY (  `id` )
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
	";
    $wpdb->query($sql1);
    
    $arUploadDir=wp_upload_dir();

	if(!is_dir($arUploadDir['basedir'])){
		mkdir($arUploadDir['basedir'], 0755);
	}

	if(!is_dir($arUploadDir['basedir'].'/price_excel')){
		mkdir($arUploadDir['basedir'].'/price_excel', 0755);
	}
}

function price_excel_uninstall(){
   /* global $wpdb;
	$table_price_excel = $wpdb->prefix.price_excel;
    $sql1 = "DROP TABLE `".$table_price_excel."`;";
    $wpdb->query($sql1);*/
}

function script_init(){
	if($_GET['page']=='price_excel'){
        wp_enqueue_script('price_excel_script', plugins_url( 'script.js' , __FILE__ ),array('jquery'));
	}
	if($_GET['page']=='price_excel_view'){
        wp_enqueue_script('price_excel_script', plugins_url( 'script_view.js' , __FILE__ ),array('jquery'));
	}	
}

function style_init(){
	if($_GET['page']=='price_excel'){
        wp_register_style('price_excel_style', plugins_url( 'style.css' , __FILE__ ));
	}
	if($_GET['page']=='price_excel_view'){
        wp_register_style('price_excel_style', plugins_url( 'style_view.css' , __FILE__ ));
	}
}

function wd_admin_notice() {
		$settings = get_option('price_excel_admin_notice');
		if (!isset($settings['disable_admin_notices']) || (isset($settings['disable_admin_notices']) && $settings['disable_admin_notices'] == 0)) {
			if (current_user_can('manage_options')) {
				return true;
			}
		}
		return false;
}

function sanitization_string($str=''){
	$str=strip_tags($str);
	$str=htmlspecialchars($str);
	return $str;
}
?>