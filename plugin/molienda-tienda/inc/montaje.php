<?php
/**
 * Montaje de la tienda por pasos: /?molienda_montar=1 hace el siguiente paso pendiente
 * y devuelve en JSON lo que ha hecho. Cuando termina, deja de responder.
 */
defined( 'ABSPATH' ) || exit;

const MOLIENDA_POR_PASO = 3;

add_action( 'wp_loaded', function () {
	if ( ! isset( $_GET['molienda_montar'] ) || get_option( 'molienda_listo' ) ) {
		return;
	}
	if ( ! class_exists( 'WooCommerce' ) ) {
		wp_send_json( array( 'error' => 'WooCommerce no está activo' ), 500 );
	}
	@set_time_limit( 120 );
	$paso      = (int) get_option( 'molienda_paso', 0 );
	$productos = molienda_productos();
	$hecho     = '';

	if ( 0 === $paso ) {
		molienda_ajustes();
		$hecho = 'ajustes, páginas, categorías, envíos, pago y cupón';
	} else {
		$inicio = ( $paso - 1 ) * MOLIENDA_POR_PASO;
		$lote   = array_slice( $productos, $inicio, MOLIENDA_POR_PASO );
		if ( $lote ) {
			$nombres = array();
			foreach ( $lote as $datos ) {
				molienda_crear_producto( $datos );
				$nombres[] = $datos['nombre'];
			}
			$hecho = 'productos: ' . implode( ', ', $nombres );
		} else {
			molienda_rematar();
			update_option( 'molienda_listo', current_time( 'mysql' ) );
			wp_send_json( array( 'listo' => true, 'hecho' => 'ventas cruzadas y enlaces permanentes' ) );
		}
	}
	update_option( 'molienda_paso', $paso + 1 );
	wp_send_json( array( 'listo' => false, 'paso' => $paso, 'hecho' => $hecho ) );
} );

/**
 * Ajustes de idioma: /?molienda_idioma=1 instala las traducciones oficiales en español
 * y pone en español las páginas que WooCommerce creó en inglés. Se puede repetir sin riesgo.
 */
add_action( 'wp_loaded', function () {
	if ( ! isset( $_GET['molienda_idioma'] ) ) {
		return;
	}
	// Segunda vuelta: direcciones de pedido y de la cuenta en español.
	if ( get_option( 'molienda_idioma_v1' ) ) {
		if ( get_option( 'molienda_idioma_v2' ) ) {
			return;
		}
		$fin = array(
			'woocommerce_checkout_order_received_endpoint'       => 'pedido-recibido',
			'woocommerce_checkout_pay_endpoint'                  => 'pagar-pedido',
			'woocommerce_myaccount_orders_endpoint'              => 'pedidos',
			'woocommerce_myaccount_view_order_endpoint'          => 'ver-pedido',
			'woocommerce_myaccount_downloads_endpoint'           => 'descargas',
			'woocommerce_myaccount_edit_account_endpoint'        => 'editar-cuenta',
			'woocommerce_myaccount_edit_address_endpoint'        => 'direcciones',
			'woocommerce_myaccount_payment_methods_endpoint'     => 'metodos-de-pago',
			'woocommerce_myaccount_lost_password_endpoint'       => 'contrasena-perdida',
			'woocommerce_logout_endpoint'                        => 'salir',
			'woocommerce_myaccount_add_payment_method_endpoint'  => 'anadir-metodo-de-pago',
			'woocommerce_myaccount_delete_payment_method_endpoint' => 'borrar-metodo-de-pago',
			'woocommerce_myaccount_set_default_payment_method_endpoint' => 'metodo-de-pago-por-defecto',
		);
		foreach ( $fin as $clave => $valor ) {
			update_option( $clave, $valor );
		}
		update_option( 'woocommerce_queue_flush_rewrite_rules', 'yes' );
		flush_rewrite_rules( false );
		update_option( 'molienda_idioma_v2', current_time( 'mysql' ) );
		wp_send_json( array( 'direcciones' => array_values( $fin ) ) );
	}
	@set_time_limit( 180 );
	require_once ABSPATH . 'wp-admin/includes/admin.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/translation-install.php';
	update_option( 'WPLANG', 'es_ES' );
	delete_site_transient( 'update_plugins' );
	delete_site_transient( 'update_themes' );
	wp_update_plugins();
	wp_update_themes();
	$traducciones = wp_get_translation_updates();
	$resultado    = array();
	if ( $traducciones ) {
		$mejora    = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin() );
		$resultado = $mejora->bulk_upgrade( $traducciones );
	}
	$paginas = array(
		'woocommerce_shop_page_id'      => array( 'Tienda', 'tienda' ),
		'woocommerce_cart_page_id'      => array( 'Carrito', 'carrito' ),
		'woocommerce_checkout_page_id'  => array( 'Finalizar compra', 'finalizar-compra' ),
		'woocommerce_myaccount_page_id' => array( 'Mi cuenta', 'mi-cuenta' ),
	);
	foreach ( $paginas as $opcion => $p ) {
		$id = (int) get_option( $opcion );
		if ( $id ) {
			wp_update_post( array( 'ID' => $id, 'post_title' => $p[0], 'post_name' => $p[1] ) );
		}
	}
	flush_rewrite_rules( false );
	update_option( 'molienda_idioma_v1', current_time( 'mysql' ) );
	wp_send_json( array( 'traducciones' => wp_list_pluck( $traducciones, 'slug' ), 'resultado' => $resultado ) );
} );

