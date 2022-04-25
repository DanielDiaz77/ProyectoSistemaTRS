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
                <div class="d-flex justify-content-between">
                    <div>
                        <i class="fa fa-align-justify"></i> Actividades
                        <button type="button" class="btn btn-secondary" @click="abrirModal()">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div v-if="usrol == 1">
                         <div class="form-inline">
                            <div class="form-group">
                                 <div class="input-group">
                                    <i class="fa fa-map-marker"></i> Area &nbsp;
                                    <select class="form-control" v-model="zona" @change="listarEventos(zona)" >
                                        <option value=''>Todo</option>
                                        <option value="GDL">Guadalajara</option>
                                        <option value="SLP">San Luis</option>
                                    </select>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                  <iframe src="https://calendar.google.com/calendar/embed?height=800&amp;wkst=1&amp;bgcolor=%23abeaff&amp;
                  ctz=America%2FMexico_City&amp;src=c2lzdGVtYXNAdHJveXN0b25lLmNvbS5teA&amp;
                  src=ZXMubWV4aWNhbiNob2xpZGF5QGdyb3VwLnYuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&amp;color=%23039BE5&amp;color=%230B8043"
                  style="border-width:0" width="1200" height="800" frameborder="0" scrolling="no">
                  </iframe>
            </div>
        </div>
    </div>
    <!--Inicio del modal agregar/actualizar actividad-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content content-event">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row m-0">
                            <div class="col-md-5 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Cliente (*)</strong></label>
                                        <v-select :on-search="selectCliente" label="nombre" :options="arrayCliente" placeholder="Seleccionar un cliente..."
                                        :onChange="getDatosCliente">
                                        </v-select>
                                </div>
                            </div>&nbsp;
                            <template v-if="idcliente">
                                <div class="col-md-3 text-center">
                                    <div class="form-group" v-if="cliente">
                                        <label><strong>Cliente</strong></label>
                                        <h4><strong v-text="cliente"></strong></h4>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="form-group" v-if="tipo">
                                        <label for=""><strong>Tipo de cliente</strong></label>
                                        <h6 for=""><strong v-text="tipo"></strong></h6>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="form-group" v-if="rfc">
                                        <label for=""><strong>RFC</strong></label>
                                        <h6 for=""><strong v-text="rfc"></strong></h6>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center sinpadding" v-if="telefono">
                                    <div class="form-group">
                                        <label for=""><strong>Teléfono</strong></label>
                                        <h6 for=""><strong v-text="telefono"></strong></h6>
                                    </div>
                                </div>&nbsp;
                                <div class="col-md-4 text-center sinpadding" v-if="company">
                                    <div class="form-group">
                                        <label for=""><strong>Contacto</strong></label>
                                        <h6 for=""><strong> {{company}} | {{tel_company}}</strong></h6>
                                    </div>
                                </div>&nbsp;
                            </template>
                        </div>
                        <div class="form-group row m-0">
                            <label class="col-md-3 form-control-label m-2 p-0 mr-0 order-md-1 order-1"><strong>Inicio</strong></label>
                            <label class="col-md-3 form-control-label m-2 p-0 mr-0 order-md-2 order-3"><strong>Fin</strong></label>
                            <label class="col-md-4 form-control-label m-2 p-0 order-md-3 order-5"><strong>Color</strong></label>
                            <div class="col-12 col-md-3 mb-2 order-md-4 order-2">
                                <date-picker name="date" v-model="start" :config="options"></date-picker>
                            </div>
                            <div class="col-12 col-md-3 mb-2 order-md-5 order-4">
                                <date-picker name="date" v-model="end" :config="options"></date-picker>
                            </div>
                            <div class="col col-md-3 mb-2 order-md-6 order-1 order-6">
                                <select class="form-control" v-model="clase">
                                    <option value="red" style="background-color: rgba(217,83,79);color: #FFFFFF;">Rojo</option>
                                    <option value="blue" style="background-color: rgba(66,139,202);color: #FFFFFF;">Azul</option>
                                    <option value="green" style="background-color: rgba(17, 192, 32, 0.9);color: #FFFFFF;">Verde</option>
                                    <option value="orange" style="background-color: rgba(253, 165, 0, 0.945);color: #FFFFFF;">Naranja</option>
                                    <option value="purple" style="background-color: rgba(181, 32, 250, 0.9);color: #FFFFFF;">Morado</option>
                                    <option value="yellow" style="background-color: rgba(250, 234, 9, 0.986);">Amarillo</option>
                                    <option value="aqua" style="background-color: rgba(124, 255, 203, 0.986);">AquaMarina</option>
                                    <option value="ruby" style="background-color: rgba(216, 17, 89, 0.986);color: #FFFFFF;">Ruby</option>
                                    <option value="darkblue" style="background-color: rgba(23, 42, 58, 0.9);color: #FFFFFF;">Azul Marino</option>
                                    <option value="brown" style="background-color: rgba(165, 117, 72, 0.9);color: #FFFFFF;">Café</option>
                                    <option value="lavender" style="background-color: rgba(120, 85, 137, 0.726);color: #FFFFFF;">Lavanda</option>
                                </select>
                            </div>
                            <div class="col-2 col-md-1 mb-2 order-md-7 order-7">
                                <div v-if="clase=='red'" class="m-2 clrred"></div>
                                <div v-if="clase=='blue'" class="m-2 clrblue"></div>
                                <div v-if="clase=='green'" class="m-2 clrgreen"></div>
                                <div v-if="clase=='orange'" class="m-2 clrorange"></div>
                                <div v-if="clase=='purple'" class="m-2 clrpurple"></div>
                                <div v-if="clase=='yellow'" class="m-2 clryellow"></div>
                                <div v-if="clase=='aqua'" class="m-2 clraqua"></div>
                                <div v-if="clase=='ruby'" class="m-2 clrruby"></div>
                                <div v-if="clase=='darkblue'" class="m-2 clrdarkblue"></div>
                                <div v-if="clase=='brown'" class="m-2 clrbrown"></div>
                                <div v-if="clase=='lavender'" class="m-2 clrlavender"></div>
                            </div>
                        </div>
                        <div class="form-group row m-0 d-flex justify-content-around">
                            <div class="col-8">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Título</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="title" placeholder="Titulo de la actividad" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                            </div>
                            <div class="col-4" v-if="usrol == 1">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Lugar</span>
                                    </div>
                                    <select class="form-control" v-model="area">
                                        <option value='' disabled>Lugar de la actividad</option>
                                        <option value="GDL">Guadalajara</option>
                                        <option value="SLP">San Luis</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row m-0">
                            <div class="col-12">
                                <!-- <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-addon btn btn-primary">Notas:</span>
                                    <textarea class="form-control custom-control rounded-0" v-model="content" rows="3" maxlength="256" style="resize:none"></textarea>
                                </div> -->
                                <template class="justify-content-center">
                                    <editor :options="editorOptions" mode="wysiwyg" v-model="content" Width="100%"/>
                                </template>
                            </div>
                            <template v-if="isEdition">
                            <div class="col mt-2">
                                <label for=""><strong>Actividad Completada:</strong></label>&nbsp;
                                 <toggle-button @change="cambiarEstadoEvent(eventid)" v-model="btnComp" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                            </div>
                            </template>
                        </div>
                        <!-- <div class="form-group row m-0">
                            <label class="col-md-3 form-control-label m-2 p-0 mr-0" for="text-area"><strong>Contendio</strong></label>
                            <div class="col-md-9">
                                <textarea placeholder="Contenido" class="form-control rounded-0 noresize" rows="3" maxlength="256" v-model="content"></textarea>
                            </div>&nbsp;
                        </div> -->
                        <div v-show="errorEvent" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjEvent" :key="error" v-text="error"></div>
                            </div>
                        </div>
                        <hr class="d-block d-sm-block d-md-none">
                        <div class="float-right d-block d-sm-block d-md-none">
                            <button type="button" v-if="isEdition==false" class="btn btn-primary" @click="registrarEvento()">Guardar</button>
                            <button type="button" v-if="isEdition" class="btn btn-primary" @click="actualizarEvento()">Actualizar</button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" v-if="isEdition==false" class="btn btn-primary" @click="registrarEvento()">Guardar</button>
                    <button type="button" v-if="isEdition" class="btn btn-primary" @click="actualizarEvento()">Actualizar</button>
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal agregar/actualizar actividad-->

    <!--Inicio del modal visualizar actividad-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content content-event2">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal2()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- INF CLIENTE -->
                    <div class="form-group row">
                        <div class="col-md-4 text-center">
                            <div class="form-group">
                                <h1 for="" class="float-left"><strong v-text="cliente"></strong></h1>
                            </div>
                        </div>&nbsp;
                        <div class="col-md-2 text-center sinpadding" v-if="telefono">
                            <div class="form-group">
                                <label for=""><strong>Teléfono</strong></label>
                                <h6 for=""><strong v-text="telefono"></strong></h6>
                            </div>
                        </div>&nbsp;
                        <div class="col-md-2 text-center sinpadding" v-if="tipo">
                            <div class="form-group">
                                <label for=""><strong>Tipo de cliente</strong></label>
                                <h6 for=""><strong v-text="tipo"></strong></h6>
                            </div>
                        </div>&nbsp;
                        <div class="col-md-2 text-center sinpadding" v-if="rfc">
                            <div class="form-group">
                                <label for=""><strong>RFC</strong></label>
                                <h6 for=""><strong v-text="rfc"></strong></h6>
                            </div>
                        </div>&nbsp;
                        <div class="col-md-4 text-center sinpadding" v-if="company">
                            <div class="form-group">
                                <label for=""><strong>Contacto</strong></label>
                                <h6 for=""><strong> {{company}} | {{tel_company}}</strong></h6>
                            </div>
                        </div>&nbsp;

                        <!-- <div class="col-md-3 text-center sinpadding" v-if="observacion">
                            <div class="form-group">
                                <label for=""><strong>Observaciones</strong></label>
                                <h6 for=""><strong> {{observacion}}</strong></h6>
                            </div>
                        </div>&nbsp; -->
                    </div>
                    <hr>
                    <div :class="['col-md','caja2-' + clase,'m-0','contentBox']">
                        <div class="row m-0 p-0">
                            <div class="col float-left">
                                <p class="text-danger font-weight-bold" style="font-size: 25px;" v-if="area == 'GDL'">Guadalajara <i class="fa fa-map-marker" aria-hidden="true"></i></p>
                                <p class="text-danger font-weight-bold" style="font-size: 25px;" v-else-if="area == 'SLP'">San Luis <i class="fa fa-map-marker" aria-hidden="true"></i></p>
                            </div>
                            <div class="col">
                                <template v-if="estado">
                                    <p class="text-success font-weight-bold float-right" style="font-size: 25px;">Completada <i class="fa fa-check-circle-o" aria-hidden="true"></i></p>
                                </template>
                                <template v-else>
                                    <p class="text-danger font-weight-bold float-right" style="font-size: 25px;">Incompleta <i class="fa fa-times-circle-o" aria-hidden="true"></i></p>
                                </template>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md col-10">
                                <h4 v-text="title"></h4>
                                <div class="form-inline">
                                    <div class="form-group mb-2">
                                        <div class="input-group">
                                            <p><small class="text-muted"><i class="fa fa-clock-o"></i>Inicio {{ start }}</small></p>&nbsp;
                                            <p><small class="text-muted"><i class="fa fa-clock-o"></i>Final {{ end }}</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md col-1">
                                <button type="button" class="btn btn-sm btntask float-right" @click="abriModalEditar(selectedEvent)">
                                    <i class="fa fa-pencil"></i>
                                </button>&nbsp;
                                <button type="button" class="btn btn-sm btntask float-right" @click="eliminarEvento(eventid)">
                                    <i class="fa fa-trash"></i>
                                </button>&nbsp;
                            </div>
                            <div class="col-md-12">
                                <!-- <p v-text="content"></p> <br> -->
                                <viewer :value="content"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" v-if="usrol == 1">
                        <div class="col-md-6 text-center mt-3">
                            <div class="form-group">
                                <h3 for="" class="float-left">Autor: <strong v-text="usuario"></strong></h3>
                            </div>
                        </div>&nbsp;
                    </div>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal2()">Cerrar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal2()">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal visualizar actividad-->
  </main>
