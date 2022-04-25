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
          <i class="fa fa-align-justify"></i> Actividades
        </div>
        <div class="card-body">
            <div class="form-inline">
                <div class="form-group mb-2 col-12">
                    <div class="input-group">
                        <select class="form-control mb-1" v-model="criterio">
                            <option value="title">Titulo</option>
                            <option value="cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="text" v-model="buscar" @keyup.enter="listarActividad(1,buscar,criterio,estado,area)" class="form-control mb-1" placeholder="Texto a buscar">
                        <button type="submit" @click="listarActividad(1,buscar,criterio,estado,area)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                    </div>&nbsp;
                    <div class="input-group">
                        <select class="form-control mb-1" v-model="estado" @change="listarActividad(1,buscar,criterio,estado,area)">
                            <option value="">Todo</option>
                            <option value="1">Completada</option>
                            <option value="0">Incompleta</option>
                        </select>
                        <button class="btn btn-primary mb-1"><li class="fa fa-question-circle"> Estado</li></button>
                    </div>&nbsp;
                    <div class="input-group">
                        <select class="form-control mb-1" v-model="area" @change="listarActividad(1,buscar,criterio,estado,area)">
                            <option value="">Todo</option>
                            <option value="GDL">Guadalajara</option>
                            <option value="SLP">San Luis</option>
                            <option value="AGUA">Aguascalientes</option>
                        </select>
                        <button class="btn btn-primary mb-1"><li class="fa fa-map-marker"> Area</li></button>
                    </div>
                    <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5">
                        <button @click="abrirModal()" class="btn btn-success btn-sm">Reporte <i class="fa fa-file-excel-o"></i></button>
                    </div>
                </div>
            </div>
            <div class="table-responsive col-md-12">
                <table class="table table-bordered table-striped table-sm table-hover">
                    <thead>
                    <tr>
                        <th>No°</th>
                        <th>Título</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Notas</th>
                        <th>Estado</th>
                        <th>Cliente</th>
                        <th>Area</th>
                    </tr>
                    </thead>
                    <tbody v-if="arrayEvento.length">
                        <tr v-for="(evento,index) in arrayEvento" :key="evento.id">
                            <td v-text="index+1"></td>
                            <td v-text="evento.title"></td>
                            <td> {{ convertDate(evento.start) }}</td>
                            <td> {{ convertDate(evento.end) }}</td>
                            <td v-text="evento.content" style="text-align: center;"></td>
                            <td>
                                <div v-if="evento.estado">
                                    <span class="badge badge-success">Completada</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Incompleta</span>
                                </div>
                            </td>
                            <td v-text="evento.cliente"></td>
                            <td>
                                <template v-if="evento.area == 'GDL'">
                                    Guadalajara
                                </template>
                                <template v-else-if="evento.area == 'SLP'">
                                    San Luis
                                </template>
                                <template v-else-if="evento.area == 'AGUA'">
                                    Aguascalientes
                                </template>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="8" class="text-center">
                                <strong>NO hay Actividades registradas con ese criterio...</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav>
                <ul class="pagination">
                    <li class="page-item" v-if="pagination.current_page > 1">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estado,area)">Ant</a>
                    </li>
                    <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estado,area)" v-text="page"></a>
                    </li>
                    <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                        <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estado,area)">Sig</a>
                    </li>
                </ul>
            </nav>
        </div>
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
      <!-- Modal exportar excel -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-success modal-md " role="document">
            <div class="modal-content content-export">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body ">
                    <!-- <h3 class="mb-3">Generar reporte de ventas</h3> -->
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Inicio: </strong></label>
                            <input type="date" class="form-control" v-model="fecha1">
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Fin: </strong></label>
                           <input type="date" class="form-control" v-model="fecha2">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-5" v-if="fecha1 && fecha2">
                        <div>
                            <button type="button" class="btn btn-primary ml-5" @click="listarExcel(fecha1,fecha2)">Descargar</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin exportar excel -->
  </main>
</template>

<script>
import moment from 'moment';
export default {
    data() {
        return {
            estado : "",
            area : "",
            arrayEvento: [],
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
            modal : 0,
            tituloModal : "",
            fecha1: "",
            fecha2 : ""
        };
    },
    computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
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
        listarActividad (page,buscar,criterio,estado,area){
            let me=this;
            var url= '/event/listarEventos?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estado + '&area='+ area;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayEvento = respuesta.actividades.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio,estado,area){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarActividad(page,buscar,criterio,estado,area);
        },
        convertDate(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            /* console.log(datec); */
            return datec;
        },
        abrirModal(){
            this.modal = 1;
            this.tituloModal = "Generar Reporte de actividades";
        },
        cerrarModal(){
            this.modal = 0;
            this.tituloModal = "";
            this.fecha1 = "";
            this.fecha2 = "";
        },
        listarExcel(inicio, fin){
            window.open('/event/ExportExcel?inicio=' + inicio + '&fin=' + fin);
            this.cerrarModal();
        },
    },
    mounted() {
        this.listarActividad(1,this.buscar,this.criterio,this.estado,this.area);
    }
};
</script>