function molienda_ajustes() {
	// Tienda en España, en euros y con formato español.
	$opciones = array(
		'woocommerce_default_country'            => 'ES:MU',
		'woocommerce_store_city'                 => 'Murcia',
		'woocommerce_store_postcode'             => '30001',
		'woocommerce_currency'                   => 'EUR',
		'woocommerce_currency_pos'               => 'right_space',
		'woocommerce_price_thousand_sep'         => '.',
		'woocommerce_price_decimal_sep'          => ',',
		'woocommerce_price_num_decimals'         => 2,
		'woocommerce_allowed_countries'          => 'specific',
		'woocommerce_specific_allowed_countries' => array( 'ES' ),
		'woocommerce_ship_to_countries'          => '',
		'woocommerce_calc_taxes'                 => 'no',
		'woocommerce_enable_coupons'             => 'yes',
		'woocommerce_weight_unit'                => 'kg',
		'woocommerce_dimension_unit'             => 'cm',
		'woocommerce_enable_guest_checkout'      => 'yes',
		'woocommerce_enable_checkout_login_reminder' => 'yes',
		'woocommerce_enable_reviews'             => 'no',
		'woocommerce_demo_store'                 => 'yes',
		'woocommerce_demo_store_notice'          => 'Tienda de demostración hecha por Dania360: puedes comprar de prueba, no se cobra nada ni se envía nada.',
		'woocommerce_coming_soon'                => 'no',
		'woocommerce_store_pages_only'           => 'no',
		'woocommerce_email_from_name'            => 'Molienda',
		'woocommerce_email_from_address'         => 'hola@dania360.com',
		'woocommerce_task_list_hidden'           => 'yes',
		'woocommerce_task_list_complete'         => 'yes',
		'woocommerce_show_marketplace_suggestions' => 'no',
		'woocommerce_allow_tracking'             => 'no',
		'woocommerce_cart_redirect_after_add'    => 'no',
		'woocommerce_enable_ajax_add_to_cart'    => 'yes',
		'blogdescription'                        => 'Café de especialidad tostado en Murcia',
		'timezone_string'                        => 'Europe/Madrid',
		'date_format'                            => 'j \d\e F \d\e Y',
		'blog_public'                            => 0,
	);
	foreach ( $opciones as $clave => $valor ) {
		update_option( $clave, $valor );
	}
	update_option( 'woocommerce_onboarding_profile', array( 'skipped' => true, 'completed' => true ) );

	// Enlaces bonitos.
	update_option( 'permalink_structure', '/%postname%/' );
	update_option( 'woocommerce_permalinks', array( 'product_base' => 'producto', 'category_base' => 'categoria-producto', 'tag_base' => 'etiqueta-producto', 'attribute_base' => '', 'use_verbose_page_rules' => false ) );

	// Páginas de tienda, carrito, pago y cuenta.
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
	}

	// Categorías.
	foreach ( molienda_categorias() as $slug => $cat ) {
		if ( ! term_exists( $slug, 'product_cat' ) ) {
			wp_insert_term( $cat[0], 'product_cat', array( 'slug' => $slug, 'description' => $cat[1] ) );
		}
	}
	// La categoría "Sin categorizar" no hace falta en una tienda de ejemplo.
	$sin = get_term_by( 'slug', 'uncategorized', 'product_cat' ) ?: get_term_by( 'slug', 'sin-categorizar', 'product_cat' );
	if ( $sin ) {
		wp_update_term( $sin->term_id, 'product_cat', array( 'name' => 'Otros' ) );
	}

	molienda_envios();
	molienda_pagos();

	// Cupón de bienvenida.
	if ( ! wc_get_coupon_id_by_code( 'bienvenida10' ) ) {
		$cupon = new WC_Coupon();
		$cupon->set_code( 'bienvenida10' );
		$cupon->set_discount_type( 'percent' );
		$cupon->set_amount( 10 );
		$cupon->set_individual_use( true );
		$cupon->set_description( 'Cupón de bienvenida del 10 % para la tienda de ejemplo.' );
		$cupon->save();
	}
}

