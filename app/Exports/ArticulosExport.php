<?php

namespace App\Exports;

use App\Articulo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArticulosExport implements FromCollection, WithHeadings
{
    protected $bodega;

    public function __construct($bodega)
    {
        $this->bodega = $bodega;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        return Articulo::join('categorias','articulos.idcategoria','=','categorias.id')
            ->select(
                'articulos.id','articulos.codigo','articulos.sku','categorias.nombre',
                'articulos.terminado','articulos.largo','articulos.alto','articulos.metros_cuadrados',
                'articulos.espesor','articulos.precio_venta','articulos.ubicacion','articulos.stock',
                'articulos.descripcion','articulos.observacion','articulos.origen','articulos.contenedor',
                'articulos.fecha_llegada','articulos.comprometido','articulos.condicion')
            ->where([['articulos.ubicacion',$this->bodega],['articulos.stock','>=',1],['articulos.condicion',1]])
            ->orderBy('articulos.id', 'asc')->get();
    }

    public function headings(): array{
        return [
            '#',
            'No° Placa',
            'Codigo',
            'Material',
            'Terminado',
            'Largo',
            'Alto',
            'Metros Cuadrados',
            'Espesor',
            'Precio',
            'Ubicacion',
            'Stock',
            'Descripcion',
            'Observacion',
            'Origen',
            'Contenedor',
            'Fecha de llegada',
            'Comprometido',
            'Condicion',
        ];
    }
}
