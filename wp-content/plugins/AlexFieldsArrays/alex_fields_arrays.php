<?php

/*

Plugin Name: Alex Fields Changes and Arrays

Plugin URI: www.alexpluginsforwordpress.blogspot.com

Description: Let me change your fields.

Author: Oleksii Mosevych

Author URI: https://plus.google.com/+alexmosevych

Text Domain: alexplugins

Version: 5.0

*/
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

//

add_filter( 'woocommerce_checkout_fields' , 'alex_cities_array' );

// Our hooked in function - $fields is passed via the filter!

function alex_cities_array( $fields ) {

    $fields['billing']['billing_email']['required']= false;

    

    if($_GET['bname']){
      $fields['billing']['billing_first_name']['default']=$_GET['bname'];      
    }
    if($_GET['bsurname']){
      $fields['billing']['billing_last_name']['default']=$_GET['bsurname'];      
    }
    if($_GET['bemail']){
      $fields['billing']['billing_email']['default']=$_GET['bemail'];      
    }
    if($_GET['bphone']){
      $fields['billing']['billing_phone']['default']=$_GET['bphone'];      
    }//else{// $fields['billing']['billing_phone']['default']= '+38';}
    return $fields;
}

add_filter( 'woocommerce_checkout_fields' , 'alex_billing_placeholders' );

// Our hooked in function - $fields is passed via the filter!

function alex_billing_placeholders( $fields ) {

//ADD PLACEHOLDERS

     $fields['billing']['billing_first_name']['placeholder'] = 'Введіть ім\'я';

     $fields['billing']['billing_last_name']['placeholder'] = 'Введіть прізвище';

     $fields['billing']['billing_email']['placeholder'] = 'Введіть адресу електронної пошти';

     $fields['billing']['billing_phone']['placeholder'] = '+380xxxxxxxxx';
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


//....................
add_action( 'woocommerce_after_order_notes', 'alex_add_pay' );

function alex_add_pay( $checkout ) {


}


add_action( 'woocommerce_before_order_notes', 'alex_add_f_1' );

function alex_add_f_1( $checkout ) {
  if($_GET['shipper']!=="Самовивіз"||$_GET['shipper']!=="Новапошта"){  
    echo '<div id="my_custom_checkout_field">' . __('') . '';
      echo 'Якщо вашого міста немає в списку - напишіть про це в примітку.';
      woocommerce_form_field( 'alex_nom_viddilennia', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Адреса чи номер відділення №'),
        'placeholder'   => __('Введіть номер відділення'),
        'required'      => 'true',
        ), $checkout->get_value( 'alex_nom_viddilennia' ));

    echo '</div>';
  }
}
///......................................................
/**
 * Process the checkout
 */
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process() {
    // Check if set, if its not set add an error.
    
    if($_GET['do_click']!=='buynow'){
        if(! $_POST['alex_pay'])
          wc_add_notice( __( 'Оберіть <b>Спосіб оплати</b>.' ), 'error' );
        if(($_POST['alex_dostavka']!=="Самовивіз")&&($_POST['alex_dostavka']!=="Новапошта") ){ 
              if ( ! $_POST['alex_nom_viddilennia'] )
                  wc_add_notice( __( 'Введіть <b>номер та адрес відділення</b>.' ), 'error' );
              if ( ! $_POST['alex_oblast'] )
                  wc_add_notice( __( 'Оберіть <b>область</b>.' ), 'error' );
              if ( ! $_POST['alex_city'] )
                  wc_add_notice( __( 'Оберіть <b>місто чи село</b> для доставки.' ), 'error' );
        }

        if($_POST['billing_email']&&$_POST['billing_phone']&&$_POST['billing_first_name']&&$_POST['billing_last_name']){
          wc_add_notice( __( '<b>Контактні дані</b> заповнено.' ));

        }
      if($_POST['alex_oblast']&&$_POST['alex_city']&&$_POST['alex_nom_viddilennia']){
          wc_add_notice( __( '<b>Адресу доставки визначено.</b>...' ));
        }
      if ( ! $_POST['alex_dostavka'] )
            wc_add_notice( __( 'Оберіть <b>Cпосіб доставки</b>.' ), 'error' );

      if($_POST['billing_email']&&$_POST['billing_phone']&&$_POST['billing_first_name']&&$_POST['billing_last_name']&&$_POST['alex_oblast']&&$_POST['alex_city']&&$_POST['alex_nom_viddilennia']&&$_POST['alex_dostavka']){
          wc_add_notice( __( '<b>Усі дані заповнено.</b>' ));
          ////////////////////////////////
////////////////
          // SMS2016();
////////////////
          ///////////////////////////////
          // wc_add_notice( __( '<b>Вам має бути надіслано SMS-повідомлення з номером замовлення на номер '.$_POST['billing_phone'].'.</b>' ));
          //if ( ! empty( $_POST['billing_phone'] ) ) {
          //  update_post_meta( $order_id, 'НОМЕР', sanitize_text_field( $_POST['billing_phone'] ) );
          //}
////////////////////////////////////////////////////////////////////////////////////////////////////////
          
      }
    }
      else if($_GET['do_click']=='buynow'){
        if($_POST['billing_phone']){
          wc_add_notice( __( '<b>Номер телефону</b> заповнено.' ));
        }
        else if(! $_POST['billing_phone']){
          wc_add_notice( __( '<b>Номер телефону</b> незаповнено.' ), 'error' );
        }
     
      }
    
 
}
///...................................................................................................
/////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_dost' );
function alex_new_meta_order_dost( $order_id ) {
    if ( ! empty( $_POST['alex_dostavka'] ) ) {
        update_post_meta( $order_id, '1. Спосіб доставки', sanitize_text_field( $_POST['alex_dostavka'] ) );
    }
}

