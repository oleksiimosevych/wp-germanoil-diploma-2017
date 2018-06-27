<?php
/*** Remove Query String from Static Resources ***/
function remove_cssjs_ver( $src ) {
 if( strpos( $src, '?ver=' ) )
 $src = remove_query_arg( 'ver', $src );
 return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );

add_filter( 'woocommerce_currencies', 'add_my_currency' );

function add_my_currency( $currencies ) {
     $currencies['ABC'] = __( 'ГРИВНЯ', 'woocommerce' );
     return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_my_currency_symbol', 10, 2);

function add_my_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'ABC': $currency_symbol = 'грн'; break;
     }
     return $currency_symbol;
}


add_action('wp_head','hook_google_analytics');

function hook_google_analytics() {
  ?>
		<!-- <script async> -->
      <!-- (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ -->
      <!-- (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), -->
      <!-- m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) -->
      <!-- })(window,document,'script','https://www.google-analytics.com/analytics.js','ga'); -->
      <!-- ga('create', 'UA-89127811-1', 'auto'); -->
      <!-- ga('send', 'pageview'); -->
    <!-- </script> -->
  
	<!-- <script async type='text/javascript'> -->
      <!-- (function (d, w, c) { (w[c] = w[c] || []).push(function() { -->
       <!-- try { w.yaCounter41635969 = new Ya.Metrika({ id:41635969, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true }); } catch(e) { } }); var n = d.getElementsByTagName('script')[0], s = d.createElement('script'), f = function () { n.parentNode.insertBefore(s, n); }; s.type = 'text/javascript'; s.async = true; s.src = 'https://mc.yandex.ru/metrika/watch.js'; if (w.opera == '[object Opera]') { d.addEventListener('DOMContentLoaded', f, false); } else { f(); } })(document, window, 'yandex_metrika_callbacks'); </script> <noscript><div><img src=https://mc.yandex.ru/watch/41635969' style='position:absolute; left:-9999px;' alt='' /></div></noscript> -->
       <?
}

?>

