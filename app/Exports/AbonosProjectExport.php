<?php

namespace App\Exports;

use App\Project;
use App\Deposit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbonosProjectExport implements FromCollection,WithHeadings
{
    protected $inicio;
    protected $fin;

    public function __construct($inicio, $fin)
    {
        $this->inicio = $inicio.' 00:00:00';
        $this->fin = $fin.' 23:59:59';

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Project::join('personas','projects.idcliente','=','personas.id')
        ->join('users','projects.idusuario','=','users.id')
        ->leftJoin('deposits','projects.id','=','deposits.depositable_id')
        ->select(
            'projects.tipo_comprobante',
            'users.usuario',
            'projects.num_comprobante',
            'personas.nombre',
            'projects.inicio',
            'projects.total',
            'projects.forma_pago',
            'projects.tipo_facturacion',
            'deposits.total as tot',
            'deposits.created_at',
            'deposits.forma_pago as pagos',
            'projects.adeudo')
        ->whereBetween('projects.inicio', [$this->inicio, $this->fin])
        ->orderBy('projects.forma_pago', 'desc')->get();

        Deposit::with('project')
        ->select('deposits.total','deposits.forma_pago','deposits.depositable_id','deposits.fecha_hora')
        ->where('deposits.depositable_id')
        ->get();
    }
    public function headings(): array{
        return [
            'Comprobante',
            'Vendedor',
            'No° de presupuesto',
            'Cliente',
            'Fecha y hora',
            'Total',
            'Forma de pago',
            'Facturacion',
            'Abonado',
            'Fecha del Abono',
            'Forma del Abono',
            'Adeudo',


        ];
    }
}
