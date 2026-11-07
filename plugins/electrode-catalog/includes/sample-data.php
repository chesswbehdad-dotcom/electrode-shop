<?php
/**
 * One-time sample catalog content so the site isn't empty on first run.
 * Everything here is editable/deletable from the admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seed categories, brands, products and pages (runs once).
 */
function elc_seed_sample_data() {
	if ( get_option( 'elc_seeded' ) ) {
		return;
	}

	$types = array(
		'rutile'     => array( 'روتیلی (عمومی)', 'الکترودهای روتیلی با قوس نرم و سرباره زودجدا؛ مناسب کارهای عمومی و سازه‌های سبک.' ),
		'basic'      => array( 'قلیایی / کم‌هیدروژن', 'الکترودهای قلیایی برای اتصالات حساس، سازه‌های سنگین و مخازن تحت فشار.' ),
		'cellulosic' => array( 'سلولزی', 'نفوذ عمیق برای پاس ریشه خطوط لوله و جوشکاری سرازیر.' ),
		'stainless'  => array( 'استنلس استیل', 'الکترودهای فولاد زنگ‌نزن برای صنایع غذایی، پتروشیمی و اتصال فلزات غیرهمجنس.' ),
		'low-alloy'  => array( 'فولاد کم‌آلیاژ', 'برای فولادهای کروم-مولیبدن و کاربردهای دمای بالا.' ),
		'cast-iron'  => array( 'چدن', 'تعمیر قطعات چدنی مثل بلوک موتور، پوسته پمپ و گیربکس.' ),
		'hardfacing' => array( 'سخت‌کاری (روکش سخت)', 'افزایش مقاومت سایشی قطعات مثل دندانه بیل مکانیکی و سنگ‌شکن.' ),
		'aluminum'   => array( 'آلومینیوم', 'جوشکاری و تعمیر قطعات آلومینیومی و آلیاژهای Al-Si.' ),
	);
	$term_ids = array();
	foreach ( $types as $slug => $t ) {
		$res = term_exists( $slug, 'electrode_type' );
		if ( ! $res ) {
			$res = wp_insert_term( $t[0], 'electrode_type', array( 'slug' => $slug, 'description' => $t[1] ) );
		}
		if ( ! is_wp_error( $res ) ) {
			$term_ids[ $slug ] = (int) $res['term_id'];
		}
	}

	$brands = array( 'آریا جوش' => 'arya', 'پارس الکترود' => 'pars' );
	foreach ( $brands as $name => $slug ) {
		if ( ! term_exists( $slug, 'electrode_brand' ) ) {
			wp_insert_term( $name, 'electrode_brand', array( 'slug' => $slug ) );
		}
	}

	$products = array(
		array(
			'title' => 'الکترود E6013 روتیلی',
			'type'  => 'rutile',
			'brand' => 'arya',
			'feat'  => '1',
			'ex'    => 'پرمصرف‌ترین الکترود عمومی؛ قوس پایدار، پاشش کم و ظاهر جوش زیبا.',
			'body'  => 'الکترود E6013 با پوشش روتیلی، انتخاب اول برای جوشکاری فولادهای ساختمانی کم‌کربن، درب و پنجره، اسکلت سبک و کارهای تعمیراتی است. با ترانس‌های معمولی (AC) هم به‌راحتی کار می‌کند و سرباره آن خودبه‌خود جدا می‌شود.',
			'm'     => array( '_elc_aws' => 'AWS A5.1 E6013', '_elc_iso' => 'ISO 2560-A: E 38 0 RC 11', '_elc_coating' => 'روتیلی', '_elc_diameters' => '2, 2.5, 3.25, 4, 5', '_elc_length' => '350', '_elc_current' => 'AC / DC±', '_elc_amperage' => '50 – 220 A', '_elc_positions' => 'همه وضعیت‌ها', '_elc_tensile' => '≥ 430 MPa', '_elc_base_metal' => 'فولادهای ساختمانی کم‌کربن (St37, St44)', '_elc_packaging' => 'کارتن ۵ کیلوگرمی', '_elc_drying' => 'معمولاً نیاز ندارد؛ در صورت رطوبت ۱۰۰ درجه، ۱ ساعت' ),
		),
		array(
			'title' => 'الکترود E7018 قلیایی کم‌هیدروژن',
			'type'  => 'basic',
			'brand' => 'arya',
			'feat'  => '1',
			'ex'    => 'استحکام بالا و مقاومت عالی در برابر ترک؛ مناسب سازه‌های سنگین و مخازن.',
			'body'  => 'الکترود E7018 با پوشش قلیایی و پودر آهن، جوشی با هیدروژن نفوذی پایین و خواص ضربه‌ای خوب در دمای پایین ایجاد می‌کند. برای اسکلت فلزی سنگین، پل، مخازن تحت فشار و ماشین‌آلات توصیه می‌شود.',
			'm'     => array( '_elc_aws' => 'AWS A5.1 E7018', '_elc_iso' => 'ISO 2560-A: E 42 4 B 42 H5', '_elc_coating' => 'قلیایی (بازی) با پودر آهن', '_elc_diameters' => '2.5, 3.25, 4, 5', '_elc_length' => '350 / 450', '_elc_current' => 'DC+ / AC', '_elc_amperage' => '70 – 230 A', '_elc_positions' => 'همه وضعیت‌ها به‌جز سرازیر', '_elc_tensile' => '≥ 490 MPa', '_elc_base_metal' => 'فولادهای ساختمانی و مخازن (St52, A516)', '_elc_packaging' => 'کارتن ۵ کیلوگرمی / قوطی وکیوم', '_elc_drying' => '۳۵۰ درجه سانتی‌گراد به مدت ۲ ساعت' ),
		),
		array(
			'title' => 'الکترود E6010 سلولزی',
			'type'  => 'cellulosic',
			'brand' => 'pars',
			'feat'  => '',
			'ex'    => 'نفوذ عمیق برای پاس ریشه لوله؛ قابل استفاده در وضعیت سرازیر.',
			'body'  => 'الکترود سلولزی E6010 برای پاس ریشه و گرم خطوط لوله نفت و گاز و جوشکاری در وضعیت عمودی سرازیر کاربرد دارد. فقط با جریان مستقیم (قطب مثبت) کار می‌کند.',
			'm'     => array( '_elc_aws' => 'AWS A5.1 E6010', '_elc_iso' => 'ISO 2560-A: E 38 3 C 21', '_elc_coating' => 'سلولزی سدیمی', '_elc_diameters' => '2.5, 3.25, 4, 5', '_elc_length' => '350', '_elc_current' => 'DC+', '_elc_amperage' => '50 – 180 A', '_elc_positions' => 'همه وضعیت‌ها شامل سرازیر', '_elc_tensile' => '≥ 430 MPa', '_elc_base_metal' => 'لوله API 5L تا X52', '_elc_packaging' => 'کارتن ۲۰ کیلوگرمی', '_elc_drying' => 'خشک نشود' ),
		),
		array(
			'title' => 'الکترود E8018-B2 کروم مولیبدن',
			'type'  => 'low-alloy',
			'brand' => 'pars',
			'feat'  => '',
			'ex'    => 'برای فولادهای 1.25Cr-0.5Mo در بویلر، لوله‌های بخار و پالایشگاه.',
			'body'  => 'الکترود کم‌هیدروژن آلیاژی برای جوشکاری لوله‌ها و تجهیزاتی که در دمای بالا کار می‌کنند. پیش‌گرم و عملیات حرارتی پس از جوش طبق دستورالعمل الزامی است.',
			'm'     => array( '_elc_aws' => 'AWS A5.5 E8018-B2', '_elc_coating' => 'قلیایی', '_elc_diameters' => '2.5, 3.25, 4', '_elc_length' => '350', '_elc_current' => 'DC+ / AC', '_elc_amperage' => '70 – 180 A', '_elc_positions' => 'همه وضعیت‌ها به‌جز سرازیر', '_elc_tensile' => '≥ 550 MPa', '_elc_base_metal' => 'A335 P11 ،A387 Gr.11', '_elc_packaging' => 'قوطی ۵ کیلوگرمی', '_elc_drying' => '۳۵۰ درجه سانتی‌گراد به مدت ۲ ساعت' ),
		),
		array(
			'title' => 'الکترود استیل E308L-16',
			'type'  => 'stainless',
			'brand' => 'arya',
			'feat'  => '1',
			'ex'    => 'جوشکاری استیل 304 و 304L؛ مقاوم در برابر خوردگی بین‌دانه‌ای.',
			'body'  => 'الکترود استنلس استیل کم‌کربن برای جوشکاری تجهیزات صنایع غذایی، دارویی، مخازن و لوله‌های استیل 304. قوس نرم و سرباره خودجداشونده دارد.',
			'm'     => array( '_elc_aws' => 'AWS A5.4 E308L-16', '_elc_iso' => 'ISO 3581-A: E 19 9 L R 12', '_elc_coating' => 'روتیلی-قلیایی', '_elc_diameters' => '2, 2.5, 3.25, 4', '_elc_length' => '300 / 350', '_elc_current' => 'AC / DC+', '_elc_amperage' => '40 – 140 A', '_elc_positions' => 'همه وضعیت‌ها به‌جز سرازیر', '_elc_tensile' => '≥ 520 MPa', '_elc_base_metal' => 'AISI 304, 304L', '_elc_packaging' => 'بسته ۲ کیلوگرمی', '_elc_drying' => '۳۰۰ درجه سانتی‌گراد به مدت ۱ ساعت' ),
		),
		array(
			'title' => 'الکترود استیل E316L-16',
			'type'  => 'stainless',
			'brand' => 'pars',
			'feat'  => '',
			'ex'    => 'دارای مولیبدن؛ مقاوم در محیط‌های کلریدی و اسیدی.',
			'body'  => 'برای جوشکاری استیل 316 و 316L در صنایع پتروشیمی، شیمیایی و دریایی که مقاومت به خوردگی حفره‌ای اهمیت دارد.',
			'm'     => array( '_elc_aws' => 'AWS A5.4 E316L-16', '_elc_iso' => 'ISO 3581-A: E 19 12 3 L R 12', '_elc_coating' => 'روتیلی-قلیایی', '_elc_diameters' => '2, 2.5, 3.25, 4', '_elc_length' => '300 / 350', '_elc_current' => 'AC / DC+', '_elc_amperage' => '40 – 140 A', '_elc_positions' => 'همه وضعیت‌ها به‌جز سرازیر', '_elc_tensile' => '≥ 490 MPa', '_elc_base_metal' => 'AISI 316, 316L', '_elc_packaging' => 'بسته ۲ کیلوگرمی', '_elc_drying' => '۳۰۰ درجه سانتی‌گراد به مدت ۱ ساعت' ),
		),
		array(
			'title' => 'الکترود استیل E309L-16 (غیرهمجنس)',
			'type'  => 'stainless',
			'brand' => 'arya',
			'feat'  => '',
			'ex'    => 'اتصال استیل به فولاد کربنی و لایه میانی روکش‌کاری.',
			'body'  => 'الکترود 309L برای اتصال فلزات غیرهمجنس (فولاد کربنی به استنلس) و به‌عنوان لایه بافر پیش از روکش استیل استفاده می‌شود.',
			'm'     => array( '_elc_aws' => 'AWS A5.4 E309L-16', '_elc_iso' => 'ISO 3581-A: E 23 12 L R 32', '_elc_coating' => 'روتیلی-قلیایی', '_elc_diameters' => '2.5, 3.25, 4', '_elc_length' => '350', '_elc_current' => 'AC / DC+', '_elc_amperage' => '50 – 150 A', '_elc_positions' => 'همه وضعیت‌ها به‌جز سرازیر', '_elc_tensile' => '≥ 520 MPa', '_elc_base_metal' => 'استیل + فولاد کربنی', '_elc_packaging' => 'بسته ۲ کیلوگرمی', '_elc_drying' => '۳۰۰ درجه سانتی‌گراد به مدت ۱ ساعت' ),
		),
		array(
			'title' => 'الکترود چدن ENi-CI (نیکل خالص)',
			'type'  => 'cast-iron',
			'brand' => 'pars',
			'feat'  => '1',
			'ex'    => 'تعمیر سرد قطعات چدنی؛ جوش نرم و قابل ماشین‌کاری.',
			'body'  => 'الکترود پایه نیکل برای تعمیر ترک‌های بلوک موتور، سرسیلندر، پوسته پمپ و قطعات چدن خاکستری. رسوب جوش قابل تراشکاری است.',
			'm'     => array( '_elc_aws' => 'AWS A5.15 ENi-CI', '_elc_iso' => 'ISO 1071: E C Ni-CI 3', '_elc_coating' => 'گرافیتی', '_elc_diameters' => '2.5, 3.25, 4', '_elc_length' => '300', '_elc_current' => 'AC / DC±', '_elc_amperage' => '60 – 140 A', '_elc_positions' => 'همه وضعیت‌ها به‌جز سرازیر', '_elc_tensile' => '≥ 300 MPa', '_elc_base_metal' => 'چدن خاکستری و داکتیل', '_elc_packaging' => 'بسته ۱ کیلوگرمی', '_elc_drying' => '۱۵۰ درجه سانتی‌گراد به مدت ۱ ساعت' ),
		),
		array(
			'title' => 'الکترود سخت‌کاری 60 راکول',
			'type'  => 'hardfacing',
			'brand' => 'arya',
			'feat'  => '',
			'ex'    => 'روکش سخت با سختی حدود 55 تا 60 HRC برای قطعات سایشی.',
			'body'  => 'برای روکش‌کاری دندانه بیل مکانیکی، فک سنگ‌شکن، تیغه بلدوزر و قطعاتی که در معرض سایش شدید و ضربه متوسط هستند.',
			'm'     => array( '_elc_aws' => 'DIN 8555: E6-UM-60', '_elc_coating' => 'روتیلی-قلیایی', '_elc_diameters' => '3.25, 4, 5', '_elc_length' => '350 / 450', '_elc_current' => 'AC / DC+', '_elc_amperage' => '90 – 230 A', '_elc_positions' => 'تخت و افقی', '_elc_tensile' => 'سختی ۵۵ تا ۶۰ HRC', '_elc_base_metal' => 'فولادهای کربنی و منگنزی', '_elc_packaging' => 'کارتن ۵ کیلوگرمی', '_elc_drying' => '۳۰۰ درجه سانتی‌گراد به مدت ۲ ساعت' ),
		),
		array(
			'title' => 'الکترود آلومینیوم E4043',
			'type'  => 'aluminum',
			'brand' => 'pars',
			'feat'  => '',
			'ex'    => 'تعمیر قطعات ریختگی آلومینیومی و آلیاژهای Al-Si.',
			'body'  => 'الکترود آلومینیوم-سیلیسیم برای تعمیر سرسیلندر، پوسته گیربکس و قطعات ریختگی آلومینیومی. پیش‌گرم قطعه توصیه می‌شود.',
			'm'     => array( '_elc_aws' => 'AWS A5.3 E4043', '_elc_coating' => 'نمکی', '_elc_diameters' => '2.5, 3.25, 4', '_elc_length' => '350', '_elc_current' => 'DC+', '_elc_amperage' => '60 – 150 A', '_elc_positions' => 'تخت و افقی', '_elc_tensile' => '≥ 95 MPa', '_elc_base_metal' => 'آلیاژهای Al-Si ریختگی', '_elc_packaging' => 'قوطی ۱ کیلوگرمی', '_elc_drying' => '۱۵۰ درجه سانتی‌گراد به مدت ۱ ساعت' ),
		),
	);

	$order = 0;
	foreach ( $products as $p ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'electrode',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_excerpt' => $p['ex'],
				'post_content' => $p['body'],
				'menu_order'   => $order++,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		if ( isset( $term_ids[ $p['type'] ] ) ) {
			wp_set_object_terms( $id, array( $term_ids[ $p['type'] ] ), 'electrode_type' );
		}
		wp_set_object_terms( $id, array( $p['brand'] ), 'electrode_brand' );
		foreach ( $p['m'] as $k => $v ) {
			update_post_meta( $id, $k, $v );
		}
		update_post_meta( $id, '_elc_stock', 'in' );
		update_post_meta( $id, '_elc_min_order', '۱ کارتن' );
		update_post_meta( $id, '_elc_featured', $p['feat'] );
	}

	// Pages.
	$pages = array(
		'contact' => array( 'تماس و استعلام قیمت', "<!-- wp:paragraph -->\n<p>برای دریافت قیمت روز و شرایط فروش عمده، فرم زیر را پر کنید یا مستقیم تماس بگیرید.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:shortcode -->\n[electrode_inquiry_form]\n<!-- /wp:shortcode -->" ),
		'about'   => array( 'درباره ما', "<!-- wp:paragraph -->\n<p>ما تأمین‌کننده انواع الکترود جوشکاری برای صنایع ساختمانی، نفت و گاز، پتروشیمی و تعمیرات هستیم. این متن نمونه است؛ از پیشخوان وردپرس آن را ویرایش کنید.</p>\n<!-- /wp:paragraph -->" ),
	);
	foreach ( $pages as $slug => $pg ) {
		if ( ! get_page_by_path( $slug ) ) {
			wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $pg[0],
					'post_content' => $pg[1],
				)
			);
		}
	}

	if ( ! get_option( 'elc_settings' ) ) {
		update_option(
			'elc_settings',
			array(
				'phone'        => '021-00000000',
				'mobile'       => '09120000000',
				'email'        => 'info@example.com',
				'address'      => 'تهران، بازار آهن (آدرس نمونه – از تنظیمات فروشگاه ویرایش کنید)',
				'hours'        => 'شنبه تا پنجشنبه، ۸ تا ۱۷',
				'notify_email' => get_option( 'admin_email' ),
				'map_lat'      => '35.6589',
				'map_lng'      => '51.2879',
				'map_zoom'     => '15',
				'teaser_title' => 'قدرت در هر اتصال',
				'teaser_text'  => 'الکترودهای استاندارد AWS برای ساختمان، نفت و گاز، پتروشیمی و تعمیرات صنعتی',
			)
		);
	}

	update_option( 'elc_seeded', 1 );
}