</template>

<script>
import Vue from 'vue';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';
import 'vue-cal/dist/i18n/es.js';
import moment from 'moment';
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import vSelect from 'vue-select';
import ToggleButton from 'vue-js-toggle-button';
import 'tui-editor/dist/tui-editor.css';
import 'tui-editor/dist/tui-editor-contents.css';
import 'codemirror/lib/codemirror.css';
import 'highlight.js/styles/github.css';
import Editor from '@toast-ui/vue-editor/src/Editor.vue';
import { Viewer } from '@toast-ui/vue-editor'
Vue.use(datePicker);
Vue.use(ToggleButton);
//Vue.component('date-picker', VueBootstrapDatetimePicker);
export default {
    components: { VueCal,vSelect,'editor': Editor,'viewer': Viewer },
    data() {
        return {
            events: [],
            projects:[],
            arrayProyect: [],
            modal: 0,
            modal2: 0,
            selectedEvent: {},
            tituloModal: "",
            eventid : "",
            idusuario : "",
            title : "",
            content : "",
            clase : "",
            start : "",
            end : "",
            area: "",
            estado : 0,
            options: {
                format: 'YYYY-MM-DD HH:mm:ss',
                useCurrent: false,
                showClear: true,
                showClose: true,
                daysOfWeekDisabled: [0],
                /* minDate: moment().subtract(60, 'seconds'), */
                minDate : moment().startOf('month').format('YYYY-MM-DD hh:mm'),
                maxDate: moment().add(60, 'days'),
            },
            isEdition : false,
            isView : false,
            dateToday : "",
            errorEvent: 0,
            errorMostrarMsjEvent: [],
            idcliente : "",
            cliente : "",
            rfc : "",
            telefono : "",
            ciudad : "",
            domicilio : "",
            company : "",
            tel_company : "",
            email : "",
            tipo : "",
            observacion : "",
            arrayCliente : [],
            btnComp : false,
            usrol : 0,
            usarea : "",
            usuario : "",
            zona : "",
            editorOptions: {
                useCommandShortcut: true,
                useDefaultHTMLSanitizer: true,
                usageStatistics: true,
                hideModeSwitch: false,
                language: 'es_MX',
                toolbarItems: [
                    'heading',
                    'bold',
                    'italic',
                    'strike',
                    'divider',
                    'hr',
                    'quote',
                    'divider',
                    'ul',
                    'ol',
                    'task',
                    'indent',
                    'outdent',
                    'divider',
                    'table',
                    'link',
                    'divider',
                    'code',
                    'codeblock'
                ]
            },
        };
    },
    computed:{
        getDateToday : function(){
            let me = this;
            let date = "";
            moment.locale('es');
            date = moment().startOf('month').format('YYYY-MM-DD hh:mm');
            me.dateToday = moment().startOf('month').format('YYYY-MM-DD hh:mm');
            /* date = moment().format('YYYY-MM-DD');
            me.dateToday = moment().format('YYYY-MM-DD'); */
            return date;
        }
    },
    methods: {
        listarEventos(zona){
            let me=this;
            var url = '/event?zona=' + zona;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.events = response.data.eventos;
                me.projects = response.data.projects;
                /* console.log("Rol: " + respuesta.userrol); */
                me.usrol = respuesta.userrol;
                me.usarea = respuesta.userarea;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        abrirModal(){
            this.modal = 1;
            this.tituloModal = "Nueva Actividad";
            this.idusuario = "";
            this.title = "";
            this.content = "";
            this.clase = "green";
            this.start = "";
            this.end = "";
            this.isEdition = false;
            this.estado = 0;
            this.idcliente = "";
            this.cliente = "";
            this.rfc = "";
            this.telefono = "";
            this.ciudad = "";
            this.domicilio = "";
            this.company = "";
            this.tel_company = "";
            this.email = "";
            this.tipo = "";
            this.observacion = "";
            this.usuario = "";
            this.btnComp = false;
            this.area = this.usarea;
            /* this.options.minDate = moment().subtract(5, 'minutes'); */
            this.options.minDate = moment().startOf('month').format('YYYY-MM-DD hh:mm');
        },
        abriModalEditar(Event){
            this.modal = 1;
            this.modal2 = 0;
            this.tituloModal = "Editar " + Event.title;
            this.eventid = Event.id;
            this.idusuario = Event.userid;
            this.title = Event.title;
            this.content = Event.content;
            this.clase = Event.class;
            this.start = Event.start;
            this.end = Event.end;
            this.estado = Event.estado;
            this.area = Event.area;
            this.idcliente = Event.idcliente;
            this.cliente = Event.cliente;
            this.rfc = Event.rfc;
            this.telefono = Event.telefono;
            this.ciudad = Event.ciudad;
            this.domicilio = Event.domicilio;
            this.company = Event.company;
            this.tel_company = Event.tel_company;
            this.email = Event.email;
            this.tipo = Event.tipo;
            this.observacion = Event.observacion;
            this.errorMostrarMsjEvent = [];

            if(this.estado){
                this.btnComp = true;
            }
        },
        onEventClick (event, e) {
            this.selectedEvent = event;
            this.modal2 = 1;
            this.tituloModal = this.selectedEvent.title;
            this.eventid = this.selectedEvent.id;
            this.idusuario = this.selectedEvent.userid;
            this.title = this.selectedEvent.title;
            this.content = this.selectedEvent.content;
            this.clase = this.selectedEvent.class;
            this.start = this.selectedEvent.start;
            this.end = this.selectedEvent.end;
            this.estado = this.selectedEvent.estado;
            this.area = this.selectedEvent.area;

            this.idcliente = this.selectedEvent.idcliente;
            this.cliente = this.selectedEvent.cliente;
            this.rfc = this.selectedEvent.rfc;
            this.telefono = this.selectedEvent.telefono;
            this.ciudad = this.selectedEvent.ciudad;
            this.domicilio = this.selectedEvent.domicilio;
            this.company = this.selectedEvent.company;
            this.tel_company = this.selectedEvent.tel_company;
            this.email = this.selectedEvent.email;
            this.tipo = this.selectedEvent.tipo;
            this.observacion =  this.selectedEvent.observacion;
            this.usuario =  this.selectedEvent.user;
            this.isEdition = true;
            this.isView = true;
            e.stopPropagation()

            /* this.options.minDate = this.start; */
            this.options.minDate = moment().startOf('month').format('YYYY-MM-DD hh:mm');

            if(this.estado){
                this.btnComp = true;
            }

            /* console.log("MinDate EventClick " + this.options.minDate); */

           /*  var minDateCR = moment(this.options.minDate).format('YYYY-MM-DD HH:mm:ss');
            console.log("MinDate = "+ minDateCR);
            console.log("StarDate = "+ this.start); */

        },
        cerrarModal() {
            this.modal = 0;
            this.selectedEvent = {};
            this.tituloModal = "";
            this.eventid = "";
            this.idusuario = "";
            this.title = "";
            this.content = "";
            this.clase = "";
            this.start = "";
            this.end = "";
            this.estado = 0;
            this.idcliente = "";
            this.cliente = "";
            this.rfc = "";
            this.telefono = "";
            this.ciudad = "";
            this.domicilio = "";
            this.company = "";
            this.tel_company = "";
            this.email = "";
            this.tipo = "";
            this.observacion = "";
            this.isEdition = false;
            this.btnComp = false;
            this.usuario = "";
            this.area = "";
            /* this.options.minDate = moment().subtract(60, 'seconds').format('YYYY-MM-DD HH:mm:ss'); */
            this.options.minDate = moment().startOf('month').format('YYYY-MM-DD hh:mm');
            this.listarEventos(this.zona);

            /* console.log("MinDate at Close " + this.options.minDate); */
        },
        cerrarModal2() {
            this.modal2 = 0;
            this.selectedEvent = {};
            this.tituloModal = "";
            this.eventid = "";
            this.idusuario = "";
            this.title = "";
            this.content = "";
            this.clase = "";
            this.start = "";
            this.end = "";
            this.estado = 0;
            this.idcliente = "";
            this.cliente = "";
            this.rfc = "";
            this.telefono = "";
            this.ciudad = "";
            this.domicilio = "";
            this.company = "";
            this.tel_company = "";
            this.email = "";
            this.tipo = "";
            this.observacion = "";
            this.usuario = "";
            this.area = "";
            this.isEdition = false;
            this.btnComp = false;
            /* this.options.minDate = moment().format('YYYY-MM-DD HH:mm:ss'); */
            this.options.minDate = moment().startOf('month').format('YYYY-MM-DD hh:mm');
            this.listarEventos(this.zona);

            /* console.log("MinDate at Close " + this.options.minDate); */
        },
        selectCliente(search,loading){
            let me=this;
            loading(true)
            var url= '/cliente/selectCliente?filtro='+search;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                q: search
                me.arrayCliente=respuesta.clientes;
                loading(false)
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        getDatosCliente(val1){
            let me = this;
            me.loading = true;
            me.idcliente = val1.id;
            me.cliente = val1.nombre;
            me.rfc =  val1.rfc;
            me.tipo = val1.tipo;
            me.telefono = val1.telefono;
            me.company = val1.company;
            me.tel_company = val1.tel_company;
            me.observacion = val1.observacion;
        },
        validarEvent() {
            this.errorEvent = 0;
            this.errorMostrarMsjEvent = [];

            if (!this.title) this.errorMostrarMsjEvent.push("El titulo no puede estar vacio...");
            if (!this.content) this.errorMostrarMsjEvent.push("El contenido no puede estar vacio...");
            if (!this.start) this.errorMostrarMsjEvent.push("Selecciona la fecha de inicio...");
            if (!this.end) this.errorMostrarMsjEvent.push("Selecciona la fecha de final...");
            if (this.idcliente==0) this.errorMostrarMsjEvent.push("Seleccione un cliente");
            if (this.errorMostrarMsjEvent.length) this.errorEvent = 1;

            return this.errorEvent;
        },
        registrarEvento(){
            if (this.validarEvent()) {
                return;
            }

            let me = this;

            axios.post("/event/registrar", {
                'start': this.start,
                'end': this.end,
                'title': this.title,
                'content': this.content,
                'clase': this.clase,
                'area': this.area,
                'idcliente' : this.idcliente
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarEventos(this.zona);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarEvento(){
            if (this.validarEvent()) {
                return;
            }
            let me = this;
            axios.put("/event/actualizar", {
                'id': this.eventid,
                'start': this.start,
                'end': this.end,
                'title': this.title,
                'content': this.content,
                'clase': this.clase,
                'idcliente' : this.idcliente,
                'idusuario' : this.idusuario,
                'area': this.area,
                'estado' : this.estado
            })
            .then(function(response) {
                me.cerrarModal();
                me.listarEventos('');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        eliminarEvento(id){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de eliminar esta actividad?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.delete('/event/'+ id).then(function(response) {
                        me.cerrarModal2();
                        me.listarEventos('');
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El evento ha sido eliminado con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        cambiarEstadoEvent(id){
            let me = this;
            if(me.btnComp == true){
                me.estado = 1;
            }else{
                me.estado = 0;
            }
            var antestado = me.estado;
            axios.put('/event/completar',{
                'id': id,
                'estado' : this.estado
            }).then(function (response) {

                if(antestado == 1){
                  Swal.fire(
                    "Completado!",
                    "La actividad ha sido marcada como completada con éxito.",
                    "success"
                    )
                }else{
                    Swal.fire(
                        "Cambiado!",
                        "La actividad ha sido marcada como incompleta.",
                        "warning"
                    )
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
    },
    mounted() {
        this.listarEventos('');
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
    .content-event{
        height: 760px !important;
    }
    .content-event2{
        height: 650px !important;
    }
    /* Green-theme. */
    .vuecal__menu, .vuecal__cell-events-count {background-color: #42b983;}
    .vuecal__menu button {border-bottom-color: #fff;color: #fff;}
    .vuecal__menu button.active {background-color: rgba(255, 255, 255, 0.15);}
    .vuecal__title-bar {background-color: #e4f5ef;}
    .vuecal__cell.today, .vuecal__cell.current {background-color: rgba(240, 240, 255, 0.4);}
    .vuecal:not(.vuecal--day-view) .vuecal__cell.selected {background-color: rgba(235, 255, 245, 0.4);}
    .vuecal__cell.selected:before {border-color: rgba(66, 185, 131, 0.5);}
    /* Different color for different event types. */
    .vuecal__event.red {background-color: rgba(217,83,79);border: 1px solid rgb(217,83,79);color: #fff;}
    .vuecal__event.blue {background-color: rgba(66,139,202);border: 1px solid rgb(66,139,202);color: #fff;}
    .vuecal__event.green {background-color: rgba(17, 192, 32, 0.9);border: 1px solid rgb(17, 192, 32, 0.9);color: #fff;}
    .vuecal__event.orange {background-color: rgba(253, 165, 0, 0.945);border: 1px solid rgb(253, 165, 0, 0.945);color: #fff;}
    .vuecal__event.purple {background-color: rgba(181, 32, 250, 0.9);border: 1px solid rgb(181, 32, 250, 0.9);color: #fff;}
    .vuecal__event.yellow {background-color: rgba(250, 234, 9, 0.986);border: 1px solid rgb(250, 234, 9, 0.986);color: #000;}
    .vuecal__event.aqua {background-color: rgba(124, 255, 203, 0.986);border: 1px solid rgb(124, 255, 203, 0.986);color: #000;}
    .vuecal__event.ruby {background-color: rgba(216, 17, 89, 0.986);border: 1px solid rgb(216, 17, 89, 0.986);color: #fff;}
    .vuecal__event.darkblue {background-color: rgba(23, 42, 58, 0.9);border: 1px solid rgb(23, 42, 58, 0.9);color: #fff;}
    .vuecal__event.brown {background-color: rgba(165, 117, 72, 0.9);border: 1px solid rgb(165, 117, 72, 0.9);color: #fff;}
    .vuecal__event.lavender {background-color: rgba(120, 85, 137, 0.726);border: 1px solid rgb(120, 85, 137, 0.726);color: #fff;}

    .clrred{
        background-color: rgba(217,83,79) !important;
        width: 20px;
        height: 20px;
    }
    .clrblue{
        background-color:rgba(66,139,202) !important;
        width: 20px;
        height: 20px;
    }
    .clrgreen{
        background-color:rgba(17, 192, 32, 0.9) !important;
        width: 20px;
        height: 20px;
    }
    .clrorange{
        background-color: rgba(253, 165, 0, 0.945) !important;
        width: 20px;
        height: 20px;
    }
    .clrpurple{
        background-color:rgba(181, 32, 250, 0.9) !important;
        width: 20px;
        height: 20px;
    }
    .clryellow{
        background-color: rgba(250, 234, 9, 0.986) !important;
        width: 20px;
        height: 20px;
    }
    .clraqua{
        background-color: rgba(124, 255, 203, 0.986) !important;
        width: 20px;
        height: 20px;
    }
    .clrruby{
        background-color: rgba(216, 17, 89, 0.986) !important;
        width: 20px;
        height: 20px;
    }
    .clrdarkblue{
        background-color: rgba(23, 42, 58, 0.9) !important;
        width: 20px;
        height: 20px;
    }
    .clrbrown{
        background-color: rgba(165, 117, 72, 0.9) !important;
        width: 20px;
        height: 20px;
    }
    .clrlavender{
        background-color: rgba(120, 85, 137, 0.726) !important;
        width: 20px;
        height: 20px;
    }
    textarea.noresize{
        height: 100px;
        resize: none;
        /*  min-height: 60px;
        max-height: 300px; */
    }
    div.caja2-red{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(217,83,79);
        box-shadow: 0 1px 6px rgba(217,83,79);
        height: 250px;
    }
    div.caja2-blue{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(66,139,202);
        box-shadow: 0 1px 6px rgba(66,139,202);
        height: 250px;
    }
    div.caja2-green{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(17, 192, 32, 0.9);
        box-shadow: 0 1px 6px rgba(17, 192, 32, 0.9);
        height: 250px;
    }
    div.caja2-orange{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(253, 165, 0, 0.945);
        box-shadow: 0 1px 6px rgba(253, 165, 0, 0.945);
        height: 250px;
    }
    div.caja2-purple{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(181, 32, 250, 0.9);
        box-shadow: 0 1px 6px rgba(181, 32, 250, 0.9);
        height: 250px;
    }
    div.caja2-yellow{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(250, 234, 9, 0.986);
        box-shadow: 0 1px 6px rgba(250, 234, 9, 0.986);
        height: 250px;
    }
    div.caja2-aqua{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(124, 255, 203, 0.986);
        box-shadow: 0 1px 6px rgba(124, 255, 203, 0.986);
        height: 250px;
    }
    div.caja2-ruby{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(216, 17, 89, 0.986);
        box-shadow: 0 1px 6px rgba(216, 17, 89, 0.986);
        height: 250px;
    }
    div.caja2-darkblue{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(23, 42, 58, 0.9);
        box-shadow: 0 1px 6px rgba(23, 42, 58, 0.9);
        height: 250px;
    }
    div.caja2-brown{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(165, 117, 72, 0.9);
        box-shadow: 0 1px 6px rgba(165, 117, 72, 0.9);
        height: 250px;
    }
    div.caja2-lavender{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(120, 85, 137, 0.726);
        box-shadow: 0 1px 6px rgba(120, 85, 137, 0.726);
        height: 250px;
    }
    .contentBox{
        height: auto !important;
    }

</style>
