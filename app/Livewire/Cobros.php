<?php

namespace App\Livewire;
use App\Models\Contribuyente;
use App\Models\SesionCaja;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

use Livewire\Component;

class Cobros extends Component
{
    use WithPagination;
    public $search;
    public $updateModal = false;
    public $deleteModal = false;
    public $createModal = false;
    public $monto_inicial;
    public $contribuyentes;

    public function mount()
    {
        // Consulta SQL para obtener los contribuyentes con pagos pendientes
        $this->contribuyentes = DB::table('contribuyentes')
        ->join('pago_servicios', 'contribuyentes.id', '=', 'pago_servicios.contribuyente_id')
        ->select('contribuyentes.*')
        ->where('pago_servicios.estado', 'Pendiente')
        ->distinct()
        ->get();
    }

    public function render()
    {
        return view('livewire.cobros.cobros');
    }

    public function openModal()
    {
        $this->createModal = true;
    }

    public function closeModal()
    {
        $this->createModal = false;
    }
}