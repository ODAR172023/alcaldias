<?php

namespace App\Livewire;
use App\Models\SesionCajaModelo;

use Livewire\Component;

class Sesiones extends Component
{
    public $Modal = false;
    public $monto_inicial;
    public $sesiones = null;

    public function mount()
    {
        // Verificar si existe un registro con status = 0
        $sesiones = SesionCajaModelo::where('status', 1)->first();
        
        // Redirigir a la ruta correspondiente
        if ($sesiones != null) {
            return redirect()->to('/cobros');
        } else {
            return view('livewire.sesiones');
        }
    }


    //public function render()
    //{
    //    return view('livewire.sesiones');
    //}

    public function store()
    {
        $validatedDate = $this->validate([
            'monto_inicial' => 'required',
        ]);

        $validatedDate['usuario_id'] = auth()->user()->id;
        $validatedDate['created_at'] = now();
        $validatedDate['status'] = 1;

        SesionCajaModelo::create($validatedDate);
  
        session()->flash('message', 'Se ha iniciado sesión exitosamente');
        $this->Modal = false;
      
    }

}
