<?php
/**
 * Plugin Name: Molienda Tienda
 * Description: Catálogo, ajustes y funciones a medida de la tienda de ejemplo Molienda: recomendador de molienda, perfil de taza, consejos de molienda, envíos y pago de prueba.
 * Version: 1.0.3
 * Author: Dania360
 * Requires Plugins: woocommerce
 * Text Domain: molienda-tienda
 */

defined( 'ABSPATH' ) || exit;

define( 'MOLIENDA_VER', '1.0.3' );
define( 'MOLIENDA_DIR', plugin_dir_path( __FILE__ ) );
define( 'MOLIENDA_URL', plugin_dir_url( __FILE__ ) );

require_once MOLIENDA_DIR . 'inc/datos.php';
require_once MOLIENDA_DIR . 'inc/montaje.php';
require_once MOLIENDA_DIR . 'inc/tienda.php';
