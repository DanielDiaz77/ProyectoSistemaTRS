<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Project;

class ProjectsExport implements FromCollection,WithHeadings
{
    protected $inicio;
    protected $fin;
    protected $ArrUsuarios;

    public function __construct($inicio, $fin,$ArrUsuarios)
    {
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->ArrUsuarios = $ArrUsuarios;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        return Project::leftjoin('personas AS creador','creador.id','=','projects.idusuario')
        ->leftjoin('personas AS clientes', 'clientes.id','=','projects.idcliente')
        ->select(
            'projects.tipo_comprobante',
            'projects.num_comprobante',
            'projects.title',
            'projects.content',
            'projects.inicio',
            'projects.fin',
            'projects.impuesto',
            'projects.total',
            'projects.adeudo',
            'projects.forma_pago',
            'projects.lugar_entrega',
            'projects.estado',
            'projects.pagado',
            'projects.pagado_parcial',
            'projects.entregado',
            'projects.entregado_parcial',
            'projects.flete',
            'projects.instalacion',
            'projects.area',
            'projects.tipo_facturacion',
            'projects.observacion',
            'projects.observacionpriv',
            'creador.nombre as usuario',
            'clientes.nombre as cliente',
            'clientes.tipo',
            'clientes.telefono',
            'clientes.rfc',
            'clientes.cfdi',
            'clientes.company',
            'clientes.tel_company')
        ->Users($this->ArrUsuarios)
        ->whereBetween('projects.created_at', [$this->inicio, $this->fin])
        ->orderBy('projects.created_at', 'asc')->get();
    }

    public function headings(): array{
        return [
            'Comprobante',
            'No° de presupuesto',
            'Titulo',
            'Descripcion',
            'Inicio',
            'Fecha Compromiso',
            'IVA',
            'Valor del proyecto',
            'Adeudo',
            'Forma de pago',
            'Lugar de entrega',
            'Estado',
            'Pagado',
            'Pagado Parcialmente',
            'Entregado',
            'Entregado Parcialmente',
            'Flete p/p cliente',
            'Instalacion',
            'Area',
            'Tipo de facturacion',
            'Observaciones',
            'Observaciones Privadas',
            'Atendio',
            'Cliente',
            'Tipo d/Cliente',
            'Telefono d/Cliente',
            'RFC d/Cliente',
            'Uso CFDI d/Cliente',
            'Contacto d/Cliente',
            'Tel/Contacto d/Cliente'
        ];
    }
}
