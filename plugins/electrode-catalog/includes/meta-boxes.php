<?php
/**
 * Admin meta boxes for electrode products.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'elc_specs', 'مشخصات فنی الکترود', 'elc_render_specs_box', 'electrode', 'normal', 'high' );
		add_meta_box( 'elc_commerce', 'قیمت و موجودی', 'elc_render_commerce_box', 'electrode', 'side', 'high' );
	}
);

/**
 * Render a set of fields.
 *
 * @param WP_Post $post   Post.
 * @param array   $fields Field definitions.
 */
function elc_render_fields( $post, $fields ) {
	echo '<div class="elc-fields" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px">';
	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$id    = esc_attr( ltrim( $key, '_' ) );
		echo '<p style="margin:0">';
		if ( 'checkbox' === $field['type'] ) {
			printf(
				'<label><input type="checkbox" name="%1$s" value="1" %2$s> %3$s</label>',
				esc_attr( $id ),
				checked( $value, '1', false ),
				esc_html( $field['label'] )
			);
		} elseif ( 'select' === $field['type'] ) {
			printf( '<label for="%1$s"><strong>%2$s</strong></label><br><select id="%1$s" name="%1$s" style="width:100%%">', esc_attr( $id ), esc_html( $field['label'] ) );
			foreach ( $field['options'] as $opt => $label ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $opt ), selected( $value, $opt, false ), esc_html( $label ) );
			}
			echo '</select>';
		} else {
			printf(
				'<label for="%1$s"><strong>%2$s</strong></label><br><input type="%3$s" id="%1$s" name="%1$s" value="%4$s" placeholder="%5$s" style="width:100%%" %6$s>',
				esc_attr( $id ),
				esc_html( $field['label'] ),
				esc_attr( 'number' === $field['type'] ? 'number' : 'text' ),
				esc_attr( $value ),
				esc_attr( isset( $field['ph'] ) ? $field['ph'] : '' ),
				'number' === $field['type'] ? 'min="0" step="1"' : ''
			);
		}
		echo '</p>';
	}
	echo '</div>';
}

/**
 * @param WP_Post $post Post.
 */
function elc_render_specs_box( $post ) {
	wp_nonce_field( 'elc_save_meta', 'elc_meta_nonce' );
	elc_render_fields( $post, elc_spec_fields() );
	echo '<p class="description">فیلدهای خالی در جدول مشخصات سایت نمایش داده نمی‌شوند.</p>';
}

/**
 * @param WP_Post $post Post.
 */
function elc_render_commerce_box( $post ) {
	echo '<div style="display:block">';
	foreach ( elc_commerce_fields() as $key => $field ) {
		elc_render_fields( $post, array( $key => $field ) );
		echo '<div style="height:8px"></div>';
	}
	echo '</div>';
}

add_action(
	'save_post_electrode',
	function ( $post_id ) {
		if ( ! isset( $_POST['elc_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elc_meta_nonce'] ) ), 'elc_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array_merge( elc_spec_fields(), elc_commerce_fields() );
		foreach ( $fields as $key => $field ) {
			$name = ltrim( $key, '_' );
			if ( 'checkbox' === $field['type'] ) {
				update_post_meta( $post_id, $key, isset( $_POST[ $name ] ) ? '1' : '' );
				continue;
			}
			$raw = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
			if ( 'number' === $field['type'] ) {
				$raw = '' === $raw ? '' : (string) absint( $raw );
			} elseif ( 'select' === $field['type'] && ! isset( $field['options'][ $raw ] ) ) {
				$raw = 'in';
			}
			update_post_meta( $post_id, $key, $raw );
		}
	}
);

/**
 * Admin list columns.
 */
add_filter(
	'manage_electrode_posts_columns',
	function ( $cols ) {
		$new = array();
		foreach ( $cols as $k => $v ) {
			$new[ $k ] = $v;
			if ( 'title' === $k ) {
				$new['elc_aws']   = 'کلاس AWS';
				$new['elc_price'] = 'قیمت';
				$new['elc_stock'] = 'موجودی';
			}
		}
		return $new;
	}
);

add_action(
	'manage_electrode_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'elc_aws' === $col ) {
			echo esc_html( get_post_meta( $post_id, '_elc_aws', true ) );
		} elseif ( 'elc_price' === $col ) {
			echo esc_html( elc_price_label( $post_id ) );
		} elseif ( 'elc_stock' === $col ) {
			$s = elc_stock( $post_id );
			echo esc_html( $s['label'] );
		}
	},
	10,
	2
);
