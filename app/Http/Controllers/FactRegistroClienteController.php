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
use Carbon\Carbon;
use App\Exports\huespedes_export; 
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class FactRegistroClienteController extends Controller
{
    // LISTADO REGISTRO
    public function index(Request $request)
    {
        $query = DB::table('fact_registro_clientes as fr')
            ->leftJoin('dim_registro_clientes as c', 'c.doc_identidad', '=', 'fr.doc_identidad')
            ->leftJoin('fact_pago_hab as ph', 'ph.id_estadia', '=', 'fr.id_estadia')
            ->leftJoin('dim_met_pago as mp', 'mp.id_met_pago', '=', 'ph.id_met_pago');

        // Aplicar filtros de fecha
        $filtro = $request->get('filtro', 'todos');
        
        switch ($filtro) {
            case 'hoy':
                $query->whereDate('fr.fecha_ingreso', today());
                break;
            case 'semana':
                $query->whereBetween('fr.fecha_ingreso', [
                    now()->startOfWeek(\Carbon\Carbon::SUNDAY),
                    now()->endOfWeek(\Carbon\Carbon::SATURDAY)
                ]);
                break;
            case 'personalizado':
                if ($request->fecha_inicio) {
                    $query->whereDate('fr.fecha_ingreso', '>=', $request->fecha_inicio);
                }
                if ($request->fecha_fin) {
                    $query->whereDate('fr.fecha_ingreso', '<=', $request->fecha_fin);
                }
                break;
            // 'todos' no aplica filtro
        }

        $registros = $query->select(
                'fr.id_estadia',
                'fr.habitacion',                
                'fr.hora_ingreso',               
                'fr.hora_salida',                   
                'c.nombre_apellido',              
                'fr.doc_identidad',                 
                'c.fecha_nacimiento',             
                'c.estado_civil',                   
                'c.lugar_nacimiento',               
                'ph.monto as precio',               
                'mp.met_pago as metodo_pago',       
                'ph.boleta',                        
                'fr.obs',                           
                'fr.fecha_ingreso',
                'fr.fecha_salida', 
                'fr.turno',
                'fr.fecha_ingreso_real',
                'fr.hora_ingreso_real'
            )
            ->groupBy(
                'fr.id_estadia',
                'fr.habitacion',
                'fr.hora_ingreso',
                'fr.hora_salida',        
                'fr.fecha_ingreso',
                'fr.fecha_salida', 
                'c.nombre_apellido',
                'fr.doc_identidad',
                'c.fecha_nacimiento',        
                'c.estado_civil',            
                'c.lugar_nacimiento',        
                'ph.monto',
                'mp.met_pago',
                'ph.boleta',
                'fr.obs',
                'fr.turno',
                'fr.fecha_ingreso_real',
                'fr.hora_ingreso_real',                      
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
        $habitaciones = [
            201, 202, 203, 204, 205, 206, 207, 208, 209, 210,
            301, 302, 303, 304, 305, 306, 307, 308, 309, 310,
            401, 402, 403, 404, 405, 406, 407, 408, 409, 410
        ];

        return view('registros.edit', compact('estadia', 'pago', 'metodos', 'habitaciones'));
    }

    // FORM AGREGAR REGISTRO
    public function create()
    {
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        $habitaciones = [
            201, 202, 203, 204, 205, 206, 207, 208, 209, 210,
            301, 302, 303, 304, 305, 306, 307, 308, 309, 310,
            401, 402, 403, 404, 405, 406, 407, 408, 409, 410
        ];

        return view('registros.create', compact('metodos', 'habitaciones'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'obs'           => 'nullable|string|max:1000',
            'hora_ingreso'  => 'required',
            'hora_salida'   => 'nullable',    
            'fecha_salida' => 'nullable|date',       
            'fecha_ingreso' => 'required|date',
            'habitacion'    => 'required|in:201,202,203,204,205,206,207,208,209,210,301,302,303,304,305,306,307,308,309,310,401,402,403,404,405,406,407,408,409,410', 
            'turno'         => 'required|in:0,1',
            'fecha_ingreso_real' => 'nullable|required_if:turno,1|date',
            'hora_ingreso_real' => 'nullable|required_if:turno,1',
            'monto'         => 'required|numeric|min:0',
            'id_met_pago'   => 'required|exists:dim_met_pago,id_met_pago',
            'boleta'        => 'nullable|in:SI,NO',
            'ciudad_procedencia' => 'nullable|string|max:100',    
            'ciudad_destino' => 'nullable|string|max:100',         
            'motivo_viaje' => 'nullable|string|max:100',          
            'placa_vehiculo' => 'nullable|string|max:20', 
        ]);

        DB::beginTransaction();
        try {
            $estadia = FactRegistroCliente::findOrFail($id);

            $estadia->hora_ingreso  = $request->input('hora_ingreso');
            $estadia->hora_salida   = $request->input('hora_salida');    
            $estadia->fecha_ingreso = $request->input('fecha_ingreso');
            $estadia->fecha_salida = $request->input('fecha_salida');
            $estadia->habitacion    = $request->input('habitacion');
            $estadia->obs           = $request->input('obs');
            $estadia->turno         = $request->input('turno');
            $estadia->ciudad_procedencia = $request->input('ciudad_procedencia');  
            $estadia->ciudad_destino = $request->input('ciudad_destino');          
            $estadia->motivo_viaje = $request->input('motivo_viaje');              
            $estadia->placa_vehiculo = $request->input('placa_vehiculo'); 
            
            // Campos auxiliares solo para turno NOCHE
            if ($request->input('turno') == 1) {
                $estadia->fecha_ingreso_real = $request->input('fecha_ingreso_real');
                $estadia->hora_ingreso_real = $request->input('hora_ingreso_real');
            } else {
                // Si cambia a turno DÍA, limpiar campos auxiliares
                $estadia->fecha_ingreso_real = null;
                $estadia->hora_ingreso_real = null;
            }

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
        \Log::info('=== STORE DEBUG ===');
        \Log::info('Request data:', $request->all());

        try {
            // Validación básica
            $request->validate([
                'doc_identidad' => 'required|string|max:20',
                'nombre_apellido' => 'required|string|max:100',
                'obs' => 'nullable|string|max:1000',
                'hora_ingreso' => 'required',
                'hora_salida' => 'nullable', 
                'fecha_salida' => 'nullable|date',            
                'fecha_ingreso' => 'required|date',
                'habitacion' => 'required|in:201,202,203,204,205,206,207,208,209,210,301,302,303,304,305,306,307,308,309,310,401,402,403,404,405,406,407,408,409,410', 
                'turno' => 'required|in:0,1',
                'fecha_ingreso_real' => 'nullable|required_if:turno,1|date',
                'hora_ingreso_real' => 'nullable|required_if:turno,1',
                'monto' => 'required|numeric|min:0',
                'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
                'boleta' => 'nullable|in:SI,NO',
                'pagos.*.id_met_pago' => 'nullable|exists:dim_met_pago,id_met_pago',
                'pagos.*.monto' => 'nullable|numeric|min:0',
                'monto_individual' => 'nullable|numeric|min:0',
                'monto_boleta' => 'nullable|numeric|min:0',
                
            ]);

            \Log::info('Validación pasó correctamente');

            DB::beginTransaction();

            // 1) Cliente
            $doc = $request->input('doc_identidad');
            $cliente = DimRegistroCliente::find($doc);
            
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
            $fr->fecha_salida = $request->input('fecha_salida');
            $fr->habitacion = $request->input('habitacion');
            $fr->doc_identidad = $doc;
            $fr->obs = $request->input('obs');
            $fr->turno = $request->input('turno');
            $fr->ciudad_procedencia = $request->input('ciudad_procedencia');  
            $fr->ciudad_destino = $request->input('ciudad_destino');        
            $fr->motivo_viaje = $request->input('motivo_viaje');           
            $fr->placa_vehiculo = $request->input('placa_vehiculo');  
            
            // Campos auxiliares solo para turno NOCHE
            if ($request->input('turno') == 1) {
                $fr->fecha_ingreso_real = $request->input('fecha_ingreso_real');
                $fr->hora_ingreso_real = $request->input('hora_ingreso_real');
            }

            $fr->save();

            // 3) Pago principal
            if ($request->has('id_met_pago')) {
                $ph = new FactPagoHab();
                $ph->id_estadia = $fr->id_estadia;
                $ph->id_met_pago = $request->input('id_met_pago');
                $ph->monto = $request->input('monto_individual') ?? $request->input('monto');
                $ph->boleta = $request->input('boleta') === 'SI' ? 'SI' : 'NO';
                $ph->save();
            }

            /* 4) Pagos adicionales
            $pagos = $request->input('pagos', []);
            foreach ($pagos as $pago) {
                if (!empty($pago['id_met_pago']) && !empty($pago['monto'])) {
                    $ph = new FactPagoHab();
                    $ph->id_estadia = $fr->id_estadia;
                    $ph->id_met_pago = $pago['id_met_pago'];
                    $ph->monto = $pago['monto'];
                    $ph->boleta = $request->input('boleta') === 'SI' ? 'SI' : 'NO';
                    $ph->save();
                }
            } */

            // 4) Pagos adicionales
            \Log::info('=== PROCESANDO PAGOS ADICIONALES ===');
            $pagos = $request->input('pagos', []);
            \Log::info('Array de pagos recibido:', ['pagos' => $pagos, 'count' => count($pagos)]);

            foreach ($pagos as $index => $pago) {
                \Log::info("Iterando pago índice {$index}:", $pago);
                
                if (!empty($pago['id_met_pago']) && !empty($pago['monto'])) {
                    \Log::info("✅ Pago {$index} válido - Guardando...");
                    
                    $ph = new FactPagoHab();
                    $ph->id_estadia = $fr->id_estadia;
                    $ph->id_met_pago = $pago['id_met_pago'];
                    $ph->monto = $pago['monto'];
                    $ph->boleta = $request->input('boleta') === 'SI' ? 'SI' : 'NO';
                    $ph->save();
                    
                    \Log::info("✅ Pago guardado con ID: {$ph->id_pago}");
                } else {
                    \Log::warning("❌ Pago {$index} rechazado - Vacío o inválido");
                }
            }
            \Log::info('=== FIN PROCESAMIENTO PAGOS ADICIONALES ===');

            DB::commit();
            \Log::info('Registro guardado exitosamente');

            return redirect()->route('registros.index')->with('success', 'Registro creado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Error de validación:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error general:', ['message' => $e->getMessage()]);
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * GUARDAR CLIENTE NUEVO VIA AJAX
     */
    public function storeCliente(Request $request)
    {
        try {
            $request->validate([
                'doc_identidad' => 'required|string|max:20|unique:dim_registro_clientes,doc_identidad',
                'nombre_apellido' => 'required|string|max:100|min:3',
                'estado_civil' => 'nullable|string|max:20',
                'fecha_nacimiento' => 'nullable|date',
                'lugar_nacimiento' => 'nullable|string|max:100',
            ]);

            \Log::info('Validación pasó correctamente');

            $cliente = new DimRegistroCliente();
            $cliente->doc_identidad = $request->input('doc_identidad');
            $cliente->nombre_apellido = $request->input('nombre_apellido');
            $cliente->estado_civil = $request->input('estado_civil');
            $cliente->fecha_nacimiento = $request->input('fecha_nacimiento');
            $cliente->lugar_nacimiento = $request->input('lugar_nacimiento');
            $cliente->save();

            return response()->json([
                'ok' => true,
                'message' => 'Cliente guardado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
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

    /**
     * EXPORTAR A EXCEL CON FILTROS
     */
    public function exportExcel(Request $request)
    {
        $registros = $this->getFilteredData($request);
        
        $filename = 'libro_huespedes.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($registros) {
            $file = fopen('php://output', 'w');
            
            // Encabezados
            fputcsv($file, [
                'Nro', 'Nombre y Apellidos', 'Sexo', 'Edad', 'Fecha Nac.',
                'Lugar Nac.', 'Nacionalidad', 'Documento', 'DOC.NRO',
                'Estado Civil', 'Profesión', 'Ciudad Proc.', 'Ciudad Dest.',
                'Motivo Viaje', 'N° Hab.', 'Fecha Ingreso', 'Hora Ingreso',
                'Fecha Salida', 'Hora Salida', 'Turno', 'Método Pago', 'Monto'
            ]);
            
            // Datos
            $contador = 1;
            foreach($registros as $registro) {
                fputcsv($file, [
                    $contador++,
                    $registro->nombre_apellido,
                    $registro->sexo,
                    $registro->fecha_nacimiento ? \Carbon\Carbon::parse($registro->fecha_nacimiento)->age : '',
                    $registro->fecha_nacimiento ? \Carbon\Carbon::parse($registro->fecha_nacimiento)->format('d/m/Y') : '',
                    $registro->lugar_nacimiento,
                    $registro->nacionalidad,
                    $registro->doc_identidad,
                    preg_replace('/[^0-9]/', '', $registro->doc_identidad),
                    $registro->estado_civil,
                    $registro->profesion_ocupacion,
                    $registro->ciudad_procedencia,
                    $registro->ciudad_destino,
                    $registro->motivo_viaje,
                    $registro->habitacion,
                    $registro->fecha_ingreso_real ?: $registro->fecha_ingreso,
                    $registro->hora_ingreso_real ?: $registro->hora_ingreso,
                    $registro->fecha_salida,
                    $registro->hora_salida,
                    $registro->turno == 0 ? 'DÍA' : 'NOCHE',
                    $registro->metodo_pago,
                    $registro->precio
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * EXPORTAR A PDF CON FILTROS
     */
    public function exportPDF(Request $request)
    {
        $registros = $this->getFilteredData($request);
        
        $pdf = Pdf::loadView('exports.huespedes-pdf', compact('registros'))
                ->setPaper('a4', 'landscape');
        
        return $pdf->download('libro_huespedes.pdf');
    }

    /**
     * OBTENER DATOS FILTRADOS (misma lógica que index)
     */
    private function getFilteredData(Request $request)
    {
        $query = DB::table('fact_registro_clientes as fr')
            ->leftJoin('dim_registro_clientes as c', 'c.doc_identidad', '=', 'fr.doc_identidad')
            ->leftJoin('fact_pago_hab as ph', 'ph.id_estadia', '=', 'fr.id_estadia')
            ->leftJoin('dim_met_pago as mp', 'mp.id_met_pago', '=', 'ph.id_met_pago');

        // Aplicar filtros de fecha (misma lógica que index)
        $filtro = $request->get('filtro', 'todos');
        
        switch ($filtro) {
            case 'hoy':
                $query->whereDate('fr.fecha_ingreso', today());
                break;
            case 'semana':
                $query->whereBetween('fr.fecha_ingreso', [
                    now()->startOfWeek(\Carbon\Carbon::SUNDAY),
                    now()->endOfWeek(\Carbon\Carbon::SATURDAY)
                ]);
                break;
            case 'personalizado':
                if ($request->fecha_inicio) {
                    $query->whereDate('fr.fecha_ingreso', '>=', $request->fecha_inicio);
                }
                if ($request->fecha_fin) {
                    $query->whereDate('fr.fecha_ingreso', '<=', $request->fecha_fin);
                }
                break;
            // 'todos' no aplica filtro
        }

        return $query->select(
                'fr.id_estadia',
                'fr.habitacion',                
                'fr.hora_ingreso',               
                'fr.hora_salida', 
                'fr.fecha_salida',                  
                'c.nombre_apellido',              
                'fr.doc_identidad',                 
                'c.fecha_nacimiento',             
                'c.estado_civil',                   
                'c.lugar_nacimiento',
                'c.nacionalidad',                   
                'c.sexo',                          
                'c.profesion_ocupacion',           
                'fr.ciudad_procedencia',           
                'fr.ciudad_destino',               
                'fr.motivo_viaje',                 
                'fr.placa_vehiculo',               
                'ph.monto as precio',               
                'mp.met_pago as metodo_pago',       
                'ph.boleta',                        
                'fr.obs',                           
                'fr.fecha_ingreso',
                'fr.turno',
                'fr.fecha_ingreso_real',
                'fr.hora_ingreso_real'
            )
            ->groupBy(
                'fr.id_estadia',
                'fr.habitacion',
                'fr.hora_ingreso',
                'fr.hora_salida',  
                'fr.fecha_salida',      
                'fr.fecha_ingreso',
                'c.nombre_apellido',
                'fr.doc_identidad',
                'c.fecha_nacimiento',        
                'c.estado_civil',            
                'c.lugar_nacimiento',
                'c.nacionalidad',            
                'c.sexo',                     
                'c.profesion_ocupacion',       
                'fr.ciudad_procedencia',       
                'fr.ciudad_destino',          
                'fr.motivo_viaje',             
                'fr.placa_vehiculo',          
                'ph.monto',
                'mp.met_pago',
                'ph.boleta',
                'fr.obs',
                'fr.turno',
                'fr.fecha_ingreso_real',
                'fr.hora_ingreso_real',                      
            )
            ->orderByDesc('fr.id_estadia')
            ->get();
    }
}