<div class="shop-side">
    {{-- BUSCAR GLOBAL --}}
    <div class="mb-3">
        <div class="side-title">Buscar</div>

        <form id="frmBuscarGlobal" method="GET" action="{{ route('productos.buscar') }}">
            {{-- Mantener filtros actuales --}}
            <input type="hidden" name="orden" value="{{ request('orden','mix') }}">
            <input type="hidden" name="id_categoria" value="{{ request('id_categoria') }}">
            <input type="hidden" name="unidad_medida" value="{{ request('unidad_medida') }}">

            <div class="input-group">
                <span class="input-group-text shop-search-icon">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                </span>
                <input id="txtBuscarLocal"
                       name="q"
                       type="text"
                       class="form-control shop-search-input"
                       placeholder="Buscar productos."
                       value="{{ request('q') }}"
                       autocomplete="off">
            </div>

            {{-- LIMPIAR (restablece TODO al estado normal) --}}
            <a href="{{ route('productos.index') }}"
               id="btnLimpiarFiltros"
               class="btn btn-outline-secondary btn-sm w-100 mt-2">
                Limpiar
            </a>
        </form>
    </div>

    <div class="side-divider"></div>

    {{-- CATEGORÍAS (backend: FK id_categoria) --}}
    @php
        $catSel = request('id_categoria');
        $qSel   = request('q');
    @endphp

    <div class="mb-2">
        <div class="side-title">Categorías</div>

        <div class="d-grid gap-1">

            {{-- TODAS (mantiene búsqueda q) --}}
            <form method="GET" action="{{ route('productos.buscar') }}">
                <input type="hidden" name="orden" value="{{ request('orden','mix') }}">
                <input type="hidden" name="q" value="{{ $qSel }}">
                <button type="submit"
                        class="side-chip {{ empty($catSel) ? 'active' : '' }}">
                    Todas
                </button>
            </form>

            @foreach(($categorias ?? []) as $cat)
                <form method="GET" action="{{ route('productos.buscar') }}">
                    <input type="hidden" name="orden" value="{{ request('orden','mix') }}">
                    <input type="hidden" name="q" value="{{ $qSel }}">
                    <input type="hidden" name="id_categoria" value="{{ $cat->id_categoria }}">
                    <button type="submit"
                            class="side-chip {{ (string)$catSel === (string)$cat->id_categoria ? 'active' : '' }}">
                        {{ $cat->cat_nombre }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <div class="side-divider"></div>

    {{-- PRECIO (solo UI local) --}}
    <div>
        <div class="side-title">Precio</div>
        <input id="rangePrecio" type="range" class="form-range" min="0" max="30" step="1" value="30">
        <div class="small text-muted fw-bold" id="lblPrecio">Hasta $30.00</div>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('frmBuscarGlobal');
        const txt  = document.getElementById('txtBuscarLocal');
        if (!form || !txt) return;

        let t = null;

        function buildUrl() {
            const url = new URL(form.action, window.location.origin);
            const data = new FormData(form);

            for (const [k, v] of data.entries()) {
                const val = (v ?? '').toString().trim();
                if (val !== '') url.searchParams.set(k, val);
            }
            url.searchParams.delete('page');
            return url.toString();
        }

        async function runSearch() {
            const caretStart = txt.selectionStart;
            const caretEnd = txt.selectionEnd;

            const url = buildUrl();

            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const nuevo = doc.getElementById('productosContenido');
                const actual = document.getElementById('productosContenido');

                if (!nuevo || !actual) {
                    window.location.href = url;
                    return;
                }

                actual.innerHTML = nuevo.innerHTML;

                if (history && history.replaceState) {
                    history.replaceState(null, '', url);
                }

                if (typeof window.applyPriceFilter === 'function') {
                    window.applyPriceFilter();
                }

            } catch (e) {
                window.location.href = url;
                return;
            } finally {
                txt.focus({ preventScroll: true });
                try { txt.setSelectionRange(caretStart, caretEnd); } catch (_) {}
            }
        }

        txt.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(runSearch, 300);
        });

        txt.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(t);
                runSearch();
            }
        });
    })();
</script>
