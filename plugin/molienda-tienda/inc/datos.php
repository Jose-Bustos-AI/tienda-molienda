<?php
/**
 * Catálogo de la tienda de ejemplo Molienda.
 */
defined( 'ABSPATH' ) || exit;

const MOLIENDA_MOLIENDAS = array( 'En grano', 'Espresso', 'Moka italiana', 'Filtro (V60 y Chemex)', 'Prensa francesa' );

function molienda_categorias() {
	return array(
		'cafes-de-origen'      => array( 'Cafés de origen', 'Cafés de un solo origen, de finca o cooperativa, tostados para que se note de dónde vienen.' ),
		'blends'               => array( 'Blends', 'Mezclas pensadas para el espresso y el café con leche de cada día.' ),
		'ediciones-limitadas'  => array( 'Ediciones limitadas', 'Microlotes y cajas especiales. Cuando se acaban, se acaban.' ),
		'metodos-y-accesorios' => array( 'Métodos y accesorios', 'Todo lo necesario para preparar en casa un café como el de la cafetería.' ),
	);
}

/**
 * Cada café variable lleva: precios por peso (250 g y 1 kg), ficha de origen,
 * notas de cata y un perfil de taza del 1 al 5.
 */
function molienda_productos() {
	return array(
		array(
			'slug' => 'etiopia-yirgacheffe', 'nombre' => 'Etiopía Yirgacheffe', 'tipo' => 'cafe', 'cat' => array( 'cafes-de-origen' ), 'img' => 'etiopia.jpg', 'orden' => 1, 'destacado' => true,
			'precios' => array( '250 g' => array( 13.90 ), '1 kg' => array( 44.90 ) ),
			'corta' => 'Floral y luminoso, como una taza de té de jazmín con un punto de limón. Perfecto en filtro.',
			'larga' => 'Lote lavado de pequeños productores de Kochere, en la región de Yirgacheffe. Las cerezas se despulpan el mismo día y fermentan 36 horas antes de secarse en camas africanas. Lo tostamos claro para respetar su acidez cítrica y su aroma floral.',
			'notas' => 'Jazmín · bergamota · limón', 'perfil' => array( 5, 2, 4 ),
			'ficha' => array( 'Origen' => 'Kochere, Yirgacheffe (Etiopía)', 'Altitud' => '1.900 - 2.200 m', 'Variedad' => 'Heirloom etíope', 'Proceso' => 'Lavado', 'Tueste' => 'Claro' ),
		),
		array(
			'slug' => 'colombia-huila', 'nombre' => 'Colombia Huila', 'tipo' => 'cafe', 'cat' => array( 'cafes-de-origen' ), 'img' => 'colombia.jpg', 'orden' => 2,
			'precios' => array( '250 g' => array( 12.50 ), '1 kg' => array( 39.90 ) ),
			'corta' => 'Dulce y redondo, con caramelo y manzana roja. El café que gusta a todo el mundo.',
			'larga' => 'De la finca familiar La Esperanza, en Pitalito (Huila). Cosecha seleccionada a mano y secada en marquesinas. Un café equilibrado que funciona igual de bien en espresso que en prensa francesa.',
			'notas' => 'Caramelo · manzana roja · panela', 'perfil' => array( 3, 3, 5 ),
			'ficha' => array( 'Origen' => 'Pitalito, Huila (Colombia)', 'Altitud' => '1.600 - 1.800 m', 'Variedad' => 'Caturra y Castillo', 'Proceso' => 'Lavado', 'Tueste' => 'Medio claro' ),
		),
		array(
			'slug' => 'kenia-nyeri-aa', 'nombre' => 'Kenia Nyeri AA', 'tipo' => 'cafe', 'cat' => array( 'cafes-de-origen' ), 'img' => 'kenia.jpg', 'orden' => 3,
			'precios' => array( '250 g' => array( 15.90 ), '1 kg' => array( 52.90 ) ),
			'corta' => 'Jugoso e intenso: grosella negra y pomelo. Para quien busca un café que sorprenda.',
			'larga' => 'Grano de tamaño AA de la estación de lavado de Gatomboya, en Nyeri. Doble fermentación y lavado al estilo keniano, que le da esa acidez brillante y ese cuerpo jugoso tan característicos.',
			'notas' => 'Grosella negra · pomelo · tomate dulce', 'perfil' => array( 5, 3, 3 ),
			'ficha' => array( 'Origen' => 'Nyeri (Kenia)', 'Altitud' => '1.700 - 1.900 m', 'Variedad' => 'SL28 y SL34', 'Proceso' => 'Lavado doble', 'Tueste' => 'Claro' ),
		),
		array(
			'slug' => 'brasil-cerrado', 'nombre' => 'Brasil Cerrado', 'tipo' => 'cafe', 'cat' => array( 'cafes-de-origen' ), 'img' => 'brasil.jpg', 'orden' => 4,
			'precios' => array( '250 g' => array( 10.90 ), '1 kg' => array( 34.90 ) ),
			'corta' => 'Chocolate con leche y avellana, con mucho cuerpo y poca acidez. Ideal con leche.',
			'larga' => 'Proceso natural de la región del Cerrado Mineiro: las cerezas se secan enteras al sol, lo que le da un dulzor a chocolate y un cuerpo cremoso. Es la base de nuestro blend de espresso.',
			'notas' => 'Chocolate con leche · avellana · cacahuete', 'perfil' => array( 2, 4, 4 ),
			'ficha' => array( 'Origen' => 'Cerrado Mineiro (Brasil)', 'Altitud' => '1.000 - 1.200 m', 'Variedad' => 'Mundo Novo y Catuaí', 'Proceso' => 'Natural', 'Tueste' => 'Medio' ),
		),
		array(
			'slug' => 'guatemala-antigua', 'nombre' => 'Guatemala Antigua', 'tipo' => 'cafe', 'cat' => array( 'cafes-de-origen' ), 'img' => 'guatemala.jpg', 'orden' => 5,
			'precios' => array( '250 g' => array( 12.90 ), '1 kg' => array( 41.90 ) ),
			'corta' => 'Cacao, canela y piel de naranja. Un café de sobremesa con mucha personalidad.',
			'larga' => 'Bourbon cultivado a la sombra en las faldas del volcán de Agua, en Antigua. El suelo volcánico y las noches frías le dan una dulzura especiada que brilla en moka y en prensa francesa.',
			'notas' => 'Cacao · canela · naranja', 'perfil' => array( 3, 4, 4 ),
			'ficha' => array( 'Origen' => 'Antigua (Guatemala)', 'Altitud' => '1.500 - 1.700 m', 'Variedad' => 'Bourbon', 'Proceso' => 'Lavado', 'Tueste' => 'Medio' ),
		),
		array(
			'slug' => 'descafeinado-colombia', 'nombre' => 'Descafeinado Colombia', 'tipo' => 'cafe', 'cat' => array( 'cafes-de-origen' ), 'img' => 'descafeinado.jpg', 'orden' => 6,
			'precios' => array( '250 g' => array( 12.90 ), '1 kg' => array( 41.90 ) ),
			'corta' => 'Todo el sabor y nada de cafeína: panela, cacao y ciruela. Para el café de la noche.',
			'larga' => 'Descafeinado con el proceso natural de caña de azúcar, sin disolventes químicos. Conserva el dulzor del café colombiano y deja menos del 0,1 % de cafeína.',
			'notas' => 'Panela · cacao · ciruela', 'perfil' => array( 2, 3, 4 ),
			'ficha' => array( 'Origen' => 'Tolima (Colombia)', 'Altitud' => '1.500 - 1.900 m', 'Variedad' => 'Caturra y Colombia', 'Proceso' => 'Lavado, descafeinado con caña de azúcar', 'Tueste' => 'Medio' ),
		),
		array(
			'slug' => 'blend-huerta', 'nombre' => 'Blend Huerta', 'tipo' => 'cafe', 'cat' => array( 'blends' ), 'img' => 'huerta.jpg', 'orden' => 7, 'destacado' => true,
			'precios' => array( '250 g' => array( 11.50 ), '1 kg' => array( 36.90, 32.90 ) ),
			'corta' => 'Nuestro espresso de la casa: chocolate negro, caramelo y una crema densa. En oferta en formato de 1 kg.',
			'larga' => 'Un 60 % de Brasil Cerrado y un 40 % de Colombia Huila, tostados por separado y mezclados después. Pensado para cafeteras de espresso y para el café con leche: dulce, con cuerpo y sin amargor.',
			'notas' => 'Chocolate negro · caramelo · nuez', 'perfil' => array( 2, 5, 4 ),
			'ficha' => array( 'Origen' => 'Brasil 60 % y Colombia 40 %', 'Altitud' => '1.000 - 1.800 m', 'Variedad' => 'Mundo Novo, Catuaí y Caturra', 'Proceso' => 'Natural y lavado', 'Tueste' => 'Medio oscuro' ),
		),
		array(
			'slug' => 'panama-geisha', 'nombre' => 'Panamá Geisha', 'tipo' => 'geisha', 'cat' => array( 'ediciones-limitadas' ), 'img' => 'geisha.jpg', 'orden' => 8, 'destacado' => true,
			'precios' => array( '100 g' => array( 24.90 ) ),
			'corta' => 'Microlote de Geisha en lata de 100 g: bergamota, melocotón y jazmín. Solo 40 latas por tueste.',
			'larga' => 'La variedad más premiada del mundo, cultivada en Boquete (Panamá) a 1.700 metros. Proceso natural de secado lento en camas elevadas. Lo tostamos muy claro y lo servimos en lata hermética de 100 g.',
			'notas' => 'Bergamota · melocotón · jazmín', 'perfil' => array( 5, 2, 5 ),
			'ficha' => array( 'Origen' => 'Boquete, Chiriquí (Panamá)', 'Altitud' => '1.700 m', 'Variedad' => 'Geisha', 'Proceso' => 'Natural', 'Tueste' => 'Muy claro' ),
		),
		array(
			'slug' => 'caja-descubre', 'nombre' => 'Caja Descubre', 'tipo' => 'simple', 'cat' => array( 'ediciones-limitadas' ), 'img' => 'caja.jpg', 'orden' => 9, 'destacado' => true,
			'precio' => 39.30, 'oferta' => 34.90, 'peso' => 0.45,
			'corta' => 'Tres orígenes en bolsas de 100 g (Etiopía, Colombia y Brasil) con tarjeta de cata. Ahorras 4,40 €.',
			'larga' => 'La mejor forma de empezar en el café de especialidad: tres cafés muy distintos para comparar en casa. Incluye una tarjeta de cata para apuntar lo que notas en cada uno. Se envía en grano; si la quieres molida, dínoslo en las notas del pedido.',
		),
		array(
			'slug' => 'gotero-ceramica-v60', 'nombre' => 'Gotero de cerámica con jarra', 'tipo' => 'simple', 'cat' => array( 'metodos-y-accesorios' ), 'img' => 'v60.jpg', 'orden' => 10,
			'precio' => 26.90, 'peso' => 0.6,
			'corta' => 'Gotero cónico de cerámica con jarra de vidrio de 600 ml. El método más fácil para un café de filtro limpio.',
			'larga' => 'Cerámica gruesa que mantiene la temperatura durante toda la extracción y jarra de vidrio de borosilicato apta para el fuego. Incluye 40 filtros de papel.',
		),
		array(
			'slug' => 'molinillo-manual', 'nombre' => 'Molinillo manual de muelas', 'tipo' => 'simple', 'cat' => array( 'metodos-y-accesorios' ), 'img' => 'molinillo.jpg', 'orden' => 11,
			'precio' => 69.90, 'oferta' => 59.90, 'peso' => 0.55,
			'corta' => 'Muelas cónicas de acero y 40 ajustes de molienda, del espresso a la prensa francesa.',
			'larga' => 'Cuerpo de acero inoxidable y pomo de nogal. Las muelas cónicas muelen de forma uniforme sin calentar el café. Moler justo antes de preparar es el cambio que más se nota en la taza.',
		),
		array(
			'slug' => 'prensa-francesa', 'nombre' => 'Prensa francesa 600 ml', 'tipo' => 'simple', 'cat' => array( 'metodos-y-accesorios' ), 'img' => 'prensa.jpg', 'orden' => 12,
			'precio' => 29.90, 'peso' => 0.7,
			'corta' => 'Vidrio de borosilicato y acero negro mate. Café con cuerpo en cuatro minutos, sin filtros de papel.',
			'larga' => 'Filtro de malla de acero de triple capa que deja pasar los aceites del café y retiene los posos. Capacidad para tres tazas.',
		),
		array(
			'slug' => 'taza-artesana', 'nombre' => 'Taza artesana de gres', 'tipo' => 'simple', 'cat' => array( 'metodos-y-accesorios' ), 'img' => 'taza.jpg', 'orden' => 13,
			'precio' => 14.90, 'peso' => 0.35,
			'corta' => 'Gres esmaltado a mano con borde terracota. Cada taza es un poco distinta. 300 ml.',
			'larga' => 'Hecha en un pequeño taller de alfarería de Totana. Apta para lavavajillas y microondas.',
		),
	);
}

