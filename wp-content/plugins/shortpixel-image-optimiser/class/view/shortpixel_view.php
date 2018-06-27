<?php

class ShortPixelView {
    
    private $ctrl;
    
    public function __construct($controller) {
        $this->ctrl = $controller;
    }
    
        //handling older
    public function ShortPixelView($controller) {
        $this->__construct($controller);
    }

    public function displayQuotaExceededAlert($quotaData, $averageCompression = false) 
    { ?>    
        <br/>
        <div class="wrap sp-quota-exceeded-alert">
            <?php if($averageCompression) { ?>
            <div style="float:right; margin-top: 10px">
                <div class="bulk-progress-indicator">
                    <div style="margin-bottom:5px"><?php _e('Average reduction','shortpixel');?></div>
                    <div id="sp-avg-optimization"><input type="text" id="sp-avg-optimization-dial" value="<?php echo("" . round($averageCompression))?>" class="dial"></div>
                    <script>
                        jQuery(function() {
                            ShortPixel.percentDial("#sp-avg-optimization-dial", 60);
                        });
                    </script>
                </div>
            </div>
            <?php } ?>
            <h3><?php /* translators: header of the alert box */ _e('Quota Exceeded','shortpixel');?></h3>
            <p><?php /* translators: body of the alert box */ 
              printf(__('The plugin has optimized <strong>%s images</strong> and stopped because it reached the available quota limit.','shortpixel'), 
                      number_format($quotaData['APICallsMadeNumeric'] + $quotaData['APICallsMadeOneTimeNumeric']));?> 
            <?php if($quotaData['totalProcessedFiles'] < $quotaData['totalFiles']) { ?>
                <?php 
                    printf(__('<strong>%s images and %s thumbnails</strong> are not yet optimized by ShortPixel.','shortpixel'),
                        number_format($quotaData['mainFiles'] - $quotaData['mainProcessedFiles']), 
                        number_format(($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles']))); ?>
                <?php } ?></p>
            <div> <!-- style='float:right;margin-top:20px;'> -->
                <a class='button button-primary' href='https://shortpixel.com/login/<?php echo($this->ctrl->getApiKey());?>' target='_blank'><?php _e('Upgrade','shortpixel');?></a>
                <input type='button' name='checkQuota' class='button' value='<?php _e('Confirm New Quota','shortpixel');?>' onclick="javascript:window.location.reload();">
            </div>
            <p><?php _e('Get more image credits by referring ShortPixel to your friends!','shortpixel');?> 
                <a href="https://shortpixel.com/login/<?php echo($this->ctrl->getApiKey());?>/tell-a-friend" target="_blank">
                    <?php _e('Check your account','shortpixel');?>
                </a> <?php _e('for your unique referral link. For each user that joins, you will receive +100 additional image credits/month.','shortpixel');?>
            </p>
            
        </div> <?php 
    }
    
    public static function displayApiKeyAlert() 
    { ?>
        <p><?php _e('In order to start the optimization process, you need to validate your API Key in the '
                . '<a href="options-general.php?page=wp-shortpixel">ShortPixel Settings</a> page in your WordPress Admin.','shortpixel');?>
        </p>
        <p><?php _e('If you don’t have an API Key, you can get one delivered to your inbox, for free.','shortpixel');?></p>
        <p><?php _e('Please <a href="https://shortpixel.com/wp-apikey" target="_blank">sign up to get your API key.</a>','shortpixel');?>
        </p>
    <?php
    }
    
    public static function displayActivationNotice($when = 'activate')  { ?>
        <div class='notice notice-warning' id='short-pixel-notice-<?php echo($when);?>'>
            <?php if($when != 'activate') { ?>
            <div style="float:right;"><a href="javascript:dismissShortPixelNotice('<?php echo($when);?>')" class="button" style="margin-top:10px;"><?php _e('Dismiss','shortpixel');?></a></div>
            <?php } ?>
            <h3><?php _e('ShortPixel Optimization','shortpixel');?></h3> <?php
            switch($when) {
                case '2h' : 
                    _e("Action needed. Please <a href='https://shortpixel.com/wp-apikey' target='_blank'>get your API key</a> to activate your ShortPixel plugin.",'shortpixel') . "<BR><BR>";
                    break;
                case '3d':
                    _e("Your image gallery is not optimized. It takes 2 minutes to <a href='https://shortpixel.com/wp-apikey' target='_blank'>get your API key</a> and activate your ShortPixel plugin.",'shortpixel') . "<BR><BR>";
                    break;
                case 'activate':
                    self::displayApiKeyAlert();
                    break;
            }
            ?>
        </div>
    <?php
    }
    
    public function displayBulkProcessingForm($quotaData,  $thumbsProcessedCount, $under5PercentCount, $bulkRan, 
                                              $averageCompression, $filesOptimized, $savedSpace, $percent, $customCount) {
        ?>
        <div class="wrap short-pixel-bulk-page">
            <h1>Bulk Image Optimization by ShortPixel</h1>
        <?php
        if ( !$bulkRan ) {
            ?>
            <div class="notice notice-info sp-floating-block sp-full-width">
                <form class='start' action='' method='POST' id='startBulk'>
                    <input type='hidden' id='mainToProcess' value='<?php echo($quotaData['mainFiles'] - $quotaData['mainProcessedFiles']);?>'/>
                    <input type='hidden' id='totalToProcess' value='<?php echo($quotaData['totalFiles'] - $quotaData['totalProcessedFiles']);?>'/>
                    <div class="bulk-stats-container">
                        <h3 style='margin-top:0;'><?php _e('Your media library','shortpixel');?></h3>
                        <div class="bulk-label"><?php _e('Original images','shortpixel');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['mainMlFiles']));?></div><br>
                        <div class="bulk-label"><?php _e('Smaller thumbnails','shortpixel');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['totalMlFiles'] - $quotaData['mainMlFiles']));?></div>
                        <div style='width:165px; display:inline-block; padding-left: 5px'>
                            <input type='checkbox' id='thumbnails' name='thumbnails' onclick='ShortPixel.checkThumbsUpdTotal(this)' <?php echo($this->ctrl->processThumbnails() ? "checked":"");?>> 
                            <?php _e('Include thumbnails','shortpixel');?>
                        </div><br>
                        <?php if($quotaData["totalProcessedMlFiles"] > 0) { ?>
                        <div class="bulk-label bulk-total"><?php _e('Total images','shortpixel');?></div>
                        <div class="bulk-val bulk-total"><?php echo(number_format($quotaData['totalMlFiles']));?></div>
                        <br><div class="bulk-label"><?php _e('Already optimized originals','shortpixel');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['mainProcessedMlFiles']));?></div><br>
                        <div class="bulk-label"><?php _e('Already optimized thumbnails','shortpixel');?></div>
                        <div class="bulk-val"><?php echo(number_format($quotaData['totalProcessedMlFiles'] - $quotaData['mainProcessedMlFiles']));?></div><br>
                        <?php } ?>
                        <div class="bulk-label bulk-total"><?php _e('Total to be optimized','shortpixel');?></div>
                        <div class="bulk-val bulk-total" id='displayTotal'><?php echo(number_format($quotaData['totalMlFiles'] - $quotaData['totalProcessedMlFiles']));?></div>

                        <?php if($customCount > 0) { ?>
                        <h3 style='margin-bottom:10px;'><?php _e('Your custom folders','shortpixel');?></h3>
                        <div class="bulk-label bulk-total"><?php _e('Total to be optimized','shortpixel');?></div>
                        <div class="bulk-val bulk-total" id='displayTotal'><?php echo(number_format($customCount));?></div>                        
                        <?php  } ?>
                    </div>
                    <?php if($quotaData['totalFiles'] - $quotaData['totalProcessedFiles'] + $customCount > 0) { ?>
                    <div class="bulk-play">
                        <input type='hidden' name='bulkProcess' id='bulkProcess' value='Start Optimizing'/>
                        <a href='javascript:void(0);' onclick="document.getElementById('startBulk').submit();" class='button'>
                            <div style="width: 320px">
                                <div class="bulk-btn-img" class="bulk-btn-img">
                                    <img src='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/robo-slider.png' ));?>'/>
                                </div>
                                <div  class="bulk-btn-txt">
                                    <?php printf(__('<span class="label">Start Optimizing</span><br> <span class="total">%s</span> images','shortpixel'),
                                                 number_format($quotaData['totalFiles'] - $quotaData['totalProcessedFiles']));?>
                                </div>
                                <div class="bulk-btn-img" class="bulk-btn-img">
                                    <img src='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/arrow.png' ));?>'/>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php }  else {?>
                    <div class="bulk-play bulk-nothing-optimize">
                        <?php _e('Nothing to optimize! The images that you add to Media Gallery will be automatically optimized after upload.','shortpixel');?>
                    </div>
                    <?php } ?>
                </form>
            </div>
            <?php if($quotaData['totalFiles'] - $quotaData['totalProcessedFiles'] > 0) { ?>
                <div class='shortpixel-clearfix'></div>
                <div class="bulk-wide">
                    <h3 style='font-size: 1.1em; font-weight: bold;'>
                        <?php _e('After you start the bulk process, in order for the optimization to run, you must keep this page open and your computer running. If you close the page for whatever reason, just turn back to it and the bulk process will resume.','shortpixel');?>
                    </h3>
                </div>
            <?php } ?>
            <div class='shortpixel-clearfix'></div>
            <div class="bulk-text-container">
                <h3><?php _e('What are Thumbnails?','shortpixel');?></h3>
                <p><?php _e('Thumbnails are smaller images usually generated by your WP theme. Most themes generate between 3 and 6 thumbnails for each Media Library image.','shortpixel');?></p>
                <p><?php _e("The thumbnails also generate traffic on your website pages and they influence your website's speed.",'shortpixel');?></p>
                <p><?php _e("It's highly recommended that you include thumbnails in the optimization as well.",'shortpixel');?></p>
            </div>
            <div class="bulk-text-container" style="padding-right:0">
                <h3><?php _e('How does it work?','shortpixel');?></h3>
                <p><?php _e('The plugin processes images starting with the newest ones you uploaded in your Media Library.','shortpixel');?></p>
                <p><?php _e('You will be able to pause the process anytime.','shortpixel');?></p>
                <p><?php echo($this->ctrl->backupImages() ? __("<p>Your original images will be stored in a separate back-up folder.</p>",'shortpixel') : "");?></p>
                <p><?php _e('You can watch the images being processed live, right here, after you start optimizing.','shortpixel');?></p>
            </div>
            <?php
        } elseif($percent) // bulk is paused
        { ?>
            <?php echo($this->displayBulkProgressBar(false, $percent, "", $quotaData['APICallsRemaining'], $this->ctrl->getAverageCompression(), 1, $customCount));?>
            <p><?php _e('Please see below the optimization status so far:','shortpixel');?></p>
            <?php $this->displayBulkStats($quotaData['totalProcessedFiles'], $quotaData['mainProcessedFiles'], $under5PercentCount, $averageCompression, $savedSpace);?>
            <?php if($quotaData['totalProcessedFiles'] < $quotaData['totalFiles']) { ?>
                <p><?php printf(__('%d images and %d thumbnails are not yet optimized by ShortPixel.','shortpixel'),
                                number_format($quotaData['mainFiles'] - $quotaData['mainProcessedFiles']),
                                number_format(($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles'])));?> 
                </p>
            <?php } ?>
            <p><?php _e('You can continue optimizing your Media Gallery from where you left, by clicking the Resume processing button. Already optimized images will not be reprocessed.','shortpixel');?></p>
        <?php
        } else { ?>
            <div class="sp-container">
                <div class='notice notice-success sp-floating-block sp-single-width' style="height: 80px;overflow:hidden;">
                    <div style='float:left;margin:5px 20px 5px 0'><img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider.png' ));?>"></div>
                    <div class="sp-bulk-summary">
                        <input type="text" value="<?php echo("" . round($averageCompression))?>" id="sp-total-optimization-dial" class="dial">
                    </div>
                    <p style="margin-top:4px;">
                        <span style="font-size:1.2em;font-weight:bold"><?php _e('Congratulations!','shortpixel');?></span><br>
                        <?php _e('Your media library has been successfully optimized!','shortpixel');?>
                        <span class="sp-bulk-summary"><a href='javascript:void(0);'><?php _e('Summary','shortpixel');?></a></span>
                    </p>
                </div>
                <div class='notice notice-success sp-floating-block sp-single-width' style="height: 80px;overflow:hidden;padding-right: 0;">
                    <div style="float:left; margin-top:-5px">
                        <p style='margin-bottom: -2px; font-weight: bold;'>
                            <?php _e('Share your optimization results:','shortpixel');?>
                        </p>
                        <div style='display:inline-block; margin: 16px 16px 6px 0;float:left'>
                            <div id="fb-root"></div>
                            <script>
                                (function(d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0];
                                    if (d.getElementById(id)) return;
                                    js = d.createElement(s); js.id = id;
                                    js.src = "//connect.facebook.net/<?php echo(get_locale());?>/sdk.js#xfbml=1&version=v2.6";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));
                            </script>
                            <div style="float:left;width:240px;">
                                <div class="fb-like" data-href="https://www.facebook.com/ShortPixel" data-width="260" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
                            </div>
                            <div style="float:left;margin:-7px 0 0 10px">
                                <a href="https://twitter.com/share" class="twitter-share-button" data-url="https://shortpixel.com" 
                                   data-text="<?php 
                                        if(0+$averageCompression>20) {
                                            _e("I just optimized my images by ",'shortpixel');
                                        } else {
                                            _e("I just optimized my images ",'shortpixel');
                                        }
                                        echo(round($averageCompression) ."%");
                                        _e("with @ShortPixel, a great plugin for increasing #WordPress page speed:",'shortpixel');?>" 
                                   data-size='large'><?php _e('Tweet','shortpixel');?></a>
                            </div>
                            <script>
                                jQuery(function() {
                                    jQuery("#sp-total-optimization-dial").val("<?php echo("" . round($averageCompression))?>");
                                    ShortPixel.percentDial("#sp-total-optimization-dial", 60);
                                    
                                    jQuery(".sp-bulk-summary").spTooltip({
                                        tooltipSource: "inline",
                                        tooltipSourceID: "#sp-bulk-stats"
                                    });
                                });
                                !function(d,s,id){//Just optimized my site with ShortPixel image optimization plugin
                                    var js,
                                        fjs=d.getElementsByTagName(s)[0],
                                        p=/^http:/.test(d.location)?'http':'https';
                                    if(!d.getElementById(id)){js=d.createElement(s);
                                    js.id=id;js.src=p+'://platform.twitter.com/widgets.js';
                                    fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
                            </script>
                        </div>
                    </div>
                    <?php if(0+$averageCompression>30) {?> 
                    <div class='shortpixel-rate-us' style='float:left;padding-top:0'>
                        <a href="https://wordpress.org/support/view/plugin-reviews/shortpixel-image-optimiser?rate=5#postform" target="_blank">
                            <span>
                                <?php _e('Please rate us!','shortpixel');?>&nbsp;
                            </span><br><img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/stars.png' ));?>">
                        </a>
                    </div>
                    <?php } ?>
                </div>
                <div id="sp-bulk-stats" style="display:none">
                    <?php $this->displayBulkStats($quotaData['totalProcessedFiles'], $quotaData['mainProcessedFiles'], $under5PercentCount, $averageCompression, $savedSpace);?>
                </div>            
            </div>
            <p><?php printf(__('Go to the ShortPixel <a href="%soptions-general.php?page=wp-shortpixel#stats">Stats</a> '
                             . 'and see all your websites\' optimized stats. Download your detailed <a href="https://api.shortpixel.com/v2/report.php?key=%s">Optimization Report</a> '
                             . 'to check your image optimization statistics for the last 40 days.','shortpixel'), get_admin_url(), $this->ctrl->getApiKey());?></p>
            <?php 
            $failed = $this->ctrl->getPrioQ()->getFailed();
            if(count($failed)) { ?>
                <div class="bulk-progress" style="margin-bottom: 15px">
                    <p>
                        <?php _e('The following images could not be processed because of their limited write rights. This usually happens if you have changed your hosting provider. Please restart the optimization process after you granted write rights to all the files below.','shortpixel');?>
                    </p>
                    <?php $this->displayFailed($failed); ?>
                </div>
            <?php } ?>
            <div class="bulk-progress notice notice-info sp-floating-block sp-double-width">
                <?php
                $todo = $reopt = false;
                if($quotaData['totalProcessedFiles'] < $quotaData['totalFiles']) { 
                    $todo = true;
                    $mainNotProcessed = $quotaData['mainFiles'] - $quotaData['mainProcessedFiles'];
                    $thumbsNotProcessed = ($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles']);
                    ?>
                    <p>
                        <?php 
                        if($mainNotProcessed && $thumbsNotProcessed) {
                            printf(__("%s images and %s thumbnails are not yet optimized by ShortPixel.",'shortpixel'), 
                                    number_format($mainNotProcessed), number_format($thumbsNotProcessed)); 
                        } elseif($mainNotProcessed) {
                            printf(__("%s images are not yet optimized by ShortPixel.",'shortpixel'), number_format($mainNotProcessed)); 
                        } elseif($thumbsNotProcessed) {
                            printf(__("%s thumbnails are not yet optimized by ShortPixel.",'shortpixel'), number_format($thumbsNotProcessed)); 
                        }
                        _e('','shortpixel');
                        if (count($quotaData['filesWithErrors'])) { 
                            _e('Some have errors:','shortpixel');
                            foreach($quotaData['filesWithErrors'] as $id => $data) {
                                if(ShortPixelMetaFacade::isCustomQueuedId($id)) {
                                    echo('<a href="'.trailingslashit(network_site_url("/")) . ShortPixelMetaFacade::filenameToRootRelative($data['Path']).'" title="'.$data['Message'].'" target="_blank">'.$data['Name'].'</a>,&nbsp;');
                                } else {
                                    echo('<a href="post.php?post='.$id.'&action=edit" title="'.$data['Message'].'">'.$data['Name'].'</a>,&nbsp;');
                                }
                            } 
                        } ?>
                    </p>
                <?php }
                $settings = $this->ctrl->getSettings();
                $optType = $settings->compressionType == '1' ? 'lossy' : 'lossless';
                $otherType = $settings->compressionType == '1' ? 'lossless' : 'lossy';
                if(   !$this->ctrl->backupFolderIsEmpty()
                   && (   ($quotaData['totalProcLossyFiles'] > 0 && $settings->compressionType == 0)
                       || ($quotaData['totalProcLosslessFiles'] > 0 && $settings->compressionType == 1)))
                {     
                    $todo = $reopt = true;
                    $statType = $settings->compressionType == '1' ? 'Lossless' : 'Lossy';
                    $thumbsCount = $quotaData['totalProc'.$statType.'Files'] - $quotaData['mainProc'.$statType.'Files'];
                    ?>
                    <p id="with-thumbs" <?php echo(!$settings->processThumbnails ? 'style="display:none;"' : "");?>>
                        <?php printf(__('%s images and %s thumbnails were optimized <strong>%s</strong>. You can re-optimize <strong>%s</strong> the ones that have backup.','shortpixel'), 
                                     number_format($quotaData['mainProc'.$statType.'Files']),
                                     number_format($thumbsCount), $otherType, $optType);?>
                    </p>
                    <p id="without-thumbs" <?php echo($settings->processThumbnails ? 'style="display:none;"' : "");?>>
                        <?php printf(__('%s images were optimized <strong>%s</strong>. You can re-optimize <strong>%s</strong> the ones that have backup. ','shortpixel'), 
                                     number_format($quotaData['mainProc'.$statType.'Files']),
                                     $otherType, $optType);?>
                        <?php echo($thumbsCount ? number_format($thumbsCount) . __(' thumbnails will be restored to originals.','shortpixel') : '');?>
                    </p>
                    <?php
                } ?>
                <p><?php if($todo) {
                        _e('Restart the optimization process for these images by clicking the button below.','shortpixel');
                    } else {
                        _e('Restart the optimization process for new images added to your library by clicking the button below.','shortpixel');
                    } 
                    printf(__('Already  <strong>%s</strong> optimized images will not be reprocessed.','shortpixel'), $todo ? ($optType) : '');
                    if($reopt) { ?>
                    <br><?php _e('Please note that reoptimizing images as <strong>lossy/lossless</strong> may use additional credits.','shortpixel')?> 
                    <a href="http://blog.shortpixel.com/the-all-new-re-optimization-functions-in-shortpixel/" target="_blank"><?php _e('More info','shortpixel');?></a>
                    <?php } ?>
                </p>
                <form action='' method='POST' >
                    <input type='checkbox' id='bulk-thumbnails' name='thumbnails' <?php echo($this->ctrl->processThumbnails() ? "checked":"");?> 
                           onchange="ShortPixel.onBulkThumbsCheck(this)"> <?php _e('Include thumbnails','shortpixel');?><br><br>
                    <input type='submit' name='bulkProcess' id='bulkProcess' class='button button-primary' value='<?php _e('Restart Optimizing','shortpixel');?>'>
                </form>
            </div>
        <?php } ?>
        </div>
        <?php
    }

    public function displayBulkProcessingRunning($percent, $message, $remainingQuota, $averageCompression, $type) {
        ?>
        <div class="wrap short-pixel-bulk-page">
            <h1><?php _e('Bulk Image Optimization by ShortPixel','shortpixel');?></h1>
            <?php $this->displayBulkProgressBar(true, $percent, $message, $remainingQuota, $averageCompression, $type);?>
            <div class="sp-floating-block notice bulk-notices-parent">
                <div class="bulk-notice-container">
                    <div class="bulk-notice-msg bulk-lengthy">
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/loading-dark-big.gif' ));?>">
                        <?php _e('Lengthy operation in progress:','shortpixel');?><br>
                        <?php _e('Optimizing image','shortpixel');?> <a href="#" data-href="<?php echo(get_admin_url());?>/post.php?post=__ID__&action=edit" target="_blank">placeholder.png</a>
                    </div>
                    <div class="bulk-notice-msg bulk-error" id="bulk-error-template">
                        <div style="float: right; margin-top: -4px; margin-right: -8px;">
                            <a href="javascript:void(0);" onclick="ShortPixel.removeBulkMsg(this)" style='color: #c32525;'>&#10006;</a>
                        </div>
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/exclamation-big.png' ));?>">
                        <span class="sp-err-title"><?php _e('Error processing file:','shortpixel');?><br></span>
                        <span class="sp-err-content"><?php echo $message; ?></span> <a class="sp-post-link" href="<?php echo(get_admin_url());?>/post.php?post=__ID__&action=edit" target="_blank">placeholder.png</a>
                    </div>
                </div>
            </div>
            <div class="bulk-progress bulk-slider-container notice notice-info sp-floating-block sp-full-width">
                <div  class="short-pixel-block-title"><span><?php _e('Just optimized:','shortpixel');?></span><span class="filename"></span></div>
                <div class="bulk-slider">
                    <div class="bulk-slide" id="empty-slide">
                        <div class="bulk-slide-images">
                            <div class="img-original">
                                <div><img class="bulk-img-orig" src=""></div>
                              <div><?php _e('Original image','shortpixel');?></div>
                            </div>
                            <div class="img-optimized">
                                <div><img class="bulk-img-opt" src=""></div>
                              <div><?php _e('Optimized image','shortpixel');?></div>
                            </div>
                        </div>
                        <div class="img-info">
                            <div style="font-size: 14px; line-height: 10px; margin-bottom:16px;"><?php /*translators: percent follows */ _e('Optimized by:','shortpixel');?></div>
                            <span class="bulk-opt-percent"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function displayBulkProgressBar($running, $percent, $message, $remainingQuota, $averageCompression, $type = 1, $customPending = false) {
        $percentBefore = $percentAfter = '';
        if($percent > 24) {
            $percentBefore = $percent . "%";
        } else {
            $percentAfter = $percent . "%";
        }
        ?>
            <div class="notice notice-info bulk-progress sp-floating-block sp-full-width">
                <div style="float:right">
                    <?php if(false) { ?>
                    <div class="bulk-progress-indicator">
                        <div style="margin-bottom:5px"><?php _e('Remaining credits','shortpixel');?></div>
                        <div style="margin-top:22px;margin-bottom: 5px;font-size:2em;font-weight: bold;"><?php echo(number_format($remainingQuota))?></div>
                        <div>images</div>
                    </div>
                    <?php } ?>
                    <div class="bulk-progress-indicator">
                        <div style="margin-bottom:5px"><?php _e('Average reduction','shortpixel');?></div>
                        <div id="sp-avg-optimization"><input type="text" id="sp-avg-optimization-dial" value="<?php echo("" . round($averageCompression))?>" class="dial"></div>
                        <script>
                            jQuery(function() {
                                ShortPixel.percentDial("#sp-avg-optimization-dial", 60);
                            });
                        </script>
                    </div>
                </div>
                <?php if($running) { ?>
                <h2><?php echo($type & 1 ? __('Media Library','shortpixel') . " " : "");
                          echo($type & 3 == 3 ? __('and','shortpixel') . " " : "");
                          echo($type & 2 ? __('Custom folders','shortpixel') . " " : ""); _e('optimization in progress ...','shortpixel');?></h2>
                <p style="margin: 0 0 18px;"><?php _e('Bulk optimization has started.','shortpixel');?><br>
                    <?php printf(__('This process will take some time, depending on the number of images in your library. In the meantime, you can continue using 
                    the admin as usual, <a href="%s" target="_blank">in a different browser window or tab</a>.<br>
                   However, <strong>if you close this window, the bulk processing will pause</strong> until you open the media gallery or the ShortPixel bulk page again.','shortpixel'), get_admin_url());?>
                </p>
                <?php } else { ?>
                <h2><?php echo(__('Media Library','shortpixel') . ' ' . ($type & 2 ? __("and Custom folders",'shortpixel') . ' ' : "") . __('optimization paused','shortpixel')); ?></h2>
                <p style="margin: 0 0 50px;"><?php _e('Bulk processing is paused until you resume the optimization process.','shortpixel');?></p>
                <?php }?>
                <div id="bulk-progress" class="progress" >
                    <div class="progress-img" style="left: <?php echo($percent);?>%;">
                        <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/slider.png' ));?>">
                        <span><?php echo($percentAfter);?></span>
                    </div>
                    <div class="progress-left" style="width: <?php echo($percent);?>%"><?php echo($percentBefore);?></div>
                </div>
                <div class="bulk-estimate">
                    &nbsp;<?php echo($message);?>
                </div>
                <?php if (true || ($type & 1)) { //now we display the action buttons always when a type of bulk is running ?>
                <form action='' method='POST' style="display:inline;">
                    <input type="submit" class="button button-primary bulk-cancel"  onclick="clearBulkProcessor();"
                           name="bulkProcessStop" value="Stop" style="margin-left:10px"/>
                    <input type="submit" class="button button-primary bulk-cancel"  onclick="clearBulkProcessor();"
                           name="<?php echo($running ? "bulkProcessPause" : "bulkProcessResume");?>" value="<?php echo($running ? __('Pause','shortpixel') : __('Resume processing','shortpixel'));?>"/>
                    <?php if(!$running && $customPending) {?>
                        <input type="submit" class="button button-primary bulk-cancel"  onclick="clearBulkProcessor();"
                               name="skipToCustom" value="<?php _e('Only other media','shortpixel');?>" title="<?php _e('Process only the other media, skipping the Media Library','shortpixel');?>" style="margin-right:10px"/>
                    <?php }?>
                </form>
                <?php } else { ?>
                    <a href="options-general.php?page=wp-shortpixel" class="button button-primary bulk-cancel" style="margin-left:10px"><?php _e('Manage custom folders','shortpixel');?></a>
                <?php }?>
            </div>
        <?php
    }
    
    public function displayBulkStats($totalOptimized, $mainOptimized, $under5PercentCount, $averageCompression, $savedSpace) {?>
            <div class="bulk-progress bulk-stats">
                <div class="label"><?php _e('Processed Images and PDFs:','shortpixel');?></div><div class="stat-value"><?php echo(number_format($mainOptimized));?></div><br>
                <div class="label"><?php _e('Processed Thumbnails:','shortpixel');?></div><div class="stat-value"><?php echo(number_format($totalOptimized - $mainOptimized));?></div><br>
                <div class="label totals"><?php _e('Total files processed:','shortpixel');?></div><div class="stat-value"><?php echo(number_format($totalOptimized));?></div><br>
                <div class="label totals"><?php _e('Minus files with <5% optimization (free):','shortpixel');?></div><div class="stat-value"><?php echo(number_format($under5PercentCount));?></div><br><br>
                <div class="label totals"><?php _e('Used quota:','shortpixel');?></div><div class="stat-value"><?php echo(number_format($totalOptimized - $under5PercentCount));?></div><br>
                <br>
                <div class="label"><?php _e('Average optimization:','shortpixel');?></div><div class="stat-value"><?php echo($averageCompression);?>%</div><br>
                <div class="label"><?php _e('Saved space:','shortpixel');?></div><div class="stat-value"><?php echo($savedSpace);?></div>
            </div>
        <?php
    }
     
    public function displayFailed($failed) {
        ?>
            <div class="bulk-progress bulk-stats">
                <?php foreach($failed as $fail) { 
                    if($fail->type == ShortPixelMetaFacade::CUSTOM_TYPE) {
                        $meta = $fail->meta;
                        ?> <div class="label"><a href="<?php echo(trailingslashit(network_site_url("/")) . $fail->meta->getWebPath());?>"><?php echo(substr($fail->meta->getName(), 0, 80));?> - ID: C-<?php echo($fail->id);?></a></div><br/>
                    <?php } else {
                        $meta = wp_get_attachment_metadata($fail);
                        ?> <div class="label"><a href="/wp-admin/post.php?post=<?php echo($fail->id);?>&action=edit"><?php echo(substr($fail->meta["file"], 0, 80));?> - ID: <?php echo($fail->id);?></a></div><br/>
                    <?php }
                }?>
            </div>
        <?php
    }

    function displaySettings($showApiKey, $editApiKey, $quotaData, $notice, $resources = null, $averageCompression = null, $savedSpace = null, $savedBandwidth = null, 
                         $remainingImages = null, $totalCallsMade = null, $fileCount = null, $backupFolderSize = null, 
                         $customFolders = null, $folderMsg = false, $addedFolder = false, $showAdvanced = false) { 
        //wp_enqueue_script('jquery.idTabs.js', plugins_url('/js/jquery.idTabs.js',__FILE__) );
        ?>        
        <h1><?php _e('ShortPixel Plugin Settings','shortpixel');?></h1>
        <p style="font-size:18px">
            <a href="https://shortpixel.com/<?php echo($this->ctrl->getVerifiedKey() ? "login/".$this->ctrl->getApiKey() : "pricing");?>" target="_blank" style="font-size:18px">
                <?php _e('Upgrade now','shortpixel');?>
            </a> |
            <a href="https://shortpixel.com/contact/<?php echo($this->ctrl->getEncryptedData());?>" target="_blank" style="font-size:18px"><?php _e('Support','shortpixel');?> </a>
        </p>
        <?php if($notice !== null) { ?>
        <br/>
        <div style="background-color: #fff; border-left: 4px solid <?php echo($notice['status'] == 'error' ? '#ff0000' : ($notice['status'] == 'warn' ? '#FFC800' : '#7ad03a'));?>; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1); padding: 1px 12px;;width: 95%">
                  <p><?php echo($notice['msg']);?></p>
        </div>
        <?php } ?>
        <?php if($folderMsg) { ?>
        <br/>
        <div style="background-color: #fff; border-left: 4px solid #ff0000; box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1); padding: 1px 12px;;width: 95%">
                  <p><?php echo($folderMsg);?></p>
        </div>
        <?php } ?>

        <article id="shortpixel-settings-tabs" class="sp-tabs">
            <form name='wp_shortpixel_options' action='options-general.php?page=wp-shortpixel&noheader=true'  method='post' id='wp_shortpixel_options'>
                <section <?php echo($showAdvanced ? "" : "class='sel-tab'");?> id="tab-settings">
                    <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-settings"><?php _e('General','shortpixel');?></a></h2>
                    <?php $this->displaySettingsForm($showApiKey, $editApiKey, $quotaData);?>
                </section> 
                <?php if($this->ctrl->getVerifiedKey()) {?>
                <section <?php echo($showAdvanced ? "class='sel-tab'" : "");?> id="tab-adv-settings">
                    <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-adv-settings"><?php _e('Advanced','shortpixel');?></a></h2>
                    <?php $this->displayAdvancedSettingsForm($customFolders, $addedFolder);?>
                </section>
                <?php } ?>
            </form><span style="display:none">&nbsp;</span><?php //the span is a trick to keep the sections ordered as nth-child in styles: 1,2,3,4 (otherwise the third section would be nth-child(2) too, because of the form)
            if($averageCompression !== null) {?>
            <section id="tab-stats">
                <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-stats"><?php _e('Statistics','shortpixel');?></a></h2>
                <?php
                    $this->displaySettingsStats($quotaData, $averageCompression, $savedSpace, $savedBandwidth, 
                                                $remainingImages, $totalCallsMade, $fileCount, $backupFolderSize);?>
            </section> 
            <?php }
            if($resources !== null) {?>
            <section id="tab-resources">
		        <h2><a class='tab-link' href='javascript:void(0);' data-id="tab-resources"><?php _e('WP Resources','shortpixel');?></a></h2>
                <?php echo((isset($resources['body']) ? $resources['body'] : __("Please reload",'shortpixel')));?>
            </section>
            <?php } ?>
        </article>
        <script>
            jQuery(document).ready(function () {
                ShortPixel.adjustSettingsTabs();
                jQuery( window ).resize(function() {
                    ShortPixel.adjustSettingsTabs();
                });
                if(window.location.hash) {
                    var target = 'tab-' + window.location.hash.substring(window.location.hash.indexOf("#")+1)
                    ShortPixel.switchSettingsTab(target);
                }
                jQuery("article.sp-tabs a.tab-link").click(function(){ShortPixel.switchSettingsTab(jQuery(this).data("id"))});
            });
        </script>
        <?php
    }    
    
    public function displaySettingsForm($showApiKey, $editApiKey, $quotaData) {
        $settings = $this->ctrl->getSettings();
        $checked = ($this->ctrl->processThumbnails() ? 'checked' : '');
        $checkedBackupImages = ($this->ctrl->backupImages() ? 'checked' : '');
        $cmyk2rgb = ($this->ctrl->getCMYKtoRGBconversion() ? 'checked' : '');
        $removeExif = ($settings->keepExif ? '' : 'checked');
        $resize = ($this->ctrl->getResizeImages() ? 'checked' : '');
        $resizeDisabled = ($this->ctrl->getResizeImages() ? '' : 'disabled');        
        $minSizes = $this->ctrl->getMaxIntermediateImageSize();
        $thumbnailsToProcess = isset($quotaData['totalFiles']) ? ($quotaData['totalFiles'] - $quotaData['mainFiles']) - ($quotaData['totalProcessedFiles'] - $quotaData['mainProcessedFiles']) : 0;
        ?>
        <div class="wp-shortpixel-options">
        <?php if($this->ctrl->getVerifiedKey()) { ?>
            <p><?php printf(__('New images uploaded to the Media Library will be optimized automatically.<br/>If you have existing images you would like to optimize, you can use the <a href="%supload.php?page=wp-short-pixel-bulk">Bulk Optimization Tool</a>.','shortpixel'),get_admin_url());?></p>
        <?php } else { 
            if($showApiKey) {?>
            <h3><?php _e('Step 1:','shortpixel');?></h3>
            <p style='font-size: 14px'><?php printf(__('If you don\'t have an API Key, <a href="https://shortpixel.com/wp-apikey%s" target="_blank">sign up here.</a> It\'s free and it only takes one minute, we promise!','shortpixel'),$this->ctrl->getAffiliateSufix());?></p>
            <h3><?php _e('Step 2:','shortpixel');?></h3>
            <p style='font-size: 14px'><?php _e('Please enter here the API Key you received by email and press Validate.','shortpixel');?></p>
            <?php } 
        }?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="key"><?php _e('API Key:','shortpixel');?></label></th>
                    <td>
                        <?php 
                        $canValidate = false;
                        if($showApiKey) {
                            $canValidate = true;?>
                        <input name="key" type="text" id="key" value="<?php echo( $this->ctrl->getApiKey() );?>" 
                               class="regular-text" <?php echo($editApiKey ? "" : 'disabled') ?>>
                        <?php } elseif(defined("SHORTPIXEL_API_KEY")) { 
                            $canValidate = true;?>
                        <input name="key" type="text" id="key" disabled="true" placeholder="<?php _e('Multisite API Key','shortpixel');?>" class="regular-text">
                        <?php } ?>
                        <input type="hidden" name="validate" id="valid" value=""/>
                        <button type="button" id="validate" class="button button-primary" title="<?php _e('Validate the provided API key','shortpixel');?>"
                                onclick="ShortPixel.validateKey()" <?php echo $canValidate ? "" : "disabled"?>><?php _e('Validate','shortpixel');?></button>
                        <?php if($showApiKey && !$editApiKey) { ?>
                        <p class="settings-info"><?php _e('Key defined in wp-config.php.','shortpixel');?></p>
                        <?php } ?>
                        
                    </td>
                </tr>
        <?php if (!$this->ctrl->getVerifiedKey()) { //if invalid key we display the link to the API Key ?>
            </tbody>
        </table>
        <?php } else { //if valid key we display the rest of the options ?>
                <tr>
                    <th scope="row">
                        <label for="compressionType"><?php _e('Compression type:','shortpixel');?></label>
                    </th>
                    <td>
                        <input type="radio" name="compressionType" value="1" <?php echo( $this->ctrl->getCompressionType() == 1 ? "checked" : "" );?>><?php 
                            _e('Lossy (recommended)','shortpixel');?></br>
                        <p class="settings-info"><?php _e('<b>Lossy compression: </b>lossy has a better compression rate than lossless compression.</br>The resulting image is identical with the original to the human eye. You can run a test for free ','shortpixel');?>
                            <a href="https://shortpixel.com/online-image-compression" target="_blank"><?php _e('here','shortpixel');?></a>.</p></br>
                        <input type="radio" name="compressionType" value="0" <?php echo( $this->ctrl->getCompressionType() != 1 ? "checked" : "" );?>><?php 
                            _e('Lossless','shortpixel');?>
                        <p class="settings-info">
                            <?php _e('<b>Lossless compression: </b> the shrunk image will be identical with the original and smaller in size.</br>In some rare cases you will need to use 
                        this type of compression. Some technical drawings or images from vector graphics are possible situations.','shortpixel');?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="thumbnails"><?php _e('Also include thumbnails:','shortpixel');?></label></th>
                    <td><input name="thumbnails" type="checkbox" id="thumbnails" <?php echo( $checked );?>> <?php 
                            _e('Apply compression also to <strong>image thumbnails.</strong> ','shortpixel');?>
                            <?php echo($thumbnailsToProcess ? "(" . number_format($thumbnailsToProcess) . " " . __('thumbnails to optimize','shortpixel') . ")" : "");?>
                        <p class="settings-info">
                            <?php _e('It is highly recommended that you optimize the thumbnails as they are usually the images most viewed by end users and can generate most traffic.<br>Please note that thumbnails count up to your total quota.','shortpixel');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="backupImages"><?php _e('Image backup','shortpixel');?></label></th>
                    <td>
                        <input name="backupImages" type="checkbox" id="backupImages" <?php echo( $checkedBackupImages );?>> <?php _e('Save and keep a backup of your original images in a separate folder.','shortpixel');?>
                        <p class="settings-info"><?php _e('You <strong>need to have backup active</strong> in order to be able to restore images to originals or to convert from Lossy to Lossless and back.','shortpixel');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cmyk2rgb"><?php _e('CMYK to RGB conversion','shortpixel');?></label></th>
                    <td>
                        <input name="cmyk2rgb" type="checkbox" id="cmyk2rgb" <?php echo( $cmyk2rgb );?>><?php _e('Adjust your images for computer and mobile screen display.','shortpixel');?>
                        <p class="settings-info"><?php _e('Images for the web only need RGB format and converting them from CMYK to RGB makes them smaller.','shortpixel');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="removeExif"><?php _e('Remove EXIF','shortpixel');?></label></th>
                    <td>
                        <input name="removeExif" type="checkbox" id="removeExif" <?php echo( $removeExif );?>><?php _e('Remove the EXIF tag of the image (recommended).','shortpixel');?>
                        <p class="settings-info"> <?php _e('EXIF is a set of various pieces of information that are automatically embedded into the image upon creation. This can include GPS position, camera manufacturer, date and time, etc.  
                            Unless you really need that data to be preserved, we recommend removing it as it can lead to <a href="http://blog.shortpixel.com/how-much-smaller-can-be-images-without-exif-icc" target="_blank">better compression rates</a>.','shortpixel');?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="resize"><?php _e('Resize large images','shortpixel');?></label></th>
                    <td>
                        <input name="resize" type="checkbox" id="resize" <?php echo( $resize );?>> <?php 
                               _e('to maximum','shortpixel');?> <input type="text" name="width" id="width" style="width:70px" 
                               value="<?php echo( max($this->ctrl->getResizeWidth(), min(1024, $minSizes['width'])) );?>" <?php echo( $resizeDisabled );?>/> <?php 
                               _e('pixels wide &times;','shortpixel');?>
                        <input type="text" name="height" id="height" style="width:70px" value="<?php echo( max($this->ctrl->getResizeHeight(), min(1024, $minSizes['height'])) );?>" <?php echo( $resizeDisabled );?>/> <?php 
                               _e('pixels high (original aspect ratio is preserved and image is not cropped)','shortpixel');?>
                        <p class="settings-info"> 
                            <?php _e('Recommended for large photos, like the ones taken with your phone. Saved space can go up to 80% or more after resizing.','shortpixel');?><br/>
                        </p>
                        <div style="margin-top: 10px;">
                            <input type="radio" name="resize_type" id="resize_type_outer" value="outer" <?php echo($settings->resizeType == 'inner' ? '' : 'checked') ?> style="margin: -50px 10px 60px 0;">
                            <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/resize-outer.png' ));?>" title="<?php _e('Sizes will be greater or equal to the corresponding value. For example, if you set the resize dimensions at 1000x1200, an image of 2000x3000px will be resized to 1000x1500px while an image of 3000x2000px will be resized to 1800x1200px','shortpixel');?>">
                            <input type="radio" name="resize_type" id="resize_type_inner" value="inner" <?php echo($settings->resizeType == 'inner' ? 'checked' : '') ?> style="margin: -50px 10px 60px 35px;">
                            <img src="<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/resize-inner.png' ));?>" title="<?php _e('Sizes will be smaller or equal to the corresponding value. For example, if you set the resize dimensions at 1000x1200, an image of 2000x3000px will be resized to 800x1200px while an image of 3000x2000px will be resized to 1000x667px','shortpixel');?>">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="save" id="save" class="button button-primary" title="<?php _e('Save Changes','shortpixel');?>" value="<?php _e('Save Changes','shortpixel');?>"> &nbsp;
            <input type="submit" name="save" id="bulk" class="button button-primary" title="<?php _e('Save and go to the Bulk Processing page','shortpixel');?>" value="<?php _e('Save and Go to Bulk Process','shortpixel');?>"> &nbsp;
        </p>
        </div>
        <script>
            jQuery(document).ready(function () {
                ShortPixel.setupGeneralTab(document.wp_shortpixel_options.compressionType, 
                                       Math.min(1024, <?php echo($minSizes['width']);?>),
                                       Math.min(1024, <?php echo($minSizes['height']);?>));
            });
        </script>
        <?php }
    }
    
    public function displayAdvancedSettingsForm($customFolders = false, $addedFolder = false) {
        $settings = $this->ctrl->getSettings();
        $minSizes = $this->ctrl->getMaxIntermediateImageSize();
        $hasNextGen = $this->ctrl->hasNextGen();
        $frontBootstrap = ($settings->frontBootstrap ? 'checked' : '');
        $includeNextGen = ($settings->includeNextGen ? 'checked' : '');
        $createWebp = ($settings->createWebp ? 'checked' : '');
        $autoMediaLibrary = ($settings->autoMediaLibrary ? 'checked' : '');
        $optimizeRetina = ($settings->optimizeRetina ? 'checked' : '');
        ?>
        <div class="wp-shortpixel-options">
        <?php if(!$this->ctrl->getVerifiedKey()) { ?>
            <p><?php _e('Please enter your API key in the General tab first.','shortpixel');?></p>
        <?php } else { //if valid key we display the rest of the options ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="resize"><?php _e('Additional media folders','shortpixel');?></label></th>
                    <td>
                        <?php if($customFolders) { ?>
                            <table class="shortpixel-folders-list">
                                <tr style="font-weight: bold;">
                                    <td><?php _e('Folder name','shortpixel');?></td>
                                    <td><?php _e('Type &amp;<br>Status','shortpixel');?></td>
                                    <td><?php _e('Files','shortpixel');?></td>
                                    <td><?php _e('Last change','shortpixel');?></td>
                                    <td></td>
                                </tr>
                            <?php foreach($customFolders as $folder) {
                                $typ = $folder->getType(); 
                                $typ = $typ ? $typ . "<br>" : "";
                                $stat = $this->ctrl->getSpMetaDao()->getFolderOptimizationStatus($folder->getId());
                                $cnt = $folder->getFileCount();
                                $st = ($cnt == 0 
                                    ? __("Empty",'shortpixel')
                                    : ($stat->Total == $stat->Optimized 
                                        ? __("Optimized",'shortpixel')
                                        : ($stat->Optimized + $stat->Pending > 0 ? __("Pending",'shortpixel') : __("Waiting",'shortpixel'))));
                                
                                $err = $stat->Failed > 0 && !$st == __("Empty",'shortpixel') ? " ({$stat->Failed} failed)" : "";
                                
                                $action = ($st == __("Optimized",'shortpixel') || $st == __("Empty",'shortpixel') ? __("Stop monitoring",'shortpixel') : __("Stop optimizing",'shortpixel'));
                                
                                $fullStat = $st == __("Empty",'shortpixel') ? "" : __("Optimized",'shortpixel') . ": " . $stat->Optimized . ", " 
                                        . __("Pending",'shortpixel') . ": " . $stat->Pending . ", " . __("Waiting",'shortpixel') . ": " . $stat->Waiting . ", " 
                                        . __("Failed",'shortpixel') . ": " . $stat->Failed;
                                ?>
                                <tr>
                                    <td>
                                        <?php echo($folder->getPath()); ?>
                                    </td>
                                    <td>
                                        <?php if(!($st == "Empty")) { ?>
                                        <a href="javascript:none();"  title="<?php echo $fullStat; ?>" style="text-decoration: none;">
                                            <img src='<?php echo(plugins_url( 'shortpixel-image-optimiser/res/img/info-icon.png' ));?>' style="margin-bottom: -2px;"/>
                                        </a>&nbsp;<?php  } echo($typ.$st.$err); ?>

                                    </td>
                                    <td>
                                        <?php echo($cnt); ?> files
                                    </td>
                                    <td>
                                        <?php echo($folder->getTsUpdated()); ?>
                                    </td>
                                    <td>
                                        <input type="button" class="button remove-folder-button" data-value="<?php echo($folder->getPath()); ?>" title="<?php echo($action . " " . $folder->getPath()); ?>" value="<?php echo $action;?>">
                                        <input type="button" style="display:none;" class="button button-alert recheck-folder-button" data-value="<?php echo($folder->getPath()); ?>" 
                                               title="<?php _e('Full folder refresh, check each file of the folder if it changed since it was optimized. Might take up to 1 min. for big folders.','shortpixel');?>" 
                                               value="<?php _e('Refresh','shortpixel');?>">
                                    </td>
                                </tr>
                            <?php }?>
                            </table>
                        <?php } ?>
                        <input type="hidden" name="removeFolder" id="removeFolder"/>
                        <input type="hidden" name="recheckFolder" id="removeFolder"/>
                        <input type="text" name="addCustomFolderView" id="addCustomFolderView" class="regular-text" value="<?php echo($addedFolder);?>" disabled style="width: 50em;max-width: 70%;">&nbsp;
                        <input type="hidden" name="addCustomFolder" id="addCustomFolder" value="<?php echo($addedFolder);?>"/>
                        <input type="hidden" id="customFolderBase" value="<?php echo $this->ctrl->getCustomFolderBase(); ?>">
                        <a class="button button-primary select-folder-button" title="<?php _e('Select the images folder on your server.','shortpixel');?>" href="javascript:void(0);">
                            <?php _e('Select ...','shortpixel');?> 
                        </a>
                        <input type="submit" name="saveAdv" id="saveAdvAddFolder" class="button button-primary" title="<?php _e('Add Folder','shortpixel');?>" value="<?php _e('Add Folder','shortpixel');?>">
                        <p class="settings-info">
                            <?php _e('Use the Select... button to select site folders. ShortPixel will optimize images and PDFs from the specified folders and their subfolders. The optimization status for each image or PDF in these folders can be seen in the <a href="upload.php?page=wp-short-pixel-custom">Other Media list</a>, under the Media menu.','shortpixel');?>
                        </p>
                        <div class="sp-folder-picker-shade">
                            <div class="sp-folder-picker-popup">
                                <div class="sp-folder-picker-title"><?php _e('Select the images folder','shortpixel');?></div>
                                <div class="sp-folder-picker"></div>
                                <input type="button" class="button button-info select-folder-cancel" value="<?php _e('Cancel','shortpixel');?>" style="margin-right: 30px;">
                                <input type="button" class="button button-primary select-folder" value="<?php _e('Select','shortpixel');?>">
                            </div>
                        </div>
                        <script>
                            jQuery(document).ready(function () {
                                ShortPixel.initFolderSelector();
                            });
                        </script>
                    </td>
                </tr>
                <?php if($hasNextGen) { ?>
                <tr>
                    <th scope="row"><label for="nextGen"><?php _e('Optimize NextGen galleries','shortpixel');?></label></th>
                    <td>
                        <input name="nextGen" type="checkbox" id="nextGen" <?php echo( $includeNextGen );?>> <?php _e('Optimize NextGen galleries.','shortpixel');?>
                        <p class="settings-info">
                            <?php _e('Check this to add all your current NextGen galleries to the custom folders list and to also have all the future NextGen galleries and images optimized automatically by ShortPixel.','shortpixel');?>
                        </p>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="createWebp"><?php _e('WebP versions','shortpixel');?></label></th>
                    <td>
                        <input name="createWebp" type="checkbox" id="createWebp" <?php echo( $createWebp );?>> <?php _e('Create also <a href="http://blog.shortpixel.com/how-webp-images-can-speed-up-your-site/" target="_blank">WebP versions</a> of the images <strong>for free</strong>.','shortpixel');?>
                        <p class="settings-info">
                            <?php _e('WebP images can be up to three times smaller than PNGs and 25% smaller than JPGs. Choosing this option <strong>does not use up additional credits</strong>.','shortpixel');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="optimizeRetina"><?php _e('Optimize Retina images','shortpixel');?></label></th>
                    <td>
                        <input name="optimizeRetina" type="checkbox" id="optimizeRetina" <?php echo( $optimizeRetina );?>> <?php _e('Optimize also the Retina images (@2x) if they exist.','shortpixel');?>
                        <p class="settings-info">
                            <?php _e('If you have a Retina plugin that generates Retina-specific images (@2x), ShortPixel can optimize them too, alongside the regular Media Library images and thumbnails. <a href="http://blog.shortpixel.com/how-to-use-optimized-retina-images-on-your-wordpress-site-for-best-user-experience-on-apple-devices/" target="_blank">More info.</a>','shortpixel');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="authentication"><?php _e('HTTP AUTH credentials','shortpixel');?></label></th>
                    <td>
                        <input name="siteAuthUser" type="text" id="siteAuthUser" value="<?php echo( $settings->siteAuthUser );?>" class="regular-text" placeholder="<?php _e('User','shortpixel');?>"><br>
                        <input name="siteAuthPass" type="text" id="siteAuthPass" value="<?php echo( $settings->siteAuthPass );?>" class="regular-text" placeholder="<?php _e('Password','shortpixel');?>">
                        <p class="settings-info"> 
                            <?php _e('Only fill in these fields if your site (front-end) is not publicly accessible and visitors need a user/pass to connect to it. If you don\'t know what is this then just <strong>leave the fields empty</strong>.','shortpixel');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="resize"><?php _e('Process in front-end','shortpixel');?></label></th>
                    <td>
                        <input name="frontBootstrap" type="checkbox" id="resize" <?php echo( $frontBootstrap );?>> <?php _e('Automatically optimize images added by users in front end.','shortpixel');?>
                        <p class="settings-info">
                            <?php _e('Check this if you have users that add images or PDF documents from custom forms in the front-end. This could increase the load on your server if you have a lot of users simultaneously connected.','shortpixel');?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="autoMediaLibrary"><?php _e('Optimize media on upload','shortpixel');?></label></th>
                    <td>
                        <input name="autoMediaLibrary" type="checkbox" id="autoMediaLibrary" <?php echo( $autoMediaLibrary );?>> <?php _e('Automatically optimize Media Library items after they are uploaded (recommended).','shortpixel');?>
                        <p class="settings-info">
                            <?php _e('By default, ShortPixel will automatically optimize all the freshly uploaded image and PDF files. If you uncheck this you\'ll need to either run Bulk ShortPixel or go to Media Library (in list view) and click on the right side "Optimize now" button(s).','shortpixel');?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="saveAdv" id="saveAdv" class="button button-primary" title="<?php _e('Save Changes','shortpixel');?>" value="<?php _e('Save Changes','shortpixel');?>"> &nbsp;
            <input type="submit" name="saveAdv" id="bulkAdvGo" class="button button-primary" title="<?php _e('Save and go to the Bulk Processing page','shortpixel');?>" value="<?php _e('Save and Go to Bulk Process','shortpixel');?>"> &nbsp;
        </p>
        </div>
        <script>
            jQuery(document).ready(function () { ShortPixel.setupAdvancedTab();});
        </script>
        <?php }
    }
    
    function displaySettingsStats($quotaData, $averageCompression, $savedSpace, $savedBandwidth, 
                         $remainingImages, $totalCallsMade, $fileCount, $backupFolderSize) { ?>
        <a id="facts"></a>
        <h3><?php _e('Your ShortPixel Stats','shortpixel');?></h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="averagCompression"><?php _e('Average compression of your files:','shortpixel');?></label></th>
                    <td><?php echo($averageCompression);?>%</td>
                </tr>
                <tr>
                    <th scope="row"><label for="savedSpace"><?php _e('Saved disk space by ShortPixel','shortpixel');?></label></th>
                    <td><?php echo($savedSpace);?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="savedBandwidth"><?php _e('Bandwith* saved with ShortPixel:','shortpixel');?></label></th>
                    <td><?php echo($savedBandwidth);?></td>
                </tr>
            </tbody>
        </table>

        <p style="padding-top: 0px; color: #818181;" ><?php _e('* Saved bandwidth is calculated at 10,000 impressions/image','shortpixel');?></p>

        <h3><?php _e('Your ShortPixel Plan','shortpixel');?></h3>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row" bgcolor="#ffffff"><label for="apiQuota"><?php _e('Your ShortPixel plan','shortpixel');?></label></th>
                    <td bgcolor="#ffffff">
                        <?php printf(__('%s/month, renews in %s  days, on %s ( <a href="https://shortpixel.com/login/%s" target="_blank">Need More? See the options available</a> )','shortpixel'),
                                $quotaData['APICallsQuota'], floor(30 + (strtotime($quotaData['APILastRenewalDate']) - time()) / 86400),
                                date('M d, Y', strtotime($quotaData['APILastRenewalDate']. ' + 30 days')), $this->ctrl->getApiKey());?><br/>
                        <?php printf(__('<a href="https://shortpixel.com/login/%s/tell-a-friend" target="_blank">Join our friend referral system</a> to win more credits. For each user that joins, you receive +100 images credits/month.','shortpixel'),
                                $this->ctrl->getApiKey());?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="usedQUota"><?php _e('One time credits:','shortpixel');?></label></th>
                    <td><?php echo(  number_format($quotaData['APICallsQuotaOneTimeNumeric']));?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="usedQUota"><?php _e('Number of images processed this month:','shortpixel');?></label></th>
                    <td><?php echo($totalCallsMade);?> (<a href="https://api.shortpixel.com/v2/report.php?key=<?php echo($this->ctrl->getApiKey());?>" target="_blank">
                            <?php _e('see report','shortpixel');?>
                        </a>)
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="remainingImages"><?php _e('Remaining** images in your plan:','shortpixel');?></label></th>
                    <td><?php echo($remainingImages);?> <?php _e('images','shortpixel');?></td>
                </tr>
            </tbody>
        </table>

        <p style="padding-top: 0px; color: #818181;" >
            <?php printf(__('** Increase your image quota by <a href="https://shortpixel.com/login/%s" target="_blank">upgrading your ShortPixel plan.</a>','shortpixel'),
                    $this->ctrl->getApiKey());?>
        </p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="totalFiles"><?php _e('Total number of processed files:','shortpixel');?></label></th>
                    <td><?php echo($fileCount);?></td>
                </tr>
                <?php if($this->ctrl->backupImages()) { ?>
                <tr>
                    <th scope="row"><label for="sizeBackup"><?php _e('Original images are stored in a backup folder. Your backup folder size is now:','shortpixel');?></label></th>
                    <td>
                        <form action="" method="POST">
                            <?php echo($backupFolderSize);?>
                            <input type="submit"  style="margin-left: 15px; vertical-align: middle;" class="button button-secondary" name="emptyBackup" value="<?php _e('Empty backups','shortpixel');?>"/>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table> 
        <div style="display:none">

        </div>    
        <?php        
    }

    public function renderCustomColumn($id, $data){ ?> 
        <div id='sp-msg-<?php echo($id);?>' class='column-wp-shortPixel'>

            <?php switch($data['status']) {
                case 'n/a': ?> 
                    <?php _e('Optimization N/A','shortpixel');?> <?php
                    break;
                case 'notFound': ?> 
                    <?php _e('Image does not exist.','shortpixel');?> <?php
                    break;
                case 'invalidKey': 
                    if(defined("SHORTPIXEL_API_KEY")) { // multisite key - need to be validated on each site but it's not invalid
                        ?> <?php _e('Please <a href="options-general.php?page=wp-shortpixel">go to Settings</a> to validate the API Key.','shortpixel');?> <?php
                    } else {
                        ?> <?php _e('Invalid API Key. <a href="options-general.php?page=wp-shortpixel">Check your Settings</a>','shortpixel');?> <?php
                    } 
                    break;
                case 'quotaExceeded': 
                    echo($this->getQuotaExceededHTML(isset($data['message']) ? $data['message'] : ''));
                    break;
                case 'optimizeNow': 
                    if($data['showActions']) { ?>  
                        <a class='button button-smaller button-primary' href="javascript:manualOptimization('<?php echo($id)?>')">
                            <?php _e('Optimize now','shortpixel');?>
                        </a> 
                    <?php }
                    echo($data['message']);
                    if(isset($data['thumbsTotal']) && $data['thumbsTotal'] > 0) {
                        echo("<br>+" . $data['thumbsTotal'] . " thumbnails");
                    }
                    break;
                case 'retry': ?> 
                    <?php echo($data['message'])?>  <a class='button button-smaller button-primary' href="javascript:manualOptimization('<?php echo($id)?>')">
                        <?php _e('Retry','shortpixel');?>
                    </a> <?php
                    break;
                case 'pdfOptimized': 
                case 'imgOptimized': 
                    $this->renderListCell($id, $data['showActions'], 
                            !$data['thumbsOpt'] && $data['thumbsTotal'], $data['thumbsTotal'], $data['backup'], $data['type'],
                            $this->getSuccessText($data['percent'],$data['bonus'],$data['type'],$data['thumbsOpt'],$data['thumbsTotal'], $data['retinasOpt']));
                    break;
                }
                //die(var_dump($data));
                ?>
        </div>
        <?php 
    }
    
    public function getSuccessText($percent, $bonus, $type, $thumbsOpt = 0, $thumbsTotal = 0, $retinasOpt = 0) {
        return   ($percent ? __('Reduced by','shortpixel') . ' <strong>' . $percent . '%</strong> ' : '')
                .(!$bonus ? ' ('.$type.')':'')
                .($bonus && $percent ? '<br>' : '') 
                .($bonus ? __('Bonus processing','shortpixel') : '') 
                .($bonus ? ' ('.$type.')':'') . '<br>'
                .($thumbsOpt ? ( $thumbsTotal > $thumbsOpt 
                        ? sprintf(__('+%s of %s thumbnails optimized','shortpixel'),$thumbsOpt,$thumbsTotal) 
                        : sprintf(__('+%s thumbnails optimized','shortpixel'),$thumbsOpt)) : '')
                .($retinasOpt ? '<br>' . sprintf(__('+%s Retina images optimized','shortpixel') , $retinasOpt) : '' ) ;
    }
    
    public function renderListCell($id, $showActions, $optimizeThumbs, $thumbsTotal, $backup, $type, $message) {
        if($showActions) { ?>
            <div class='sp-column-actions'>
            <?php if($optimizeThumbs) { ?>
            <a class='button button-smaller button-primary' href="javascript:optimizeThumbs(<?php echo($id)?>);">
                <?php printf(__('Optimize %s  thumbnails','shortpixel'),$thumbsTotal);?>
            </a>
            <?php }
            if($backup) {
                if($type) { 
                    $invType = $type == 'lossy' ? 'lossless' : 'lossy'; ?>
                    <a class='button button-smaller' href="javascript:reoptimize('<?php echo($id)?>', '<?php echo($invType)?>');" 
                       title="<?php _e('Reoptimize from the backed-up image','shortpixel');?>">
                        <?php _e('Re-optimize','shortpixel');?> <?php echo($invType)?>
                    </a><?php
                } ?>
                <a class='button button-smaller' href="admin.php?action=shortpixel_restore_backup&attachment_ID=<?php echo($id)?>">
                    <?php _e('Restore backup','shortpixel');?>
                </a>
            <?php } ?>
        </div> 
        <?php } ?> 
        <div class='sp-column-info'>
            <?php echo($message);?>
        </div> <?php
    }
    
    public function getQuotaExceededHTML($message = '') {
        return "<div class='sp-column-actions' style='width:110px;'> 
        <a class='button button-smaller button-primary' href='https://shortpixel.com/login/". $this->ctrl->getApiKey() . "' target='_blank'>"
            . __('Extend Quota','shortpixel') . 
        "</a>
        <a class='button button-smaller' href='admin.php?action=shortpixel_check_quota'>" 
            . __('Check&nbsp;&nbsp;Quota','shortpixel') .
        "</a></div>
        <div class='sp-column-info'>" . $message . " Quota Exceeded.</div>";
    }
}
