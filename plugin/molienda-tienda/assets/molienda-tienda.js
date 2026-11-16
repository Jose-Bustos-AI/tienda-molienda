/* Molienda Tienda: recomendador de molienda y consejo en la ficha del café. */
(function () {
	// Pestañas del recomendador, con teclado.
	document.querySelectorAll('.mol-met').forEach(function (bloque) {
		var tabs = Array.prototype.slice.call(bloque.querySelectorAll('[role=tab]'));
		function elegir(tab, foco) {
			tabs.forEach(function (t) {
				var activo = t === tab;
				t.setAttribute('aria-selected', activo ? 'true' : 'false');
				t.tabIndex = activo ? 0 : -1;
				var panel = document.getElementById(t.getAttribute('aria-controls'));
				if (panel) { panel.hidden = !activo; if (activo) { panel.classList.remove('entra'); void panel.offsetWidth; panel.classList.add('entra'); } }
			});
			if (foco) tab.focus();
		}
		tabs.forEach(function (tab, i) {
			tab.addEventListener('click', function () { elegir(tab); });
			tab.addEventListener('keydown', function (e) {
				var n = null;
				if (e.key === 'ArrowRight' || e.key === 'ArrowDown') n = tabs[(i + 1) % tabs.length];
				if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') n = tabs[(i - 1 + tabs.length) % tabs.length];
				if (e.key === 'Home') n = tabs[0];
				if (e.key === 'End') n = tabs[tabs.length - 1];
				if (n) { e.preventDefault(); elegir(n, true); }
			});
		});
	});

	// Consejo según la molienda elegida en la ficha del café.
	var select = document.querySelector('select[name="attribute_molienda"]');
	var consejo = document.querySelector('.mol-consejo');
	if (select && consejo && window.MOLIENDA) {
		var poner = function () {
			var texto = MOLIENDA.consejos[select.value];
			consejo.textContent = texto || '';
			consejo.classList.toggle('visible', !!texto);
		};
		select.addEventListener('change', poner);
		if (window.jQuery) window.jQuery(select).on('change', poner);
		poner();
	}

	function euros(n) { return n.toFixed(2).replace('.', ',') + ' €'; }
	function avisar(el) {
		if (window.jQuery) window.jQuery(el).trigger('change');
		else el.dispatchEvent(new Event('change', { bubbles: true }));
	}

	// Ficha del café: los desplegables de molienda y peso pasan a ser botones.
	var form = document.querySelector('form.variations_form');
	if (form) {
		var variaciones = [];
		try { variaciones = JSON.parse(form.getAttribute('data-product_variations')) || []; } catch (e) { variaciones = []; }
		var grupos = [];
		form.querySelectorAll('table.variations select').forEach(function (sel) {
			var etiqueta = form.querySelector('label[for="' + sel.id + '"]');
			var grupo = document.createElement('div');
			grupo.className = 'mol-pildoras';
			grupo.setAttribute('role', 'group');
			if (etiqueta) { etiqueta.id = etiqueta.id || 'mol-et-' + sel.id; grupo.setAttribute('aria-labelledby', etiqueta.id); }
			Array.prototype.forEach.call(sel.options, function (op) {
				if (!op.value) return;
				var b = document.createElement('button');
				b.type = 'button';
				b.setAttribute('data-valor', op.value);
				b.textContent = op.textContent;
				if (sel.name === 'attribute_peso') {
					var v = variaciones.filter(function (x) { return x.attributes && x.attributes.attribute_peso === op.value; })[0];
					if (v) { var s = document.createElement('small'); s.textContent = euros(v.display_price); b.appendChild(s); }
				}
				b.addEventListener('click', function () { sel.value = op.value; avisar(sel); });
				grupo.appendChild(b);
			});
			sel.parentNode.insertBefore(grupo, sel);
			sel.classList.add('mol-oculto');
			sel.setAttribute('tabindex', '-1');
			grupos.push({ sel: sel, grupo: grupo });
		});
		var pintar = function () {
			grupos.forEach(function (g) {
				g.grupo.querySelectorAll('button').forEach(function (b) {
					var valor = b.getAttribute('data-valor'), existe = false;
					Array.prototype.forEach.call(g.sel.options, function (op) { if (op.value === valor && !op.disabled) existe = true; });
					b.disabled = !existe;
					b.setAttribute('aria-pressed', g.sel.value === valor ? 'true' : 'false');
				});
			});
		};
		if (grupos.length) {
			form.classList.add('mol-con-pildoras');
			form.addEventListener('change', pintar);
			if (window.jQuery) window.jQuery(form).on('change woocommerce_update_variation_values reset_data found_variation', function () { setTimeout(pintar, 0); });
			pintar();
		}

		// Precio por taza según el peso elegido.
		var taza = form.querySelector('.mol-taza');
		if (taza && window.jQuery) {
			var gramosTaza = parseInt(taza.getAttribute('data-gramos'), 10) || 15;
			var aGramos = function (texto) {
				var m = /([\d.,]+)\s*(kg|g)/i.exec(texto || '');
				if (!m) return 0;
				var n = parseFloat(m[1].replace(',', '.'));
				return m[2].toLowerCase() === 'kg' ? n * 1000 : n;
			};
			window.jQuery(form).on('found_variation', function (ev, v) {
				var gramos = aGramos((v.attributes && v.attributes.attribute_peso) || taza.getAttribute('data-peso'));
				if (!gramos || !v.display_price) { taza.textContent = ''; taza.classList.remove('visible'); return; }
				var tazas = Math.floor(gramos / gramosTaza);
				taza.textContent = 'Salen unas ' + tazas + ' tazas: ' + euros(v.display_price / tazas) + ' cada una.';
				taza.classList.add('visible');
			}).on('reset_data', function () { taza.textContent = ''; taza.classList.remove('visible'); });
		}
	}

	// Cantidad con botones de menos y más.
	document.querySelectorAll('form.cart .quantity').forEach(function (caja) {
		var campo = caja.querySelector('input.qty');
		if (!campo || campo.type === 'hidden' || caja.querySelector('.mol-cant')) return;
		caja.classList.add('mol-cantidad');
		[['−', -1, 'Quitar una unidad'], ['+', 1, 'Añadir una unidad']].forEach(function (d) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'mol-cant';
			b.textContent = d[0];
			b.setAttribute('aria-label', d[2]);
			b.addEventListener('click', function () {
				var paso = parseFloat(campo.step) || 1, min = parseFloat(campo.min) || 1, max = parseFloat(campo.max) || Infinity;
				var n = (parseFloat(campo.value) || 0) + d[1] * paso;
				campo.value = Math.max(min, Math.min(max, n));
				avisar(campo);
			});
			if (d[1] < 0) caja.insertBefore(b, caja.firstChild); else caja.appendChild(b);
		});
	});

	// Carrito: cuánto falta para el envío gratis.
	var carrito = document.querySelector('.wp-block-woocommerce-cart');
	if (carrito && window.MOLIENDA && MOLIENDA.envioGratis) {
		var barra = document.createElement('div');
		barra.className = 'mol-envio';
		barra.hidden = true;
		barra.innerHTML = '<p class="mol-envio__texto" aria-live="polite"></p><div class="mol-envio__pista" aria-hidden="true"><span></span></div>';
		carrito.parentNode.insertBefore(barra, carrito);
		var texto = barra.querySelector('.mol-envio__texto'), relleno = barra.querySelector('.mol-envio__pista span');
		var ultimo = null;
		var actualizar = function () {
			var tienda = window.wp && wp.data && wp.data.select('wc/store/cart');
			if (!tienda) return;
			var datos = tienda.getCartData(), t = datos && datos.totals;
			if (!t || !datos.itemsCount) { barra.hidden = true; ultimo = null; return; }
			var div = Math.pow(10, t.currency_minor_unit || 2);
			var total = (parseInt(t.total_items, 10) - parseInt(t.total_discount || 0, 10)) / div;
			if (total === ultimo) return;
			ultimo = total;
			var falta = MOLIENDA.envioGratis - total;
			barra.hidden = false;
			barra.classList.toggle('mol-envio--hecho', falta <= 0);
			texto.textContent = falta > 0 ? 'Te faltan ' + euros(falta) + ' para el envío gratis.' : 'Ya tienes el envío estándar gratis.';
			relleno.style.width = Math.max(4, Math.min(100, (total / MOLIENDA.envioGratis) * 100)) + '%';
		};
		var enganchar = function (intentos) {
			if (window.wp && wp.data && wp.data.select('wc/store/cart')) { wp.data.subscribe(actualizar); actualizar(); }
			else if (intentos > 0) setTimeout(function () { enganchar(intentos - 1); }, 400);
		};
		enganchar(25);
	}
})();
