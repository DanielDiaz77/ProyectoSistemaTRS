<?php

namespace App\Exports;

use App\Deposit;
use App\Salida;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalidasExport implements FromCollection
{

    protected $inicio;
    protected $fin;
    protected $ArrUsuarios;

    public function __construct($inicio, $fin,$ArrUsuarios)
    {
        $this->inicio = $inicio.' 00:00:00';
        $this->fin = $fin.' 23:59:59';
        $this->ArrUsuarios = $ArrUsuarios;

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Salida::join('personas','salidas.idcliente','=','personas.id')
        ->join('users','salidas.idusuario','=','users.id')
        ->leftJoin('deposits','salidas.id','=','deposits.depositable_id')
        ->select(
            'salidas.tipo_comprobante',
            'salidas.num_comprobante',
            'salidas.fecha_hora',
            'salidas.impuesto',
            'salidas.forma_pago',
            'salidas.tiempo_entrega',
            'salidas.lugar_entrega',
            'salidas.tipo_facturacion',
            'deposits.total as tot',
            'deposits.created_at',
            'deposits.forma_pago as pagos',
            'users.usuario',
            'personas.nombre')

        ->Users($this->ArrUsuarios)
        ->whereBetween('salidas.fecha_hora', [$this->inicio, $this->fin])
        ->orderBy('salidas.forma_pago', 'desc')->get();

        Deposit::with('venta')
        ->select('deposits.total','deposits.forma_pago','deposits.depositable_id','deposits.fecha_hora')
        ->where('deposits.depositable_id')
        ->get();
    }
    public function headings(): array{
        return [
            'Comprobante',
            'No° de presupuesto',
            'Fecha y hora',
            'IVA',
            'Total',
            'Forma de pago',
            'Tiempo de entrega',
            'Lugar de entrega',
            'Facturacion',
            'Abonado',
            'Fecha del Abono',
            'Forma del Abono',
            'Vendedor',
            'Presupuesto Especial',
            'Cliente'
        ];
    }
}
