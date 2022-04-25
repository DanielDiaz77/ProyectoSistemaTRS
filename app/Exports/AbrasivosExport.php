<?php

namespace App\Exports;

use App\Abrasivo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbrasivosExport implements FromCollection, WithHeadings
{
    protected $bodega;

    public function __construct($bodega)
    {
        $this->bodega = $bodega;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Abrasivo::select('abrasivos.id','abrasivos.codigo','abrasivos.sku',
                'abrasivos.precio_venta','abrasivos.ubicacion','abrasivos.stock',
                'abrasivos.descripcion','abrasivos.comprometido','abrasivos.condicion')
            ->where([['abrasivos.ubicacion',$this->bodega],['abrasivos.stock','>=',1],['abrasivos.condicion',1]])
            ->orderBy('abrasivos.id', 'asc')->get();
    }

    public function headings(): array{
        return [
            '#',
            'SKU',
            'Codigo',
            'Material',
            'Precio',
            'Ubicacion',
            'Stock',
            'Descripcion',
            'Comprometido',
            'Condicion',
        ];
    }
}
