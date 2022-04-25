<?php

namespace App\Exports;

use App\Deposit;
use App\DetalleVenta;
use App\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExportDet implements FromCollection,WithHeadings
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
    public function collection() {
        /* return DetalleVenta::all(); */
        return Venta::join('detalle_ventas','detalle_ventas.idventa','ventas.id')
        ->join('articulos','detalle_ventas.idarticulo','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->leftJoin('personas','ventas.idcliente','personas.id')
        ->leftJoin('users','ventas.idusuario','=','users.id')
        ->leftJoin('deposits','ventas.id','=','deposits.depositable_id')
        ->select(
            'ventas.num_comprobante',
            'ventas.fecha_hora',
            'ventas.forma_pago',
            'ventas.tipo_facturacion',
            'articulos.sku',
            'articulos.codigo',
            'categorias.nombre as categoria',
            'articulos.largo',
            'articulos.alto',
            'articulos.metros_cuadrados',
            'articulos.espesor',
            'articulos.terminado',
            'articulos.ubicacion',
            'detalle_ventas.cantidad',
            'detalle_ventas.precio',
            'ventas.total as tot',
            'deposits.total',
            'deposits.created_at',
            'detalle_ventas.descuento',
            'users.usuario',
            'personas.nombre'
            )
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
            'No° Presupuesto',
            'Fecha y hora',
            'Forma de pago',
            'Tipo de facturacion',
            'SKU',
            'No° Placa',
            'Material',
            'Largo',
            'Alto',
            'Metros Cuadrados',
            'Espesor',
            'Terminado',
            'Ubicacion',
            'Cantidad',
            'Precio',
            'Total de Presupuesto',
            'Abonos',
            'Fecha del Abono',
            'Descuento',
            'Vendedor',
            'Cliente'
        ];
    }
}
