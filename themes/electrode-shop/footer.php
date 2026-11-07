</main>

<footer class="site-footer">
	<div class="wrap">
		<div>
			<h4><?php bloginfo( 'name' ); ?></h4>
			<p><?php echo esc_html( get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : 'تأمین‌کننده انواع الکترود جوشکاری برای پروژه‌های صنعتی و ساختمانی.' ); ?></p>
		</div>
		<div>
			<h4>دسته‌بندی‌ها</h4>
			<ul>
				<?php
				$els_terms = taxonomy_exists( 'electrode_type' ) ? get_terms( array( 'taxonomy' => 'electrode_type', 'hide_empty' => true, 'number' => 6 ) ) : array();
				if ( ! is_wp_error( $els_terms ) ) :
					foreach ( $els_terms as $els_t ) :
						?>
						<li><a href="<?php echo esc_url( get_term_link( $els_t ) ); ?>"><?php echo esc_html( $els_t->name ); ?></a></li>
						<?php
					endforeach;
				endif;
				?>
			</ul>
		</div>
		<div>
			<h4>دسترسی سریع</h4>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'fallback_cb'    => 'els_fallback_menu',
					'depth'          => 1,
				)
			);
			?>
		</div>
		<div>
			<h4>تماس با ما</h4>
			<ul>
				<?php if ( els_contact( 'address' ) ) : ?><li><?php echo esc_html( els_contact( 'address' ) ); ?></li><?php endif; ?>
				<?php if ( els_contact( 'phone' ) ) : ?><li>تلفن: <a dir="ltr" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', els_contact( 'phone' ) ) ); ?>"><?php echo esc_html( els_contact( 'phone' ) ); ?></a></li><?php endif; ?>
				<?php if ( els_contact( 'mobile' ) ) : ?><li>موبایل: <a dir="ltr" href="tel:<?php echo esc_attr( els_contact( 'mobile' ) ); ?>"><?php echo esc_html( els_contact( 'mobile' ) ); ?></a></li><?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="footer-bottom">© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — همه حقوق محفوظ است.</div>
</footer>

<?php $els_wa = els_whatsapp_url( 'سلام، برای استعلام قیمت الکترود پیام می‌دهم.' ); ?>
<?php if ( $els_wa ) : ?>
	<a class="float-wa" href="<?php echo esc_url( $els_wa ); ?>" target="_blank" rel="noopener" aria-label="گفتگو در واتساپ" style="color:#fff"><?php echo els_icon( 'whatsapp', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
