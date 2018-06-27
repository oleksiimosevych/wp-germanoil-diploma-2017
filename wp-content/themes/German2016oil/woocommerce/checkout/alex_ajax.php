			 <div id='givemerajonhere' name="givemerajonhere"></div>
			<div id="alex_checkout_city_misto">
				<p class="form-row form-row my-field-class form-row-wide validate-required woocommerce-validated" id="alex_oblast_field">
					<label for="alex_city" class="">Оберіть місто/село доставки <abbr class="required" title="обов'язкове">*</abbr>
					</label>
						<?php
						global $wpdb;
						if($_GET['oblast']){
							$rregion="'".$_GET['oblast']."'";
							$city_ua = $wpdb->get_results("SELECT r.region as rr, a.area as aa, v.village as cityvillage
														FROM  located_region r, located_area a, located_village v
														where r.id=a.region
														and a.id=v.area
														and r.region=$rregion
														order by cityvillage asc;");
							?>
							<select name="alex_city" id="alex_city" class="select " data-placeholder="список">
								<option value="<?php $wrong?>">НЕ ОБРАНО!</option>
								<?php foreach ($city_ua as $ua_city) {	?>
									<option><?php echo $ua_city->cityvillage ?>, <?php echo $ua_city->aa ?> район</option>
								<?php }//e o foreach  ?>
							</select>
						<? } else{ //e o if
							echo "Область не обрано!";
						}
							?>
				</p>
			</div>
		</div><?//e o div form payment.php ?>