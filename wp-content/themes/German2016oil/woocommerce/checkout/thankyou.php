<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( $order ) : ?>
	<?php if ( $order->has_status( 'failed' ) ) : ?>
		<p class="woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
		<p class="woocommerce-thankyou-order-failed-actions">
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</p>
	<?php else : ?>

		<p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); ?></p>
		<ul class="woocommerce-thankyou-order-details order_details">
			<li class="order">
				<?php _e( 'Order Number:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<li class="date">
				<?php _e( 'Date:', 'woocommerce' ); ?>
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
			</li>
			<li class="total">
				<?php _e( 'Total:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_formatted_order_total(); ?></strong>
			</li>
			<?php if ( $order->payment_method_title ) : ?>
			<li class="method">
      				<strong><?php //echo $order->payment_method_title; ?></strong>
			</li>
      <?php endif; ?>
		</ul>
		<div class="clear"></div>
		<? do_action('here_are_my_details'); ?>
	<?php endif; ?>
	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>
<?php else : ?>
	<p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>
<?php endif; ?>

<?php if($_COOKIE['myorder_id']==$order->get_order_number()){echo "<h3><center>Це замовлення уже здійснено!</h3>";}
  else{
				$customer_id = get_current_user_id();
				// $u_phone_before = get_post_meta( $order->id, '6. Телефон для СМС', true );
    //       		$u_phone = preg_replace('#\D+#', '', $u_phone_before);
    //       		$u_phone="+".$u_phone;
    //       		echo '<pre>';   
    //           ini_set("soap.wsdl_cache_enabled", "0"); 
    //           try {  
    //               // Подключаемся к серверу   
    //               $client = new SoapClient('http://turbosms.in.ua/api/wsdl.html');   
    //               // print_r($client->__getFunctions());   
    //               $auth = [   
    //                   'login' => 'regosso',   
    //                   'password' => 'Ass222Ass222'   
    //               ];   
    //               // Авторизируемся на сервере   
    //               $result = $client->Auth($auth);   
    //               // Результат авторизации   
    //           	       echo $result->AuthResult . PHP_EOL;   
    //               // Получаем количество доступных кредитов   
    //               $result = $client->GetCreditBalance();   
    //                    echo $result->GetCreditBalanceResult . PHP_EOL;   
    //               // Текст сообщения ОБЯЗАТЕЛЬНО отправлять в кодировке UTF-8   
    //                 $my_order = $order->get_order_number();
    //               $text.= 'Ваше замовлення прийнято і очікує на обробку. No замовлення: '.$my_order;   
    //               		 echo "Номер замовлення: ".$my_order;
    //               // Отправляем сообщение на один номер.   
    //               // Подпись отправителя может содержать английские буквы и цифры. Максимальная длина - 11 символов.   
    //               // Номер указывается в полном формате, включая плюс и код страны   
    //               //$u_phone = "+";
    //               $sms = [   
    //                   'sender' => 'Rassilka',   
    //                   //'sender' => 'GERMANOIL',   
    //                   // 'destination' => '+380XXXXXXXXX',   
    //                   'destination' => $u_phone,   
    //                   'text' => $text   
    //               ];
    //               if(!$_GET['reloaded']){  
    //                 $result = $client->SendSMS($sms);  
    //                 //$_GET['reloaded']='etwas';
    //                 setcookie("myorder_id", $my_order);
    //               }
    //           } catch(Exception $e) {  
    //               	echo 'ПОМИЛКА: ' . $e->getMessage() . PHP_EOL;  
    //           }  
    //         	echo '</pre>'; 
            	// echo '<p>НОМЕР: ' . get_post_meta( $order->id, 'billing_phone', true ) . '</p>';
          		// echo '<h3>Номер вашого замовлення GERMANOIL надіслано в SMS на номер '.$u_phone.'</h3>';
          		echo '<h3>Номер вашого замовлення GERMANOIL надіслано на Вашу емейл-адресу '.$u_phone.'</h3>';
}



?>