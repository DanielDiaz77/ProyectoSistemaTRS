<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Traslado;
use App\DetalleTraslado;

class TrasladosExport implements FromCollection,WithHeadings
{

    protected $idtraslado;

    public function __construct($idtraslado){
        $this->idtraslado = $idtraslado;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        return $traslado = Traslado::join('detalle_traslados','detalle_traslados.idtraslado','traslados.id')
        ->leftJoin('users','traslados.idusuario','=','users.id')
        ->leftJoin('personas','users.id','=','personas.id')
        ->leftJoin('articulos','detalle_traslados.idarticulo','articulos.id')
        ->leftJoin('categorias','articulos.idcategoria','=','categorias.id')
        ->select(
            'traslados.num_comprobante',
            'traslados.nueva_ubicacion',
            'personas.nombre as usuario',
            'traslados.estado',
            'traslados.entregado',
            'categorias.nombre',
            'articulos.codigo',
            'articulos.sku',
            'articulos.largo',
            'articulos.alto',
            'articulos.metros_cuadrados',
            'articulos.terminado',
            'articulos.ubicacion',
            'detalle_traslados.ubicacion as ubicacionTR',
            'traslados.fecha_hora',
            'traslados.observacion')
        ->where('traslados.id',$this->idtraslado)->get();

    }
    public function headings(): array{
        return [
            'No° Traslado',
            'Destino',
            'Realizo',
            'Estado',
            'Entregado',
            'Material',
            'No° de placa',
            'Codigo de material',
            'Largo',
            'Alto',
            'Metros 2',
            'Terminado',
            'Ubicacion Actual',
            'Ubicacion Anterior',
            'Fecha',
            'Notas'
        ];
    }
}
