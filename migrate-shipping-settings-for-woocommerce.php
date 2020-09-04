<?php
/**
 * Migrate shipping settings for WooCommerce
 *
 * This extensions allows users to migrate WooCommerce shipping settings between sites.
 *
 * @package WordPress
 * @subpackage migrate-shipping-settings-for-woocommerce
 * @since 1.0
 *
 * Plugin Name: Migrate shipping settings for WooCommerce
 * Plugin URI: https://github.com/woorion/migrate-shipping-settings-for-woocommerce
 * Description: Migrate WooCommerce shipping settings between sites.
 * Author: woorion
 * Author URI: https://github.com/woorion
 * Version: 1.0
 * Requires at least: 4.0
 * Tested up to: 5.5
 * WC requires at least: 4.0
 * WC tested up to: 4.4.1
 * Text Domain: woorion_migrate_shipping
 * Domain Path: /languages
 */

/**
 * Return if plugin is called directly.
 * 
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	 exit;
}

/**
 * Return if WooCommerce plugin is not active.
 * 
 * @since 1.0
 */
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	 return;
}

/**
 * Add settings links on plugin page.
 *
 * @param string $links The original settings link on the plugin page.
 * @return string $links The updated settings link on the plugin page.
 * @since 1.0
 */
function woorion_settings_links( $links ) {

	$import_url    = admin_url( '/admin.php?page=wc-settings&tab=shipping&section=migrate_shipping_settings' );
	$settings_link = '<a href="' . $import_url . '">' . __( 'Settings', 'smntcs-custom-logo-link' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woorion_settings_links' );

/**
 * Add meta links on plugin page.
 *
 * @param string $links The original meta links on the plugin page.
 * @return string $links The updated meta links on the plugin page.
 * @since 1.0
 */
function woorion_meta_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
	
	if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
		$links_array[] = '<a href="https://github.com/woorion/migrate-shipping-settings-for-woocommerce/issues/new">Report problem</a>';
		// $links_array[] = '<a href="#">FAQ</a>';
		// $links_array[] = '<a href="#">Support</a>';
		// $links_array[] = '<a href="#">Check for updates</a>';
	}
	
	return $links_array;

}
add_filter( 'plugin_row_meta', 'woorion_meta_links', 10, 4 );

/**
 * Add tabs to shipping settings page.
 *
 * @param string $links The original tabs on the settings page.
 * @return string $links The updated tabs on the settings page.
 * @since 1.0
 */
function woorion_add_settings_tabs( $settings_tab ) {

	$settings_tab['migrate_shipping_settings'] = __( 'Migrate shipping settings' );
	
	return $settings_tab;

}
add_filter( 'woocommerce_get_sections_shipping', 'woorion_add_settings_tabs' );

/**
 * 
 */
function woorion_get_settings( $settings, $current_section ) {
	
	$custom_settings = array();

	if ( 'migrate_shipping_settings' == $current_section ) {
		$custom_settings = array(

			array(
				'name' => __( 'Migrate shipping settings' ),
				'type' => 'title',
				'desc' => __( '⬆️ Export the shipping settings of this site or<br> ⬇️ import shipping settings of another site into this site.' ),
				'id'   => 'migtation_title',
			),

			array(
				'name'     => __( 'Export' ),
				'type'     => 'button',
				'desc'     => __( 'Export the shipping settings of this site as CSV file.' ),
				'desc_tip' => false,
				'class'    => 'button-primary',
				'id'       => 'export_button',

			),

			array(
				'name'     => __( 'Import' ),
				'type'     => 'upload',
				'desc'     => __( 'Export the shipping settings of another site into this site as CSV file.' ),
				'desc_tip' => false,
				'id'       => 'import_upload',
			),			
			
			array(
				'type' => 'sectionend',
				'id'   => 'export_shipping',
			),

		);

		return $custom_settings;
	} else {
		return $settings;
	}
}
add_filter( 'woocommerce_get_settings_shipping', 'woorion_get_settings', 10, 2 );

function freeship_add_admin_field_upload( $value ) {
	 $option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
	 $description  = WC_Admin_Settings::get_field_description( $value ); ?>

	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			 <?php echo $description['tooltip_html']; ?>
		</th>

		<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ); ?>">
			<input
				name ="<?php echo esc_attr( $value['name'] ); ?>"
				id   ="<?php echo esc_attr( $value['id'] ); ?>"
				type ="file"
				style="<?php echo esc_attr( $value['css'] ); ?>"
				value="<?php echo esc_attr( $value['name'] ); ?>"
				class="<?php echo esc_attr( $value['class'] ); ?>"
			/> 
			 <?php echo $description['description']; ?>
		</td>
	</tr>

	 <?php
}
add_action( 'woocommerce_admin_field_upload', 'freeship_add_admin_field_upload' );

function freeship_add_admin_field_button( $value ) {
	 $option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
	 $description  = WC_Admin_Settings::get_field_description( $value ); 
	?>

<tr valign="top">
<th scope="row" class="titledesc">
<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
		<?php echo $description['tooltip_html']; ?>
</th>

<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ); ?>">

<input
			name ="<?php echo esc_attr( $value['name'] ); ?>"
			id   ="<?php echo esc_attr( $value['id'] ); ?>"
			type ="submit"
			style="<?php echo esc_attr( $value['css'] ); ?>"
			value="<?php echo esc_attr( $value['name'] ); ?>"
			class="<?php echo esc_attr( $value['class'] ); ?>"
/> 
		<?php echo $description['description']; ?>

</td>
</tr>

		<?php
}
add_action( 'woocommerce_admin_field_button', 'freeship_add_admin_field_button' );

