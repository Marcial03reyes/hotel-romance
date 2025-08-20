<?php

namespace App\Http\Controllers;

use App\Models\DimRegistroCliente;
use App\Models\DimMetPago;
use App\Models\DimProductoBodega;
use App\Models\FactRegistroCliente;
use App\Models\FactPagoHab;
use App\Models\FactPagoProd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactRegistroClienteController extends Controller
{
    // LISTADO REGISTRO
    public function index()
    {
        $registros = DB::table('fact_registro_clientes as fr')
            ->leftJoin('dim_registro_clientes as c', 'c.doc_identidad', '=', 'fr.doc_identidad')
            ->leftJoin('fact_pago_hab as ph', 'ph.id_estadia', '=', 'fr.id_estadia')
            ->leftJoin('dim_met_pago as mp', 'mp.id_met_pago', '=', 'ph.id_met_pago')
            ->leftJoin('fact_pago_prod as pp', 'pp.id_estadia', '=', 'fr.id_estadia')
            ->select(
                'fr.id_estadia',
                'fr.hora_ingreso',
                'fr.hora_salida',        
                'fr.fecha_ingreso',
                'fr.habitacion',
                'c.nombre_apellido',
                'fr.doc_identidad',
                'ph.monto as tarifa',
                'mp.met_pago as metodo_pago',
                'ph.boleta',
                DB::raw('COUNT(pp.id_compra) as consumo_count')
            )
            ->groupBy(
                'fr.id_estadia',
                'fr.hora_ingreso',
                'fr.hora_salida',        
                'fr.fecha_ingreso',
                'fr.habitacion',
                'c.nombre_apellido',
                'fr.doc_identidad',
                'ph.monto',
                'mp.met_pago',
                'ph.boleta'
            )
            ->orderByDesc('fr.id_estadia')
            ->get();

        return view('registros.index', compact('registros'));
    }

    public function edit($id)
    {
        $estadia = FactRegistroCliente::findOrFail($id);
        $pago = FactPagoHab::where('id_estadia', $id)->first();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        $habitaciones = [201,202,203,301,302,303];

        return view('registros.edit', compact('estadia', 'pago', 'metodos', 'habitaciones'));
    }

    // FORM AGREGAR REGISTRO
    public function create()
    {
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        $productos = DimProductoBodega::orderBy('nombre')->get();
        $habitaciones = [201,202,203,301,302,303];

        return view('registros.create', compact('metodos', 'productos', 'habitaciones'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'hora_ingreso'  => 'required',
            'hora_salida'   => 'nullable',           
            'fecha_ingreso' => 'required|date',
            'habitacion'    => 'required|in:201,202,203,301,302,303',
            'monto'       => 'required|numeric|min:0',
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'boleta'      => 'nullable|in:SI,NO',
        ]);

        DB::beginTransaction();
        try {
            $estadia = FactRegistroCliente::findOrFail($id);

            $estadia->hora_ingreso  = $request->input('hora_ingreso');
            $estadia->hora_salida   = $request->input('hora_salida');    
            $estadia->fecha_ingreso = $request->input('fecha_ingreso');
            $estadia->habitacion    = $request->input('habitacion');
            $estadia->save();

            $pago = FactPagoHab::where('id_estadia', $id)->first();
            if (!$pago) {
                $pago = new FactPagoHab();
                $pago->id_estadia = $id;
            }
            $pago->id_met_pago = $request->input('id_met_pago');
            $pago->monto       = $request->input('monto');
            $pago->boleta      = $request->boolean('boleta') ? 'SI' : ($request->input('boleta') ?? 'NO');
            $pago->save();

            DB::commit();
            return redirect()->route('registros.index')->with('success', 'Registro actualizado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('No se pudo actualizar: '.$e->getMessage())->withInput();
        }
    }

    // GUARDAR REGISTRO (con Plan B para evitar mass assignment)
    public function store(Request $request)
    {
        // Validación básica
        $request->validate([
            'doc_identidad' => 'required|string|max:20',
            'nombre_apellido' => 'required|string|max:100',
            'hora_ingreso' => 'required',
            'hora_salida' => 'nullable',             
            'fecha_ingreso' => 'required|date',
            'habitacion' => 'required|in:201,202,203,301,302,303',

            // Pago habitación
            'monto' => 'required|numeric|min:0',
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'boleta' => 'nullable|in:SI,NO',

            // Consumos (opcionales) - ✅ AGREGADO COMPROBANTE
            'consumo.*.id_prod_bod' => 'nullable|exists:dim_productos_bodega,id_prod_bod',
            'consumo.*.cantidad' => 'nullable|integer|min:1',
            'consumo.*.precio_unitario' => 'nullable|numeric|min:0',
            'consumo.*.id_met_pago' => 'nullable|exists:dim_met_pago,id_met_pago',
            'consumo.*.comprobante' => 'nullable|in:SI,NO',  // ✅ NUEVO CAMPO
        ]);

        DB::beginTransaction();
        try {
            // 1) Cliente: verificar si existe
            $doc = $request->input('doc_identidad');
            $cliente = DimRegistroCliente::find($doc);
            
            // Si no existe, crear nuevo 
            if (!$cliente) {
                $cliente = new DimRegistroCliente();
                $cliente->doc_identidad = $doc;
                $cliente->nombre_apellido = $request->input('nombre_apellido');
                $cliente->save();
            }

            // 2) Estadía
            $fr = new FactRegistroCliente();
            $fr->hora_ingreso = $request->input('hora_ingreso');
            $fr->hora_salida = $request->input('hora_salida');     
            $fr->fecha_ingreso = $request->input('fecha_ingreso');
            $fr->habitacion = $request->input('habitacion');
            $fr->doc_identidad = $doc;
            $fr->save();

            // 3) Pago habitación (tarifa)
            $ph = new FactPagoHab();
            $ph->id_estadia = $fr->id_estadia;
            $ph->id_met_pago = $request->input('id_met_pago');
            $ph->monto = $request->input('monto');
            $ph->boleta = $request->input('boleta') === 'SI' ? 'SI' : 'NO';
            $ph->save();

            // 4) Consumos múltiples (opcional) - ✅ CORREGIDO
            $consumos = $request->input('consumo', []);
            foreach ($consumos as $linea) {
                if (
                    !empty($linea['id_prod_bod']) &&
                    !empty($linea['cantidad']) &&
                    isset($linea['precio_unitario']) &&
                    !empty($linea['id_met_pago'])
                ) {
                    $fp = new FactPagoProd();
                    $fp->id_estadia = $fr->id_estadia;
                    $fp->id_prod_bod = $linea['id_prod_bod'];
                    $fp->cantidad = $linea['cantidad'];
                    $fp->precio_unitario = $linea['precio_unitario'];
                    $fp->id_met_pago = $linea['id_met_pago'];
                    $fp->comprobante = $linea['comprobante'] ?? 'NO';  // ✅ AGREGADO
                    $fp->save();
                }
            }

            DB::commit();

            return redirect()->route('registros.index')->with('success', 'Registro creado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Ocurrió un error al guardar el registro: '.$e->getMessage())->withInput();
        }
    }

    /**
     * VER Y EDITAR CONSUMOS (reemplaza el método actual)
     * URL: GET /registros/{id}/consumo
     */
    public function consumo($id)
    {
        $estadia = FactRegistroCliente::findOrFail($id);

        // Obtener información básica del cliente
        $cliente = DimRegistroCliente::where('doc_identidad', $estadia->doc_identidad)->first();

        // Obtener todos los consumos con información completa
        $consumos = DB::table('fact_pago_prod as pp')
            ->join('dim_productos_bodega as p', 'p.id_prod_bod', '=', 'pp.id_prod_bod')
            ->join('dim_met_pago as mp', 'mp.id_met_pago', '=', 'pp.id_met_pago')
            ->select(
                'pp.id_compra',
                'pp.id_prod_bod',
                'p.nombre as producto',
                'pp.cantidad',
                'pp.precio_unitario',
                'pp.id_met_pago',
                'mp.met_pago as metodo',
                'pp.comprobante', 
                DB::raw('(pp.cantidad * pp.precio_unitario) as total')
            )
            ->where('pp.id_estadia', '=', $id)
            ->get();

        // Obtener productos disponibles para agregar nuevos consumos
        $productos = DimProductoBodega::orderBy('nombre')->get();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();

        return view('registros.consumo-edit', compact('estadia', 'cliente', 'consumos', 'productos', 'metodos'));
    }

    /**
     * AGREGAR NUEVO CONSUMO
     * URL: POST /registros/{id}/consumo
     */
    public function storeConsumo(Request $request, $id)
    {
        $request->validate([
            'id_prod_bod' => 'required|exists:dim_productos_bodega,id_prod_bod',
            'cantidad' => 'required|integer|min:1|max:999',
            'precio_unitario' => 'required|numeric|min:0.01|max:99999.99',
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'comprobante' => 'nullable|in:SI,NO',
        ]);

        try {
            // Verificar que la estadía existe
            $estadia = FactRegistroCliente::findOrFail($id);

            $consumo = new FactPagoProd();
            $consumo->id_estadia = $id;
            $consumo->id_prod_bod = $request->input('id_prod_bod');
            $consumo->cantidad = $request->input('cantidad');
            $consumo->precio_unitario = $request->input('precio_unitario');
            $consumo->id_met_pago = $request->input('id_met_pago');
            $consumo->comprobante = $request->input('comprobante', 'NO');
            $consumo->save();

            return redirect()->route('registros.consumo', $id)
                ->with('success', 'Consumo agregado correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors('Error al agregar consumo: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ACTUALIZAR CONSUMO EXISTENTE
     * URL: PUT /registros/{id}/consumo/{consumoId}
     */
    public function updateConsumo(Request $request, $id, $consumoId)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1|max:999',
            'precio_unitario' => 'required|numeric|min:0.01|max:99999.99',
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'comprobante' => 'nullable|in:SI,NO',
        ]);

        try {
            $consumo = FactPagoProd::where('id_compra', $consumoId)
                                ->where('id_estadia', $id)
                                ->firstOrFail();

            $consumo->cantidad = $request->input('cantidad');
            $consumo->precio_unitario = $request->input('precio_unitario');
            $consumo->id_met_pago = $request->input('id_met_pago');
            $consumo->comprobante = $request->input('comprobante', 'NO');
            $consumo->save();

            return redirect()->route('registros.consumo', $id)
                ->with('success', 'Consumo actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors('Error al actualizar consumo: ' . $e->getMessage());
        }
    }

    /**
     * ELIMINAR CONSUMO - ✅ CORREGIDO
     * URL: DELETE /registros/{id}/consumo/{consumoId}
     */
    public function destroyConsumo($id, $consumoId)
    {
        try {
            // Obtener nombre del producto antes de eliminar
            $consumoData = DB::table('fact_pago_prod as pp')
                ->join('dim_productos_bodega as p', 'p.id_prod_bod', '=', 'pp.id_prod_bod')
                ->select('p.nombre as producto_nombre')
                ->where('pp.id_compra', $consumoId)
                ->where('pp.id_estadia', $id)
                ->first();

            if (!$consumoData) {
                return back()->withErrors('Consumo no encontrado.');
            }

            // Eliminar el consumo
            FactPagoProd::where('id_compra', $consumoId)
                    ->where('id_estadia', $id)
                    ->delete();

            return redirect()->route('registros.consumo', $id)
                ->with('success', "Consumo eliminado: {$consumoData->producto_nombre}");

        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar consumo: ' . $e->getMessage());
        }
    }

    // ELIMINAR ESTADÍA (y sus pagos relacionados)
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Borramos consumos
            FactPagoProd::where('id_estadia', $id)->delete();
            // Borramos pago habitación
            FactPagoHab::where('id_estadia', $id)->delete();
            // Borramos estadía
            FactRegistroCliente::where('id_estadia', $id)->delete();

            DB::commit();
            return redirect()->route('registros.index')->with('success', 'Registro eliminado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('No se pudo eliminar: '.$e->getMessage());
        }
    }

    /**
     * LOOKUP cliente por documento para verificación AJAX
     * URL: GET /registros/lookup-cliente?doc={documento}
     */
    public function lookupCliente(Request $request)
    {
        $doc = $request->query('doc');
        
        if (!$doc) {
            return response()->json(['ok' => false, 'message' => 'Documento requerido']);
        }

        $cliente = DimRegistroCliente::where('doc_identidad', $doc)->first();
        
        if (!$cliente) {
            return response()->json(['ok' => false, 'message' => 'Cliente no encontrado']);
        }

        return response()->json([
            'ok' => true,
            'doc_identidad' => $cliente->doc_identidad,
            'nombre_apellido' => $cliente->nombre_apellido,
        ]);
    }
}