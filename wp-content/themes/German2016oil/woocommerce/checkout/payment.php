<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
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
 * @version     2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div name="all_we_have">
<?
if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
	alex_needs_fields_here();
 do_action( 'woocommerce_review_order_before_order_total' ); ?>
					<tr class="order-total">
						<th><h3><?php _e( 'Всього', 'woocommerce' ); ?></h3></th>
						<td><h2><?php wc_cart_totals_order_total_html(); ?></h2></td>
					</tr>
					<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
					<hr>
<?	if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<div >
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
				<? if($_GET['do_click']!=='buynow'){ ?>
	          	<?php do_action( 'alex_action_add_h3' ); ?>
					<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
						<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
					<div class = 'dostavka'>
					</div>
						<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
					<?php endif; ?>
					<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
						<tr class="fee">
							<th><?php echo esc_html( $fee->name ); ?></th>
							<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
						</tr>
					<?php endforeach; ?>
					<?php if ( wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart ) : ?>
						<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
							<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
								<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
									<th><?php echo esc_html( $tax->label ); ?></th>
									<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr class="tax-total">
								<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
								<td><?php wc_cart_totals_taxes_total_html(); ?></td>
							</tr>
						<?php endif; ?>
					<?php endif; ?>
					<?php do_action( 'alex_adds_new_action' ); ?>
				<?php } ?>
			</div>
			<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
			<script type="text/javascript" async src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
			<script async src="http://moja-stylna-shapochka.com.ua/wp-content/themes/German2016oil/js/mask_2016.js"></script>
			<script async type="text/javascript" src="http://moja-stylna-shapochka.com.ua/wp-content/themes/German2016oil/js/use_mask_2016.js"></script>
		
			
<script type="text/javascript">
			$(document).ready(function() {
				// $("#alex_shipper").html("<h4><font color='red'>Обреріть спосіб доставки!</font></h4>");
				$("input[type=radio][name='shipping_method[0]']").click(function() {
			        if (this.value == 'local_pickup:11') { //intime
			            $( "#alex_oblast" ).show( "slow"); $( "#alex_oblast1" ).show( "slow"); $("#alex_checkout_city_misto").show("slow"); $("#my_custom_checkout_field").show("slow");
						// $('#ship-to-different-address-checkbox').hide("slow");
						$("#alex_shipper").html("<select hidden class='input-text select' name='alex_dostavka'><option value='ІнТайм'>ІнТайм</option></select> <?php $_GET['alex_dostavka']='ІнТайм';  setcookie('sposib', 'ІнТайм');?>");
			        }
			        else if (this.value == 'nova_poshta_shipping_method') {
			            $( "#alex_oblast" ).hide( ); $( "#alex_oblast1" ).hide( "slow"); $("#alex_checkout_city_misto").hide("slow"); $("#my_custom_checkout_field").hide("slow");
						$("#alex_shipper").html("<select hidden class='input-text select' name='alex_dostavka'><option value='Новапошта'>Новапошта</option></select> <?php $_GET['alex_dostavka']='Новапошта';  setcookie('sposib', 'Новапошта');?>"); 
			        }
			        else if(this.value == 'local_pickup:12'){
			        	$( "#alex_oblast" ).hide( "slow");///samovivis
						$( "#alex_oblast1" ).hide( "slow"); $("#alex_checkout_city_misto").hide("slow"); $("#my_custom_checkout_field").hide("slow");
						//$('#ship-to-different-address-checkbox').hide("slow");
						$("#alex_shipper").html("<select hidden class='input-text  select' name='alex_dostavka'><option value='Самовивіз'>Самовивіз</option></select> <?php $_GET['alex_dostavka']='Самовивіз'; setcookie('sposib', 'Самовивіз'); ?>");
			        }
			    });
				$("select[name='alex_oblast']").bind("change", function(){
					$.get("", {bname: $("#billing_first_name").val(),
					bsurname: $("input[name='billing_last_name']").val(),
					bemail: $("input[name='billing_email']").val(),
					bphone:$("input[name='billing_phone']").val(),
					 oblast: $("select[name='alex_oblast']").val(),
					 do_click: $("#do_click1").val(),
					 dostavka: $("select[name='alex_dostavka']").val()},
					 function(data){
					 	//;div[name='alex_checkout_oblast_misto']
						$("body").html(data, "select[name='alex_oblast']");
					});
				});
				$("#alex_dostavka1").bind("change", function(){
					$.get("", {bname: $("#billing_first_name").val(),
					bsurname: $("input[name='billing_last_name']").val(),
					bemail: $("input[name='billing_email']").val(),
					bphone:$("input[name='billing_phone']").val(),
					 // oblast: $("select[name='alex_oblast']").val(),
					 do_click: $("#do_click1").val(),
					 dostavka: $("#alex_dostavka1").val(),
					 shipper: $("#alex_dostavka1").val()},
					 function(data){
					 	//;div[name='alex_checkout_oblast_misto']
						$("body").html(data, "select[name='alex_dostavka1']");
					});
				});
			})
