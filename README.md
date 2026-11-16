# Molienda · tienda WooCommerce de demostración

Tienda de café de especialidad tostado en Murcia: cada café se pide en grano o molido para tu cafetera y en 250 g o 1 kg, con su perfil de taza, y la portada incluye un recomendador que te dice qué molienda pedir y qué cafés te van a gustar según cómo preparas el café.

**Verla publicada:** https://molienda.dania360.com/

## Qué hay en este repositorio
Solo el código hecho a medida para esta tienda; WordPress y WooCommerce no se incluyen.

```
tema/molienda/      Tema hijo de Twenty Twenty-Five: plantillas de bloques, estilos (theme.json), tipografías y JavaScript
plugin/molienda-tienda/    Plugin propio: funciones de la tienda, datos del catálogo y montaje automático
```

## Cómo está hecha
- WordPress con WooCommerce 11, en el hosting de Hostinger, con su propio subdominio y certificado de seguridad
- Tema hijo propio sobre Twenty Twenty-Five: paleta, tipografías (Fraunces y Manrope) servidas desde la propia web, cabecera, pie y portada a medida
- Portada con foto a pantalla completa, titular animado, tarjeta flotante del café de la semana, cinta de frases en movimiento y secciones que aparecen al hacer scroll, sin animaciones si el visitante pide menos movimiento
- Portada con bloque de edición limitada (el microlote de Geisha) y preguntas frecuentes desplegables
- Página de tienda con presentación y accesos rápidos a las cuatro categorías
- Tienda, carrito, pago y direcciones de la web en español, con la traducción oficial de WooCommerce

## Catálogo y compra
- 13 productos en 4 categorías: cafés de origen, blends, ediciones limitadas y métodos y accesorios
- 8 cafés con variaciones de molienda (en grano, espresso, italiana, filtro y prensa francesa); 7 de ellos también de peso (250 g y 1 kg), cada una con su precio
- La molienda y el peso se eligen con botones, no con desplegables, y el botón del peso enseña su precio
- Ficha de origen a la vista en cada café (región, altitud, variedad, proceso y tueste) y perfil de taza con notas de cata y barras de acidez, cuerpo y dulzor
- Precio por taza calculado al momento según el peso elegido
- Pestañas de conservación y de envíos y cambios en cada ficha
- Al elegir la molienda aparece un consejo de cómo debe ser y para qué cafetera sirve
- Ofertas con precio tachado, también en una sola variación (el blend en 1 kg)
- Fotos de producto creadas para la marca
- Envío estándar a 3,90 €, gratis desde 35 € (entonces el estándar de pago desaparece) y exprés en 24 h a 6,90 €
- Cupón de bienvenida BIENVENIDA10 con un 10 % de descuento
- Aviso en el carrito de cuánto falta para el envío gratis, con barra de progreso
- Venta cruzada en el carrito: con cada café propone el gotero y el molinillo
- Pago de prueba que no pide tarjeta ni cobra nada y termina en la página de pedido recibido
- Tres pedidos de prueba completos: con molienda, cupón y envío gratis; desde el móvil con envío exprés; y desde el móvil con la ficha nueva, dos unidades en oferta, cupón y envío gratis

## Detalles
- Sin plugins de pago: todo lo propio está en un plugin hecho a medida para la tienda
- Recomendador "¿Cómo preparas tu café?": cinco métodos con su molienda, receta (dosis, agua, temperatura y tiempo) y dos cafés recomendados que se abren ya con esa molienda elegida
- Descripción para buscadores y para compartir en la portada, la tienda, las categorías y cada ficha
- Montaje automático del catálogo, los envíos, el pago y el cupón desde el propio plugin
- Lista para conectar Stripe, Redsys o Bizum cuando sea una tienda real

## Cómo instalarlo
1. WordPress 6.6 o superior con WooCommerce activo.
2. Copia `tema/molienda` en `wp-content/themes/` y actívalo (necesita Twenty Twenty-Five instalado).
3. Copia `plugin/molienda-tienda` en `wp-content/plugins/` y actívalo.
4. El plugin incluye un montaje por pasos que crea las categorías, los productos, los envíos y las páginas; se desactiva solo al terminar.

Tipografías de Google Fonts con licencia SIL Open Font License, incluidas en `tema/molienda/assets/fonts`.

## Sobre esta tienda
Diseño y desarrollo de [Dania360](https://dania360.com). Es una tienda de ejemplo: la marca y los productos son inventados, las fotos se crearon para la marca y el pago es de prueba, no cobra nada. El código se publica para enseñar cómo está hecha; no tiene licencia de uso.

Más tiendas en el portafolio: https://tiendas.dania360.com
