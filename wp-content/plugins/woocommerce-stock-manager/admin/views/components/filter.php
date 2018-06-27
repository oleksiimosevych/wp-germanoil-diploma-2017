      
      <ul class="stock-manager-navigation">
        <li><span class="navigation-filter-default activ"><?php _e('Filter','stock-manager'); ?></span></li>
        <li><span class="navigation-filter-by-sku"><?php _e('Search by sku','stock-manager'); ?></span></li>
        <!--<li><span class="navigation-filter-display"><?php //_e('Display setting','stock-manager'); ?></span></li>-->
      </ul>
      
      <div class="clear"></div>
      
      <div class="stock-filter filter-block active-filter">
        <form method="get" action="">
          
          <select name="order-by">
            <option value=""><?php _e('Order by','stock-manager'); ?></option>
            <option value="name-asc" <?php if( isset( $_GET['order-by'] ) && $_GET['order-by'] == 'name-asc' ){ echo 'selected="selected"'; } ?>><?php _e('Product name ASC','stock-manager'); ?></option>
            <option value="name-desc" <?php if( isset( $_GET['order-by'] ) && $_GET['order-by'] == 'name-desc' ){ echo 'selected="selected"'; } ?>><?php _e('Product name DESC','stock-manager'); ?></option>
            <option value="sku-asc" <?php if( isset( $_GET['order-by'] ) && $_GET['order-by'] == 'sku-asc' ){ echo 'selected="selected"'; } ?>><?php _e('SKU ASC','stock-manager'); ?></option>
            <option value="sku-desc" <?php if( isset( $_GET['order-by'] ) && $_GET['order-by'] == 'sku-desc' ){ echo 'selected="selected"'; } ?>><?php _e('SKU DESC','stock-manager'); ?></option>
          </select>

          <select name="product-type">
            <option value="simple" <?php if(isset($_GET['product-type']) && $_GET['product-type'] == 'simple'){ echo 'selected="selected"'; } ?> ><?php _e('Simple products','stock-manager'); ?></option>
            <option value="variable" <?php if(isset($_GET['product-type']) && $_GET['product-type'] == 'variable'){ echo 'selected="selected"'; } ?>><?php _e('Products with variation','stock-manager'); ?></option>
          </select>
          
          <select name="product-category">
            <option value="all"><?php _e('All categories','stock-manager'); ?></option>
            <?php
              if(isset($_GET['product-category']) && $_GET['product-category'] != 'all' ){
                echo $stock->products_categories($_GET['product-category']);
              }else{
                echo $stock->products_categories();
              }
              
            ?>
          </select>
        
          <select name="manage-stock">
            <option value=""><?php _e('Manage stock','stock-manager'); ?></option>
            <option value="no" <?php if( isset( $_GET['manage-stock'] ) && $_GET['manage-stock'] == 'no' ){ echo 'selected="selected"'; } ?>><?php _e('No manage stock','stock-manager'); ?></option>
            <option value="yes" <?php if( isset( $_GET['manage-stock'] ) && $_GET['manage-stock'] == 'yes' ){ echo 'selected="selected"'; } ?>><?php _e('Yes manage stock','stock-manager'); ?></option>
          </select>
      
          <select name="stock-status">
            <option value=""><?php _e('Stock status','stock-manager'); ?></option>
            <option value="instock" <?php if( isset( $_GET['stock-status'] ) && $_GET['stock-status'] == 'instock' ){ echo 'selected="selected"'; } ?>><?php _e('In stock','stock-manager'); ?></option>
            <option value="outofstock" <?php if( isset( $_GET['stock-status'] ) && $_GET['stock-status'] == 'outofstock' ){ echo 'selected="selected"'; } ?>><?php _e('Out of stock','stock-manager'); ?></option>
          </select>
      
          <input type="hidden" name="page" value="stock-manager" />
          <input type="submit" name="show-stock-status" value="<?php _e('Show','stock-manager'); ?>" class="btn btn-info" />
        </form>
        <a href="<?php echo admin_url().'admin.php?page=stock-manager'; ?>" class="btn btn-danger"><?php _e('Clear filter','stock-manager'); ?></a>
      </div>
      
      <div class="clear"></div>
      
      <div class="filter-by-sku filter-block">
        <form method="get" action="">
          <input type="text" name="sku" class="sku-seach-field" />
          <input type="hidden" name="page" value="stock-manager" />
          <input type="submit" name="show-sku-item" value="<?php _e('Search by sku','stock-manager'); ?>" class="btn btn-info" />
        </form>
      </div>
      
      <div class="clear"></div>
      