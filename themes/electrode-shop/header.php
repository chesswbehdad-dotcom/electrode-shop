<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#1d232b">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main">رفتن به محتوا</a>

<?php
$els_phone = els_contact( 'phone' );
$els_hours = els_contact( 'hours' );
$els_email = els_contact( 'email' );
?>
<div class="topbar">
	<div class="wrap">
		<div>
			<?php if ( $els_phone ) : ?><span>تلفن: <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $els_phone ) ); ?>" dir="ltr"><?php echo esc_html( $els_phone ); ?></a></span><?php endif; ?>
			<?php if ( $els_email ) : ?><span class="hide-sm"><a href="mailto:<?php echo esc_attr( $els_email ); ?>"><?php echo esc_html( $els_email ); ?></a></span><?php endif; ?>
		</div>
		<?php if ( $els_hours ) : ?><div class="hide-sm"><?php echo esc_html( $els_hours ); ?></div><?php endif; ?>
	</div>
</div>

<header class="site-header">
	<div class="wrap">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'style' => 'max-height:52px;width:auto' ) ); ?>
			<?php else : ?>
				<span class="brand__mark"><svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19 16 8" stroke="#c4ccd4" stroke-width="3" stroke-linecap="round"/><circle cx="18" cy="6" r="3" fill="#f26b1d"/></svg></span>
				<span><?php bloginfo( 'name' ); ?><small><?php bloginfo( 'description' ); ?></small></span>
			<?php endif; ?>
		</a>

		<button class="nav-toggle" aria-expanded="false" aria-controls="site-nav"><span></span><span></span><span></span><span class="screen-reader-text">منو</span></button>

		<nav class="nav" id="site-nav" aria-label="منوی اصلی">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav__list',
					'fallback_cb'    => 'els_fallback_menu',
					'depth'          => 1,
				)
			);
			?>
		</nav>
		<a class="btn btn--primary header-cta" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">استعلام قیمت</a>
	</div>
</header>
<main id="main">