/**
 * Métodos del recomendador: molienda, receta y cafés que mejor le van.
 */
function molienda_metodos() {
	return array(
		'espresso' => array(
			'nombre' => 'Cafetera espresso', 'corto' => 'Espresso', 'molienda' => 'Espresso', 'escala' => 1,
			'receta' => array( 'Dosis' => '18 g de café', 'En la taza' => '36 g de bebida', 'Temperatura' => '93 °C', 'Tiempo' => '25 - 30 s' ),
			'consejo' => 'Si sale demasiado rápido y ácido, pide la molienda un punto más fina la próxima vez.',
			'cafes' => array( 'blend-huerta', 'brasil-cerrado' ),
		),
		'moka' => array(
			'nombre' => 'Cafetera italiana', 'corto' => 'Italiana', 'molienda' => 'Moka italiana', 'escala' => 2,
			'receta' => array( 'Dosis' => 'El filtro lleno, sin apretar', 'Agua' => 'Caliente, hasta la válvula', 'Fuego' => 'Medio bajo', 'Tiempo' => 'Retírala al primer borboteo' ),
			'consejo' => 'Echar el agua ya caliente evita que el café se queme mientras sube.',
			'cafes' => array( 'guatemala-antigua', 'brasil-cerrado' ),
		),
		'filtro' => array(
			'nombre' => 'Goteo, V60 o Chemex', 'corto' => 'Filtro', 'molienda' => 'Filtro (V60 y Chemex)', 'escala' => 3,
			'receta' => array( 'Dosis' => '15 g de café', 'Agua' => '250 ml', 'Temperatura' => '94 °C', 'Tiempo' => '2:30 - 3:00 min' ),
			'consejo' => 'Moja primero el café con el doble de agua que de café y espera 30 segundos: se nota mucho.',
			'cafes' => array( 'etiopia-yirgacheffe', 'kenia-nyeri-aa' ),
		),
		'prensa' => array(
			'nombre' => 'Prensa francesa', 'corto' => 'Prensa', 'molienda' => 'Prensa francesa', 'escala' => 4,
			'receta' => array( 'Dosis' => '30 g de café', 'Agua' => '500 ml', 'Temperatura' => '95 °C', 'Tiempo' => '4 min' ),
			'consejo' => 'Al terminar, aparta la espuma de arriba con una cuchara antes de bajar el émbolo.',
			'cafes' => array( 'colombia-huila', 'guatemala-antigua' ),
		),
		'grano' => array(
			'nombre' => 'Tengo molinillo', 'corto' => 'En grano', 'molienda' => 'En grano', 'escala' => 0,
			'receta' => array( 'Guárdalo' => 'En la bolsa, bien cerrada', 'Dónde' => 'Lejos de la luz y del calor', 'Cuándo moler' => 'Justo antes de prepararlo', 'Mejor antes de' => '6 semanas desde el tueste' ),
			'consejo' => 'El café molido pierde la mitad de su aroma en 15 minutos. En grano aguanta semanas.',
			'cafes' => array( 'panama-geisha', 'etiopia-yirgacheffe' ),
		),
	);
}
