<?//php
// *
//  * The template for displaying all pages.
//  *
//  * This is the template that displays all pages by default.
//  * Please note that this is the WordPress construct of pages
//  * and that other 'pages' on your WordPress site will use a
//  * different template.
//  *
//  * @package FreeStore
?>

<?php
/*
Template Name: Contact
*/
?>
 
<?php get_header();?>
 
<?php
 
 // if($recaptcha_valid===false) {
    
 //        $commentError .=' Введіть капчу!';
 //        $hasError = true;
 //    }

$recaptcha_valid = apply_filters( 'recaptcha_valid' , null )  !== false;
if($recaptcha_valid){echo "";}
    else if(!$recaptcha_valid) {
        //echo "enter captcha";
        $hasError=true;
        $commentError="Введіть капчу!";
    }

 if(isset($_POST['submitted'])) {
    if(trim($_POST['contact_name']) === '') {
        $nameError = 'Введіть своє ім\'я';
        $hasError = true;
    } else {
        $name = trim($_POST['contact_name']);
    }
 
    if(trim($_POST['contact_email']) === '')  {
        $emailError = 'Введіть номер мобільного';
        $hasError = true;
    } else if (!eregi("^\+38\ \([0-9]{3}\)\ [0-9]{3}-[0-9]{2}-[0-9]{2}$", trim($_POST['contact_email']))) {
        $emailError = '<p class=\'error\'>Введіть правильний номер мобільного.</p>';
        $hasError = true;
    } else {
        $email = trim($_POST['contact_email']);
    }
 
    /*if(trim($_POST['contact_theme']) === '') {
        $themeError = 'Введіть тему ';
        $hasError = true;
    } else {
        $theme = trim($_POST['contact_theme']);
    }*/
 
    if(trim($_POST['contact_comments']) === '') {
        $commentError = 'Введіть повідомлення';
        $hasError = true;
    } else {
        if(function_exists('stripslashes')) {
            $comments = stripslashes(trim($_POST['contact_comments']));
        } else {
            $comments = trim($_POST['contact_comments']);
        }
    }
 
    if($resend){
        $hasError=true;
        $commentError="Ви намагалися повторно надіслати своє повідомлення!";
    }

    if(!isset($hasError)&&(!$_GET['resend'])) {
        $emailTo = get_option('tz_email');
        if (!isset($emailTo) || ($emailTo == '') ){
            $emailTo = get_option('admin_email');
        }
        $subject = 'Повідомлення з сайту GERMANOIL від '.$name;
        $body = "Ім'я: $name \n\nНомер мобільного: $email \n\nТема: $theme \n\nПовідомлення: $comments";
        $headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
        wp_mail($emailTo, $subject, $body, $headers);
        $emailSent = true;

    }
 
} ?>








	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			
			<?php get_template_part( '/templates/titlebar' ); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'templates/contents/content', 'page' ); ?>
			<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js">
			</script>
			<script type="text/javascript" src="/wp-content/themes/German2016oil/js/mask_2016.js"></script>

			<script type="text/javascript" src="/wp-content/themes/German2016oil/js/use_mask_2016.js"></script>


					<div id="contact_form">
                           <?php if($_GET['resend']){
                            echo "<div class=\"contact_message\">Ваше повідомлення успішно відправлено!<br>
                            Наш менеджер перетелефонує до вас як тільки зможе :)</div>";
                           } if(isset($emailSent) && $emailSent == true) { 

                            ?>
                                 <div class="contact_message">Ваше повідомлення успішно відправлено!</div>
                           <?php 
                           
                            wp_redirect( 'http://moja-stylna-shapochka.com.ua/contact-us?resend=true' );
                           } else { ?>
                                 <?php if(isset($hasError) || isset($captchaError)) { ?>
 
                                 <?php } ?>
                            <? if(!$_GET['resend']){ ?>
                                 <form action="<?php the_permalink(); ?>" id="contactForm" method="post">
 
                                       <div class="contact_left">
                                            <div class="contact_name">
                                            <!-- <h3>Ваше ім'я</h3> -->
                                                 <input type="text" placeholder="Ваше ім'я" name="contact_name" id="contact_name" value="<?php if(isset($_POST['contact_name'])) echo $_POST['contact_name'];?>" class="required requiredField" />
                                                 <?php if($nameError != '') { ?>
                                                       <div class="errors"><font color="red"><?=$nameError;?></font></div>
                                                 <?php } ?>
                                            </div>
                                            <div class="contact_email">
                                            <!-- <h3>Ваш email</h3> -->
                                                 <input type="text" placeholder="Введіть моб. тел. " name="contact_email"  id= "billing_phone" value="<?php if(isset($_POST['contact_email']))  echo $_POST['contact_email'];?>" class="required requiredField email" />
                                                 <?php if($emailError != '') { ?>
                                                       <div class="errors"><font color="red"><?=$emailError;?></font></div>
                                                 <?php } ?>
                                            </div>
                                            
                                       </div>
 
                                       <div class="contact_right">
                                            <div class="contact_textarea">
                                            <!-- <h3>Питання та пропозиції</h3> -->
                                                 <textarea placeholder="Повідомлення" name="contact_comments" id="commentsText" rows="12" cols="56" class="required requiredField"><?php if(isset($_POST['contact_comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['contact_comments']); } else { echo $_POST['contact_comments']; } } ?></textarea>
                                                 <?php if($commentError != '') { ?>
                                                       <div class="errors"><font color="red"><?=$commentError;?></font></div>
                                                 <?php } ?> 
                                            </div>
                                            <?
                                            //$disabled='';
                                            $attr = array(
                                                'data-theme' => 'dark',
                                            );
                                            
                                            do_action( 'recaptcha_print' , $attr );
                                            
                                                    $recaptcha_valid = apply_filters( 'recaptcha_valid' , null )  !== false;
                                                 if($recaptcha_valid){echo "";}
                                                 else if(!$recaptcha_valid) {
                                                    echo "<font color='red'>Потрібно підтвердити що ви людина! <br>Введіть капчу. </font>";
                                                    $hasError=true;
                                                    $commentError="Введіть капчу!";
                                                    // $something = apply_filters( 'recaptcha_error' , $something );
                                                    }
                                                    if ( is_wp_error( $something ) )
                                                        wp_die( $something );
                                                    // go furtherwiththing                                            
                                                ?>



                                            <button type="contsubmit" class="contact_submit" <?php echo $disabled; ?> background="white">Надіслати</button>
                                            <input type="hidden" name="submitted" id="submitted" value="true" />
                                       </div>
                                 </form>
                                 <? }//end of if get[resend] ?>
                           <?php } ?>
                      </div>

				<?php



//                my_ajax_comment_form_sumbit();
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->


	<?php $resend=true; get_sidebar(); ?>
	
	<div class="clearboth"></div>


	
<?php get_footer(); ?>
<style type="text/css">
	/*-------------------------------- Contact --------------------------------*/
#masthead{margin-top: -20px !important;}


#contact_form {
        position: relative;
        width: 100%;
	margin-top: 20px;
}
 
.contact_left {
        float: left;
        width: 20%;
}
 
.contact_right {
        float: right;
	width: -moz-calc(100% - 180px);
	width: -webkit-calc(100% - 180px);
	width: calc(100% - 180px);
}
 
.contact_name, .contact_email, .contact_theme, .contact_textarea { position: relative; }
 
.contact_name input[type="text"],
.contact_email input[type="text"],
.contact_theme input[type="text"] {
        position: relative;
	width: 100%;
        height: 30px;
        line-height: 30px;
	padding: 0 0 0 31px;
        margin: 0 0 20px;
        background: #f7f7f7 url(/wp-content/themes/German2016oil/images/nameid.png) no-repeat 0 0;
        background-size: 25px 25px !important;
        border: none;
        border-radius: 4px;
        box-shadow: inset 0.5px 0.5px 3px #aaaaad;
        font: normal 13px Arial, sans-serif;
        color: #434343;
}
.contact_email input[type="text"] { background: #f7f7f7 url(/wp-content/themes/German2016oil/images/mailid.png) no-repeat 0 0; }
.contact_theme input[type="text"] { background: #f7f7f7 url(/wp-content/themes/German2016oil/images/url.png) no-repeat 2px 0; }
 
.contact_textarea textarea {
        position: relative;
	width: -moz-calc(100% - 20px);
	width: -webkit-calc(100% - 20px);
	width: calc(100% - 20px);
        height: 116px;
        padding: 7px 10px;
	margin: 0 0 18px;
        background: #f7f7f7;
        border: none;
        border-radius: 4px;
        box-shadow: inset 0.5px 0.5px 3px #aaaaad;
	font: normal 13px Arial, sans-serif;
        color: #434343;
}
 
.contact_name input[type="text"]:focus, .contact_email input[type="text"]:focus, .contact_theme input[type="text"]:focus, .contact_textarea textarea:focus, .contact_submit:focus {
        outline: none;
        box-shadow: 0 0 5px #aaaaad;
}
 
.contact_submit {
	float: right;
	width: 120px;
	padding-top: 7px;
	padding-bottom: 4px;
	margin: 0 0 15px;
	/*background: ;*/
        border: none;
        border-radius: 4px;
	text-transform: uppercase;
	text-align: center;
	font-size: 16px;
	color: #fff;
	transition: background-color ease-in-out .15s;
	cursor: pointer;
}
.contact_submit:hover { background: #096fa2; }
 
.contact_message {
        width: 100%;
        height: 22px;
        padding: 70px 0;
        text-align: center;
        font: normal 22px Arial, sans-serif;
        color: #434343;
}
 
.errors, .errorss {
	position: absolute;
	bottom: 2px;
	left: 10px;
	font: normal 12px Arial, sans-serif;
        color: red;
	z-index: 999;
}
</style>