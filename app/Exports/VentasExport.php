<?php

namespace App\Exports;

use App\Deposit;
use App\Venta;
use App\DetalleVenta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExport implements FromCollection,WithHeadings
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
    public function collection(){
        return Venta::join('personas','ventas.idcliente','=','personas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->leftJoin('deposits','ventas.id','=','deposits.depositable_id')
        ->select(
            'ventas.tipo_comprobante',
            'ventas.num_comprobante',
            'ventas.fecha_hora',
            'ventas.impuesto',
            'ventas.total',
            'ventas.moneda',
            'ventas.tipo_cambio',
            'ventas.forma_pago',
            'ventas.tiempo_entrega',
            'ventas.lugar_entrega',
            'ventas.num_cheque',
            'ventas.banco',
            'ventas.tipo_facturacion',
            'deposits.total as tot',
            'deposits.created_at',
            'deposits.forma_pago as pagos',
            'users.usuario',
            'ventas.special',
            'personas.nombre')

        ->Users($this->ArrUsuarios)
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
            'No° de presupuesto',
            'Fecha y hora',
            'IVA',
            'Total',
            'Moneda',
            'Tipo de cambio',
            'Forma de pago',
            'Tiempo de entrega',
            'Lugar de entrega',
            'Cheque',
            'Banco',
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
