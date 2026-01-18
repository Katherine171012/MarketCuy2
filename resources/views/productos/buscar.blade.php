<div class="shop-side">
    {{-- BUSCAR GLOBAL (SERVER) --}}
    <div class="mb-3">
        <div class="side-title">Buscar</div>

        <form id="frmBuscarGlobal" method="GET" action="{{ route('productos.buscar') }}">
            {{-- Mantener filtros actuales --}}
            <input type="hidden" name="orden" value="{{ request('orden','id_asc') }}">
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
                       placeholder="Buscar productos..."
                       value="{{ request('q') }}">
            </div>
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
                <input type="hidden" name="orden" value="{{ request('orden','id_asc') }}">
                <input type="hidden" name="q" value="{{ $qSel }}">
                <button type="submit"
                        class="side-chip {{ empty($catSel) ? 'active' : '' }}">
                    Todas
                </button>
            </form>

            @foreach(($categorias ?? []) as $cat)
                <form method="GET" action="{{ route('productos.buscar') }}">
                    <input type="hidden" name="orden" value="{{ request('orden','id_asc') }}">
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
    (function(){
        // Buscar GLOBAL (server-side) con debounce sin cambiar el estilo
        const form = document.getElementById('frmBuscarGlobal');
        const txt  = document.getElementById('txtBuscarLocal');
        if(!form || !txt) return;

        let t = null;
        txt.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => form.submit(), 450);
        });
    })();
</script>
