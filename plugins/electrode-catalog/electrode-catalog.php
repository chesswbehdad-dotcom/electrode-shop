<?php
/**
 * Plugin Name:       Electrode Catalog – کاتالوگ الکترود
 * Description:       کاتالوگ محصولات الکترود جوشکاری با مشخصات فنی، دسته‌بندی و فرم استعلام قیمت.
 * Version:           1.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Behdad Tajeddin
 * Text Domain:       electrode-catalog
 * License:           GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELC_VERSION', '1.0.0' );
define( 'ELC_FILE', __FILE__ );
define( 'ELC_DIR', plugin_dir_path( __FILE__ ) );
define( 'ELC_URL', plugin_dir_url( __FILE__ ) );

require_once ELC_DIR . 'includes/helpers.php';
require_once ELC_DIR . 'includes/post-types.php';
require_once ELC_DIR . 'includes/meta-boxes.php';
require_once ELC_DIR . 'includes/inquiries.php';
require_once ELC_DIR . 'includes/settings.php';
require_once ELC_DIR . 'includes/sample-data.php';

register_activation_hook( __FILE__, 'elc_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Activation: register types, flush rewrites, seed demo content once.
 */
function elc_activate() {
	elc_register_post_types();
	elc_seed_sample_data();
	flush_rewrite_rules();
}
