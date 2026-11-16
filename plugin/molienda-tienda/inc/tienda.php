<?php
/**
 * Funciones visibles de la tienda Molienda.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'molienda-tienda', MOLIENDA_URL . 'assets/molienda-tienda.css', array(), MOLIENDA_VER );
	wp_enqueue_script( 'molienda-tienda', MOLIENDA_URL . 'assets/molienda-tienda.js', array(), MOLIENDA_VER, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script( 'molienda-tienda', 'MOLIENDA', array( 'consejos' => molienda_consejos() ) );
} );

/** Frase de ayuda que aparece al elegir cada molienda en la ficha del café. */
function molienda_consejos() {
	return array(
		'En grano'              => 'En grano aguanta semanas: muélelo justo antes de prepararlo.',
		'Espresso'              => 'Molienda fina, como azúcar glas. Para cafeteras de espresso con portafiltro.',
		'Moka italiana'         => 'Molienda media fina, como sal de mesa. Para la cafetera italiana de toda la vida.',
		'Filtro (V60 y Chemex)' => 'Molienda media, como arena de playa. Para goteo, V60, Chemex y cafeteras de filtro.',
		'Prensa francesa'       => 'Molienda gruesa, como sal gorda. Para prensa francesa y cold brew.',
	);
}

/** Recomendador: [molienda_metodo] */
add_shortcode( 'molienda_metodo', function () {
	$metodos = molienda_metodos();
	$iconos  = array(
		'espresso' => '<path d="M8 12h14v6a7 7 0 0 1-14 0zM22 13h2a3 3 0 0 1 0 6h-2M6 28h18M12 4c-1 2 1 3 0 5M17 4c-1 2 1 3 0 5"/>',
		'moka'     => '<path d="M10 28h12l-2-10h-8zM12 18l-1-6h10l-1 6M11 12l2-6h6l2 6M21 14l4-1v6l-4 2M16 6V3"/>',
		'filtro'   => '<path d="M6 8h20l-7 10h-6zM13 18v3M19 18v3M9 21h14v5a2 2 0 0 1-2 2H11a2 2 0 0 1-2-2z"/>',
		'prensa'   => '<path d="M9 9h14v19H9zM16 3v15M11 18h10M12 3h8M23 13h3v9h-3"/>',
		'grano'    => '<path d="M16 4c-6 3-9 7-9 12s3 9 9 12c6-3 9-7 9-12s-3-9-9-12z"/><path d="M16 5c-2 4-2 8 0 11s2 8 0 11"/>',
	);
	$escala = array( 'En grano', 'Fina', 'Media fina', 'Media', 'Gruesa' );
	ob_start();
	?>
	<section class="mol-met" id="molienda" aria-labelledby="mol-met-titulo">
		<div class="mol-met__in">
			<div class="mol-met__cab">
				<p class="mol-ceja"><span></span>Encuentra tu molienda</p>
				<h2 class="mol-titulo" id="mol-met-titulo">¿Cómo preparas <em>tu café?</em></h2>
				<p>Elige tu cafetera y te decimos qué molienda pedir, cómo prepararlo y qué cafés te van a gustar más.</p>
			</div>
			<div class="mol-met__botones" role="tablist" aria-label="Método de preparación">
				<?php $i = 0; foreach ( $metodos as $clave => $m ) : ?>
					<button type="button" role="tab" id="mol-tab-<?php echo esc_attr( $clave ); ?>" aria-controls="mol-panel-<?php echo esc_attr( $clave ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
						<svg viewBox="0 0 32 32" aria-hidden="true"><?php echo $iconos[ $clave ]; // phpcs:ignore ?></svg>
						<span><?php echo esc_html( $m['nombre'] ); ?></span>
					</button>
				<?php $i++; endforeach; ?>
			</div>
			<?php $i = 0; foreach ( $metodos as $clave => $m ) : ?>
				<div class="mol-met__panel" role="tabpanel" id="mol-panel-<?php echo esc_attr( $clave ); ?>" aria-labelledby="mol-tab-<?php echo esc_attr( $clave ); ?>" <?php echo 0 === $i ? '' : 'hidden'; ?>>
					<div class="mol-met__receta">
						<p class="mol-met__pide">Pídelo con molienda</p>
						<p class="mol-met__molienda"><?php echo esc_html( $m['molienda'] ); ?></p>
						<div class="mol-met__escala" aria-label="Grosor de la molienda: <?php echo esc_attr( $escala[ $m['escala'] ] ); ?>">
							<?php foreach ( $escala as $n => $txt ) : ?>
								<span class="<?php echo $n === $m['escala'] ? 'activo' : ''; ?>"><i style="--t:<?php echo (int) ( 4 + $n * 3 ); ?>px"></i><?php echo esc_html( $txt ); ?></span>
							<?php endforeach; ?>
						</div>
						<dl class="mol-met__datos">
							<?php foreach ( $m['receta'] as $k => $v ) : ?>
								<div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
						<p class="mol-met__consejo"><b>Truco:</b> <?php echo esc_html( $m['consejo'] ); ?></p>
					</div>
					<div class="mol-met__cafes">
						<p class="mol-met__pide">Te van a gustar</p>
						<?php foreach ( $m['cafes'] as $slug ) :
							$p = wc_get_product( molienda_id_por_slug( $slug ) );
							if ( ! $p ) {
								continue;
							}
							$url = add_query_arg( 'attribute_molienda', rawurlencode( $m['molienda'] ), $p->get_permalink() );
							?>
							<a class="mol-met__cafe" href="<?php echo esc_url( $url ); ?>">
								<?php echo $p->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore ?>
								<span>
									<b><?php echo esc_html( $p->get_name() ); ?></b>
									<small><?php echo esc_html( get_post_meta( $p->get_id(), '_molienda_notas', true ) ); ?></small>
									<em><?php echo wp_kses_post( $p->get_price_html() ); ?></em>
									<strong>Pedirlo molido para <?php echo esc_html( mb_strtolower( $m['corto'] ) ); ?> →</strong>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php $i++; endforeach; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
} );

