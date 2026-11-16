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
})();
