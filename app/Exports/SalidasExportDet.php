<?php

namespace App\Exports;
use App\Deposit;
use App\DetalleSalida;
use App\Salida;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalidasExportDet implements FromCollection,WithHeadings
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
        return Salida::join('detalle_salidas','detalle_salidas.idsalida','salidas.id')
        ->join('articulos','detalle_salidas.idarticulo','articulos.id')
        ->leftJoin('personas','salidas.idcliente','personas.id')
        ->leftJoin('users','salidas.idusuario','=','users.id')
        ->leftJoin('deposits','salidas.id','=','deposits.depositable_id')
        ->select(
            'salidas.num_comprobante',
            'salidas.fecha_hora',
            'salidas.forma_pago',
            'salidas.tipo_facturacion',
            'articulos.sku',
            'articulos.codigo',
            'articulos.ubicacion',
            'detalle_salidas.cantidad',
            'detalle_salidas.precio',
            'salidas.total as tot',
            'salidas.adeudo',
            'deposits.total',
            'deposits.created_at',
            'detalle_salidas.descuento',
            'users.usuario',
            'personas.nombre'
            )
        ->Users($this->ArrUsuarios)
        ->whereBetween('salidas.fecha_hora', [$this->inicio, $this->fin])
        ->orderBy('salidas.forma_pago', 'desc')->get();

        Deposit::with('salida')
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
            'Codigo Material',
            'Ubicacion',
            'Cantidad',
            'Precio',
            'Total de Presupuesto',
            'Adeudo',
            'Abonos',
            'Fecha del Abono',
            'Descuento',
            'Vendedor',
            'Cliente'
        ];
    }
}
