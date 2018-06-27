<?php
if(!class_exists('SIMPLE_CSV_EXPORTER_SETTINGS')) {
    class SIMPLE_CSV_EXPORTER_SETTINGS  {
        /**
         * Construct the plugin object
         */
        public function __construct()        {
            // register actions
            add_action('admin_init', array(&$this, 'admin_init'));
            add_action('admin_menu', array(&$this, 'add_menu'));
        } // END public function __construct

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()    {
            register_setting('wp_ccsve-group', 'ccsve_post_type');
            register_setting('wp_ccsve-group', 'ccsve_std_fields');
            register_setting('wp_ccsve-group', 'ccsve_tax_terms');
            register_setting('wp_ccsve-group', 'ccsve_custom_fields');
            register_setting('wp_ccsve-group', 'ccsve_woocommerce_fields');

            add_settings_section(
                'simple_csv_exporter_settings-section',
                'CSV/XLS Export Settings',
                array(&$this, 'settings_section_simple_csv_exporter_settings'),
                'simple_csv_exporter_settings'
            );

            add_settings_field(
                'ccsve_post_type',
                'Custom Post Type to Export',
                array(&$this, 'settings_field_select_post_type'),
                'simple_csv_exporter_settings',
                'simple_csv_exporter_settings-section'
            );

            add_settings_field(
                'ccsve_std_fields',
                'Standard WP fields to Export',
                array(&$this, 'settings_field_select_std_fields'),
                'simple_csv_exporter_settings',
                'simple_csv_exporter_settings-section'
            );

            add_settings_field(
                'ccsve_custom_fields',
                'Custom Fields to Export',
                array(&$this, 'settings_field_select_custom_fields'),
                'simple_csv_exporter_settings',
                'simple_csv_exporter_settings-section'
            );

            add_settings_field(
                'ccsve_tax_terms',
                'Taxonomy Terms to Export',
                array(&$this, 'settings_field_select_tax_terms'),
                'simple_csv_exporter_settings',
                'simple_csv_exporter_settings-section'
            );

            // WOO COMMERCE
            /*add_settings_field(
                'ccsve_woocommerce_fields',
                'WooCommerce Fields to Export',
                array(&$this, 'settings_field_select_woocommerce_fields'),
                'simple_csv_exporter_settings',
                'simple_csv_exporter_settings-section'
            );*/

        } // END public static function activate

        public function settings_section_simple_csv_exporter_settings()  {
            echo '<p>From this page you can add the default post type with its connected taxonomies and custom fields, that you wish to export.<br>After that, anytime you will use the urls <strong>'.get_bloginfo('url').'/?export=csv</strong> for a CSV file, or <strong>'.get_bloginfo('url').'/?export=xls</strong>, you will get that post type data.</p>';
            echo '<p>You must choose the post type and save the settings <strong>before</strong> you can see the taxonomies or custom fields for a custom post type. Once the page reloads, you will see the connected taxonomies and custom fields for the post type.</p>';
            echo '<p>At the bottom of this page you can export right away what you just selected, after saving first.</p>';
            echo '<hr>';
            echo '<p>If you want to export from a different post type than the one saved in these settings, also from frontend, use the url <strong>'.get_bloginfo('url').'/?export=csv&post_type=your_post_type_slug</strong> for a CSV file, or <strong>'.get_bloginfo('url').'/?export=xls&post_type=your_post_type_slug</strong> to get a XLS.</p>';
            echo '<hr>';
            echo '<p><i>When opening the exported xls, Excel will prompt the user with a warning, but the file is perfectly fine and can then be opened. <strong>Unfortunately this can\'t be avoided</strong>, <a href="http://blogs.msdn.com/b/vsofficedeveloper/archive/2008/03/11/excel-2007-extension-warning.aspx" target="_blank">read more here</a>.</i></p>';
        }

        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_select_post_type() {
            $args = array(
                'public'   => true,
            );
            // Get the field name from the $args array
            $items = get_post_types($args);
            // Get the value of this setting
            $options = get_option('ccsve_post_type');
            // echo a proper input type="text"
            foreach ($items as $item) {
                $checked = ($options == $item) ? ' checked="checked" ' : '';
                // radio buttons, 1 post type per time
                echo '<input type="radio" id="post_type"'.$item.' name="ccsve_post_type" value="'.$item.'" '.$checked.'" />';
                // checkboxes dont work
                //echo '<input type="checkbox" name="ccsve_post_type['.$item.']" value="'.$item.'" '.$checked.' />';
                echo '<label for=post_type'.$item.'> '.$item.'</label>';
                echo ' <br />';
            }
        }

        public function settings_field_select_std_fields() {
            $ccsve_post_type = get_option('ccsve_post_type');
            $fields = generate_std_fields($ccsve_post_type);
            $ccsve_std_fields = get_option('ccsve_std_fields');
            $ccsve_std_fields_num = count($fields);
            echo '<select multiple="multiple" size="'.$ccsve_std_fields_num.'" name="ccsve_std_fields[selectinput][]">';
            foreach ($fields as $field) {
                if (in_array($field, $ccsve_std_fields['selectinput'])) {
                    echo '\n\t<option selected="selected" value="'. $field . '">'.$field.'</option>';
                } else {
                    echo '\n\t\<option value="'.$field .'">'.$field.'</option>';
                }
            }
        }

        public function settings_field_select_tax_terms()  {
            $ccsve_post_type = get_option('ccsve_post_type');
            $object_tax = get_object_taxonomies($ccsve_post_type, 'names');
            $ccsve_tax_terms = get_option('ccsve_tax_terms');
            $ccsve_tax_terms_num = count($object_tax);
            echo '<select multiple="multiple" size="'.$ccsve_tax_terms_num.'" name="ccsve_tax_terms[selectinput][]">';
            foreach ($object_tax as $tax) {
                if (in_array($tax, $ccsve_tax_terms['selectinput'])) {
                    echo '\n\t<option selected="selected" value="'. $tax . '">'.$tax.'</option>';
                } else {
                    echo '\n\t\<option value="'.$tax .'">'.$tax.'</option>';
                }
            }
        }

        public function settings_field_select_custom_fields()   {
            $ccsve_post_type = get_option('ccsve_post_type');
            $meta_keys = generate_post_meta_keys($ccsve_post_type);
            $ccsve_custom_fields = get_option('ccsve_custom_fields');
            $ccsve_meta_keys_num = count($meta_keys);
            echo '<select multiple="multiple" size="'.$ccsve_meta_keys_num.'" name="ccsve_custom_fields[selectinput][]">';
            foreach ($meta_keys as $meta_key) {
                if (in_array($meta_key, $ccsve_custom_fields['selectinput'])) {
                    echo '\n\t<option selected="selected" value="'. $meta_key . '">'.$meta_key.'</option>';
                } else {
                    echo '\n\t\<option value="'.$meta_key .'">'.$meta_key.'</option>';
                }
            }
        }

        // WOO COMMERCE
        /*public function settings_field_select_woocommerce_fields()   {
            $ccsve_post_type = get_option('ccsve_post_type');

            if($ccsve_post_type == 'product' && class_exists('WC_Product')) {

                global $woocommerce;

                //$product = wc_get_product( $post->ID );

                //$meta_keys = generate_post_meta_keys($ccsve_post_type);
                $meta_keys = array(
                    'sku',
                    'regular_price',
                    'sale_price',
                    'manage_stock',
                    'stock_status',
                    'backorders',
                    'stock',
                    'featured',
                    'featured_image',
                    'product_gallery'
                );
                $ccsve_woocommerce_fields = get_option('ccsve_woocommerce_fields');
                $ccsve_meta_keys_num = count($meta_keys);

                echo '<select multiple="multiple" size="'.$ccsve_meta_keys_num.'" name="ccsve_woocommerce_fields[selectinput][]">';
                foreach ($meta_keys as $meta_key) {
                    if (in_array($meta_key, $ccsve_woocommerce_fields['selectinput'])){
                        echo '\n\t<option selected="selected" value="'. $meta_key . '">'.$meta_key.'</option>';
                    } else {
                        echo '\n\t\<option value="'.$meta_key .'">'.$meta_key.'</option>';
                    }
                }

            } // if class exists
        }*/


    // ADD MENU
    public function add_menu() {
        // Add a page to manage this plugin's settings
        add_submenu_page(
            'tools.php',
            'CSV/XLS Export Settings',
            'CSV/XLS Export',
            'manage_options',
            'simple_csv_exporter_settings',
            array(&$this, 'plugin_settings_page')
        );
    } // END public function add_menu()

    // MENU CALLBACK
    public function plugin_settings_page() {
        if(!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        // Render the settings template
        //include(sprintf("%s/settings_page.php", dirname(__FILE__)));
        ?>
        <div class="wrap">
        <h2>CSV/XLS Exporter Settings</h2>
        <form method="post" action="options.php">

          <?php @settings_fields('wp_ccsve-group'); ?>

          <?php @do_settings_fields('wp_ccsve-group'); ?>

          <?php do_settings_sections('simple_csv_exporter_settings'); ?>

          <?php @submit_button(); ?>

          <a class="ccsve_button button button-success" href="options-general.php?page=simple_csv_exporter_settings&export=csv">Export to CSV</a>

          <a class="ccsve_button button button-success" href="options-general.php?page=simple_csv_exporter_settings&export=xls">Export to XLS</a>

         <p><i>When opening the exported xls, Excel will prompt the user with a warning, but the file is perfectly fine and can then be opened. <strong>Unfortunately this can\'t be avoided</strong>, <a href="http://blogs.msdn.com/b/vsofficedeveloper/archive/2008/03/11/excel-2007-extension-warning.aspx" target="_blank">read more here</a>.</i></p>

        </form>
      </div>
    <?php
    } // END public function plugin_settings_page()

    } // END class simple_csv_exporter_settings_Settings

} // END if(!class_exists('simple_csv_exporter_settings_Settings'))

function generate_post_meta_keys($post_type){

    /*$store_meta_keys = 'simple_xls_exporter_'.$post_type.'_6h';
    delete_transient($store_meta_keys);

    if(get_transient($store_meta_keys) === false) {*/

        global $wpdb;
        $query = "
            SELECT DISTINCT($wpdb->postmeta.meta_key)
            FROM $wpdb->posts
            LEFT JOIN $wpdb->postmeta
            ON $wpdb->posts.ID = $wpdb->postmeta.post_id
            WHERE $wpdb->posts.post_type = '%s'
            AND $wpdb->postmeta.meta_key != ''
            AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
          ";
         // Removed this
         // AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
         // as second AND, to show also fields starting with _
        $meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));

        /*set_transient($store_meta_keys, $meta_keys, 6 * HOUR_IN_SECONDS); // 24h
    }
    $meta_keys = get_transient($store_meta_keys);*/

    /*echo '<pre>';
    var_dump($meta_keys);
    echo '</pre>';*/

    return $meta_keys;
}

// Get standard WP Fields
function generate_std_fields($post_type){
    $fields = array('permalink', 'post_thumbnail');
    $q = new WP_Query(array('post_type' => $post_type, 'post_status' => 'publish', 'posts_per_page' => 1));
    $p = $q->posts[0];
    foreach($p as $f => $v) {
      $fields[] = $f;
    }
    return $fields;
 }

function ccsve_checkboxes_fix($input, $post_type) {
    $options = get_option('ccsve_custom_fields');
    $merged = $options;
    $merged[] = $input;
}
