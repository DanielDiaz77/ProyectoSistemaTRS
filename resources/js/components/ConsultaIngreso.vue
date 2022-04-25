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
          <i class="fa fa-align-justify"></i> Ingresos
        </div>
        <!-- Listado -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-12">
                        <select class="form-control mb-1" v-model="criterio">
                            <option value="num_comprobante">No° Comprobante</option>
                            <option value="fecha_hora">Fecha</option>
                            <option value="estado">Estado</option>
                        </select>
                        <div class="input-group">
                            <input type="text" v-model="buscar" @keyup.enter="listarIngreso(1,buscar,criterio)" class="form-control mb-1" placeholder="Texto a buscar">
                            <button type="submit" @click="listarIngreso(1,buscar,criterio)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Usuario</th>
                                <th>Proveedor</th>
                                <th>Tipo Comprobante</th>
                                <th>No° Comprobante</th>
                                <th>Fecha Hora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayIngreso.length">
                            <tr v-for="ingreso in arrayIngreso" :key="ingreso.id">
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" @click="verIngreso(ingreso.id)">
                                        <i class="icon-eye"></i>
                                    </button>&nbsp;
                                </td>
                                <td v-text="ingreso.usuario"></td>
                                <td v-text="ingreso.nombre"></td>
                                <td v-text="ingreso.tipo_comprobante"></td>
                                <td v-text="ingreso.num_comprobante"></td>
                                <td v-text="ingreso.fecha_hora"></td>
                                <td v-text="ingreso.estado "></td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <strong>NO hay ingresos registrados o con ese criterio...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->
         <!-- Ver ingreso -->
        <template v-else-if="listado==2">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Proveedor</label>
                            <p v-text="proveedor"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Tipo Comprobante</label>
                            <p v-text="tipo_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Número de Comprobante</label>
                            <p v-text="num_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Registrado por:</label>
                            <p v-text="user"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Fecha:</label>
                            <p v-text="fecha_llegada"></p>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Detalles</th>
                                    <th>Código de material</th>
                                    <th>No° Placa</th>
                                    <th>Espesor</th>
                                    <th>largo</th>
                                    <th>Alto</th>
                                    <th>Metros <sup>2</sup></th>
                                    <th>Cantidad</th>
                                    <th>Precio M<sup>2</sup></th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td>
                                        <button type="button" @click="abrirModal3(index)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                    </td>
                                    <td v-text="detalle.sku"></td>
                                    <td v-text="detalle.codigo"></td>
                                    <td v-text="detalle.espesor"></td>
                                    <td v-text="detalle.largo"></td>
                                    <td v-text="detalle.alto"></td>
                                    <td v-text="detalle.metros_cuadrados"></td>
                                    <td v-text="detalle.cantidad"></td>
                                    <td v-text="detalle.precio_compra"></td>
                                    <td v-text="detalle.descripcion"></td>
                                    <td>
                                        <div v-if="detalle.condicion == 1">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else-if="detalle.condicion ==3">
                                            <span class="badge badge-warning">Cortado</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Desactivado</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <strong>NO hay artículos en este detalle...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin ver ingreso-->
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>

    <!--Inicio del modal Visualizar articulo detalle listado==2-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal3}" data-spy="scroll"  role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-info modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" v-text="tituloModal + sku"></h4>
            <button type="button" class="close" @click="cerrarModal3()" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
                <h1 class="text-center" v-text="sku"></h1>
                <template v-if="file">
                    <lightbox class="m-0" album="" :src="'http://inventariostroystone.com/images/'+file">
                        <img class="img-responsive imgcenter" width="500px" :src="'http://inventariostroystone.com/images/'+file">
                    </lightbox>&nbsp;
                </template>
                <div v-if="condicion == 1" class="text-center">
                    <span class="badge badge-success">Activo</span>
                </div>
                <div v-else-if="condicion == 3" class="text-center">
                    <span class="badge badge-warning">Cortado</span>
                </div>
                <div v-else class="text-center">
                    <span class="badge badge-danger">Desactivado</span>
                </div>&nbsp;
                <table class="table table-bordered table-striped table-sm text-center table-hover">
                    <thead>
                        <tr class="text-center">
                            <th class="text-center" colspan="2">Detalle del artículo</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>NO° DE PLACA</strong></td>
                        <td v-text="codigo"></td>
                    </tr>
                    <tr>
                        <td><strong>MATERIAL</strong></td>
                        <select disabled class="form-control selectDetalle" v-model="idcategoria_r">
                            <option value="0" disabled>Seleccione un material</option>
                            <option class="text-center" v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                        </select>
                    </tr>
                    <tr>
                        <td><strong>CODIGO DE MATERIAL</strong></td>
                        <td v-text="sku"></td>
                    </tr>
                    <tr >
                        <td><strong>TERMINADO</strong></td>
                        <td v-text="terminado"></td>
                    </tr>
                    <tr >
                        <td><strong>LARGO</strong></td>
                        <td v-text="largo"></td>
                    </tr>
                    <tr >
                        <td><strong>ALTO</strong></td>
                        <td v-text="alto"></td>
                    </tr>
                    <tr >
                        <td><strong>METROS<sup>2</sup> </strong></td>
                        <td v-text="metros_cuadrados"></td>
                    </tr>
                    <tr >
                        <td><strong>ESPESOR</strong></td>
                        <td v-text="espesor"> </td>
                    </tr>
                    <tr >
                        <td><strong>PRECIO</strong></td>
                        <td v-text="precio_venta"></td>
                    </tr>
                    <tr >
                        <td><strong>BODEGA DE DESCARGA</strong></td>
                        <td v-text="ubicacion"></td>
                    </tr>
                    <tr >
                        <td><strong>Cantidad</strong></td>
                        <td v-text="cantidad"></td>
                    </tr>
                    <tr >
                        <td><strong>DESCRIPCION</strong></td>
                        <td v-text="descripcion_r"></td>
                    </tr>
                    <tr >
                        <td><strong>OBSERVACIONES</strong></td>
                        <td v-text="observacion_r"></td>
                    </tr>
                    <tr >
                        <td><strong>ORIGEN</strong></td>
                        <td v-text="origen"></td>
                    </tr>
                    <tr >
                        <td><strong>CONTENEDOR</strong></td>
                        <td v-text="contenedor"></td>
                    </tr>
                    <tr >
                        <td><strong>ESPESOR</strong></td>
                        <td v-text="espesor"></td>
                    </tr>
                    <tr >
                        <td><strong>FECHA DE LLEGADA</strong></td>
                        <td v-text="fecha_llegada"></td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <barcode :value="codigo" :options="{formar: 'EAN-13'}">
                            Sin código de barras.
                    </barcode>
                </div>
                <hr class="d-block d-sm-block d-md-none">
                <div class="float-right d-block d-sm-block d-md-none">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal3()">Cerrar</button>
                </div>
          </div>
          <div class="modal-footer d-none d-sm-none d-md-block">
            <button type="button" class="btn btn-secondary" @click="cerrarModal3()">Cerrar</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
  </main>
