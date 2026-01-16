<div class="shop-side">
    {{-- BUSCAR LOCAL (solo UI, no backend) --}}
    <div class="mb-3">
        <div class="side-title">Buscar</div>

        <div class="input-group">
            <span class="input-group-text shop-search-icon">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>

            <input id="txtBuscarLocal"
                   type="text"
                   class="form-control shop-search-input"
                   placeholder="Buscar productos...">
        </div>
    </div>

    <div class="side-divider"></div>

    {{-- CATEGORÍAS (backend: usa tu filtro pro_categoria) --}}
    <div class="mb-2">
        <div class="side-title">Categorías</div>

        @php
            $catSel = request('pro_categoria');
        @endphp

        <div class="d-grid gap-1">
            <a href="{{ route('productos.index') }}"
               class="side-chip {{ empty($catSel) ? 'active' : '' }}"
               style="text-decoration:none;">
                Todas
            </a>

            @foreach(['Alimentos','Medicinas','Ropa','Otros'] as $cat)
                <form method="GET" action="{{ route('productos.buscar') }}">
                    <input type="hidden" name="orden" value="{{ request('orden','id_asc') }}">
                    <input type="hidden" name="pro_categoria" value="{{ $cat }}">
                    <button type="submit"
                            class="side-chip {{ $catSel===$cat ? 'active' : '' }}">
                        {{ $cat }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <div class="side-divider"></div>

    {{-- PRECIO (solo UI local) --}}
    <div>
        <div class="side-title">Precio</div>
        <input id="rangePrecio" type="range" class="form-range" min="0" max="100" step="1" value="100">
        <div class="small text-muted fw-bold" id="lblPrecio">Hasta $100.00</div>
    </div>
</div>
