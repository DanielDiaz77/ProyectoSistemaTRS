<?php

namespace App\Exports;

use App\Deposit;
use App\Venta;
use App\DetalleVenta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbonosExport implements FromCollection,WithHeadings
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
    public function collection(){
        return Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->leftJoin('deposits','ventas.id','=','deposits.depositable_id')
        ->select(
            'ventas.tipo_comprobante',
            'users.usuario',
            'ventas.num_comprobante',
            'personas.nombre',
            'ventas.fecha_hora',
            'ventas.total',
            'ventas.forma_pago',
            'ventas.tipo_facturacion',
            'deposits.total as tot',
            'deposits.created_at',
            'deposits.forma_pago as pagos',
            'ventas.adeudo')
        ->whereBetween('ventas.fecha_hora', [$this->inicio, $this->fin])
        ->orderBy('ventas.forma_pago', 'desc')->get();

        Deposit::with('venta')
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
