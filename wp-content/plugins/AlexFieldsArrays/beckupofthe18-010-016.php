<?php
/* DB go!*/
global $jal_db_version;
$jal_db_version = "1.0";

function jal_install () {
   global $wpdb;
   global $jal_db_version;

   $table_name = $wpdb->prefix . "liveshoutbox";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time bigint(11) DEFAULT '0' NOT NULL,
    name tinytext NOT NULL,
    text text NOT NULL,
    url VARCHAR(55) NOT NULL,
    UNIQUE KEY id (id)
  );";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
       $welcome_name = "Mr. Wordpress";
       $welcome_text = "Поздравляю, установка прошла успешно!";

      $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
 
      add_option("jal_db_version", $jal_db_version);

   }
}
register_activation_hook(__FILE__,'jal_install');

 $installed_ver = get_option( "jal_db_version" );

   if( $installed_ver != $jal_db_version ) {

      $sql = "CREATE TABLE " . $table_name . " (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    time bigint(11) DEFAULT '0' NOT NULL,
    name tinytext NOT NULL,
    text text NOT NULL,
    url VARCHAR(100) NOT NULL,
    UNIQUE KEY id (id)
  );";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      update_option( "jal_db_version", $jal_db_version );
  }

/*Db stop*/












//..........................

add_filter( 'woocommerce_checkout_fields' , 'alex_states_array' );

// Our hooked in function - $fields is passed via the filter!

function alex_states_array( $fields ) {

//ADD ARRAYS

    $fields['shipping']['state']['label']= 'Область';

    $fields['shipping']['state']['type']= 'select';

    $fields['shipping']['state']['required']= true;

    $fields['shipping']['state']['options'] = [

        //

        ////$address_fields['state']['options'] = [

        'Львівська' => 'Львівська',

        'Київська' => 'Київська',

        'Вінницька' => 'Вінницька',

        'Дніпропетровська' => 'Дніпропетровська',

        'Івано-Франківська' => 'Івано-Франківська'

        ////];

    

    ];

    return $fields;

}

//

add_filter( 'woocommerce_checkout_fields' , 'alex_cities_array' );

// Our hooked in function - $fields is passed via the filter!

function alex_cities_array( $fields ) {

    $fields['billing']['billing_email']['required']= false;

    $fields['billing']['billing_phone']['default']= '+380';

    $fields['billing']['billing_city']['type']= 'select';

    // $fields['shipping']['shipping_city']['options'] = [

    //     'Дрогобич' => 'Дрогобич',

    //     'Київ' => 'Київ',

    //     'Львів' => 'Львів',

    //     'Раневичі' => 'Раневичі',

    //     'Жовква' => 'Жовква',

    //     'Херсон' => 'Херсон',

    //     'Одеса' => 'Одеса',

    //     'Чернігів' => 'Чернігів',

    //     'Івано-Франківськ' => 'Івано-Франківськ',

    //     'Турка' => 'Турка'

    // ];

    return $fields;

}

add_filter( 'woocommerce_checkout_fields' , 'alex_billing_placeholders' );

// Our hooked in function - $fields is passed via the filter!

function alex_billing_placeholders( $fields ) {

//ADD PLACEHOLDERS

     $fields['billing']['billing_first_name']['placeholder'] = 'Введіть ім\'я';

     $fields['billing']['billing_last_name']['placeholder'] = 'Введіть прізвище';

     $fields['billing']['billing_email']['placeholder'] = 'Введіть адресу електронної пошти';

     $fields['billing']['billing_phone']['placeholder'] = 'Введіть номер мобільного';
     return $fields;

}

//Hook in

add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields3' );

// Our hooked in function - $address_fields is passed via the filter!

//working!

function custom_override_default_address_fields3( $address_fields ) {

    //$address_fields['state']['required'] = false;

  //  $address_fields['state']['label'] = 'Область';

//    $address_fields['state']['required'] = true;

    return $address_fields;

}









add_action( 'woocommerce_before_order_notes', 'alex_add_f_1' );

