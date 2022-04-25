<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
    </ol>
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Notas de Crédito
                <!-- <button type="button" @click="abrirModal('categoria','registrar')" class="btn btn-secondary" v-if="listado==0">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button> -->
            </div>
            <!-- Listado -->
            <template v-if="listado==0">
                <div class="card-body">
                    <div class="form-inline">
                        <div class="form-group mb-2 col-12">
                            <div class="input-group">
                                <select class="form-control mb-1" v-model="criterio">
                                    <option value="cliente">Cliente</option>
                                    <option value="foma_pago">Forma de pago</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <input type="text" v-model="buscar" @keyup.enter="listarCreditos(1,buscar,criterio,estado)" class="form-control mb-1" placeholder="Texto a buscar">
                                <button type="submit" @click="listarCreditos(1,buscar,criterio,estado)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                            </div>
                            <div class="input-group input-group-sm ml-xl-5 mt-1 mt-md-0">
                                <button class="btn btn-sm btn-danger" type="button"><i class="fa fa-question-circle-o" aria-hidden="true"></i>&nbsp; Estado</button>
                                <select class="form-control" v-model="estado" @change="listarCreditos(1,buscar,criterio,estado)">
                                    <option value="">Todos</option>
                                    <option value="Por Validar">Por Validar</option>
                                    <option value="Vigente">Vigente</option>
                                    <option value="Abonada">Abonada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>No° de documento</th>
                                <th>Total</th>
                                <th>Forma de pago</th>
                                <th>Fecha</th>
                                <th>Observaciones</th>
                                <th>Cliente</th>
                                <th>Tipo de cliente</th>
                                <th>Estado</th>
                                <th>Validación</th>
                                <th>Vendedor</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayCreditos.length">
                                <tr v-for="credito in arrayCreditos" :key="credito.id">
                                    <td>
                                        <div class="form-inline">
                                            <template v-if="credito.estado != 'Abonada'">
                                                <button type="button" class="btn btn-danger btn-sm" @click="eliminarCredito(credito.id,credito.idcliente)">
                                                    <i class="icon-trash"></i>
                                                </button>&nbsp;
                                            </template>
                                        </div>
                                    </td>
                                    <td v-text="credito.num_documento"></td>
                                    <td v-text="credito.total"></td>
                                    <td v-text="credito.forma_pago"></td>
                                    <td> {{ convertDate(credito.fecha_hora) }} </td>
                                    <td v-text="credito.nota"></td>
                                    <td v-text="credito.nombre"></td>
                                    <td v-text="credito.tipo"></td>
                                    <td>
                                        <div v-if="credito.estado == 'Vigente'">
                                            <span class="badge badge-info">Vigente</span>
                                        </div>
                                        <div v-else-if="credito.estado == 'Abonada'">
                                            <span class="badge badge-success">Abonada</span>
                                        </div>
                                        <div v-else-if="credito.estado == 'Por Validar'">
                                            <span class="badge badge-warning">Por Validar</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <template v-if="credito.estado == 'Abonada'">
                                            <span class="badge badge-success">Abonada</span>
                                        </template>
                                        <template v-else>
                                            <input type="checkbox" :id="'chkEn'+credito.id"
                                            @change="cambiarEstado(credito.id,credito.estado,credito.num_documento)"
                                            :checked="credito.estado != 'Por Validar'">
                                            <template v-if="credito.estado == 'Vigente'">
                                                <label :for="'chkEn'+credito.id">Vigente</label>
                                            </template>
                                            <template v-else-if="credito.estado == 'Por Validar'">
                                                <label :for="'chkEn'+credito.id">Por Validar</label>
                                            </template>
                                        </template>
                                    </td>
                                    <td v-text="credito.vendedor"></td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <strong>NO hay creditos registrados o con ese criterio...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estado)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estado)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estado)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </template>
        </div>
    </div>
    <!-- Fin ejemplo de tabla Listado -->
  </main>
</template>

<script>
import moment from 'moment';
export default {
    data() {
        return {
            credito_id: 0,
            num_documento : '',
            total: 0,
            forma_pago : '',
            fecha_hora : '',
            observacion : '',
            estado : '',
            arrayCreditos: [],
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            criterio : 'cliente',
            buscar : '',
            estado : '',
            listado : 0

        };
    },
    computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                var from = this.pagination.current_page - this.offset;
                if(from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        },
    methods: {
        listarCreditos(page,buscar,criterio,estado){
            let me=this;
            var url= '/credito?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado=' + estado;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayCreditos = respuesta.creditos.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio,estado){
            let me = this;
            me.pagination.current_page = page;
            me.listarCreditos(page,buscar,criterio,estado);
        },
        cambiarEstado(id,estado,docnum){
            let me = this;
            var newEstado;
            if(estado == 'Por Validar'){
                newEstado = 'Vigente'
            }else if(estado == 'Vigente'){
                newEstado = 'Por Validar'
            }
            axios.put('/credito/cambiarEstado',{
                'id': id,
                'estado' : newEstado
            }).then(function (response) {
                if(newEstado == 'Vigente'){
                    swal.fire(
                    'Completado!',
                    `La nota de credito ${docnum} ha sido marcada como valida`,
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    `La nota de credito ${docnum} ha sido marcada como invalida`,
                    'warning')
                }
            }).catch(function (error) {
                console.log(error);
            });
            this.listarCreditos(this.pagination.current_page,this.buscar, this.criterio,this.estado);
        },
        eliminarCredito(id,idcliente){

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de eliminar esta nota de crédito?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/cliente/deleteCredit',{
                        'id': id
                    }).then(function (response) {
                        swal.fire(
                            'Eliminado!',
                            'La nota de credito ha sido eliminada con éxito.',
                            'success'
                        );
                        me.listarCreditos(me.pagination.current_page,me.buscar, me.criterio,me.estado);
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){

                }
            })

        },
        convertDate(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            /* console.log(datec); */
            return datec;
        },
    },
    mounted() {
        this.listarCreditos(1,this.buscar, this.criterio,this.estado);
    }
};
</script>
