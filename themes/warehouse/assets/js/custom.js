/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
/* cart modal*/
document.querySelectorAll('#blockcart-modal strong').forEach(function(el) {
  if (el.textContent.trim().includes('Delivery')) {
    var parent = el.parentElement;
    if (parent) {
      parent.style.display = 'none';
    }
  }
});


document.addEventListener('DOMContentLoaded', function () {
  if (window.innerWidth <= 768) {
    function waitAndInit() {
      const totalEl = document.querySelector('.cart-total .value');
      const confirmBtn = document.querySelector('#confirm_order');
      const termsOriginal = document.querySelector('#conditions_to_approve\\[terms-and-conditions\\]');
      const termsWrapperOriginal = document.querySelector('.terms-and-conditions');

      if (!totalEl || !confirmBtn || !termsOriginal || !termsWrapperOriginal) {
        return setTimeout(waitAndInit, 300);
      }

      // Crear wrapper sticky
      const stickyWrapper = document.createElement('div');
      stickyWrapper.classList.add('mobile-sticky-checkout');

      // Total
      const totalCloned = document.createElement('div');
      totalCloned.className = 'sticky-total';
      totalCloned.innerHTML = `<strong>Total:</strong> <span id="stickyTotalValue">${totalEl.textContent.trim()}</span>`;

      // Clon del checkbox
      const termsCloneWrapper = document.createElement('label');
      termsCloneWrapper.className = 'sticky-terms';
      termsCloneWrapper.style.display = 'flex';
      termsCloneWrapper.style.alignItems = 'center';
      termsCloneWrapper.style.fontSize = '14px';
      termsCloneWrapper.style.gap = '6px';
      termsCloneWrapper.style.marginTop = '5px';

      const termsClone = document.createElement('input');
      termsClone.type = 'checkbox';
      termsClone.checked = termsOriginal.checked;

      termsClone.addEventListener('change', () => {
        termsOriginal.checked = termsClone.checked;
        termsOriginal.dispatchEvent(new Event('change'));
      });

      termsOriginal.addEventListener('change', () => {
        termsClone.checked = termsOriginal.checked;
      });

      const labelText = document.createElement('span');
      labelText.innerHTML = `I agree to the <a href="https://murphyfurniture.ie/content/terms-and-conditions-of-use" target="_blank">terms of service</a>.`;

      termsCloneWrapper.appendChild(termsClone);
      termsCloneWrapper.appendChild(labelText);

      // Botón clonado
      const btnCloned = confirmBtn.cloneNode(true);
      btnCloned.id = 'confirm_order_mobile';
      btnCloned.addEventListener('click', () => confirmBtn.click());

      // Añadir todo al wrapper
      stickyWrapper.appendChild(totalCloned);
      stickyWrapper.appendChild(termsCloneWrapper);
      stickyWrapper.appendChild(btnCloned);
      document.body.appendChild(stickyWrapper);
    // Ocultar originales (solo después de clonar)
confirmBtn.style.display = 'none';
confirmBtn.style.position = 'absolute';
confirmBtn.style.opacity = '0';
confirmBtn.style.pointerEvents = 'none';

termsWrapperOriginal.style.display = 'none';

      // Ocultar originales (solo después de clonar)
      confirmBtn.style.display = 'none';
      termsWrapperOriginal.style.display = 'none';

      // Sincronización total cada 500ms
      let lastVal = totalEl.textContent.trim();
      setInterval(() => {
        const current = document.querySelector('.cart-total .value')?.textContent.trim();
        if (current && current !== lastVal) {
          lastVal = current;
          document.querySelector('#stickyTotalValue').textContent = current;
        }
      }, 500);
    }

    waitAndInit();
  }
});
/* end cart modal*/

/* OVERLAY FILTROS MOVIL INICIO */


/* ===========================
   PRO Bottom Sheet - Facets (Mobile)
   PrestaShop 8.x
   =========================== */