function alex_add_f_1( $checkout ) {

    echo '<div id="my_custom_checkout_field">' . __('') . '';

    $my_array_of_ua_regions= array(
        'Не обрано' =>'Не обрано' ,
    // 'Автономна Республіка Крим'=> 'Автономна Республіка Крим',
    'Вінницька область'=> 'Вінницька область',
    'Волинська область'=> 'Волинська область',
    'Дніпропетровська область'=> 'Дніпропетровська область',
    'Донецька область'=> 'Донецька область',
    'Житомирська область'=> 'Житомирська область',
    'Закарпатська область'=> 'Закарпатська область',
    'Запорізька область'=> 'Запорізька область',
    'Івано-Франківська область'=> 'Івано-Франківська область',
     'Київська область'=> 'Київська область',
     'Кіровоградська область'=> 'Кіровоградська область',
     'Луганська область'=> 'Луганська область',
     'Львівська область'=> 'Львівська область',
     'Миколаївська область'=> 'Миколаївська область',
     'Одеська область'=> 'Одеська область',
     'Полтавська область'=> 'Полтавська область',
     'Рівненська область'=> 'Рівненська область',
     'Сумська область'=> 'Сумська область',
     'Тернопільська область'=> 'Тернопільська область',
     'Харківська область'=> 'Харківська область',
     'Херсонська область'=> 'Херсонська область',
     'Хмельницька область'=> 'Хмельницька область',
     'Черкаська область'=> 'Черкаська область',
     'Чернівецька область'=> 'Чернівецька область',
    'Чернігівська область'=>'Чернігівська область' );
    
   woocommerce_form_field( 'alex_oblast', array(
          'type'          => 'select',
          'options'       => $my_array_of_ua_regions,
            'class'         => array('my-field-class form-row-wide'),
          'label'         => __('Оберіть область доставки'),
          'placeholder'   => __('список'),
          'required'      => 'true',
          ), $checkout->get_value( 'alex_oblast' ));


    // global $wpdb; 
    //     $sql = 'SELECT tt.*, t.name AS name FROM '.$wpdb->term_taxonomy.' tt, '.$wpdb->terms.' t WHERE tt.taxonomy = "category" AND t.term_id = tt.term_id';   
    //       $result = $wpdb->get_results($sql, ARRAY_A);     
    //       foreach($result as $cat){?
    //         echo $cat['name'];
    //     <?  }
$my_array_of_ua_cities= [
          'Не обрано'=>['Не обрано'=>'Не обрано'],

          'Вінницька область'=> 'Вінницька область',
          'Волинська область'=> 'Волинська область',
          'Дніпропетровська область'=> 'Дніпропетровська область',
          'Донецька область'=> 'Донецька область',
          'Житомирська область'=> 'Житомирська область',
          'Закарпатська область'=> 'Закарпатська область',
          'Запорізька область'=> 'Запорізька область',
          'Івано-Франківська область'=> 'Івано-Франківська область',
           'Київська область'=> 'Київська область',
           'Кіровоградська область'=> 'Кіровоградська область',
           'Луганська область'=> 'Луганська область',
          'Львівська область'=> 
                  ['Не обрано'=>'Не обрано',
                  'Дрогобич'=>'Дрогобич',
                  'Львів'=>'Львів',
                  'Трускавець'=>'Трускавець'
                  ],
           'Миколаївська область'=> 'Миколаївська область',
           'Одеська область'=> 'Одеська область',
           'Полтавська область'=> 'Полтавська область',
           'Рівненська область'=> 'Рівненська область',
           'Сумська область'=> 'Сумська область',
           'Тернопільська область'=> 'Тернопільська область',
           'Харківська область'=> 'Харківська область',
           'Херсонська область'=> 'Херсонська область',
           'Хмельницька область'=> 'Хмельницька область',
           'Черкаська область'=> 'Черкаська область',
           'Чернівецька область'=> 'Чернівецька область',
          'Чернігівська область'=>'Чернігівська область'

          ]      
          ;
          $my_array_of_ua_cities_selected= array('swich does not work' => 'OOPs..' );
          

//           foreach( $wpdb->get_results("
//             SELECT  cat.cid as city_id,
//             cat.uk_name as city_name_uk,
//             cat.ru_name as city_name_ru
//             FROM cities_and_towns as cat
//             LEFT JOIN regions_cities rc ON cat.cid = rc.cid
//             WHERE rc.rid = 9
// ORDER BY city_name_uk ASC;
//             ") as $key => $row) {
//           // each column in your row will be accessible like this
//           $my_column = $row->column_name;}
//           echo $my_column;
          echo 'Якщо вашого міста немає в списку - напишіть про це в примітку.';
          echo sanitize_text_field( $_POST['alex_oblast'] );
        if(isset($_POST['alex_oblast'])){
          $selected_region = $_POST['alex_oblast'];
          switch ($selected_region) {
            case "Львівська область": 
              # code...
              $my_array_of_ua_cities_selected=$my_array_of_ua_cities["Львівська область"];
              break;
             
            default:
              # code...
              $my_array_of_ua_cities_selected=$my_array_of_ua_cities['Не обрано'];
              break;
          }
        }
    // for($i=0;$i<23;$i++){
      // if($my_array_of_ua_regions[$i]=='Львівська область'){
          woocommerce_form_field( 'alex_misto', array(
              'type'          => 'select',
              'options'       => $my_array_of_ua_cities_selected,
              'class'         => array('my-field-class form-row-wide'),
              'label'         => __('Оберіть місто доставки'),
              'placeholder'   => __('список'),
              'required'      => 'true',
              ), $checkout->get_value( 'alex_misto' ));
      // }
    // }

    woocommerce_form_field( 'alex_nom_viddilennia', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Відділення №'),
        'placeholder'   => __('Введіть номер відділення'),
        'required'      => 'true',
        ), $checkout->get_value( 'alex_nom_viddilennia' ));

    woocommerce_form_field( 'my_field_name', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Адреса відділення'),
        'placeholder'   => __('Введіть адресу відділення'),
        ), $checkout->get_value( 'my_field_name' ));


    echo '</div>';

}
///......................................................
/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process() {
    // Check if set, if its not set add an error.
    if ( ! $_POST['my_field_name'] )
        wc_add_notice( __( 'Please enter something into this new shiny field.' ), 'error' );
}
///...................................................................................................
/////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order' );

