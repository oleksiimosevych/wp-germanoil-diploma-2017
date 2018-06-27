<?php
/**
 * @package FreeStore
 */
global $woocommerce; ?>
<header id="masthead" class="site-header">
	<?php if ( ! get_theme_mod( 'freestore-header-remove-topbar' ) ) : ?>
	<div class="site-header-topbar">
	</div>
	<?php endif; ?>
	<div class="site-container">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
		    <?php else : ?>
		        <h1 class="site-title"><a href="http://www.moja-stylna-shapochka.com.ua" title="GERMANOIL">GERMANOIL</a></h1>
		        <h2 class="site-description">ЯКІСТЬ ГАРАНТОВАНО!</h2>
		        <h1 class="site-title2"><a href="http://www.moja-stylna-shapochka.com.ua" title="GERMANOIL">GERMANOIL</a></h1>
		        <h2 class="site-description2">ЯКІСТЬ ГАРАНТОВАНО!</h2>
		    <?php endif; ?>
		</div><!-- .site-branding -->
		<div class ='phones'>
			<div class='phonewithimg'><img src='/wp-content/themes/German2016oil/templates/header/kyivstar-logo-ua.png' class ='operator-icon' id='kyivstar' alt='kyivstar'>
			 <span class='telephone'>(098) 666-66-66</span></div>
			<div class='phonewithimg'><img src='/wp-content/themes/German2016oil/templates/header/lifecell-logo-ua.png' class ='operator-icon' id='lifacell' alt='lifacell'>
			 <span class='telephone'>(093) 207-58-90</span></div>
			<div class='phonewithimg'><a href ="http://moja-stylna-shapochka.com.ua/contact-us/"><img src='/wp-content/themes/German2016oil/templates/header/mailid.png' class ='operator-icon' id='lifacell' alt='lifacell'>
			 <span class='telephone'>Зворотній зв'язок</a></span></div>
			<!-- <div class='phonewithimg'><img src='../../../logos/operators/skype-logo-ua.png' class ='operator-icon' id='skype' alt='skype'>
			<a href ='skype:germanoil_com_ua?chat'> <span class='telephone'>GermanOil</span></a></div> -->
		</div>
		<?php if ( freestore_is_woocommerce_activated() ) : ?>
				<div class="header-cart">
	                <a class="header-cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e( 'View your shopping cart', 'freestore' ); ?>">
	                    <span class="header-cart-amount">
	                        <?php echo sprintf( _n( '(%d)', '(%d)', $woocommerce->cart->cart_contents_count, 'freestore' ), $woocommerce->cart->cart_contents_count); ?> - <?php echo $woocommerce->cart->get_cart_total(); ?>
	                    </span>
	                    <span class="header-cart-checkout<?php echo ( $woocommerce->cart->cart_contents_count > 0 ) ? ' cart-has-items' : ''; ?>">
	                        <i class="fa fa-shopping-cart"></i>
	                    </span>
	                </a></div>
		<?php endif; ?>
		<nav id="site-navigation" class="main-navigation" role="navigation">
			<span class="header-menu-button"><i class="fa fa-bars"></i><span><?php echo esc_attr( get_theme_mod( 'freestore-header-menu-text', 'menu' ) ); ?></span></span>
			<div id="main-menu" class="main-menu-container">
				<div class="main-menu-close"><i class="fa fa-angle-right"></i><i class="fa fa-angle-left"></i></div>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
			</div>
		</nav><!-- #site-navigation -->
		<div class="clearboth"></div>
</div></header><!-- #masthead -->
<style type="text/css">h1.site-title{width: 300px !important; position: relative !important;background-color: black; z-index: 100;margin-left: -15px;    height: 88px;margin-top: -10px !important;}
div.site-branding{margin-top: -90px !important;position: relative !important;z-index: 200; margin:0px;}h2.site-description{color: white !important; margin: 0; margin-left: 57px;padding: 0;float: left;position: relative; z-index: 500 !important;margin-top: -18px;}h1.site-title2 a{	color: #29a6e5;}h1.site-title2{	position: fixed !important; margin-top: -90px; margin-left: -10px; color: #29a6e5;}.site-description2{	position: fixed; color: white; font-size: 10px;top: 60px;}</style>