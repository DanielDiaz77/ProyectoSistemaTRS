<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\DetalleVenta;
use App\Venta;

class VentasClienteExport implements FromCollection,WithHeadings
{
    protected $idcliente;
    protected $date1;
    protected $date2;

    public function __construct($idcliente,$date1,$date2){
        $this->idcliente = $idcliente;
        $this->date1 = $date1.' 00:00:00';
        $this->date2 = $date2.' 23:59:59';
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        return DetalleVenta::join('ventas','detalle_ventas.idventa','ventas.id')
        ->join('articulos','detalle_ventas.idarticulo','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->leftJoin('personas','ventas.idcliente','personas.id')
        ->leftJoin('users','ventas.idusuario','=','users.id')

        ->select(
            'ventas.num_comprobante',
            'ventas.fecha_hora',
            'ventas.forma_pago',
            'ventas.total',
            'ventas.impuesto',
            'ventas.adeudo',
            'ventas.tiempo_entrega',
            'ventas.lugar_entrega',
            'ventas.entregado',
            'ventas.entrega_parcial',
            'ventas.pagado',
            'ventas.pago_parcial',
            'users.usuario as vendedor',
            'personas.nombre',
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
            'detalle_ventas.descuento'
        )
        ->where([['ventas.idcliente',$this->idcliente],['ventas.estado','Registrado']])
        ->whereBetween('ventas.fecha_hora', [$this->date1, $this->date2])
        ->orderBy('ventas.fecha_hora', 'desc')->get();
    }
    public function headings(): array{
        return [
            'No° de presupuesto',
            'Fecha y hora',
            'Forma de pago',
            'Total',
            'IVA',
            'Adeudo',
            'Tiempo de entrega',
            'Lugar de entrega',
            'Entregado',
            'Entregado Parcialmente',
            'Pagado',
            'Pagado Parcialmente',
            'Vendedor',
            'Cliente',
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
            'Descuento'
        ];
    }
}
