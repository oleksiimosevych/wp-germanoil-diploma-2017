<div class="wf-banner updated below-h2">
    <p class="main">
        <ul>
        <li style='color:red;'><strong>Your Business is precious! Go Premium!</strong></li>
        <strong><?php _e('HikeForce Import Export Plugin Premium version helps you to seamlessly import/export products into your Woocommerce Store.', 'wf_csv_import_export'); ?></strong><br/><br/>
        <?php _e('- Export Products (Simple, Group, External and Variations) in to a CSV file <strong>( Basic version supports only Simple Products )</strong>.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Import Products (Simple, Group, External and Variations) in CSV format in to WooComemrce Store.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Export Products by Category.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Various Filter options for exporting Products.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Map and Transform fields while Importing Products.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Change values while improting products using Evaluation Fields.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Choice to Update or Skip existing imported products.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- WPML Supported. French and German (Deutschland) language support Out of the Box.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Import/Export file from/to a remote server via FTP.', 'wf_csv_import_export'); ?><br/>
        <?php _e('- Excellent Support for setting it up!', 'wf_csv_import_export'); ?><br/>
    </ul>
    </p>
    <p>
        <a href="http://www.xadapter.com/product/product-import-export-plugin-for-woocommerce/" target="_blank" class="button button-primary"><?php _e( 'Upgrade to Premium Version', 'wf_csv_import_export'); ?></a>
        <a href="http://productimportexport.hikeforce.com/wp-admin/admin.php?page=wf_woocommerce_csv_im_ex" target="_blank" class="button"><?php _e( 'Live Demo', 'wf_csv_import_export'); ?></a>
        <a href="http://www.xadapter.com/2016/06/20/setting-up-product-import-export-plugin-for-woocommerce/" target="_blank" class="button"><?php _e( 'Documentation', 'wf_csv_import_export' ); ?></a>
        <a href="<?php echo plugins_url( 'Product_Commercial_Sample_CSV.csv', WF_ProdImpExpCsv_FILE ); ?>" target="_blank" class="button"><?php _e('Sample Commercial CSV', 'wf_csv_import_export'); ?></a>
        <a href="<?php echo plugins_url( 'Product_WooCommerce_Sample_CSV.csv', WF_ProdImpExpCsv_FILE ); ?>" target="_blank" class="button"><?php _e('Sample WooCommerce CSV', 'wf_csv_import_export'); ?></a>
    </p>
</div>
<style>
    .wf-banner img {
        float: right;
        margin-left: 1em;
        padding: 15px 0
    }
</style>

<div class="tool-box">
    <h3 class="title"><?php _e('Import Products in CSV Format:', 'wf_csv_import_export'); ?></h3>
    <p><?php _e('Import products in CSV format ( works for simple products)  from different sources', 'wf_csv_import_export'); ?></p>
    <p class="submit">
        <?php
        $merge_url = admin_url('admin.php?import=woocommerce_csv&merge=1');
        $import_url = admin_url('admin.php?import=woocommerce_csv');
        ?>
        <a class="button button-primary" id="mylink" href="<?php echo admin_url('admin.php?import=woocommerce_csv'); ?>"><?php _e('Import Products', 'wf_csv_import_export'); ?></a>
        &nbsp;
        <input type="checkbox" id="merge" value="0"><?php _e('Merge products if exists', 'wf_csv_import_export'); ?> <br>
    </p>
</div>
<script type="text/javascript">
    jQuery('#merge').click(function () {
        if (this.checked) {
            jQuery("#mylink").attr("href", '<?php echo $merge_url ?>');
        } else {
            jQuery("#mylink").attr("href", '<?php echo $import_url ?>');
        }
    });
</script>