<?php
/**
 * Ficha de producto, página de tienda y portada: piezas a medida de Molienda.
 */
defined( 'ABSPATH' ) || exit;

/** Gramos de café por taza que se usan para calcular el precio por taza. */
const MOLIENDA_GRAMOS_TAZA = 15;

/** Importe a partir del cual el envío estándar es gratis. */
const MOLIENDA_ENVIO_GRATIS = 35;

/* ---------- Descripción para buscadores y para compartir ---------- */

function molienda_descripcion_pagina() {
	if ( is_front_page() ) {
		return 'Café de especialidad tostado en Murcia cada martes. Elige el origen, el peso y la molienda para tu cafetera. Envío gratis desde 35 €.';
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		return wp_strip_all_tags( get_post_field( 'post_excerpt', get_queried_object_id() ) );
	}
	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		return wp_strip_all_tags( term_description() );
	}
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return 'Cafés de origen, blends, ediciones limitadas y accesorios para preparar café en casa. En grano o molido para tu cafetera, en 250 g o 1 kg.';
	}
	return '';
}

add_action( 'wp_head', function () {
	$texto = trim( molienda_descripcion_pagina() );
	if ( '' === $texto ) {
		return;
	}
	$imagen = MOLIENDA_URL . 'assets/img/etiopia.jpg';
	if ( function_exists( 'is_product' ) && is_product() ) {
		$foto = get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
		if ( $foto ) {
			$imagen = $foto;
		}
	}
	echo '<meta name="description" content="' . esc_attr( $texto ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $texto ) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $imagen ) . '">' . "\n";
}, 2 );

/* ---------- Ficha de producto ---------- */

/** Devuelve los datos de origen de un café (los atributos que no son de variación). */
function molienda_origen( $product ) {
	$campos = array( 'Origen', 'Altitud', 'Variedad', 'Proceso', 'Tueste' );
	$datos  = array();
	foreach ( $product->get_attributes() as $atributo ) {
		if ( ! is_object( $atributo ) || $atributo->get_variation() ) {
			continue;
		}
		if ( in_array( $atributo->get_name(), $campos, true ) ) {
			$datos[ $atributo->get_name() ] = implode( ', ', $atributo->get_options() );
		}
	}
	return $datos;
}

/** Precio por taza: hueco que rellena el navegador al elegir el peso. */
add_action( 'woocommerce_before_add_to_cart_button', function () {
	global $product;
	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		return;
	}
	$peso = '';
	foreach ( $product->get_attributes() as $atributo ) {
		if ( is_object( $atributo ) && 'Peso' === $atributo->get_name() && ! $atributo->get_variation() ) {
			$peso = implode( '', $atributo->get_options() );
		}
	}
	echo '<p class="mol-taza" data-peso="' . esc_attr( $peso ) . '" data-gramos="' . (int) MOLIENDA_GRAMOS_TAZA . '" aria-live="polite"></p>';
}, 5 );

/** Compromisos de la tienda justo debajo del botón de compra. */
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product ) {
		return;
	}
	$cafe   = (bool) get_post_meta( $product->get_id(), '_molienda_notas', true );
	$lineas = array(
		$cafe ? 'Tostado el martes y enviado esa misma semana' : 'Sale del almacén en 24 horas',
		'Envío en 48-72 h, gratis desde ' . MOLIENDA_ENVIO_GRATIS . ' €',
		$cafe ? 'Bolsa con válvula y cierre zip, lista para guardar' : 'Cambios y devoluciones durante 30 días',
	);
	echo '<ul class="mol-confianza">';
	foreach ( $lineas as $linea ) {
		echo '<li><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10.5l4 4 8-9"/></svg>' . esc_html( $linea ) . '</li>';
	}
	echo '</ul>';
}, 31 );

/** Ficha de origen a la vista, sin tener que abrir una pestaña. */
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product ) {
		return;
	}
	$datos = molienda_origen( $product );
	if ( ! $datos ) {
		return;
	}
	echo '<div class="mol-origen"><p class="mol-origen__titulo">De dónde viene</p><dl>';
	foreach ( $datos as $nombre => $valor ) {
		echo '<div><dt>' . esc_html( $nombre ) . '</dt><dd>' . esc_html( $valor ) . '</dd></div>';
	}
	echo '</dl></div>';
}, 33 );