function molienda_envios() {
	if ( get_option( 'molienda_envio_estandar' ) ) {
		return;
	}
	$zona = new WC_Shipping_Zone();
	$zona->set_zone_name( 'España' );
	$zona->set_zone_order( 1 );
	$zona->add_location( 'ES', 'country' );
	$zona->save();

	$estandar = $zona->add_shipping_method( 'flat_rate' );
	update_option( 'woocommerce_flat_rate_' . $estandar . '_settings', array( 'title' => 'Envío estándar (48-72 h)', 'tax_status' => 'none', 'cost' => '3.90' ) );
	$gratis = $zona->add_shipping_method( 'free_shipping' );
	update_option( 'woocommerce_free_shipping_' . $gratis . '_settings', array( 'title' => 'Envío gratis (48-72 h)', 'requires' => 'min_amount', 'min_amount' => '35', 'ignore_discounts' => 'no' ) );
	$expres = $zona->add_shipping_method( 'flat_rate' );
	update_option( 'woocommerce_flat_rate_' . $expres . '_settings', array( 'title' => 'Envío exprés (24 h)', 'tax_status' => 'none', 'cost' => '6.90' ) );

	update_option( 'molienda_envio_estandar', $estandar );
	WC_Cache_Helper::get_transient_version( 'shipping', true );
}

function molienda_pagos() {
	update_option( 'woocommerce_cheque_settings', array(
		'enabled'      => 'yes',
		'title'        => 'Pago de prueba',
		'description'  => 'Tienda de demostración: no se pide tarjeta y no se cobra nada. Al confirmar verás cómo termina un pedido real.',
		'instructions' => 'Este pedido es de prueba: no se ha cobrado nada y no se enviará nada.',
	) );
	foreach ( array( 'bacs', 'cod' ) as $pasarela ) {
		$ajustes            = get_option( 'woocommerce_' . $pasarela . '_settings', array() );
		$ajustes['enabled'] = 'no';
		update_option( 'woocommerce_' . $pasarela . '_settings', $ajustes );
	}
	update_option( 'woocommerce_gateway_order', array( 'cheque' => 0, 'bacs' => 1, 'cod' => 2 ) );
}

function molienda_imagen( $archivo, $titulo ) {
	$ya = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_molienda_origen', 'meta_value' => $archivo, 'fields' => 'ids', 'numberposts' => 1, 'post_status' => 'any' ) );
	if ( $ya ) {
		return (int) $ya[0];
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$subida = wp_upload_bits( $archivo, null, file_get_contents( MOLIENDA_DIR . 'assets/img/' . $archivo ) );
	if ( ! empty( $subida['error'] ) ) {
		return 0;
	}
	$id = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => $titulo, 'post_status' => 'inherit' ), $subida['file'] );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $subida['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $titulo );
	update_post_meta( $id, '_molienda_origen', $archivo );
	return (int) $id;
}

function molienda_ids_categorias( $slugs ) {
	$ids = array();
	foreach ( $slugs as $slug ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $t ) {
			$ids[] = $t->term_id;
		}
	}
	return $ids;
}

