<?php
/**
 * Plugin Name: Electrode Bootstrap (one-time setup)
 * Description: یک‌بار اجرا می‌شود: افزونه کاتالوگ و قالب را فعال می‌کند، پیوندهای یکتا، زبان فارسی و منوها را تنظیم می‌کند. پس از اجرا کاری انجام نمی‌دهد و می‌توانید حذفش کنید.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_loaded',
	function () {
		if ( wp_installing() || get_option( 'electrode_bootstrap_done' ) ) {
			return;
		}
		// Claim the run first so concurrent requests don't repeat it.
		if ( ! add_option( 'electrode_bootstrap_done', 'running', '', false ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// 1) Site identity, timezone, permalinks.
		update_option( 'blogname', 'الکترود آریا' );
		update_option( 'blogdescription', 'مرکز تأمین انواع الکترود جوشکاری' );
		update_option( 'timezone_string', 'Asia/Tehran' );
		update_option( 'permalink_structure', '/%postname%/' );

		// 2) Persian language pack (best effort; site works without it).
		if ( ! in_array( 'fa_IR', get_available_languages(), true ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/translation-install.php';
			if ( function_exists( 'wp_download_language_pack' ) && WP_Filesystem() ) {
				wp_download_language_pack( 'fa_IR' );
			}
		}
		if ( in_array( 'fa_IR', get_available_languages(), true ) ) {
			update_option( 'WPLANG', 'fa_IR' );
		}

		// 3) Catalog plugin (its activation hook seeds sample content).
		$plugin = 'electrode-catalog/electrode-catalog.php';
		if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) && ! is_plugin_active( $plugin ) ) {
			activate_plugin( $plugin );
		}

		// 4) Theme.
		if ( wp_get_theme( 'electrode-shop' )->exists() && 'electrode-shop' !== get_stylesheet() ) {
			switch_theme( 'electrode-shop' );
		}

		// 5) Menus.
		if ( ! wp_get_nav_menu_object( 'منوی اصلی' ) ) {
			$menu_id = wp_create_nav_menu( 'منوی اصلی' );
			if ( ! is_wp_error( $menu_id ) ) {
				$items = array(
					array( 'خانه', home_url( '/' ) ),
					array( 'محصولات', home_url( '/products/' ) ),
					array( 'درباره ما', home_url( '/about/' ) ),
					array( 'تماس با ما', home_url( '/contact/' ) ),
				);
				foreach ( $items as $i => $it ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'    => $it[0],
							'menu-item-url'      => $it[1],
							'menu-item-status'   => 'publish',
							'menu-item-type'     => 'custom',
							'menu-item-position' => $i + 1,
						)
					);
				}
				set_theme_mod( 'nav_menu_locations', array( 'primary' => $menu_id, 'footer' => $menu_id ) );
			}
		}

		// Remove the default "Hello world" demo post.
		$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
		if ( $hello ) {
			wp_trash_post( $hello->ID );
		}

		flush_rewrite_rules();
		update_option( 'electrode_bootstrap_done', gmdate( 'c' ), false );

		// The theme was switched mid-request; reload so the new theme's functions.php is loaded.
		if ( ! wp_doing_ajax() && ! ( defined( 'WP_CLI' ) && WP_CLI ) && 'GET' === ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
			$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
			wp_safe_redirect( home_url( $uri ) );
			exit;
		}
	}
);