/** Perfil de taza (acidez, cuerpo y dulzor) y notas de cata en la ficha del café. */
function molienda_perfil_html( $id ) {
	$perfil = get_post_meta( $id, '_molienda_perfil', true );
	$notas  = get_post_meta( $id, '_molienda_notas', true );
	if ( ! $perfil || ! $notas ) {
		return '';
	}
	$html = '<div class="mol-perfil"><p class="mol-perfil__titulo">En la taza</p><ul class="mol-perfil__notas">';
	foreach ( explode( '·', $notas ) as $nota ) {
		$html .= '<li>' . esc_html( trim( $nota ) ) . '</li>';
	}
	$html .= '</ul><dl class="mol-perfil__barras">';
	foreach ( array( 'Acidez', 'Cuerpo', 'Dulzor' ) as $n => $nombre ) {
		$valor = (int) $perfil[ $n ];
		$html .= '<div><dt>' . $nombre . '</dt><dd aria-label="' . $valor . ' de 5"><span style="--v:' . $valor . '"></span></dd></div>';
	}
	return $html . '</dl></div>';
}

add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( $product ) {
		echo molienda_perfil_html( $product->get_id() ); // phpcs:ignore
	}
}, 25 );

/** Consejo de molienda bajo los desplegables de variación. */
add_action( 'woocommerce_before_add_to_cart_button', function () {
	global $product;
	if ( $product && $product->is_type( 'variable' ) ) {
		echo '<p class="mol-consejo" aria-live="polite"></p>';
	}
} );

/** Notas de cata bajo el nombre en las tarjetas de producto. */
add_action( 'woocommerce_after_shop_loop_item_title', function () {
	$notas = get_post_meta( get_the_ID(), '_molienda_notas', true );
	if ( $notas ) {
		echo '<p class="mol-tarjeta-notas">' . esc_html( $notas ) . '</p>';
	}
}, 6 );

/** En las tarjetas de cafés, el botón dice "Elegir molienda". */
add_filter( 'woocommerce_product_add_to_cart_text', function ( $texto, $product ) {
	return $product->is_type( 'variable' ) ? 'Elegir molienda' : $texto;
}, 10, 2 );

/** Si el pedido tiene envío gratis, no mostrar el estándar de pago (sí el exprés). */
add_filter( 'woocommerce_package_rates', function ( $tarifas ) {
	$hay_gratis = false;
	foreach ( $tarifas as $t ) {
		if ( 'free_shipping' === $t->get_method_id() ) {
			$hay_gratis = true;
		}
	}
	if ( $hay_gratis ) {
		$estandar = (int) get_option( 'molienda_envio_estandar' );
		foreach ( $tarifas as $clave => $t ) {
			if ( 'flat_rate' === $t->get_method_id() && (int) $t->get_instance_id() === $estandar ) {
				unset( $tarifas[ $clave ] );
			}
		}
	}
	return $tarifas;
}, 100 );

/** El pago de prueba deja el pedido como "Procesando", igual que un pago con tarjeta. */
add_filter( 'woocommerce_cheque_process_payment_order_status', function () {
	return 'processing';
} );

/** Sin referencias (SKU) en una tienda de ejemplo: así no sale "SKU: N/D". */
add_filter( 'wc_product_sku_enabled', '__return_false' );

/** Comillas normales en lugar de « » en todos los textos de la tienda. */
function molienda_comillas( $texto ) {
	return ( is_string( $texto ) && false !== strpbrk( $texto, '«»' ) ) ? str_replace( array( '«', '»' ), '"', $texto ) : $texto;
}
add_filter( 'gettext', 'molienda_comillas', 99 );
add_filter( 'ngettext', 'molienda_comillas', 99 );
add_filter( 'gettext_with_context', 'molienda_comillas', 99 );
add_filter( 'woocommerce_add_message', 'molienda_comillas', 99 );
add_filter( 'woocommerce_add_success', 'molienda_comillas', 99 );
add_filter( 'woocommerce_add_error', 'molienda_comillas', 99 );
add_filter( 'woocommerce_add_notice', 'molienda_comillas', 99 );
/* Los textos de los bloques del carrito y del pago se traducen en el navegador. */
add_action( 'wp_enqueue_scripts', function () {
	$js = "(function(){if(!window.wp||!wp.hooks)return;var f=function(t){return typeof t==='string'?t.replace(/[\\u00ab\\u00bb]/g,'\"'):t;};['i18n.gettext','i18n.ngettext','i18n.gettext_with_context','i18n.ngettext_with_context'].forEach(function(h){wp.hooks.addFilter(h,'molienda/comillas',f,99);});})();";
	wp_add_inline_script( 'wp-i18n', $js, 'after' );
}, 1 );
add_filter( 'load_script_translations', function ( $json ) {
	return is_string( $json ) ? str_replace( array( '«', '»', '«', '»' ), '\"', $json ) : $json;
}, 99 );

/** Las páginas se revalidan siempre: así nadie ve precios o carritos antiguos guardados en el navegador. */
add_action( 'send_headers', function () {
	if ( ! is_admin() ) {
		header( 'Cache-Control: no-cache, must-revalidate, max-age=0', true );
	}
}, 99 );

/** Tienda de ejemplo: que no aparezca en buscadores. */
add_filter( 'wp_robots', function ( $robots ) {
	$robots['noindex']  = true;
	$robots['nofollow'] = true;
	return $robots;
} );