(() => {
  const byId = (id) => document.getElementById(id);
  const qs = (s) => document.querySelector(s);

  let backdrop;

  function getBackdrop() {
    if (backdrop && backdrop.isConnected) return backdrop;
    backdrop = qs('.facets-backdrop');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.className = 'facets-backdrop';
      document.body.appendChild(backdrop);
    }
    return backdrop;
  }

  function open() {
    const sheet = byId('facets_search_wrapper');
    if (!sheet) return;

    getBackdrop().classList.add('is-open');
    document.body.classList.add('facets-modal-open');

    // el tema a veces lo vuelve display:none tras XHR
    sheet.style.display = 'flex';
    sheet.style.flexDirection = 'column';

    sheet.classList.remove('is-open');
    sheet.getBoundingClientRect();
    requestAnimationFrame(() => {
      sheet.classList.add('is-open');
      sheet.style.display = 'flex';
    });

    setTimeout(() => bindSwipeToClose(sheet), 80);
  }

  function close() {
    const sheet = byId('facets_search_wrapper');
    if (!sheet) return;

    sheet.classList.remove('is-open');
    getBackdrop().classList.remove('is-open');
    document.body.classList.remove('facets-modal-open');

    sheet.style.transition = '';
    sheet.style.transform = '';
    sheet.classList.remove('is-dragging');

    setTimeout(() => (sheet.style.display = ''), 250);
  }

  function bindSwipeToClose(sheet) {
    if (sheet.dataset.swipeBound === '1') return;
    sheet.dataset.swipeBound = '1';

    let startY = 0, lastY = 0, active = false;
    const scroller = () => sheet.querySelector('#search_filters_wrapper');

    const reset = () => {
      sheet.style.transition = '';
      sheet.style.transform = '';
      sheet.classList.remove('is-dragging');
    };

    sheet.addEventListener('touchstart', (e) => {
      if (!sheet.classList.contains('is-open')) return;
      const t = e.touches && e.touches[0];
      if (!t) return;

      const s = scroller();
      if (s && s.scrollTop > 0) return;

      active = true;
      startY = lastY = t.clientY;

      sheet.classList.add('is-dragging');
      sheet.style.transition = 'none';
    }, { passive: true });

    sheet.addEventListener('touchmove', (e) => {
      if (!active) return;
      const t = e.touches && e.touches[0];
      if (!t) return;

      lastY = t.clientY;
      const d = lastY - startY;

      if (d > 0) {
        e.preventDefault();
        sheet.style.transform = `translateY(${d}px)`;
      }
    }, { passive: false });

    sheet.addEventListener('touchend', () => {
      if (!active) return;
      active = false;

      const d = lastY - startY;
      if (d > 110) { reset(); close(); return; }

      sheet.style.transition = 'transform .2s ease';
      sheet.style.transform = 'translateY(0px)';
      setTimeout(reset, 220);
    });

    sheet.addEventListener('touchcancel', () => { active = false; reset(); });
  }

  function rebindSoon() {
    setTimeout(() => {
      const sheet = byId('facets_search_wrapper');
      if (sheet) bindSwipeToClose(sheet);
    }, 150);
  }

  function init() {
    getBackdrop().addEventListener('click', close);

    // ✅ CLAVE: Delegación + capture para interceptar SIEMPRE aunque PS re-renderice el botón
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('#search_filter_toggler');
      if (!btn) return;

      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation(); // bloquea el handler nativo que te oculta content-wrapper

      open();
    }, true);

    // OK cierra
    document.addEventListener('click', (e) => {
      if (e.target.closest('#facets_search_wrapper button.ok')) close();
    });

    // Clear all: mantener abierto + rebind
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.js-search-filters-clear-all')) return;
      open();
      rebindSoon();
    }, true);

    // Eventos PS: rebind (por re-render)
    if (window.prestashop && typeof window.prestashop.on === 'function') {
      window.prestashop.on('updateFacets', rebindSoon);
      window.prestashop.on('updateProductList', rebindSoon);
    }
  }

  document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', init)
    : init();
})();




/* OVERLAY FILTROS MOVIL INICIO */

/* ocultar backthetop in ch eckout */
(() => {
  // Solo checkout
  if (document.body?.id !== 'checkout') return;

  const kill = () => {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    // Opción A: eliminar del DOM (más definitivo)
    btn.remove();

    // Si prefieres NO remover, comenta btn.remove() y usa esto:
    // btn.style.setProperty('display', 'none', 'important');
    // btn.style.setProperty('visibility', 'hidden', 'important');
    // btn.style.setProperty('opacity', '0', 'important');
    // btn.style.setProperty('pointer-events', 'none', 'important');
  };

  // 1) al cargar
  kill();

  // 2) por si lo reinsertan o cambian clases/estilos
  const obs = new MutationObserver(() => kill());
  obs.observe(document.documentElement, { childList: true, subtree: true, attributes: true });

  // 3) por si lo muestran al hacer scroll
  window.addEventListener('scroll', kill, { passive: true });
})();


/* PRODUCT REFERENCE COMBINATION */

document.addEventListener('DOMContentLoaded', () => {
  const refWrap = document.querySelector('.product-reference');
  const refStrong = document.querySelector('.product-reference strong');
  if (!refWrap || !refStrong || !document.querySelector('#product-details')) return;

  const getGroupId = () => {
    const ul = document.querySelector('.product-variants ul[id^="group_"]');
    const m = ul?.id?.match(/^group_(\d+)$/);
    return m ? m[1] : null;
  };

  const readData = () => {
    const raw = document.querySelector('#product-details')?.getAttribute('data-product');
    if (!raw) return null;
    try { return JSON.parse(raw); } catch { return null; }
  };

  const apply = () => {
    const data = readData();
    if (!data) return;

    const gid = getGroupId();
    const dyn = gid ? (data?.attributes?.[gid]?.reference || '').trim() : '';
    const base = (refWrap.dataset.baseRef || '').trim() || (data?.reference || '').trim();

    const ref = dyn || base;
    if (ref && refStrong.textContent.trim() !== ref) refStrong.textContent = ref;
  };

  apply();
  if (window.prestashop?.on) prestashop.on('updatedProduct', apply);
});