</script>	
			<? if($_GET['do_click']!=='buynow'){ ?>
			<?php
			if(isset($_GET['oblast']))
				{/*echo "intime";*/ $_POST['alex_dostavka']='ІнТайм'; $_GET['alex_dostavka']='ІнТайм';
					echo "<select hidden class='input-text select' name='alex_dostavka'><option value='ІнТайм'>ІнТайм</option></select>"; $_GET['alex_dostavka']='ІнТайм';  setcookie('sposib', 'ІнТайм');
				}
			else{
				if($_COOKIE['sposib']=='Самовивіз'){
					// echo "COOsamovivis";
					$_POST['alex_dostavka']='Самовивіз';
				}
				if($_COOKIE['sposib']=='Новапошта'){
					$_POST['alex_dostavka']='Новапошта';
					// echo "COONP";
				}
			}
			 ?>
			<div id="alex_checkout_oblast_misto" name = "alex_checkout_oblast_misto">
<? //tut vybir dostavki ?>
				<div id="alex_shipper">
					
				</div>
<? //tut vybir dostavki end ?>
				<? if($_GET['shipper']!=="Самовивіз"||$_GET['shipper']!=="Новапошта"){  ?>
				<?/*DOSTAVKA----------------------------------------*/?>
						<?php wc_cart_totals_shipping_html(); ?>
				<?/*DOSTAVKA----------------------------------------*/?>
				<?php do_action( 'alex_action_add_novaposhta' ); ?>
					<p class="form-row form-row my-field-class form-row-wide validate-required woocommerce-validated" id="alex_oblast_field">
						<label id="alex_oblast" for="alex_oblast" class=""  <abbr class="required" title="обов'язкове">Необхідно обрати область *</abbr>
						</label>
						<?php
						global $wpdb;
						$regions_ua = $wpdb->get_results("SELECT id, region
														FROM located_region;", ARRAY_A);
						?>
						<select name="alex_oblast" id="alex_oblast1" class="select " data-placeholder="список">
						<?php if($_GET['oblast']){ ?>
							<option value="<?php echo $_GET['oblast']; ?>"><?php echo /*$jsonid."|".*/$_GET['oblast']; ?></option>
						<?php }else{ ?>
								<option value="<?php $wrong?>">НЕ ОБРАНО!</option>
						<?php } //end og else?>
						<?php
							foreach ($regions_ua as $ua_region) {
							?><option value="<?php echo $ua_region['region']; ?>"><?php echo /*$jsonid."|".*/$ua_region['region']; ?></option>
						<?php } //end of foreach 
						?> 
					</select>
				</p>
			<?php include "alex_ajax.php"; ?>
			  <? }//end of if from samovivis ?>
			 <?php } // end of if from do click?>
				<? if($_GET['do_click']!=='buynow'){ ?>
			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>
		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
					<?php } // end of if from do click?>
	<?php endif;
}
?>
<!-- sposoby oplaty -->
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart->needs_payment() ) : 
	?>
		<ul class="wc_payment_methods payment_methods methods">
			<?php
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
					}
				} else {
					echo '<li>' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_country() ? __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : __( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>';
				}
			?>
		</ul>

	<?php endif; ?>

	<div class="form-row place-order">
		<noscript>
			<?php _e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ); ?>
			<br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>" />
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

<!-- ........zusammen.............. -->
					<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
					<tr class="order-total">
					<br>
						<th><h3><?php _e( 'До оплати', 'woocommerce' ); ?></h3></th>
						<td><?php wc_cart_totals_order_total_html(); ?></td>
					</tr>

					<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
<!-- ...................... -->


		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php echo apply_filters( 'woocommerce_order_button_html', '<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" />' ); ?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout' ); ?>
	</div>
</div>

<?php
if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
?>
</div>
<?