</template>
<script>
import vSelect from 'vue-select';
import VueBarcode from 'vue-barcode';
import VueLightbox from 'vue-lightbox';
import moment from 'moment';
Vue.component("Lightbox",VueLightbox);
export default {
    data() {
        return {
            idproveedor: 0,
            proveedor: '',
            user: '',
            nombre: "",
            tipo_comprobante: "FACTURA",
            num_comprobante: "",
            impuesto: 0.16,
            idarticulo : 0,
            articulo : "",
            codigo: "",
            condicion : 0,
            precio_venta : 0,
            cantidad : 0,
            total: 0.0,
            arrayArticulo : [],
            arrayIngreso : [],
            arrayDetalle : [],
            listado : 1,
            modal: 0,
            modal3: 0,
            ind : '',
            tituloModal: "",
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            criterio : 'num_comprobante',
            buscar : '',
            idcategoria_r :0,
            sku : '',
            terminado : '',
            largo : 0,
            alto : 0,
            metros_cuadrados : 0,
            espesor : 0,
            ubicacion : '',
            stock : 0,
            descripcion_r: '',
            observacion_r : '',
            origen : '',
            contenedor : '',
            fecha_llegada : '',
            file : '',
            imagenMinatura : '',
            arrayCategoria : []

        };
    },
    components: {
        vSelect,
        'barcode': VueBarcode
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
            },
            imagen(){
                return this.imagenMinatura;
            },
        },
    methods: {

        listarIngreso (page,buscar,criterio){
            let me=this;
            var url= '/ingreso?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayIngreso = respuesta.ingresos.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarIngreso(page,buscar,criterio);
        },
        mostrarDetalle(){
            this.listado = 0;
            this.codigo = "";
            this.idarticulo = 0;
            this.articulo = "";
            this.sku = "";
            this.idcategoria_r = 0;
            this.largo = 0;
            this.alto = 0;
            this.metros_cuadrados = 0;
            this.terminado = '';
            this.espesor = 0;
            this.precio_venta = 0;
            this.precio_venta = 0;
            this.cantidad = 0;
            this.file = '';
            this.origen = '';
            this.contenedor = '';
            this.fecha_llegada = '';
            this.ubicacion = '';
            this.arrayDetalle = [];
            this.idproveedor = 0;
            this.num_comprobante = 0;
            this.selectCategoria();
        },
        ocultarDetalle(){
            this.listado = 1;
            this.codigo = "";
            this.idarticulo = 0;
            this.articulo = "";
            this.sku = "";
            this.idcategoria_r = 0;
            this.largo = 0;
            this.alto = 0;
            this.metros_cuadrados = 0;
            this.terminado = '';
            this.espesor = 0;
            this.precio_venta = 0;
            this.precio_venta = 0;
            this.cantidad = 0;
            this.file = '';
            this.origen = '';
            this.contenedor = '';
            this.fecha_llegada = '';
            this.ubicacion = '';
            this.arrayDetalle = [];
            this.idproveedor = 0;
            this.num_comprobante = 0;
        },
        verIngreso(id){

            let me = this;
            me.listado = 2;

            //Obtener los datos del ingreso
            var arrayIngresoT=[];
            var url= '/ingreso/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayIngresoT = respuesta.ingreso;

                var fechaform  = arrayIngresoT[0]['fecha_hora'];

                me.proveedor = arrayIngresoT[0]['nombre'];
                me.tipo_comprobante=arrayIngresoT[0]['tipo_comprobante'];
                me.num_comprobante=arrayIngresoT[0]['num_comprobante'];
                me.user=arrayIngresoT[0]['usuario'];
                moment.locale('es');
                me.fecha_llegada=moment(fechaform).format('llll');
            })
            .catch(function (error) {
                console.log(error);
            });

            //Obtener los detalles del ingreso
            var url= '/ingreso/obtenerDetalles?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayDetalle = respuesta.detalles;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        selectCategoria(){
            let me=this;
            var url= '/categoria/selectCategoria';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayCategoria = respuesta.categorias;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        abrirModal3(index){
            let me = this;
            me.ind = index;
            me.modal3 = 1;
            me.tituloModal      = "Artículo ";
            me.sku              = me.arrayDetalle[index]['sku'];
            me.codigo           = me.arrayDetalle[index]['codigo'];
            me.idcategoria_r    = me.arrayDetalle[index]['idcategoria'];
            me.largo            = me.arrayDetalle[index]['largo'];
            me.alto             = me.arrayDetalle[index]['alto'];
            me.ubicacion        = me.arrayDetalle[index]['ubicacion'];
            me.terminado        = me.arrayDetalle[index]['terminado'];
            me.espesor          = me.arrayDetalle[index]['espesor'];
            me.precio_venta     = me.arrayDetalle[index]['precio_compra'];
            me.metros_cuadrados = me.arrayDetalle[index]['metros_cuadrados'];
            me.contenedor       = me.arrayDetalle[index]['contenedor'];
            me.fecha_llegada    = me.arrayDetalle[index]['fecha_llegada'];
            me.origen           = me.arrayDetalle[index]['origen'];
            me.cantidad         = me.arrayDetalle[index]['cantidad'];
            me.file             = me.arrayDetalle[index]['file'];
            me.descripcion_r    = me.arrayDetalle[index]['descripcion'];
            me.observacion_r    = me.arrayDetalle[index]['observacion'];
            me.condicion    = me.arrayDetalle[index]['condicion'];
            me.selectCategoria();
        },
        cerrarModal3() {
            this.modal3 = 0;
            this.sku = '';
            this.codigo = '';
            this.idcategoria_r = 0;
            this.largo = 0;
            this.alto = 0;
            this.terminado = '';
            this.espesor = 0;
            this.precio_venta = 0;
            this.metros_cuadrados = 0;
            this.stock = 0;
            this.file = '';
            this.descripcion_r = '';
            this.ind = '';
        }
    },
    mounted() {
        this.listarIngreso(1,this.buscar, this.criterio);
    }
};
</script>
<style>
    .modal-content {
      width: 100% !important;
      position: absolute !important;
    }
    .mostrar {
      display: list-item !important;
      opacity: 1 !important;
      position: absolute !important;
      background-color: #3c29297a !important;
    }
    .div-error {
      display: flex;
      justify-content: center;
    }
    .text-error {
      color: red !important;
      font-weight: bold;
    }
    @media (min-width: 600px){
        .btnagregar{
            margin-top: 2rem;
        }
    }
    .selectDetalle {
        background: white;
        border: none;
        text-align: center;

  }
</style>