add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_oblast' );
function alex_new_meta_order_oblast( $order_id ) {
    if ( ! empty( $_POST['alex_oblast'] ) ) {
        update_post_meta( $order_id, '2. Область', sanitize_text_field( $_POST['alex_oblast'] ) );
    }
}

add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_city' );
function alex_new_meta_order_city( $order_id ) {
    if ( ! empty( $_POST['alex_city'] ) ) {
        update_post_meta( $order_id, '3. Місто', sanitize_text_field( $_POST['alex_city'] ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_viddilennia' );
function alex_new_meta_order_viddilennia( $order_id ) {
    if ( ! empty( $_POST['alex_nom_viddilennia'] ) ) {
        update_post_meta( $order_id, '4. Відділення', sanitize_text_field( $_POST['alex_nom_viddilennia'] ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_pay' );
function alex_new_meta_order_pay( $order_id ) {
    if ( ! empty( $_POST['alex_pay'] ) ) {
        update_post_meta( $order_id, '5. Оплата', sanitize_text_field( $_POST['alex_pay'] ) );
    }
}

add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_phone' );
function alex_new_meta_order_phone( $order_id ) {
    if ( ! empty( $_POST['billing_phone'] ) ) {
        update_post_meta( $order_id, '6. Телефон для СМС', sanitize_text_field( $_POST['billing_phone'] ) );
    }
}
///not for mail
add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_bname' );
function alex_new_meta_order_bname( $order_id ) {
    if ( ! empty( $_POST['billing_first_name'] ) ) {
        update_post_meta( $order_id, '01. Ім\'я', sanitize_text_field( $_POST['billing_first_name'] ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_blname' );
function alex_new_meta_order_blname( $order_id ) {
    if ( ! empty( $_POST['billing_last_name'] ) ) {
        update_post_meta( $order_id, '02. Прізвище', sanitize_text_field( $_POST['billing_last_name'] ) );
    }
}
add_action( 'woocommerce_checkout_update_order_meta', 'alex_new_meta_order_comments' );
function alex_new_meta_order_comments( $order_id ) {
    if ( ! empty( $_POST['order_comments'] ) ) {
        update_post_meta( $order_id, '7. Коментар', sanitize_text_field( $_POST['order_comments'] ) );
    }
}
////////////////////////---------------------------------------------
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'waodaba1', 10, 1 );
// nema add_action( 'woocommerce_thankyou', 'waodaba1', 10, 1 );
function waodaba1($order){
    echo "<h3>ДОСТАВКА:</h3>";

    echo '<table border=1 ><tr><td><strong>'.__('01. Ім\'я').':</strong></td><td> '. get_post_meta( $order->id, '01. Ім\'я', true ).'</td></tr>'.'<tr><td><strong>'.__('02. Прізвище').':</strong></td><td> '. get_post_meta( $order->id, '02. Прізвище', true ).'</td></tr>';

    echo  '<tr><td><strong>'.__('1. Спосіб доставки').':</strong></td><td> ' . get_post_meta( $order->id, '1. Спосіб доставки', true ) . '</td></tr>';
    if(get_post_meta( $order->id, '2. Область', true )){echo '<tr><td><strong>'.__('2. Область').':</strong></td><td> ' . get_post_meta( $order->id, '2. Область', true ) . '</td></tr>
    <tr><td><strong>'.__('3. Місто').':</strong></td><td> ' . get_post_meta( $order->id, '3. Місто', true ) . '</td></tr>'.
    '<tr><td><strong>'.__('4. Відділення').':</strong></td><td> ' . get_post_meta( $order->id, '4. Відділення', true ) . '</td></tr>';}
    else{ echo '<tr><td><strong>' . __( 'Нова Пошта Область' ) . ':</strong></td><td>' . $order->shipping_state . '</td></tr>';
    echo '<tr><td><strong>' . __( 'Нова Пошта Місто' ) . ':</strong></td><td>' . $order->shipping_city . '</td></tr>';
    echo '<tr><td><strong>' . __( 'Нова Пошта Відділення' ) . ':</strong></td><td>' . $order->shipping_address_1 . '</td></tr>';   
    }
    echo '<tr><td><strong>'.__('5. Оплата').':</strong></td><td> ' . get_post_meta( $order->id, '5. Оплата', true ) . '</td></tr>'.   '<tr><td><strong>'.__('6. Номер телефону').':</strong></td><td> '. get_post_meta( $order->id, '6. Телефон для СМС', true ).'</td></tr>'.'<tr><td><strong>'.__('7. Коментар').':</strong></td><td> '. get_post_meta( $order->id, '7. Коментар', true ).'</td></tr></table>';
    ?>
    	<a onclick="PrintIt();"  id='print_order_here'>РОЗДРУКУВАТИ (НЕ)ЗМІНЕНЕ ЗАМОВЛЕННЯ</a>
    	<script type="text/javascript">
    		function PrintIt(){
  				window.print();
  			}
    	</script>
    	<style type="text/css">
      #mydata{border: 10px;}
    		a#print_order_here:hover{background-color: black; cursor:pointer; }
    	</style>
    <?
    
}

// define the woocommerce_email_order_meta callback 
function action_woocommerce_email_order_meta( $order, $sent_to_admin, $plain_text ) {
    ?> 
   <!--  <tr>
                <td align="center" valign="top">
                  <!- Body ->
                  <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                    <tr>
                      <td valign="top" id="body_content">
                        <!- Content ->
                        <table border="0" cellpadding="20" cellspacing="0" width="100%">
                          <tr>
                            <td valign="top">
                              <div id="body_content_inner"> -->
          <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left; <?php  echo 'border-top-width: 4px;'; ?>"><?php echo "<strong>".__('2. Область').":</strong>"; ?></th>
            <td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo get_post_meta( $order->id, '2. Область', true ); ?></td>
          </tr>

          <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left; <?php  echo 'border-top-width: 4px;'; ?>"><?php echo "<strong>".__('3. Місто').":</strong>"; ?></th>
            <td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo get_post_meta( $order->id, '3. Місто', true ); ?></td>
          </tr>

          <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left; <?php  echo 'border-top-width: 4px;'; ?>"><?php echo "<strong>".__('4. Відділення').":</strong>"; ?></th>
            <td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo get_post_meta( $order->id, '4. Відділення', true ); ?></td>
          </tr>

          <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left; <?php  echo 'border-top-width: 4px;'; ?>"><?php echo "<strong>".__('5. Оплата').":</strong>"; ?></th>
            <td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo get_post_meta( $order->id, '5. Оплата', true ); ?></td>
          </tr>

          <tr>
            <th class="td" scope="row" colspan="2" style="text-align:left; <?php  echo 'border-top-width: 4px;'; ?>"><?php echo "<strong>".__('1. Спосіб доставки').":</strong>"; ?></th>
            <td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo get_post_meta( $order->id, '1. Спосіб доставки', true ); ?></td>
          </tr>

    <? 
    //echo '<table><p><br> ' . get_post_meta( $order->id, '2. Область', true ) . '</p>
    //<p><strong>'.__('3. Місто').':</strong><br> ' . get_post_meta( $order->id, '3. Місто', true ) . '</p>'.
    //'<p><strong>'.__('4. Відділення').':</strong><br> ' . get_post_meta( $order->id, '4. Відділення', true ) . '</p>'.
    //'<p><strong>'.__('5. Оплата').':</strong><br> ' . get_post_meta( $order->id, '5. Оплата', true ) . '</p>'.    '<p><strong>'.__('1. Спосіб доставки').':</strong><br> ' . get_post_meta( $order->id, '1. Спосіб доставки', true ) . '</p></table>';
}; 
add_action( 'alex_email_in_order_table', 'action_woocommerce_email_order_meta', 10, 3 ); 

// add_action( 'woocommerce_thankyou', 'add_details_to_thankyou', 10, 3); 

// function add_details_to_thankyou($order, $sent_to_admin, $plain_text ){
//             echo "<h1>OOOOOOO</h1>";
//             echo "<h3>ДОСТАВКА:</h3>";
//     echo '<p><strong>'.__('2. Область').':</strong><br> ' . get_post_meta( $order->id, '2. Область', true ) . '</p>
//     <p><strong>'.__('3. Місто').':</strong><br> ' . get_post_meta( $order->id, '3. Місто', true ) . '</p>'.
//     '<p><strong>'.__('4. Відділення').':</strong><br> ' . get_post_meta( $order->id, '4. Відділення', true ) . '</p>'.
//     '<p><strong>'.__('5. Оплата').':</strong><br> ' . get_post_meta( $order->id, '5. Оплата', true ) . '</p>'.    '<p><strong>'.__('1. Спосіб доставки').':</strong><br> ' . get_post_meta( $order->id, '1. Спосіб доставки', true ) . '</p>';

//             //waodaba1($order);
            

// }
// nema add_action( 'woocommerce_thankyou', 'action_woocommerce_email_order_meta', 10, 3 ); 
         
// add the action aftter table 
//add_action( 'woocommerce_email_after_order_table', 'action_woocommerce_email_order_meta', 10, 3 ); 
//add befor table
//add_action( 'woocommerce_email_before_order_table', 'action_woocommerce_email_order_meta', 10, 3 ); 

/*3.0*/
function alex_needs_fields_here() {
    do_action('alex_needs_fields_here');
}
add_action('alex_needs_fields_here', 'fields_from_here_alex_needs');
 
function fields_from_here_alex_needs() {
  //Echo "<h1>Контактні дані</h1>";


}
/*7/10/16*/
add_action( 'woocommerce_before_order_notes', 'my_custom_checkout_field' );

function my_custom_checkout_field( $checkout ) {

    echo '<div id="my_custom_checkout_field"><h3>' . __('Способи оплати') . '</h3>';

    ?><p class ="form-row form-row my-field-class form-row-wide validate-required woocommerce-invalid woocommerce-invalid-required-field"><select name="alex_pay" id="alex_pay1" class="select " data-placeholder="список">
          <option value="">НЕ ОБРАНО!</option>
          <option value="Переказ на картку Приватбанку">Переказ на картку Приватбанку</option>
          <option value="Оплата готівкою при отриманні">Оплата готівкою при отриманні</option>                     
          </select></p>
    <?
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

add_action( 'woocommerce_after_order_notes', 'add_payment_methods_here' );
//woocommerce_order_details_after_customer_details
function add_payment_methods_here(){
  //echo '<div id="sposoby_dostavki"><h3>' . __('Оберіть перевізника') . '</h3>';
    //wc_cart_totals_shipping_html();
    //echo '</div>';
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
















