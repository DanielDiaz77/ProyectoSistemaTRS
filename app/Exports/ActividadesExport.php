<?php

namespace App\Exports;

use App\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActividadesExport implements FromCollection,WithHeadings
{
    protected $inicio;
    protected $fin;

    public function __construct($inicio, $fin)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Event::join('users','users.id','=','events.idusuario')
        ->join('personas','personas.id','=','events.idcliente')
        ->select(
            'events.title',
            'events.start',
            'events.end',
            'events.content',
            'personas.nombre as cliente',
            'events.area',
            'events.estado',
            'users.usuario as user',)
        ->whereBetween('events.start', [$this->inicio, $this->fin])
        ->orderBy('events.start','asc')->get();
    }
    public function headings(): array{
        return [
            'Titulo',
            'Inicio',
            'Fin',
            'Notas',
            'Cliente',
            'Area',
            'Estado',
            'Propietario'
        ];
    }
}