function alex_new_meta_order( $order_id ) {
    if ( ! empty( $_POST['alex_oblast'] ) ) {
        update_post_meta( $order_id, 'Область доставки: ', sanitize_text_field( $_POST['alex_oblast'] ) );
    }
    if ( ! empty( $_POST['alex_misto'] ) ) {
        update_post_meta( $order_id, 'Місто доставки: ', sanitize_text_field( $_POST['alex_misto'] ) );
    }
    if ( ! empty( $_POST['alex_nom_viddilennia'] ) ) {
        update_post_meta( $order_id, 'Номер відділення: ', sanitize_text_field( $_POST['alex_nom_viddilennia'] ) );
    }
    if ( ! empty( $_POST['my_field_name'] ) ) {
        update_post_meta( $order_id, 'Адреса відділення', sanitize_text_field( $_POST['my_field_name'] ) );
    }
    


}
///////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////---------------------------------------------
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'alex_billing_myfield_toadminpanel', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('My Field23').':</strong> ' . get_post_meta( $order->id, '_billing_myfield', true ) . '</p>';
    echo '<p><strong>'.__('alex_oblast').':</strong> ' . get_post_meta( $order->id, '_alex_oblast', true ) . '</p>';
    echo '<p><strong>'.__('alex_misto').':</strong> ' . get_post_meta( $order->id, '_alex_misto', true ) . '</p>';
    echo '<p><strong>'.__('alex_nom_viddilennia').':</strong> ' . get_post_meta( $order->id, '_alex_nom_viddilennia', true ) . '</p>';
    




}
/////////////////////////////////////---------------------------------------------

// Hook in

// add_filter( 'woocommerce_checkout_fields' , 'alex_added_billing_shipper' );

// // Our hooked in function - $fields is passed via the filter!

// function alex_added_billing_shipper( $fields ) {

//      $fields['billing']['billing_shipper'] = array(

//         'label'     => __('Відділення №', 'woocommerce'),

//     'placeholder'   => _x('Введіть номер відділення', 'placeholder', 'woocommerce'),

//     'required'  => true,

//     'class'     => array('form-row-wide'),

//     'clear'     => true

//      );

//      return $fields;

// }

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'alex_fields_to_adminpanel', 10, 1 );
function alex_billing_shipper_toadminpanel($order){

    echo '<h4><strong>'.__('Доставка:').':</strong> </h4>';
    echo '<p><strong>'.__('Область доставки').':</strong> ' . get_post_meta( $order->id, '_alex_oblast', true ) . '</p>';
    echo '<p><strong>'.__('Місто доставки').':</strong> ' . get_post_meta( $order->id, '_alex_misto', true ) . '</p>';
    echo '<p><strong>'.__('Номер відділення перевізника').':</strong> ' . get_post_meta( $order->id, '_alex_nom_viddilennia', true ) . '</p>';
    echo '<p><strong>'.__('Адреса відділення перевізника').':</strong> ' . get_post_meta( $order->id, '_alex_adr_viddilennia', true ) . '</p>';

}
/*3.0*/
function alex_needs_fields_here() {
    do_action('alex_needs_fields_here');
}
add_action('alex_needs_fields_here', 'fields_from_here_alex_needs');
 
function fields_from_here_alex_needs() {
  //Echo "<h1>Контактні дані</h1>";


}
/*7/10/16*/
add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );

function my_custom_checkout_field( $checkout ) {

    echo '<div id="my_custom_checkout_field"><h3>' . __('Способи оплати') . '</h3>';
    // woocommerce_form_field( 'my_field_name', array(
    //     'type'          => 'text',
    //     'class'         => array('my-field-class form-row-wide'),
    //     'label'         => __('Fill in this field'),
    //     'placeholder'   => __('Enter something'),
    //     ), $checkout->get_value( 'my_field_name' ));

    echo '</div>';
}

add_action( 'alex_adds_new_action', 'alex_add_fields_to_order' );

function alex_add_fields_to_order( $checkout ) {
    //цей текст буде під способами доставки(поля не виводяться)
}

add_action( 'alex_action_add_h3', 'alex_add_h3_to_order' );
function alex_add_h3_to_order( $checkout ) {
    echo '<div id="my_custom_checkout_field3"><h3>' . __('Вибір способу доставки') . '</h3>';
    echo '</div>';
}





// add_action( 'woocommerce_review_order_before_shipping' , 'alex_change_order_of_order' );
// function alex_change_order_of_order(){
//   echo "<h1>FROM MY OWN PLUGIN</h1>"
// }

/*

/////alex mosevych 2016

/////

*/

?>
















