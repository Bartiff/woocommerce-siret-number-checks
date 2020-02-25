<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://bartiff.net
 * @since      0.1.0
 *
 * @package    Woocommerce_Siret_Number_Checks
 * @subpackage Woocommerce_Siret_Number_Checks/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="wsnc-settings" class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form id="wsnc-form-settings" action="options.php" method="post">
        <?php
            settings_errors();
            settings_fields( $this->plugin_name );
            do_settings_sections( $this->plugin_name );
            submit_button();
        ?>
    </form>
</div>