<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Contribuyente;
use App\Models\Servicio;
use App\Models\suscripcion;
use App\Models\OperacionesSesion;
use App\Models\SesionCajaModelo;
use App\Models\PagoServicio_has_servicios;
use App\Models\PagoServicios;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class Sesioncaja extends Controller
{
   
    public $servicioId;
    public $contribuyenteId;
    public $timestamps = false;

    public function show($id)
    {
        $contribuyente = Contribuyente::findOrFail($id);
        $servicios = Servicio::all();
        $suscripciones = suscripcion::all();
        //$sesioncaja = SesionCajaModelo::all();
        //$sesioncaja = SesionCajaModelo::where('status', 1)->get();
        $sesioncaja = SesionCajaModelo::where('status', 1)->first();
        $pagoservicios = PagoServicios::where('contribuyente_id', $id)->where('estado', 'Pendiente')->get();
        $totalAPagar = PagoServicios::where('contribuyente_id', $id)->whereNull('deleted_at')->where('estado', 'Pendiente')->sum('total');
        //$totalAPagar = DB::table('pago_servicios')->where('contribuyente_id', $id)->whereNull('deleted_at')->sum('total');


        return view('sesioncaja', compact('contribuyente', 'servicios', 'suscripciones', 'pagoservicios', 'totalAPagar', 'sesioncaja'));
    }

    public function imprimirFactura($id)
    {
 
    $contribuyente = Contribuyente::findOrFail($id);
    $pagoservicios = PagoServicios::where('contribuyente_id', $id)->get();
    $totalAPagar = PagoServicios::where('contribuyente_id', $id)->whereNull('deleted_at')->sum('total');

    // Devolver una vista con el diseño de la factura
    return view('facturacaja', compact('contribuyente', 'pagoservicios', 'totalAPagar'));
    }

    public function procesarPago(Request $request)
{
    // Validar datos del formulario
    $request->validate([
        'idsesioncaja' => 'required|numeric',
        'num_recibo' => 'required|string',
        'fecha' => 'required|string',
        'monto' => 'required|numeric',
        'contribuyente_id' => 'required|numeric',
    ]);
    

    // Obtener datos del formulario
    $idsesioncaja = $request->input('idsesioncaja');
    $num_recibo = $request->input('num_recibo');
    $fecha = $request->input('fecha');
    $monto = $request->input('monto');

    // Crear una nueva entrada en la tabla OperacionesSesion
    OperacionesSesion::create([
        'idsesioncaja' => $idsesioncaja, // Asegúrate de tener acceso a $sesion
        'num_recibo' => $num_recibo, // Si no tienes un número de recibo en este momento
        'fecha' => $fecha, // Fecha actual
        'monto' =>  $monto,
    ]);

    $contribuyenteId = $request->input('contribuyente_id'); 
    PagoServicios::where('contribuyente_id', $contribuyenteId)->update(['estado' => 'Pagado']);

    // Puedes realizar otras acciones aquí, como marcar los pagos como procesados, etc.

    return redirect()->back()->with('message', 'Pago procesado correctamente.');
}


    


}