/** Pestañas: fuera la tabla de atributos (ya está en la ficha de origen), dentro envíos y conservación. */
add_filter( 'woocommerce_product_tabs', function ( $pestanas ) {
	global $product;
	unset( $pestanas['additional_information'] );
	if ( isset( $pestanas['description'] ) ) {
		$pestanas['description']['title'] = 'La historia';
	}
	if ( $product && get_post_meta( $product->get_id(), '_molienda_notas', true ) ) {
		$pestanas['molienda_conservar'] = array(
			'title'    => 'Cómo conservarlo',
			'priority' => 20,
			'callback' => function () {
				echo '<h2>Cómo conservarlo</h2><p>Guarda el café en su bolsa, bien cerrada, en un armario lejos de la luz y del calor. No hace falta nevera: la humedad le sienta peor que la temperatura.</p><p>En grano está en su mejor momento entre el día 7 y la semana 6 desde el tueste. Molido, conviene gastarlo en dos o tres semanas.</p>';
			},
		);
	}
	$pestanas['molienda_envios'] = array(
		'title'    => 'Envíos y cambios',
		'priority' => 30,
		'callback' => function () {
			echo '<h2>Envíos y cambios</h2><p>Envío estándar en 48-72 h por 3,90 €, gratis a partir de ' . (int) MOLIENDA_ENVIO_GRATIS . ' €. Envío exprés en 24 h por 6,90 €. Enviamos a toda España.</p><p>Si un café no te convence, escríbenos en los 30 días siguientes y te lo cambiamos por otro. Los accesorios se devuelven sin usar y en su caja.</p>';
		},
	);
	return $pestanas;
}, 20 );

add_filter( 'woocommerce_product_description_heading', function () {
	return 'La historia de este producto';
} );

add_filter( 'woocommerce_product_related_products_heading', function () {
	return 'También te puede gustar';
} );

/* ---------- Página de tienda ---------- */

/** Texto de presentación en la página general de la tienda. */
add_action( 'woocommerce_archive_description', function () {
	if ( is_shop() && ! is_search() ) {
		echo '<p class="mol-tienda__intro">Cafés tostados cada martes y todo lo necesario para prepararlos en casa. Elige el origen, el peso y la molienda para tu cafetera.</p>';
	}
}, 5 );

/** Accesos rápidos a las categorías encima de la rejilla. */
add_action( 'woocommerce_before_shop_loop', function () {
	if ( ! is_shop() && ! is_product_category() ) {
		return;
	}
	$actual = is_product_category() ? get_queried_object()->slug : '';
	echo '<nav class="mol-filtros" aria-label="Categorías de la tienda"><ul>';
	echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '"' . ( '' === $actual ? ' aria-current="page"' : '' ) . '>Todo</a></li>';
	foreach ( molienda_categorias() as $slug => $cat ) {
		$enlace = get_term_link( $slug, 'product_cat' );
		if ( is_wp_error( $enlace ) ) {
			continue;
		}
		echo '<li><a href="' . esc_url( $enlace ) . '"' . ( $slug === $actual ? ' aria-current="page"' : '' ) . '>' . esc_html( $cat[0] ) . '</a></li>';
	}
	echo '</ul></nav>';
}, 15 );

/* ---------- Portada: edición limitada ---------- */

add_shortcode( 'molienda_edicion', function () {
	$p = wc_get_product( molienda_id_por_slug( 'panama-geisha' ) );
	if ( ! $p ) {
		return '';
	}
	$notas = array_filter( array_map( 'trim', explode( '·', (string) get_post_meta( $p->get_id(), '_molienda_notas', true ) ) ) );
	$datos = molienda_origen( $p );
	ob_start();
	?>
	<section class="mol-edicion" aria-labelledby="mol-edicion-titulo">
		<div class="mol-edicion__in">
			<a class="mol-edicion__foto mol-revela" href="<?php echo esc_url( $p->get_permalink() ); ?>" tabindex="-1" aria-hidden="true">
				<?php echo $p->get_image( 'large', array( 'loading' => 'lazy', 'alt' => '' ) ); // phpcs:ignore ?>
				<span class="mol-edicion__sello">40 latas<br>por tueste</span>
			</a>
			<div class="mol-edicion__texto">
				<p class="mol-ceja mol-ceja--oscura"><span></span>Edición limitada</p>
				<h2 class="mol-titulo" id="mol-edicion-titulo"><?php echo esc_html( $p->get_name() ); ?>, <em>la variedad más premiada.</em></h2>
				<p class="mol-edicion__lead"><?php echo esc_html( $p->get_short_description() ); ?></p>
				<?php if ( $notas ) : ?>
					<ul class="mol-edicion__notas">
						<?php foreach ( $notas as $nota ) : ?>
							<li><?php echo esc_html( $nota ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( $datos ) : ?>
					<dl class="mol-edicion__datos">
						<?php foreach ( array( 'Origen', 'Altitud', 'Proceso' ) as $campo ) : ?>
							<?php if ( isset( $datos[ $campo ] ) ) : ?>
								<div><dt><?php echo esc_html( $campo ); ?></dt><dd><?php echo esc_html( $datos[ $campo ] ); ?></dd></div>
							<?php endif; ?>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<div class="mol-botones">
					<a class="mol-btn" href="<?php echo esc_url( $p->get_permalink() ); ?>">Pedir una lata · <?php echo wp_kses_post( wp_strip_all_tags( wc_price( $p->get_price() ) ) ); ?></a>
				</div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
} );