function molienda_atributo( $nombre, $opciones, $posicion, $variacion ) {
	$a = new WC_Product_Attribute();
	$a->set_id( 0 );
	$a->set_name( $nombre );
	$a->set_options( (array) $opciones );
	$a->set_position( $posicion );
	$a->set_visible( true );
	$a->set_variation( $variacion );
	return $a;
}

function molienda_crear_producto( $d ) {
	if ( get_page_by_path( $d['slug'], OBJECT, 'product' ) ) {
		return;
	}
	$imagen = molienda_imagen( $d['img'], $d['nombre'] );
	$simple = ( 'simple' === $d['tipo'] );
	$p      = $simple ? new WC_Product_Simple() : new WC_Product_Variable();

	$p->set_name( $d['nombre'] );
	$p->set_slug( $d['slug'] );
	$p->set_status( 'publish' );
	$p->set_catalog_visibility( 'visible' );
	$p->set_short_description( $d['corta'] );
	$p->set_description( $d['larga'] );
	$p->set_category_ids( molienda_ids_categorias( $d['cat'] ) );
	$p->set_menu_order( $d['orden'] );
	$p->set_featured( ! empty( $d['destacado'] ) );
	$p->set_reviews_allowed( false );
	if ( $imagen ) {
		$p->set_image_id( $imagen );
	}

	if ( $simple ) {
		$p->set_regular_price( (string) $d['precio'] );
		if ( ! empty( $d['oferta'] ) ) {
			$p->set_sale_price( (string) $d['oferta'] );
		}
		$p->set_weight( (string) $d['peso'] );
		$p->set_stock_status( 'instock' );
		$p->save();
		return;
	}

	$pesos  = array_keys( $d['precios'] );
	$attrs  = array( molienda_atributo( 'Molienda', MOLIENDA_MOLIENDAS, 0, true ) );
	$varias = count( $pesos ) > 1;
	$attrs[] = molienda_atributo( 'Peso', $pesos, 1, $varias );
	$pos     = 2;
	foreach ( $d['ficha'] as $nombre => $valor ) {
		$attrs[] = molienda_atributo( $nombre, array( $valor ), $pos++, false );
	}
	$attrs[] = molienda_atributo( 'Notas de cata', array( $d['notas'] ), $pos, false );
	$p->set_attributes( $attrs );
	$por_defecto = array( 'molienda' => 'En grano' );
	if ( $varias ) {
		$por_defecto['peso'] = $pesos[0];
	}
	$p->set_default_attributes( $por_defecto );
	$id = $p->save();

	update_post_meta( $id, '_molienda_notas', $d['notas'] );
	update_post_meta( $id, '_molienda_perfil', $d['perfil'] );

	foreach ( MOLIENDA_MOLIENDAS as $molienda ) {
		foreach ( $d['precios'] as $peso => $precio ) {
			$v      = new WC_Product_Variation();
			$v->set_parent_id( $id );
			$atribs = array( 'molienda' => $molienda );
			if ( $varias ) {
				$atribs['peso'] = $peso;
			}
			$v->set_attributes( $atribs );
			$v->set_regular_price( (string) $precio[0] );
			if ( isset( $precio[1] ) ) {
				$v->set_sale_price( (string) $precio[1] );
			}
			$v->set_weight( '1 kg' === $peso ? '1.05' : ( '100 g' === $peso ? '0.2' : '0.3' ) );
			$v->set_stock_status( 'instock' );
			$v->save();
		}
	}
	WC_Product_Variable::sync( $id );
	wc_delete_product_transients( $id );
}

function molienda_rematar() {
	// Ventas cruzadas en el carrito: con cada café, el gotero y el molinillo.
	$accesorios = array_filter( array_map( 'molienda_id_por_slug', array( 'gotero-ceramica-v60', 'molinillo-manual' ) ) );
	foreach ( molienda_productos() as $d ) {
		if ( 'simple' === $d['tipo'] ) {
			continue;
		}
		$p = wc_get_product( molienda_id_por_slug( $d['slug'] ) );
		if ( $p ) {
			$p->set_cross_sell_ids( $accesorios );
			$p->save();
		}
	}
	flush_rewrite_rules( false );
}

function molienda_id_por_slug( $slug ) {
	$post = get_page_by_path( $slug, OBJECT, 'product' );
	return $post ? (int) $post->ID : 0;
}
