<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\UnidadMedida;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    private function msg(string $key): string
    {
        $all = config('mensajes', []);
        return $all[$key] ?? $key;
    }

    private function viewWithMsgs(string $view, array $data = [])
    {
        $data['msg'] = config('mensajes', []);
        return view($view, $data);
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $catId = $request->get('categoria');

        if ($catId) {
            // Caso 1: Vienen del Home con una categoría seleccionada
            $productos = Producto::paginarActivosConFiltros(null, $catId, null, null, $perPage);
        } else {
            // Caso 2: Entran normal al módulo (Tu lógica original intacta)
            $productos = Producto::obtenerParaLista($perPage);
        }
        $productos->appends($request->except('page'));

        $unidades = UnidadMedida::listar();
        $categorias = Categoria::listarActivas();

        $editId = $request->get('edit');
        $productoEditar = null;

        if ($editId) {
            $productoEditar = Producto::buscarPorId($editId);

            if (!$productoEditar) {
                return redirect()->route('productos.index')
                    ->with('error', $this->msg('gen.error'));
            }
            if ($productoEditar->estado_prod === 'INA') {
                return redirect()->route('productos.index')
                    ->with('error', $this->msg('M60'));
            }
        }

        $deleteId = $request->get('delete');
        $productoEliminar = null;

        if ($deleteId) {
            $productoEliminar = Producto::buscarPorId($deleteId);

            if (!$productoEliminar) {
                return redirect()->route('productos.index')
                    ->with('error', $this->msg('gen.error'));
            }
        }

        $viewId = $request->get('view');
        $productoVer = null;

        if ($viewId) {
            $productoVer = Producto::buscarPorId($viewId);

            if (!$productoVer) {
                return redirect()->route('productos.index')
                    ->with('error', $this->msg('gen.error'));
            }
        }

        return $this->viewWithMsgs('productos.index', [
            'productos' => $productos,
            'unidades' => $unidades,
            'categorias' => $categorias,
            'productoEditar' => $productoEditar,
            'productoEliminar' => $productoEliminar,
            'productoVer' => $productoVer,
            'info' => $productos->count() === 0 ? $this->msg('M59') : null,
            'categoriaSeleccionada' => $catId,
        ]);
    }

    public function store(Request $request)
    {
        // ✅ ahora el nombre real es pro_nombre
        if (!$request->pro_nombre) {
            return back()->withErrors([
                'pro_nombre' => $this->msg('M25')
            ])->withInput();
        }

        if (!$request->id_categoria) {
            return back()->withErrors([
                'id_categoria' => 'Seleccione una categoría.'
            ])->withInput();
        }

        if ($request->pro_precio_venta === null || $request->pro_precio_venta === '') {
            return back()->withErrors([
                'pro_precio_venta' => $this->msg('M29')
            ])->withInput();
        }

        if (!is_numeric($request->pro_precio_venta)) {
            return back()->withErrors([
                'pro_precio_venta' => $this->msg('M30')
            ])->withInput();
        }

        if ($request->pro_precio_venta < 0) {
            return back()->withErrors([
                'pro_precio_venta' => $this->msg('M31')
            ])->withInput();
        }

        if (
            $request->pro_valor_compra !== null &&
            $request->pro_valor_compra !== '' &&
            $request->pro_valor_compra < 0
        ) {
            return back()->withErrors([
                'pro_valor_compra' => $this->msg('M31')
            ])->withInput();
        }

        if (
            $request->pro_saldo_inicial === null ||
            $request->pro_saldo_inicial === '' ||
            $request->pro_saldo_inicial < 0
        ) {
            return back()->withErrors([
                'pro_saldo_inicial' => $this->msg('M35')
            ])->withInput();
        }

        // ✅ validar duplicado por pro_nombre
        if (Producto::existeNombre($request->pro_nombre)) {
            return back()->withErrors([
                'pro_nombre' => $this->msg('M26')
            ])->withInput();
        }

        if ($request->hasFile('pro_imagen')) {
            $file = $request->file('pro_imagen');
            $ext = strtolower($file->getClientOriginalExtension());

            if (!in_array($ext, ['jpg', 'jpeg', 'pdf'], true)) {
                return back()->withErrors([
                    'pro_imagen' => 'Solo se permiten archivos JPG o PDF.'
                ])->withInput();
            }
        }

        try {
            $nuevoId = Producto::generarSiguienteId();

            $data = $request->all();
            $data['id_producto'] = $nuevoId;

            // ✅ IMAGEN: guardar con nombre = ID del producto
            if ($request->hasFile('pro_imagen') && $request->file('pro_imagen')->isValid()) {
                $file = $request->file('pro_imagen');
                $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $filename = $nuevoId . '.' . $ext;
                $path = $file->storeAs('productos', $filename, 'public');
                $data['pro_imagen'] = $path;
            } else {
                $data['pro_imagen'] = null;
            }

            Producto::crearProductoTx($data);

            return redirect()->route('productos.index')
                ->with('ok', $this->msg('M1'));

        } catch (\Exception $e) {

            Log::error('ProductoController@store ERROR', [
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', $this->msg('gen.error'))
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::buscarPorId($id);

        if (!$producto) {
            return redirect()->route('productos.index')
                ->with('error', $this->msg('gen.error'));
        }

        if ($producto->estado_prod === 'INA') {
            return redirect()->route('productos.index')
                ->with('error', $this->msg('M60'));
        }

        if ($request->pro_precio_venta === null || $request->pro_precio_venta === '') {
            return back()->withErrors([
                'pro_precio_venta' => $this->msg('M29')
            ])->withInput();
        }

        if (!is_numeric($request->pro_precio_venta)) {
            return back()->withErrors([
                'pro_precio_venta' => $this->msg('M30')
            ])->withInput();
        }

        if ($request->pro_precio_venta < 0) {
            return back()->withErrors([
                'pro_precio_venta' => $this->msg('M31')
            ])->withInput();
        }

        $nums = [
            'pro_saldo_inicial',
            'pro_qty_ingresos',
            'pro_qty_egresos',
            'pro_qty_ajustes',
            'pro_saldo_final'
        ];

        foreach ($nums as $n) {
            $val = $request->input($n);
            if ($val !== null && $val !== '' && (int)$val < 0) {
                return back()->withErrors([
                    'stock' => $this->msg('M35')
                ])->withInput();
            }
        }

        try {
            $data = $request->all();
            $producto->actualizarProductoTx($data);

            return redirect()->route('productos.index')
                ->with('ok', $this->msg('M2'));

        } catch (\Exception $e) {

            Log::error('ProductoController@update ERROR', [
                'id_producto' => $id,
                'msg'   => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', $this->msg('gen.error'))
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $producto = Producto::buscarPorId($id);

        if (!$producto) {
            return redirect()->route('productos.index')
                ->with('error', $this->msg('gen.error'));
        }
        if ($producto->estado_prod !== 'ACT') {
            return redirect()->route('productos.index')
                ->with('error', $this->msg('M60'));
        }

        try {
            $producto->inactivarProductoTx();

            return redirect()->route('productos.index')
                ->with('ok', $this->msg('M3'));

        } catch (\Exception $e) {

            Log::error('ProductoController@destroy ERROR', [
                'id_producto' => $id,
                'msg'   => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('productos.index')
                ->with('error', $this->msg('gen.error'));
        }
    }

    public function buscar(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $orden       = $request->input('orden');
        $idCategoria = $request->input('id_categoria');
        $unidad      = $request->input('unidad_medida');

        // ✅ búsqueda global
        $q           = $request->input('q');

        $tieneOrden      = ($orden !== null && $orden !== '');
        $tieneCategoria  = ($idCategoria !== null && $idCategoria !== '');
        $tieneUnidad     = ($unidad !== null && $unidad !== '');
        $tieneQ          = ($q !== null && trim($q) !== '');

        // ✅ ahora permite buscar solo con q
        if (!$tieneOrden && !$tieneCategoria && !$tieneUnidad && !$tieneQ) {
            return back()->withErrors([
                'parametros' => $this->msg('M57')
            ])->withInput();
        }

        try {
            $productos = Producto::paginarActivosConFiltros(
                $orden,
                $idCategoria,
                $unidad,
                $q,
                $perPage
            );

            if ($productos === null) {
                return back()->withErrors([
                    'orden' => $this->msg('M58')
                ])->withInput();
            }

            $productos->appends($request->except('page'));

            $unidades = UnidadMedida::listar();
            $categorias = Categoria::listarActivas();

            return $this->viewWithMsgs('productos.index', [
                'productos' => $productos,
                'unidades' => $unidades,
                'categorias' => $categorias,
                'productoEditar' => null,
                'productoEliminar' => null,
                'productoVer' => null,
                'info' => $productos->count() === 0 ? $this->msg('M59') : null,
            ]);

        } catch (\Exception $e) {

            Log::error('ProductoController@buscar ERROR', [
                'msg'   => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'params' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('productos.index')
                ->with('error', $this->msg('gen.error'));
        }
    }
}
