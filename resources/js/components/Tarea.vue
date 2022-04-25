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
          <i class="fa fa-align-justify"></i> CRM
          <button v-if="btnNewTask" type="button" @click="abrirModal('registrar')" class="btn btn-sm btn-secondary">
            <i class="icon-plus"></i>&nbsp;Nueva Tarea
          </button>
          <button v-if="btnNewTask==0" type="button" @click="ocultarDetalle()"  class="btn btn-sm btn-primary float-right">Volver</button>

        </div>
        <!-- Listado -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-12">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="cliente">Cliente</option>
                                <option value="tipocliente">Tipo Cliente</option>
                                <option value="fecha">Fecha</option>
                            </select>
                            <input type="text" v-model="buscar" @keyup.enter="listarTarea(1,buscar,criterio,estadoTask)" class="form-control mb-1" placeholder="Texto a buscar">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="estadoTask" @change="listarTarea(1,buscar,criterio,estadoTask)">
                                <option value="">Todo</option>
                                <option value="0">Pendiente</option>
                                <option value="1">Completado</option>
                                <option value="2">Cancelado</option>
                            </select>
                            <button type="submit" @click="listarTarea(1,buscar,criterio,estadoTask)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div class="input-group ml-0 ml-md-5">
                            <button class="btn btn-light mb-1" ><i style="color:red;" class="fa fa-search"></i> Filtro</button>
                            <input type="text" v-model="buscador" @keyup.enter="buscarClave()" class="form-control mb-1" placeholder="Busqueda Anvanzada">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Fecha</th>
                                <th>Tarea</th>
                                <th>Tipo de cliente</th>
                                <th>Clave</th>
                                <th>Estado</th>
                                <th>Vendedor</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayTarea.length" class="text-center">
                            <tr v-for="tarea in arrayTarea" :key="tarea.id">
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" @click="verTarea(tarea.idcliente)">
                                        <i class="icon-eye"></i>
                                    </button>&nbsp;

                                    <template v-if="tarea.estado != 2">
                                        <button type="button" @click="abrirModal('actualizar',tarea)" class="btn btn-warning btn-sm">
                                            <i class="icon-pencil"></i>
                                        </button> &nbsp;
                                    </template>

                                    <template v-if="tarea.estado == 0 ">
                                        <button type="button" class="btn btn-danger btn-sm" @click="desactivarTarea(tarea.id)">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </template>
                                    <!-- <template v-else>
                                    </template> -->
                                </td>
                                <td v-text="tarea.cliente"></td>
                                <td v-text="tarea.telefono"></td>
                                <td>
                                    <div v-if="tarea.fecha < dateAct">
                                        <span class="badge badge-pill badge-danger">{{ convertDate(tarea.fecha) }}</span>
                                    </div>
                                    <div v-else-if="tarea.fecha == dateAct">
                                        <span class="badge badge-pill badge-warning">{{ convertDate(tarea.fecha) }}</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-pill badge-secondary">{{ convertDate(tarea.fecha) }}</span>
                                    </div>
                                </td>
                                <td width="500px" v-text="tarea.descripcion"></td>
                                <td v-text="tarea.tipo"></td>
                                <td v-text="tarea.clave"></td>
                                <td>
                                    <div v-if="tarea.estado == 1">
                                        <span class="badge badge-pill badge-success">Completado</span>
                                    </div>
                                    <div v-else-if="tarea.estado == 0">
                                        <span class="badge badge-pill badge-warning">Pendiente</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-pill badge-danger">Cancelado</span>
                                    </div>
                                </td>
                                <td v-text="tarea.usuario"></td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <strong>Aún no tienes tareas dadas de alta...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoTask)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoTask)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoTask)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->

        <!-- Detalle -->
        <template v-else-if="listado==0">
            <div class="card-body">
                <!-- INF CLIENTE -->
                <div class="form-group row">
                    <div class="col-12 col-sm-6 col-lg-3 text-center">
                        <h1><i class="fa fa-user" aria-hidden="true"></i> {{ cliente}}</h1>
                       <!--  <p class="font-weight-bold" style="font-size: 20px;" v-text="tipo_cliente"></p> -->
                        <p class="font-weight-bold" style="font-size: 20px;" v-text="num_cliente"></p>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="telefono_cliente">
                        <div class="form-group">
                            <label for=""><strong>Teléfono</strong></label>
                            <h6 for=""><strong v-text="telefono_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                     <div class="col-md-2 text-center sinpadding" v-if="tipo_cliente">
                        <div class="form-group">
                            <label for=""><strong>Tipo de cliente</strong></label>
                            <h6 for=""><strong v-text="tipo_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="rfc_cliente">
                        <div class="form-group">
                            <label for=""><strong>RFC</strong></label>
                            <h6 for=""><strong v-text="rfc_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="contacto_cliente">
                        <div class="form-group">
                            <label for=""><strong>Contacto</strong></label>
                            <h6 for=""><strong> {{contacto_cliente}} | {{telcontacto_cliente}}</strong></h6>
                        </div>
                    </div>&nbsp;

                    <div class="col-md-3 text-center sinpadding">
                        <div class="form-group">
                            <label for=""><strong>Observaciones</strong></label>
                            <h6 for=""><strong> {{obs_cliente}}</strong></h6>
                        </div>
                    </div>&nbsp;
                </div>
                <hr>
                <div class="form-group row">
                    <div class="col-md-5 order-1 order-md-1">
                        <h4>Siguiente Tarea</h4>&nbsp;
                    </div>
                    <div class="col-md-5 order-3 order-md-2">
                        <h4 class="ml-2">Ultimas Ventas: </h4>
                    </div>&nbsp;
                    <div class="col-md-5 order-2 order-md-3 ml-3 pt-3 caja2" v-if="nextTask.length">
                        <div class="row" v-for="nexttask in nextTask" :key="nexttask.id">
                            <div class="col-md">
                                <h4 v-text="nexttask.clase"></h4>
                                <p><small class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(nexttask.fecha) }}</small></p>
                            </div>
                            <div class="col-md">
                                <button type="button" class="btn btn-sm btntask float-right" @click="UpdateTask('nextTask',nexttask)">
                                        <i class="fa fa-pencil"></i>
                                    </button>&nbsp;
                                <template v-if="nexttask.estado == 0 ">
                                    <button type="button" class="btn btn-sm btntask float-right" @click="desactivarTareaDet(nexttask.id)">
                                        <i class="fa fa-trash"></i>
                                    </button>&nbsp;
                                </template>
                            </div>
                            <div class="col-md-12">
                                <p v-text="nexttask.descripcion"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 ml-3 pt-3 caja2 order-2 order-md-3" v-else>
                        <div class="row">
                            <div class="col-md">
                                <h4>Sin tareas pendientes o asignadas...</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md text-center order-4 order-md-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                                <thead>
                                    <tr>
                                        <th>Comprobante</th>
                                        <th>Atendió</th>
                                        <th>Cliente</th>
                                        <th>No° Comprobante</th>
                                        <th>Fecha Hora</th>
                                        <th>Total</th>
                                        <th>100% Pagado</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayVentasT.length">
                                    <tr v-for="venta in arrayVentasT" :key="venta.id">
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfVenta(venta.id)">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </button>&nbsp;
                                        </td>
                                        <td v-text="venta.usuario"></td>
                                        <td v-text="venta.nombre"></td>
                                        <td v-text="venta.num_comprobante"></td>
                                        <td>{{ convertDateVenta(venta.fecha_hora) }}</td>
                                        <td v-text="venta.total"></td>
                                        <td v-if="venta.pagado">
                                            <toggle-button :value="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                        </td>
                                        <td v-else>
                                            <toggle-button :value="false" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <strong>Aún no tienes ventas con este cliente...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
                    <!-- Comentarios -->
                    <div class="col-md-6">
                       <div class="d-flex flex-column">
                            <div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>Comentarios </h3>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary rounded-circle" @click="newComment()"><i class="fa fa-plus-circle"></i></button>
                                    </div>
                                </div>
                                <!-- New Comment Box -->
                                <div class="row d-flex justify-content-center">
                                    <div class="col-12" :class="{'showNewComment' : CommentNew}" style="display: none;">
                                        <!-- <form action method="post" enctype="multipart/form-data" class="form-horizontal"> -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Comentario</span>
                                                </div>
                                                <textarea class="form-control rounded-1" rows="8" maxlength="255" v-model="commentBody"></textarea>
                                            </div>
                                            <button class="btn btn-primary mt-2 float-right" @click="saveComment(idcliente)" v-if="commentBody && itsCommentNew">Guardar</button>
                                            <button class="btn btn-primary mt-2 float-right" @click="updateComment(idcliente)" v-if="commentBody && itsCommentUpd">Actualizar</button>
                                        <!-- </form> -->
                                        <button class="btn btn-secondary mt-2 mr-1 float-right" @click="cancelComment()">Cancelar</button>
                                    </div>
                                </div>
                                <!-- End new Comment Box -->
                                <hr>
                            </div>
                            <div>
                                <div class="divtask" v-if="arrayComentarios.length">
                                    <ul class="row" v-for="comment in arrayComentarios" :key="comment.id">
                                            <li class="col-12 col-md-10" style="list-style:none;">
                                                <div class="form-group d-flex justify-content-center">
                                                    <div class="col-md my-3 pt-3 caja">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                    <h5 v-text="comment.nombre"></h5>
                                                            </div>
                                                            <div class="col-md">
                                                                <p><small style="font-size:10px;" class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(comment.fecha) }}</small></p>
                                                            </div>
                                                            <div class="col-md">
                                                                <template v-if="comment.user == user_id">
                                                                    <button type="button" class="btn btn-sm btntask float-right" @click="editComment(comment)">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </button>&nbsp;
                                                                    <button type="button" class="btn btn-sm btntask float-right" @click="deleteComentario(comment.id,idcliente)">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>&nbsp;
                                                                </template>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <p v-text="comment.body"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                </div>
                                <div v-else style="height: auto !important;">
                                    <h5>Sin Comentaríos...</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Historial -->
                    <div class="col-md-6">
                        <div class="page-header">
                            <h3 id="timeline">Historial de tareas {{ cliente }}
                                &nbsp;
                                <button type="button" class="btn btn-primary btn-circle" @click="UpdateTask('newTask')">
                                    <i class="fa fa-plus-circle"></i>
                                </button>&nbsp;
                            </h3>
                            <hr>
                        </div>
                        <div class="divtask">
                            <ul class="timeline">
                                <li v-for="tarea in arrayTareaT" :key="tarea.id">
                                    <template v-if="tarea.estado == 0 && tarea.clase == 'Llamada'">
                                        <div class="timeline-badge warning"><i class="fa fa-phone"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <div class="row">
                                                    <div class="col-md">
                                                        <h4 class="timeline-title">{{ tarea.clase }}</h4>
                                                        <p><small class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(tarea.fecha) }}</small></p>
                                                    </div>
                                                    <div class="col-md">
                                                        <button type="button" class="btn btn-sm btntask float-right">
                                                            <i class="fa fa-pencil" @click="UpdateTask('arrayTareaT',tarea)"></i>
                                                        </button>&nbsp;
                                                        <template v-if="tarea.estado == 0 ">
                                                            <button type="button" class="btn btn-sm btntask float-right" @click="desactivarTareaDet(tarea.id)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>&nbsp;
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="timeline-body">
                                                <p v-text="tarea.descripcion"></p>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="tarea.estado == 1 && tarea.clase == 'Llamada'">
                                        <div class="timeline-badge success"><i class="fa fa-phone"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <div class="row">
                                                    <div class="col-md">
                                                        <h4 class="timeline-title">{{ tarea.clase }}</h4>
                                                        <p><small class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(tarea.fecha) }}</small></p>
                                                    </div>
                                                    <div class="col-md">
                                                        <button type="button" class="btn btn-sm btntask float-right">
                                                            <i class="fa fa-pencil" @click="UpdateTask('arrayTareaT',tarea)"></i>
                                                        </button>&nbsp;
                                                        <template v-if="tarea.estado == 0 ">
                                                            <button type="button" class="btn btn-sm btntask float-right" @click="desactivarTareaDet(tarea.id)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>&nbsp;
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="timeline-body">
                                                <p v-text="tarea.descripcion"></p>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="tarea.estado == 2 && tarea.clase == 'Llamada'">
                                        <div class="timeline-badge danger"><i class="fa fa-phone"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4 class="timeline-title"><del>{{ tarea.clase }}</del></h4>
                                                <p><small class="text-muted"><i class="fa fa-clock-o"></i><del> {{ convertDate(tarea.fecha) }}</del></small>
                                                <span class="badge badge-pill badge-danger float-right">CANCELADO</span></p>
                                            </div>
                                            <div class="timeline-body">
                                                <p><del>{{tarea.descripcion}}</del></p>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="tarea.estado == 0 && tarea.clase == 'Nota'">
                                        <div class="timeline-badge warning"><i class="fa fa-comment"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <div class="row">
                                                    <div class="col-md">
                                                        <h4 class="timeline-title">{{ tarea.clase }}</h4>
                                                        <p><small class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(tarea.fecha) }}</small></p>
                                                    </div>
                                                    <div class="col-md">
                                                        <button type="button" class="btn btn-sm btntask float-right">
                                                            <i class="fa fa-pencil" @click="UpdateTask('arrayTareaT',tarea)"></i>
                                                        </button>&nbsp;
                                                        <template v-if="tarea.estado == 0 ">
                                                            <button type="button" class="btn btn-sm btntask float-right" @click="desactivarTareaDet(tarea.id)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>&nbsp;
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="timeline-body">
                                                <p v-text="tarea.descripcion"></p>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="tarea.estado == 1 && tarea.clase == 'Nota'">
                                        <div class="timeline-badge success"><i class="fa fa-comment"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <div class="row">
                                                    <div class="col-md">
                                                        <h4 class="timeline-title">{{ tarea.clase }}</h4>
                                                        <p><small class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(tarea.fecha) }}</small></p>
                                                    </div>
                                                    <div class="col-md">
                                                        <button type="button" class="btn btn-sm btntask float-right">
                                                            <i class="fa fa-pencil" @click="UpdateTask('arrayTareaT',tarea)"></i>
                                                        </button>&nbsp;
                                                        <template v-if="tarea.estado == 0 ">
                                                            <button type="button" class="btn btn-sm btntask float-right" @click="desactivarTareaDet(tarea.id)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>&nbsp;
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="timeline-body">
                                                <p v-text="tarea.descripcion"></p>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="tarea.estado == 2 && tarea.clase == 'Nota'">
                                        <div class="timeline-badge danger"><i class="fa fa-comment"></i></div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4 class="timeline-title"><del>{{ tarea.clase }}</del></h4>
                                                <p><small class="text-muted"><i class="fa fa-clock-o"></i><del> {{ convertDate(tarea.fecha) }}</del></small>
                                                <span class="badge badge-pill badge-danger float-right">CANCELADO</span></p>
                                            </div>
                                            <div class="timeline-body">
                                                <p><del>{{tarea.descripcion}}</del></p>
                                            </div>
                                        </div>
                                    </template>

                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row">
                    <!-- Actividades -->
                    <div class="col-md-6">
                        <div class="page-header">
                            <h3 id="timeline">Actividades pendientes {{ cliente }} &nbsp;</h3>
                        </div>
                        <div class="mt-3" v-if="arrayActividadesT.length">
                            <div v-for="activity in arrayActividadesT" :key="activity.id">
                                <div :class="['col-md','caja2-' + activity.class]">
                                    <div class="row m-0 p-0">
                                        <div class="col">
                                            <template v-if="activity.estado">
                                                <p class="text-success font-weight-bold float-right" style="font-size: 25px;">Completada <i class="fa fa-check-circle-o" aria-hidden="true"></i></p>
                                            </template>
                                            <template v-else>
                                                <p class="text-danger font-weight-bold float-right" style="font-size: 25px;">Incompleta <i class="fa fa-times-circle-o" aria-hidden="true"></i></p>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md col-10 mt-3">
                                            <h4 v-text="activity.title"></h4>
                                            <div class="form-inline">
                                                <div class="form-group mb-2">
                                                    <div class="input-group">
                                                        <p><i class="fa fa-clock-o"></i> Inicio {{ activity.start }}</p>&nbsp;
                                                        <p><i class="fa fa-clock-o"></i>Final {{ activity.end }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <p v-text="activity.content"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <nav>
                                <ul class="pagination mt-3">
                                    <li class="page-item" v-if="paginationEvent.current_page > 1">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaEvent(idcliente,paginationEvent.current_page - 1,)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="page in pagesNumberEvent" :key="page" :class="[page == isActivedEvent ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaEvent(idcliente,page)" v-text="page"></a>
                                    </li>
                                    <li class="page-item" v-if="paginationEvent.current_page < paginationEvent.last_page">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaEvent(idcliente,paginationEvent.current_page + 1)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div v-else>
                            <h5>Sin Actividades...</h5>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12 float-right">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin detalle -->
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!--Inicio del modal listar articulos-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content content-task">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-5 text-center">
                            <div class="form-group" v-if="isEdition == false && iscompleted==false">
                                <label for=""><strong>Cliente (*)</strong></label>
                                    <v-select :on-search="selectCliente" label="nombre" :options="arrayCliente" placeholder="Buscar clientes..." :onChange="getDatosCliente">
                                    </v-select>
                            </div>
                            <div class="form-group"  v-else>
                                <label for=""><strong>Cliente</strong></label>
                                <h4 for=""><strong v-text="cliente"></strong></h4>
                            </div>
                        </div>&nbsp;
                        <template v-if="idcliente">
                            <div class="col-md-3 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Tipo de cliente</strong></label>
                                    <h6 for=""><strong v-text="tipo_cliente"></strong></h6>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="form-group">
                                    <label for=""><strong>RFC</strong></label>
                                    <h6 for=""><strong v-text="rfc_cliente"></strong></h6>
                                </div>
                            </div>
                        </template>
                        <input type="number" hidden :value="getFechaCode" class="form-control col-md"/>
                    </div>
                    <div class="form-group row">
                        <template v-if="telefono_cliente">
                            <div class="col-md-3 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Teléfono</strong></label>
                                    <h6 for=""><strong v-text="telefono_cliente"></strong></h6>
                                </div>
                            </div>
                            <template v-if="contacto_cliente">
                                <div class="col-md-3 text-center">
                                    <div class="form-group">
                                        <label for=""><strong>Contacto</strong></label>
                                        <h6 for=""><strong v-text="contacto_cliente"></strong></h6>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="form-group">
                                        <label for=""><strong>Teléfono de Contacto</strong></label>
                                        <h6 for=""><strong v-text="telcontacto_cliente"></strong></h6>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                            </template>
                        </template>
                        <div class="col-md-3 text-center">
                            <div class="form-group">
                                <label class="form-control-label" for="text-input"><strong>Fecha</strong></label>
                                <input type="date" v-model="fecha" class="form-control col-md" placeholder="Fecha para realizar"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <template v-if="isComment">
                        </template>
                        <template v-else>
                            <div class="col-md-2 text-center">
                                <div class="form-group">
                                    <label for=""><strong>Tipo</strong></label>
                                    <select class="form-control col-md" v-model="tipo">
                                        <option value='' disabled>Seleccione(*)</option>
                                        <option value="Nota">Nota</option>
                                        <option value="Llamada">Llamada</option>
                                        <option value="Whatsapp">Whatsapp</option>
                                        <option value="Correo">Correo</option>
                                    </select>
                                </div>
                            </div>
                        </template>

                        <div class="col-md-8 text-center">
                            <label for=""><strong>Notas:</strong></label>
                            <textarea class="form-control rounded-0" rows="3" maxlength="254" v-model="descripcion"></textarea>
                        </div>
                        <template v-if="isEdition && isComment==0">
                            <div class="col-md-2 text-center">
                                <label for=""><strong>Tarea Completada:</strong></label>&nbsp;
                                <template v-if="tipoAccion == 2">
                                    <toggle-button @change="cambiarEstadoTarea(id)" v-model="btnComp" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </template>
                                <template v-else>
                                    <toggle-button @change="cambiarEstadoTareaDet(id)" v-model="btnComp" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 text-center">
                            <div v-show="errorTarea" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in errorMostrarMsjTarea" :key="error" v-text="error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" v-if="cerrarDet!=1" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="cerrarDet==1" class="btn btn-secondary" @click="cerrarModalDet()">Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarTarea()">Guardar</button>
                        <button type="button" v-if="tipoAccion==4" class="btn btn-primary" @click="registrarTareaDet()">Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarTarea()">Actualizar</button>
                        <button type="button" v-if="tipoAccion==3" class="btn btn-primary" @click="actualizarTareaDet()">Actualizar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" v-if="cerrarDet!=1" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" v-if="cerrarDet==1" class="btn btn-secondary" @click="cerrarModalDet()">Cerrar</button>
                    <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarTarea()">Guardar</button>
                    <button type="button" v-if="tipoAccion==4" class="btn btn-primary" @click="registrarTareaDet()">Guardar</button>
                    <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarTarea()">Actualizar</button>
                    <button type="button" v-if="tipoAccion==3" class="btn btn-primary" @click="actualizarTareaDet()">Actualizar</button>
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
import ToggleButton from 'vue-js-toggle-button';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton);
export default {
    data() {
        return {
            tarea_id: 0,
            nombre : "",
            descripcion : "",
            tipo : "",
            fecha : "",
            estado : 0,
            user: '',
            idcliente: 0,
            cliente: '',
            num_cliente : "",
            rfc_cliente : "",
            tipo_cliente : "",
            telefono_cliente : "",
            contacto_cliente : "",
            telcontacto_cliente : "",
            obs_cliente: "",
            arrayTarea : [],
            arrayCliente : [],
            listado : 1,
            modal: 0,
            modal2: 0,
            ind : '',
            tituloModal: "",
            tipoAccion: 0,
            errorTarea: 0,
            errorMostrarMsjTarea: [],
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
            areaUs : "",
            estadoVn : "",
            CodeDate : "",
            vig : "",
            obsEditable : 0,
            dateAct : "",
            btnComp : false,
            isEdition : false,
            estadoTask : 0,
            arrayTareaT : [],
            arrayVentasT : [],
            btnNewTask : 1,
            nextTask : [],
            cerrarDet : 0,
            isComment : 0,
            arrayCommentT : [],
            arrayActividadesT : [],
            paginationEvent : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            iscompleted : false,

            arrayComentarios : [],
            commentBody : "",
            CommentNew : 0,
            itsCommentUpd : 0,
            itsCommentNew : 0,
            comment_id : 0,
            user_id : 0,
            buscador:'',
            timeout:0,
            clave:'',
            setTimeoutBuscador:'',

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
        isActivedEvent: function(){
            return this.paginationEvent.current_page;
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
        },
        pagesNumberEvent: function() {
            if(!this.paginationEvent.to) {
                return [];
            }

            var from = this.paginationEvent.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationEvent.last_page){
                to = this.paginationEvent.last_page;
            }

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        getFechaCode : function(){
            let me = this;
            let date = "";
            moment.locale('es');
            date = moment().format('YYMMDD');
            me.CodeDate = moment().format('YYMMDD');
            me.dateAct = moment().format('YYYY-MM-DD');

            return date;
        },
    },
    methods: {
        listarTarea(page,buscar,criterio,estado){
            let me=this;
            me.btnNewTask = 1;
            var url= '/tarea?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estado;
            axios.get(url,{
                params:{
                    buscador: this.buscador
                }
            }).then(function (response) {
                var respuesta= response.data;
                me.arrayTarea = respuesta.tareas.data;
                me.pagination= respuesta.pagination;
                me.user_id = respuesta.userid;
                /* console.log(me.arrayTarea); */
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        buscarClave(){

            clearTimeout( this.setTimeoutBuscador)
            this.setTimeoutBuscador = setTimeout(this.listarTarea(), 360);
        },
        convertDate(date){
            let me=this;
            var datec = moment(date).format('MMM DD YY');
            /* console.log(datec); */
            return datec;
        },
        convertDateVenta(date){
            let me=this;
            var datec = moment(date).format('MMM DD YYYY HH:mm:ss');
            /* console.log(datec); */
            return datec;
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
            me.idcliente = val1.id
            me.rfc_cliente =  val1.rfc;
            me.tipo_cliente = val1.tipo;
            me.telefono_cliente = val1.telefono;
            me.contacto_cliente = val1.company;
            me.clave = val1.clave;
            me.telcontacto_cliente = val1.tel_company;
            me.obs_cliente = val1.observacion;
        },
        cambiarPagina(page,buscar,criterio,estado){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarTarea(page,buscar,criterio,estado);
        },
        cerrarModal(){
            this.modal = 0;
            this.buscar = "";
            this.idcliente = "";
            this.cliente = "";
            this.descripcion = "";
            this.fecha = "";
            this.nombre = "";
            this.tipo_cliente = "";
            this.rfc_cliente = "";
            this.telefono_cliente = "";
            this.contacto_cliente = "";
            this.telcontacto_cliente = "";
            this.obs_cliente = "";
            this.tipo = "";
            this.estado = 0;
            this.btnComp = false;
            this.isEdition = false;
            this.iscompleted = false;
            this.listarTarea(1,'','idcliente',this.estadoTask);
        },
        cerrarModalDet(){
            this.modal = 0;
            this.cerrarDet = 0;
            this.btnComp = false;
            this.isComment = 0;
            this.verTarea(this.idcliente);
        },
        abrirModal(accion, data = []){
            this.listado = 1;
            switch (accion) {
                case "registrar": {
                    this.modal = 1;
                    this.tituloModal = "Tarea Nueva";
                    this.tipoAccion = 1;
                    break;
                }
                case "actualizar": {
                    this.modal = 1;
                    this.id = data['id'];
                    this.cliente = data['cliente'];
                    this.tituloModal = "Modificar Tarea Para " + this.cliente;
                    this.tipoAccion = 2;
                    this.idcliente = data['idcliente'];
                    this.rfc_cliente = data['rfc'];
                    this.tipo_cliente = data['tipo'];
                    this.clave = data['clave'];
                    this.contacto_cliente = data['company'];
                    this.telcontacto_cliente = data['tel_company'];
                    this.telefono_cliente = data['telefono'];
                    this.obs_cliente = data['observacion'];
                    this.nombre = data['nombre'];
                    this.descripcion = data['descripcion'];
                    this.tipo = data['clase'];
                    this.fecha = data['fecha'];
                    this.estado =data['estado'];
                    this.isEdition = true;
                    this.iscompleted = false;

                    if(this.estado){
                        this.btnComp = true;
                    }

                    break;
                }
            }
        },
        validarTarea() {
            let me = this;
            var art;

            me.errorTarea = 0;
            me.errorMostrarMsjTarea = [];


            if (me.idcliente==0) me.errorMostrarMsjTarea.push("Seleccione un cliente");
            if (me.fecha == '') me.errorMostrarMsjTarea.push("Seleccione la fecha para la tarea");
            if(me.fecha < me.dateAct) me.errorMostrarMsjTarea.push("La fecha de la tarea no puede ser menos a la fecha actual");
            if (me.errorMostrarMsjTarea.length) me.errorTarea = 1;

            return me.errorTarea;
        },
        registrarTarea(){
            if (this.validarTarea()) {
                return;
            }
            let me = this;
            var name = "T-".concat(me.CodeDate,"-",me.tipo);
            /* console.log(name); */
            me.nombre = name;
            axios.post('/tarea/registrar',{
                'idcliente': this.idcliente,
                'nombre': this.nombre,
                'descripcion' : this.descripcion,
                'tipo' : this.tipo,
                'fecha' : this.fecha
            }).then(function(response) {
                me.cerrarModal();
                me.listarTarea(1,'','idcliente',me.estadoTask);
                me.idcliente = 0;
                me.nombre = "";
                me.descripcion ="";
                me.fecha = "";
                me.tipo = "";
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        registrarTareaDet(){
            if (this.validarTarea()) {
                return;
            }
            let me = this;
            var name = "T-".concat(me.CodeDate,"-",me.tipo);
            /* console.log(name); */
            me.nombre = name;
            axios.post('/tarea/registrar',{
                'idcliente': this.idcliente,
                'nombre': this.nombre,
                'descripcion' : this.descripcion,
                'tipo' : this.tipo,
                'fecha' : this.fecha
            }).then(function(response) {
                me.verTarea(me.idcliente);
                me.cerrarModalDet();
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarTarea(){
            if (this.validarTarea()) {
                return;
            }
            let me = this;
            axios.put('/tarea/actualizar',{
                'id' : this.id,
                'nombre' : this.nombre,
                'idcliente': this.idcliente,
                'nombre': this.nombre,
                'descripcion' : this.descripcion,
                'tipo' : this.tipo,
                'fecha' : this.fecha
            }).then(function(response) {
                me.cerrarModal();
                me.listarTarea(1,'','idcliente',me.estadoTask);
                me.idcliente = 0;
                me.nombre = "";
                me.descripcion ="";
                me.fecha = "";
                me.tipo = "";
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarTareaDet(){
            if (this.validarTarea()) {
                return;
            }
            let me = this;
            axios.put('/tarea/actualizar',{
                'id' : this.id,
                'nombre' : this.nombre,
                'idcliente': this.idcliente,
                'nombre': this.nombre,
                'clave' : this.clave,
                'descripcion' : this.descripcion,
                'tipo' : this.tipo,
                'fecha' : this.fecha
            }).then(function(response) {
                me.verTarea(me.idcliente);
                me.cerrarModalDet();
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        cambiarEstadoTarea(id){
            let me = this;
            if(me.btnComp == true){
                me.estado = 1;
            }else{
                me.estado = 0;
            }
            axios.put('/tarea/completar',{
                'id': id,
                'estado' : this.estado
            }).then(function (response) {
                if(me.estado == 1){
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                        },
                        buttonsStyling: false
                    });

                    swalWithBootstrapButtons.fire({
                        title: "Tarea completada \n ¿Desea añadir otra?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Si!",
                        cancelButtonText: "No!",
                        reverseButtons: true
                    })
                    .then(result => {
                        if (result.value) {
                            /* me.cerrarModal(); */
                            me.UpdateTask('newTaskComp');
                        }else if (result.dismiss === swal.DismissReason.cancel){
                            me.cerrarModal();
                            /* me.UpdateTask('newTaskComp'); */
                        }
                    })
                }else{
                   Swal.fire(
                        "Atención!",
                        "La tarea ha sido marcada como incompleta.",
                        "warning"
                    )
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoTareaDet(id){
            let me = this;
            if(me.btnComp == true){
                me.estado = 1;
            }else{
                me.estado = 0;
            }
            axios.put('/tarea/completar',{
                'id': id,
                'estado' : this.estado
            }).then(function (response) {
                /* me.verTarea(me.idcliente); */
                if(me.estado == 1){
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                        },
                        buttonsStyling: false
                    });

                    swalWithBootstrapButtons.fire({
                        title: "Tarea completada! \n ¿Desea añadir otra?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Si!",
                        cancelButtonText: "No!",
                        reverseButtons: true
                    })
                    .then(result => {
                        if (result.value) {
                            /* me.cerrarModal(); */
                            me.UpdateTask('newTaskComp');
                        }else if (result.dismiss === swal.DismissReason.cancel){
                            Swal.fire({
                                position: 'top-end',
                                type: 'success',
                                title: 'Completado',
                                showConfirmButton: false,
                                timer: 1000
                            });
                            me.cerrarModalDet();

                        }
                    })
                }else{
                   Swal.fire(
                        "Atención!",
                        "La tarea ha sido marcada como incompleta.",
                        "warning"
                    )
                }

            }).catch(function (error) {
                console.log(error);
            });
        },
        desactivarTarea(id) {

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de anular esta tarea?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/tarea/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarTarea(1,'','idcliente',me.estadoTask);
                        swal.fire(
                        'Anulado!',
                        'La tarea ha sido anulado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        desactivarTareaDet(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de anular esta tarea?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/tarea/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.verTarea(me.idcliente);
                        swal.fire(
                            'Anulado!',
                            'La tarea ha sido anulado con éxito.',
                            'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        desactivarComentario(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de eliminar esta comentario?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/tarea/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.verTarea(me.idcliente);
                        swal.fire(
                            'Eliminado!',
                            'El comentario ha sido eliminado con éxito.',
                            'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        ocultarDetalle(){
            this.listado = 1;
            this.cliente = 0;
            this.errorTarea =0;
            this.errorMostrarMsjTarea = [];
            this.arrayTareaT = [];
            this.nextTask = [];
            this.arrayVentasT = [];
            this.arrayCommentT = [];
            this.arrayComentarios = [];
            this.arrayActividadesT = [];
            this.idcliente = '';
            this.cliente = '';
            this.rfc_cliente = '';
            this.tipo_cliente = '';
            this.telefono_cliente = '';
            this.contacto_cliente = '';
            this.telcontacto_cliente ='';
            this.obs_cliente = '';
            this.btnNewTask = 1;
            this.cerrarDet = 0;
            this.isEdition = false;
            this.iscompleted = false;
            this.nombre = "";
            this.clave = "";
            this.descripcion = "";
            this.tipo = "";
            this.fecha = "";
            this.listarTarea(1,'','idcliente',this.estadoTask);
            this.isComment = 0;
            this.paginationEvent = {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            };
        },
        verTarea(idcliente){
            let me = this;
            me.listado = 0;
            me.btnNewTask = 0;
            var url= '/tarea/obtenerTareas?idcliente=' + idcliente;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayTareaT = respuesta.tareas.data;
                me.nextTask = respuesta.siguiente.data;
                me.arrayCommentT = respuesta.comentarios.data;
                me.idcliente = me.arrayTareaT[0]['idcliente'];
                me.cliente = me.arrayTareaT[0]['cliente'];
                me.rfc_cliente = me.arrayTareaT[0]['rfc'];
                me.tipo_cliente = me.arrayTareaT[0]['tipo'];
                me.clave = me.arrayTarea[0]['clave'];
                me.telefono_cliente = me.arrayTareaT[0]['telefono'];
                me.contacto_cliente = me.arrayTareaT[0]['company'];
                me.telcontacto_cliente = me.arrayTareaT[0]['tel_company'];
                me.obs_cliente = me.arrayTareaT[0]['observacion'];
                me.num_cliente = me.arrayTarea[0]['num_documento'];
            })
            .catch(function (error) {
                console.log(error);
            });

            var url= '/venta/obtenerVentasCliente?idcliente=' + idcliente;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayVentasT = respuesta.ventas.data;
               /*  console.log(me.arrayVentasT); */
            })
            .catch(function (error) {
                console.log(error);
            });

            this.obtenerEventos(idcliente,1);
            this.getComments(idcliente);
        },
        obtenerEventos(idcliente,page){
            let me = this;
            var url= '/event/obtenerEventsCliente?page='+ page + '&idcliente=' + idcliente;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayActividadesT = respuesta.actividades.data;
                me.paginationEvent = respuesta.pagination;
                /* console.log(me.paginationEvent); */
                /* console.log(me.arrayActividadesT); */
            })
            .catch(function (error) {
                console.log(error);
            });

            /* this.arrayActividadesT = []; */
        },
        cambiarPaginaEvent(idcliente,page){
            let me = this;
            //Actualiza la página actual
            me.paginationEvent.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.obtenerEventos(idcliente,page);
        },
        pdfVenta(id){
            window.open('/venta/pdf/'+id);
        },
        UpdateTask(accion, data = []){
            switch (accion) {
                case "nextTask": {
                    this.modal = 1;
                    this.id = data['id'];
                    this.cliente = data['cliente'];
                    this.tituloModal = "Modificar Tarea Para " + this.cliente;
                    this.tipoAccion = 3;
                    this.idcliente = data['idcliente'];
                    this.rfc_cliente = data['rfc'];
                    this.tipo_cliente = data['tipo'];
                    this.clave = data['clave'];
                    this.contacto_cliente = data['company'];
                    this.telcontacto_cliente = data['tel_company'];
                    this.telefono_cliente = data['telefono'];
                    this.obs_cliente = data['observacion'];
                    this.nombre = data['nombre'];
                    this.descripcion = data['descripcion'];
                    this.tipo = data['clase'];
                    this.fecha = data['fecha'];
                    this.estado =data['estado'];
                    this.isEdition = true;
                    this.iscompleted = false;
                    this.cerrarDet = 1;
                    this.isComment = 0;

                    if(this.estado){
                        this.btnComp = true;
                    }

                    break;
                }
                case "arrayTareaT": {
                    this.modal = 1;
                    this.id = data['id'];
                    this.cliente = data['cliente'];
                    this.tituloModal = "Modificar Tarea Para " + this.cliente;
                    this.tipoAccion = 3;
                    this.idcliente = data['idcliente'];
                    this.rfc_cliente = data['rfc'];
                    this.tipo_cliente = data['tipo'];
                    this.contacto_cliente = data['company'];
                    this.telcontacto_cliente = data['tel_company'];
                    this.telefono_cliente = data['telefono'];
                    this.obs_cliente = data['observacion'];
                    this.nombre = data['nombre'];
                    this.descripcion = data['descripcion'];
                    this.tipo = data['clase'];
                    this.fecha = data['fecha'];
                    this.estado =data['estado'];
                    this.isEdition = true;
                    this.iscompleted = false;
                    this.cerrarDet = 1;

                    if(this.estado){
                        this.btnComp = true;
                    }

                    break;
                }
                case "newTask" : {
                    this.modal = 1;
                    this.tipoAccion = 4;
                    this.cerrarDet = 1;
                    this.tituloModal = "Nueva Tarea Para " + this.cliente;
                    this.isEdition = true;
                    this.iscompleted = false;
                    this.nombre = "";
                    this.descripcion = "";
                    this.tipo = "";
                    this.fecha = "";
                    this.isComment = 0;
                    break;
                }
                case "newComment" : {
                    this.modal = 1;
                    this.tipoAccion = 4;
                    this.cerrarDet = 1;
                    this.tituloModal = "Nuevo Comentario Para " + this.cliente;
                    this.isEdition = true;
                    this.iscompleted = false;
                    this.nombre = "";
                    this.descripcion = "";
                    this.tipo = "Comentario";
                    this.fecha = "";
                    this.isComment = 1;
                    break;
                }
                case "comment": {
                    this.modal = 1;
                    this.id = data['id'];
                    this.cliente = data['cliente'];
                    this.tituloModal = "Modificar comentario de " + this.cliente;
                    this.tipoAccion = 3;
                    this.idcliente = data['idcliente'];
                    this.rfc_cliente = data['rfc'];
                    this.tipo_cliente = data['tipo'];
                    this.contacto_cliente = data['company'];
                    this.telcontacto_cliente = data['tel_company'];
                    this.telefono_cliente = data['telefono'];
                    this.obs_cliente = data['observacion'];
                    this.nombre = data['nombre'];
                    this.descripcion = data['descripcion'];
                    this.tipo = data['clase'];
                    this.fecha = data['fecha'];
                    this.estado =data['estado'];
                    this.isEdition = true;
                    this.iscompleted = false;
                    this.cerrarDet = 1;
                    this.isComment = 1;

                    if(this.estado){
                        this.btnComp = true;
                    }

                    break;
                }
                case "newTaskComp" : {
                    this.modal = 1;
                    this.tipoAccion = 4;
                    this.cerrarDet = 1;
                    this.tituloModal = "Nueva Tarea Para " + this.cliente;
                    this.isEdition = false;
                    this.iscompleted = true;
                    this.nombre = "";
                    this.descripcion = "";
                    this.tipo = "";
                    this.fecha = "";
                    this.isComment = 0;
                    break;
                }
            }
        },
        mostrarDetalle(){
            this.listado = 0;
        },
        newComment(){
            this.CommentNew = 1;
            this.commentBody = "";
            this.itsCommentUpd = 0;
            this.itsCommentNew = 1;
        },
        cancelComment(){
            this.CommentNew = 0;
            this.commentBody = "";
            this.comment_id = 0;
            this.itsCommentUpd = 0;
            this.itsCommentNew = 0;
        },
        saveComment(id){
            let me = this;

            var clienteid = id;

            axios.post('/cliente/crearComment',{
                'id' : id,
                'body' : this.commentBody
            }).then(function(response) {
                me.cancelComment();
                me.getComments(clienteid);
                swal.fire(
                'Completado!',
                'El comentario ha sido registrado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        getComments(id){
            let me = this;
            var url= '/cliente/getComments?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayComentarios = respuesta.comentarios;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        editComment(data = []){
            this.comment_id = data['id'];
            this.commentBody = data['body'];
            this.CommentNew = 1;
            this.itsCommentUpd = 1;
            this.itsCommentNew = 0;
        },
        updateComment(id){

            let me = this;
            var clienteid = id;

            axios.put("/cliente/editComment", {
                id : this.comment_id,
                body : this.commentBody
            })
            .then(function(response) {
                me.cancelComment();
                me.getComments(clienteid);
                swal.fire(
                'Completado!',
                'El comentario ha sido actualizado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        deleteComentario(id,cliente){
            let me = this;
            var clienteid = cliente;
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro eliminar este comentario?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/cliente/deleteComment', {
                        'id' : id
                    }).then(function(response) {
                         me.getComments(clienteid);
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El comentario ha sido eliminada con éxito.",
                            "success"
                        );
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        }
    },
    mounted() {
        this.listarTarea(1,this.buscar, this.criterio,this.estadoTask);
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
    input[type="number"]{
        -webkit-appearance: textfield !important;
        margin: 0;
    }
    .content-task{
        height: 480px !important;
    }
    .sinpadding [class*="col-"] {
        padding: 0;
    }
    .timeline {
        list-style: none;
        padding: 20px 0 20px;
        position: relative;
    }
    .timeline:before {
        top: 0;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 3px;
        background-color: #eeeeee;
        left: 50%;
        margin-left: -1.5px;
    }
    .timeline > li {
        margin-bottom: 20px;
        position: relative;
    }
    .timeline > li:before,
    .timeline > li:after {
        content: " ";
        display: table;
    }
    .timeline > li:after {
        clear: both;
    }
    .timeline > li:before,
    .timeline > li:after {
        content: " ";
        display: table;
    }
    .timeline > li:after {
        clear: both;
    }
    .timeline > li > .timeline-panel {
        width: 46%;
        float: left;
        border: 1px solid #d4d4d4;
        border-radius: 2px;
        padding: 20px;
        position: relative;
        -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
    }
    .timeline > li > .timeline-panel:before {
        position: absolute;
        top: 26px;
        right: -15px;
        display: inline-block;
        border-top: 15px solid transparent;
        border-left: 15px solid #ccc;
        border-right: 0 solid #ccc;
        border-bottom: 15px solid transparent;
        content: " ";
    }
    .timeline > li > .timeline-panel:after {
        position: absolute;
        top: 27px;
        right: -14px;
        display: inline-block;
        border-top: 14px solid transparent;
        border-left: 14px solid #fff;
        border-right: 0 solid #fff;
        border-bottom: 14px solid transparent;
        content: " ";
    }
    .timeline > li > .timeline-badge {
        color: #fff;
        width: 50px;
        height: 50px;
        line-height: 50px;
        font-size: 1.4em;
        text-align: center;
        position: absolute;
        top: 16px;
        left: 50%;
        margin-left: -25px;
        background-color: #999999;
        z-index: 100;
        border-top-right-radius: 50%;
        border-top-left-radius: 50%;
        border-bottom-right-radius: 50%;
        border-bottom-left-radius: 50%;
    }
    .timeline > li.timeline-inverted > .timeline-panel {
        float: right;
    }
    .timeline > li.timeline-inverted > .timeline-panel:before {
        border-left-width: 0;
        border-right-width: 15px;
        left: -15px;
        right: auto;
    }
    .timeline > li.timeline-inverted > .timeline-panel:after {
        border-left-width: 0;
        border-right-width: 14px;
        left: -14px;
        right: auto;
    }
    .timeline-badge.primary {
        background-color: #2e6da4 !important;
    }
    .timeline-badge.success {
        background-color: #3f903f !important;
    }
    .timeline-badge.warning {
        background-color: #f0ad4e !important;
    }
    .timeline-badge.danger {
        background-color: #d9534f !important;
    }
    .timeline-badge.info {
        background-color: #5bc0de !important;
    }
    .timeline-title {
        margin-top: 0;
        color: inherit;
    }
    .timeline-body > p,
    .timeline-body > ul {
        margin-bottom: 0;
    }
    .timeline-body > p + p {
        margin-top: 5px;
    }
    @media (max-width: 767px) {
        ul.timeline:before {
            left: 40px;
        }

        ul.timeline > li > .timeline-panel {
            width: calc(100% - 90px);
            width: -moz-calc(100% - 90px);
            width: -webkit-calc(100% - 90px);
        }

        ul.timeline > li > .timeline-badge {
            left: 15px;
            margin-left: 0;
            top: 16px;
        }

        ul.timeline > li > .timeline-panel {
            float: right;
        }

        ul.timeline > li > .timeline-panel:before {
            border-left-width: 0;
            border-right-width: 15px;
            left: -15px;
            right: auto;
        }

        ul.timeline > li > .timeline-panel:after {
            border-left-width: 0;
            border-right-width: 14px;
            left: -14px;
            right: auto;
        }
    }
    div.divtask{
        height: 400px;
        width: 100% !important;
        overflow-y: scroll;
        scrollbar-width: none;
    }
    .divtask::-webkit-scrollbar {
        display: none;
    }
    div.caja{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        height: auto !important;

    }
    div.caja2{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        height: 150px;

    }
    button.btntask{
        background-color: transparent;
    }
    .btn-circle {
        width: 30px;
        height: 30px;
        padding: 6px 0px;
        border-radius: 15px;
        text-align: center;
        font-size: 13px;
        line-height: 1.42857;
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
</style>
