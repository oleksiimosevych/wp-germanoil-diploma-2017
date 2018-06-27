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

if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
	alex_needs_fields_here();
	/*******/
?><!-- ........zusammen.zu bezahlen............. -->
					<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
					<tr class="order-total">
						<th><h3><?php _e( 'Всього', 'woocommerce' ); ?></h3></th>
						<td><?php wc_cart_totals_order_total_html(); ?></td>
					</tr>

					<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
<!-- ...................... -->	
<?	if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php//!-- <h1>PETRO</h1> --?>
		
		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<!-- <h1>GGGAASA</h1> -->

		<div class="col2-set" id="customer_details">
			
			<div class="col-1">
			<!-- <h1>Contact data here</h1> -->
				
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
				<!-- /**/ -->

				<?php do_action( 'alex_action_add_h3' ); ?>

					<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

						<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
					<div class = 'dostavka'>
<?/*DOSTAVKA----------------------------------------*/?>
						<?php wc_cart_totals_shipping_html(); ?>
<?/*DOSTAVKA----------------------------------------*/?>
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
				<!-- /**/ -->

				<?php//!-- <h1>PETRO11</h1> --?>
			</div>
			
			<div id="alex_checkout_oblast_misto">
				<p class="form-row form-row my-field-class form-row-wide validate-required woocommerce-validated" id="alex_oblast_field">
					<label for="alex_oblast" class="">Оберіть область доставки <abbr class="required" title="обов'язкове">*</abbr>
					</label>
					
					<?php
						global $wpdb;
						$regions_ua = $wpdb->get_results("SELECT id, region
														FROM located_region;");
						
						//print_r($cities_ua);
						//$array_of_regions= $regions_ua->fetch();
						//print_r($array_of_regions) ;
					?>
					<select onChange="var a = this.value; alert(a);" onclick="domReady();" name="alex_oblast" id="alex_oblast" class="select " data-placeholder="список">
						<option id ='default_region' value="<?php $wrong?>">НЕ ОБРАНО!</option>
					<?php
					// $region_id2=9; 
						foreach ($regions_ua as $ua_region) {
							// $region_id2=$ua_region->rid;
					?>
					<option id="ob<?php echo $ua_region->id ?>"><?php echo $ua_region->id." ".$ua_region->region ?></option>
					<?php } 
					 ?>
					</select>
				</p>
			</div>
			<?php 
				//echo $_POST['alex_oblast']
				$cifra= 10 ;
				$where= "where la.region =lr.id";

			?> 
			
















			<div id="alex_checkout_area">
				<p class="form-row form-row my-field-class form-row-wide validate-required woocommerce-validated" id="alex_oblast_field">
					<label for="alex_city" class="">Оберіть регіон доставки <abbr class="required" title="обов'язкове">*</abbr>
					</label>
					
					<?php
						global $wpdb;
						$area_ua = $wpdb->get_results("SELECT la.region as lareg,
														la.area as laa 
														from located_area la,
														located_region lr 
														".$where."
														;");
						//print_r($cities_ua);
					?>
					<select onChange="onChangeArea();" name="alex_area" id="alex_area" class="select " data-placeholder="список">
						<option id="default_area" value="<?php $wrong?>">НЕ ОБРАНО!</option>
					<?php 

					// $where= "where la.region = 4 ";
					// foreach ($regions_ua as $ua_region) {
							// while ($row = $area_ua->fetch())
							foreach ($area_ua as $ua_area) {
						// while ($row = mysql_fetch_array($cities_ua)){
							?>
							<option value ='<?php echo $ua_area->laa ?>'><?php echo $ua_area->laa ?></option>
						<?php } //} ?>
					</select>
				</p>
			</div>	

<script type="text/javascript">
	// (function () {
 //    if (window.addEventListener) {
 //        window.addEventListener('DOMContentLoaded', domReady, false);
 //    } else {
 //        window.attachEvent('onload', domReady);
 //    }
	// } ());

			function onChangeArea()
			{
				alert(a);
			}


				function domReady(){
				if(document.getElementById("obl1").selected ){
					//alert("VINNYCKA");
					document.getElementById("default_area").innerHTML = "Hello World!"
				}
			}
			</script>



			<div id="alex_checkout_city_misto">
				<p class="form-row form-row my-field-class form-row-wide validate-required woocommerce-validated" id="alex_oblast_field">
					<?php
						global $wpdb;
						$area_ua = $wpdb->get_results("SELECT r.region as rr, a.area as aa, v.village as cityvillage
														FROM  located_region r, located_area a, located_village v
														where r.id=a.region
														and a.id=v.area

														;");
						//print_r($cities_ua);
					?>
					<label for="alex_city" class="">Оберіть місто/село доставки <abbr class="required" title="обов'язкове">*</abbr>
					</label>
					<select name="alex_area" id="alex_oblast" class="select " data-placeholder="список">
							<option value="<?php $wrong?>">НЕ ОБРАНО!</option>
					<?php 
					// foreach ($regions_ua as $ua_region) {
							foreach ($area_ua as $ua_area) {
						// while ($row = mysql_fetch_array($cities_ua)){
							?>
							<option><?php echo $ua_area->cityvillage ?></option>
						<?php } //} ?>
					</select>
				</p>
			</div>



		






























			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>
		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
	<?php endif;
	/*******/
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