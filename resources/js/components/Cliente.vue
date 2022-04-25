<template>
  <main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/">Escritorio</a> </li>
    </ol>
    <div class="container-fluid p-1">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-align-justify"></i> Clientes
          <button v-if="btnNewCliente" type="button" @click="abrirModal('persona','registrar')" class="btn btn-secondary">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
          <button v-if="btnNewCliente==0" type="button" @click="ocultarDetalle()"  class="btn btn-sm btn-primary float-right">Volver</button>
          <button v-if="btnNewCliente==0 && userrol==1" type="button" @click="abrirModalEstadoCR()"  class="btn btn-sm btn-info float-right mr-2">Estado de cuenta</button>
        </div>
        <!-- Listado de clientes -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-12">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="nombre">Nombre</option>
                                <option value="tipo">Tipo Cliente</option>
                                <option value="rfc">RFC</option>
                                <option value="email">Correo electrónico</option>
                                <option value="usuario">Usuario</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <input type="text" v-model="buscar" @keyup.enter="listarPersona(1,buscar,criterio,status,zona,actives)" class="form-control mb-1" placeholder="Texto a buscar">
                            <button type="submit" @click="listarPersona(1,buscar,criterio,status,zona,actives)" class="btn btn-primary mb-1"><i class="fa fa-search mb-1"></i></button>
                        </div>
                        <div class="input-group" v-if="userrol == 1">
                            <select class="form-control mb-1" v-model="status" @change="listarPersona(1,buscar,criterio,status,zona,actives)">
                                <option value="">Todos</option>
                                <option value="1">Activos</option>
                                <option value="0">Eliminados</option>
                            </select>
                        </div>
                        <div class="input-group ml-0 ml-md-5" v-if="userrol == 1">
                            <button class="btn btn-light mb-1"><i style="color:red;" class="fa fa-map-marker"></i> Area</button>
                            <select class="form-control mb-1" v-model="zona" @change="listarPersona(1,buscar,criterio,status,zona,actives)">
                                <option value="">Todo</option>
                                <option value="GDL">Guadalajara</option>
                                <option value="SLP">San Luis</option>
                                <option value="AGS">Aguascalientes</option>
                                <option value="PBA">Puebla</option>
                            </select>
                        </div>
                        <div class="input-group ml-0 ml-md-5">
                            <button class="btn btn-light mb-1"><i style="color:blue;" class="fa fa-info"></i> Estado Ventas</button>
                            <select class="form-control mb-1" v-model="actives" @change="listarPersona(1,buscar,criterio,status,zona,actives)">
                                <option value="">Todo</option>
                                <option value="Y">Activos</option>
                                <option value="N">Inactivos</option>
                            </select>
                        </div>
                        <div class="input-group ml-0 ml-md-5">
                            <button class="btn btn-light mb-1" ><i style="color:red;" class="fa fa-search"></i> Filtro</button>
                            <input type="text" v-model="buscador" @keyup.enter="buscarClave()" class="form-control mb-1" placeholder="Busqueda Anvanzada">
                        </div>
                    </div>
                </div>
                <div class="table-responsive col-md-12">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Nombre</th>
                                <th>No° de cliente</th>
                                <!-- <th>Domicilio</th> -->
                                <th>Teléfono</th>
                                <th>Contacto</th>
                                <th>Teléfono Contacto</th>
                                <th>Tipo</th>
                                <th>Area</th>
                                <th>Clave</th>
                                <th>Vendedor</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayPersona.length">
                            <tr v-for="persona in arrayPersona" :key="persona.id">
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <div v-if="persona.active">
                                            <button type="button" @click="abrirModal('persona','actualizar',persona)" class="btn btn-warning btn-sm m-1">
                                                <i class="icon-pencil"></i>
                                            </button>
                                        </div>
                                        <div>
                                            <template v-if="userrol == 1">
                                                <button type="button" @click="desactivarCliente(persona.id,persona.nombre)" class="btn btn-danger btn-sm m-1" v-if="persona.active">
                                                <i class="icon-trash"></i>
                                            </button>
                                            </template>
                                            <template v-if="userrol == 1">
                                                <button type="button" @click="activarCliente(persona.id,persona.nombre)" class="btn btn-info btn-sm m-1" v-if="!persona.active">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </template>
                                        </div>
                                        <div>
                                            <button type="button" @click="mostrarDetalle(persona)" class="btn btn-success btn-sm m-1">
                                                <i class="icon-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td v-text="persona.nombre"></td>
                                <td v-text="persona.num_documento"></td>
                               <!--  <td> {{ persona.ciudad}} {{persona.domicilio}}  </td> -->
                                <td v-text="persona.telefono"></td>
                                <td v-if="persona.company" v-text="persona.company"></td>
                                <td v-else></td>
                                <td v-if="persona.tel_company" v-text="persona.tel_company"></td>
                                <td v-else></td>
                                <td v-text="persona.tipo"></td>
                                <td v-text="persona.area"></td>
                                <td v-text="persona.clave"></td>
                                <td v-text="persona.vendedor"></td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="10" class="text-center">
                                    <strong>NO hay clientes agregados o con ese criterio...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,status,zona,actives)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,status,zona,actives)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,status,zona,actives)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!--Fin Listado de clientes -->
        <!-- Detalle del cliente -->
        <template v-if="listado==0">
            <div class="card-body">
                <!-- Info del cliente -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-lg-3 text-center">
                        <h1><i class="fa fa-user" aria-hidden="true"></i> {{ nombre}}</h1>
                        <p class="font-weight-bold" style="font-size: 20px;" v-text="tipo"></p>
                        <p class="font-weight-bold" style="font-size: 20px;" v-text="num_documento"></p>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3 col-xl-2">
                        <label for=""><strong>Domicilio: </strong><span style="color:red;" v-show="!domicilio || !ciudad">(*Complete esta informacion)</span> </label>
                        <p> {{domicilio}} - {{ciudad}} </p>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3 col-xl-2">
                        <label for=""><strong>Telefono: </strong><span style="color:red;" v-show="!telefono">(*Complete esta informacion)</span></label>
                        <p v-text="telefono"> </p>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3 col-xl-2">
                        <label for=""><strong>Correo electrónico:</strong> </label>
                        <p v-text="email"> </p>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3 col-xl-2">
                        <label for=""><strong>RFC: </strong><span style="color:red;" v-show="!rfc">(*Complete esta informacion)</span></label>
                        <p v-text="rfc"></p>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3 col-xl-2">
                        <label for=""><strong>Uso de CFDI: </strong><span style="color:red;" v-show="!cfdi">(*Complete esta informacion)</span></label>
                        <p v-text="cfdi"> </p>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3 col-xl-2">
                        <h6>Datos de contacto: </h6>
                        <label for=""><strong>Nombre del contacto: </strong> </label>
                        <p v-text="company"></p>
                    </div>
                    <div class="col-6 col-sm-6 col-lg-3 col-xl-2">
                        <label for=""><strong>Telefono del contacto: </strong> </label>
                        <p v-text="tel_company"> </p>
                    </div>
                    <div class="col">
                        <h6>Observaciones: </h6>
                        <p v-text="observacion"></p>
                    </div>
                </div>
                <!-- Fin Info del cliente -->
                <hr>
                <!-- Fila de Siguiente Tarea & Ultimas ventas  -->
                <div class="row">
                    <!-- Siguiente tarea -->
                    <div class="col-12 col-md-8 col-lg-8 col-xl-6 offset-md-2 offset-xl-0">
                         <div class="d-flex flex-column">
                            <div class="border-bottom"><h4>Siguiente Tarea</h4>&nbsp;</div>
                            <div>
                                <div v-if="nextTask.length">
                                    <div v-for="nexttask in nextTask" :key="nexttask.id">
                                        <div class="row">
                                            <div class="col-12 caja2">
                                                <div class="d-flex justify-content-between mt-3 p-2">
                                                    <div>
                                                        <h4 v-text="nexttask.clase"></h4>
                                                        <p><small style="font-size:15px;" class="text-muted"><i class="fa fa-clock-o"></i> {{ convertDate(nexttask.fecha) }}</small></p>
                                                    </div>
                                                    <div>

                                                    </div>
                                                </div>
                                                <div>
                                                    <p v-text="nexttask.descripcion"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div v-else><h4>Sin tareas pendientes o asignadas...</h4></div>
                            </div>
                        </div>
                    </div>
                    <!-- Ultimas ventas -->
                    <div class="col-12 col-md-8 col-lg-8 col-xl-6 offset-md-2 offset-xl-0 mt-3 mt-sm-3 mt-lg-3 mt-xl-0">
                        <div class="d-flex flex-column">
                            <div><h4>Ultimas Ventas: </h4>&nbsp;</div>
                            <div>
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
                            </div>
                        </div>
                    </div>
                    <!-- Notas de credito ventas -->
                    <div class="col-12 col-md-8 col-lg-8 col-xl-8 offset-md-2 offset-xl-0 mt-3 mt-sm-3 mt-lg-3 mt-xl-0">
                        <div class="d-flex flex-column">
                            <div class="border-bottom d-flex justify-content-between">
                                <div><h4>Notas de crédito: </h4>&nbsp;</div>
                                <div v-if="active != 0">
                                    <button class="btn btn-primary btn-sm" type="button" @click="abrirModalCredit()">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Añadir
                                    </button>
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                                        <thead>
                                            <tr>
                                                <th>Opciones</th>
                                                <th>No° de Nota</th>
                                                <th>Monto</th>
                                                <th>Forma de pago</th>
                                                <th>Fecha</th>
                                                <th>Observaciones</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody v-if="arrayCreditos.length">
                                            <tr v-for="credito in arrayCreditos" :key="credito.id">
                                                <td>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        @click="eliminarCredito(credito.id,persona_id)" v-if="credito.estado != 'Abonada'">
                                                        <i class="fa fa-trash"></i>
                                                    </button>&nbsp;
                                                </td>
                                                <td v-text="credito.num_documento"></td>
                                                <td v-text="credito.total"></td>
                                                <td v-text="credito.forma_pago"></td>
                                                <td>{{ convertDateVenta(credito.fecha_hora) }}</td>
                                                <td v-text="credito.observacion"></td>
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
                                            </tr>
                                        </tbody>
                                        <tbody v-else>
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <strong>Aún no tienes notas de credito con este cliente...</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Fila de Comentarios e historial de tareas -->
                <div class="row mt-3">
                    <!-- Comentarios -->
                    <div class="col-12 col-md-8 col-lg-8 col-xl-6 offset-md-2 offset-xl-0">
                        <div class="d-flex flex-column">
                            <div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>Comentarios </h3>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary btn-sm rounded-circle" @click="newComment()"><i class="fa fa-plus-circle"></i></button>
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
                                            <button class="btn btn-primary mt-2 float-right" @click="saveComment(persona_id)" v-if="commentBody && itsCommentNew">Guardar</button>
                                            <button class="btn btn-primary mt-2 float-right" @click="updateComment(persona_id)" v-if="commentBody && itsCommentUpd">Actualizar</button>
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
                                                                <template v-if="comment.user == userid">
                                                                    <button type="button" class="btn btn-sm btntask float-right" @click="editComment(comment)">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </button>&nbsp;
                                                                    <button type="button" class="btn btn-sm btntask float-right" @click="deleteComentario(comment.id,persona_id)">
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
                    <!-- Tareas -->
                    <div class="col-12 col-md-8 col-lg-8 col-xl-6 offset-md-2 offset-xl-0 mt-3 mt-sm-3 mt-lg-3 mt-xl-0">
                        <div class="d-flex flex-column">
                            <div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3>Historial de tareas de {{ nombre }}</h3>
                                    </div>
                                    <div>
                                        <!-- <button class="btn btn-primary rounded-circle"><i class="fa fa-plus-circle"></i></button> -->
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div>
                                <div class="divtask" v-if="arrayTareaT.length">
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
                                <div v-else style="height: auto !important;">
                                    <h5>Sin historial de tareas...</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Fila de Actividades en calendario & Files Upploader -->
                <div class="form-group row mt-3">
                    <!-- Actividades -->
                    <div class="col-md-6">
                        <div class="page-header">
                            <h3 id="timeline">Actividades en calendario para {{ nombre }} &nbsp;</h3>
                        </div>
                        <hr>
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
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaEvent(persona_id,paginationEvent.current_page - 1,)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="page in pagesNumberEvent" :key="page" :class="[page == isActivedEvent ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaEvent(persona_id,page)" v-text="page"></a>
                                    </li>
                                    <li class="page-item" v-if="paginationEvent.current_page < paginationEvent.last_page">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaEvent(persona_id,paginationEvent.current_page + 1)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div v-else>
                            <h5>Sin Actividades...</h5>
                        </div>
                    </div>
                    <!-- Files Upploader -->
                    <div class="col-md-6">
                        <div class="page-header">
                            <h3 id="timeline">Archivos adjuntos de {{ nombre }} &nbsp;</h3>
                        </div>
                        <div class="divdocs form-inline" v-if="docsArray.length">
                            <div v-for="file in docsArray" :key="file.id" class="d-flex justify-content-around">
                                <div>
                                    <template v-if="file.tipo != 'pdf'">
                                        <div class="d-flex justify-content-center">
                                            <div>
                                                <lightbox class="m-0" album="" :src="'clientesfiles/'+file.url">
                                                    <figure class="figure">
                                                        <img :src="'clientesfiles/'+file.url" width="150" height="100" class="figure-img img-fluid rounded" alt="File CAPTION">
                                                        <figcaption class="figure-caption text-right" v-text="file.url"></figcaption>
                                                    </figure>
                                                </lightbox>&nbsp;
                                            </div>
                                            <div>
                                                <button @click="eliminarFile(file.id,persona_id)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
                                            </div>
                                        </div>

                                    </template>
                                    <template v-else>
                                        <div class="d-flex justify-content-center">
                                            <div>
                                                <figure class="figure">
                                                    <img @click="downloadDoc(file.url)" src="img/PDFICON.png" width="100" height="70" class="figure-img img-fluid rounded" alt="File CAPTION">
                                                    <figcaption class="figure-caption text-right" v-text="file.url"></figcaption>
                                                </figure>
                                            </div>
                                            <div>
                                                <button @click="eliminarFile(file.id,persona_id)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div v-else style="height: auto !important;">
                            <h5>Sin archivos adjuntos...</h5>
                        </div>
                        <hr>
                        <div>
                            <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Subir Archivos</label>
                                    <input type="file" class="form-control" placeholder="Subir Archivos"
                                        multiple accept="image/png,image/jpeg,image/jpg,application/pdf" @change="fieldChange">
                                </div>
                                 <button v-if="arrayFiles.length" @click="guardarFiles()" type="button" class="btn btn-sm btn-primary">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <div class="col-md-12 float-right">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                    </div>
                </div>
            </div>
        </template>

      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!--Inicio del modal agregar/actualizar-->
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
                    <div class="row">
                        <div class="col-12">
                            <h5>Seleccione el tipo de cliente</h5>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a href="#tabCliente" class="nav-link active" data-toggle="tab">Persona Física</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tabEmpresa" class="nav-link" data-toggle="tab">Persona Moral</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <!-- TAB CLIENTE -->
                                <div class="tab-pane active" id="tabCliente" role="tab panel">
                                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3" v-if="userrol==1">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Vendedor</span>
                                                </div>
                                                <select class="form-control" v-model="userid">
                                                    <option value="0" disabled>Seleccione un vendedor</option>
                                                    <option v-for="vendedor in arrayVendedores" :key="vendedor.id" :value="vendedor.id" v-text="vendedor.nombre"></option>
                                                </select>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Tipo</span>
                                                </div>
                                                <select class="form-control" v-model="tipo">
                                                    <option value="" disabled>Seleccione el tipo de cliente</option>
                                                    <option value="CLIENTE FINAL">CLIENTE FINAL</option>
                                                    <option value="CLIENTE MARMOLERO">CLIENTE MARMOLERO</option>
                                                    <option value="CLIENTE COCINERO">CLIENTE COCINERO</option>
                                                    <option value="CLIENTE ARQ">CLIENTE ARQ</option>
                                                    <option value="DECORADORA">DECORADORA</option>
                                                    <option value="MUEBLES Y DECORACION">MUEBLES Y DECORACIÓN</option>
                                                    <option value="CLIENTE MAYOR">CLIENTE MAYOR</option>
                                                    <option value="NO PROMOVER">NO PROMOVER</option>
                                                </select>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Nombre</span>
                                                </div>
                                                <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de la persona física"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Palabra Clave</span>
                                                </div>
                                                <input type="text" v-model="clave" class="form-control" placeholder="Palabra Clave"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Teléfono</span>
                                                </div>
                                                <input type="text" v-model="telefono" maxlength="13" class="form-control" placeholder="Teléfono de la persona física"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Correo electrónico</span>
                                                </div>
                                                <input type="text" v-model="email" class="form-control" placeholder="Correo electrónico"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12  col-xl-8  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Direccion</span>
                                                </div>
                                                <input type="text" v-model="domicilio" class="form-control" placeholder="Domicilio"/>
                                                <input type="text" v-model="ciudad" class="form-control" placeholder="Ciudad y estado"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-xl-8  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Contacto</span>
                                                </div>
                                                <input type="text" v-model="company" class="form-control" placeholder="Nombre de contacto"/>
                                                <input type="text" maxlength="13" v-model="tel_company" class="form-control" placeholder="Teléfono de contacto"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">RFC</span>
                                                </div>
                                                <input type="text" v-model="rfc" maxlength="13" class="form-control" placeholder="RFC persona física"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Uso del CFDI</span>
                                                </div>
                                                <select class="form-control" v-model="cfdi">
                                                    <option value="" disabled>Seleccione</option>
                                                    <option value="G01-Adquisicion de mercacias">G01-Adquisicion de mercacias</option>
                                                    <option value="G02-Devoluciones,descuentos o bonificaciones">G02-Devoluciones,descuentos o bonificaciones</option>
                                                    <option value="G03-Gastos en general">G03-Gastos en general</option>
                                                    <option value="G04-Pago con tarjeta de credito">G04-Pago con tarjeta de credito</option>
                                                    <option value="G28-Pago con tarjeta de debito">G28-Pago con tarjeta de debito</option>
                                                    <option value="I01-Construcciones">I01-Construcciones</option>
                                                    <option value="P01-Por definir">P01-Por definir</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-8 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Observaciones</span>
                                                </div>
                                                <textarea class="form-control rounded-0" style="resize: none;" rows="3" maxlength="256" v-model="observacion"></textarea>
                                            </div>
                                        </div>
                                        <div v-show="errorPersona" class="form-group row div-error">
                                            <div class="text-center text-error">
                                            <div v-for="error in errorMostrarMsjPersona" :key="error" v-text="error"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- TAB EMPRESA -->
                                <div class="tab-pane" id="tabEmpresa" role="tabpanel">
                                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3" v-if="userrol==1">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Vendedor</span>
                                                </div>
                                                <select class="form-control" v-model="userid">
                                                    <option value="0" disabled>Seleccione un vendedor</option>
                                                    <option v-for="vendedor in arrayVendedores" :key="vendedor.id" :value="vendedor.id" v-text="vendedor.nombre"></option>
                                                </select>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Tipo</span>
                                                </div>
                                                <select class="form-control" v-model="tipo">
                                                    <option value="" disabled>Seleccione el tipo de cliente</option>
                                                    <!-- <option value="PROSPECTO">PROSPECTO</option>
                                                    <option value="PRIMER CONTACTO">PRIMER CONTACTO</option> -->
                                                    <option value="CLIENTE FINAL">CLIENTE FINAL</option>
                                                    <option value="CLIENTE MARMOLERO">CLIENTE MARMOLERO</option>
                                                    <option value="CLIENTE COCINERO">CLIENTE COCINERO</option>
                                                    <option value="CLIENTE ARQ">CLIENTE ARQ</option>
                                                   <option value="DECORADORA">DECORADORA</option>
                                                    <option value="MUEBLES Y DECORACION">MUEBLES Y DECORACIÓN</option>
                                                    <option value="CLIENTE MAYOR">CLIENTE MAYOR</option>
                                                    <option value="NO PROMOVER">NO PROMOVER</option>
                                                </select>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Nombre</span>
                                                </div>
                                                <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de la persona moral"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Palabra Clave</span>
                                                </div>
                                                <input type="text" v-model="clave" class="form-control" placeholder="Palabra Clave"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Teléfono</span>
                                                </div>
                                                <input type="text" v-model="telefono" maxlength="13" class="form-control" placeholder="Teléfono de la persona moral"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Correo electrónico</span>
                                                </div>
                                                <input type="text" v-model="email" class="form-control" placeholder="Correo electrónico"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12  col-xl-8  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Direccion</span>
                                                </div>
                                                <input type="text" v-model="domicilio" class="form-control" placeholder="Domicilio"/>
                                                <input type="text" v-model="ciudad" class="form-control" placeholder="Ciudad y estado"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-xl-8  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Contacto</span>
                                                </div>
                                                <input type="text" v-model="company" class="form-control" placeholder="Nombre de contacto"/>
                                                <input type="text" maxlength="13" v-model="tel_company" class="form-control" placeholder="Teléfono de contacto"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">RFC</span>
                                                </div>
                                                <input type="text" v-model="rfc" maxlength="13" class="form-control" placeholder="RFC persona moral"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Uso del CFDI</span>
                                                </div>
                                                <select class="form-control" v-model="cfdi">
                                                    <option value="" disabled>Seleccione</option>
                                                    <option value="G01-Adquisicion de mercacias">G01-Adquisicion de mercacias</option>
                                                    <option value="G02-Devoluciones,descuentos o bonificaciones">G02-Devoluciones,descuentos o bonificaciones</option>
                                                    <option value="G03-Gastos en general">G03-Gastos en general</option>
                                                    <option value="G04-Pago con tarjeta de credito">G04-Pago con tarjeta de credito</option>
                                                    <option value="G28-Pago con tarjeta de debito">G28-Pago con tarjeta de debito</option>
                                                    <option value="I01-Construcciones">I01-Construcciones</option>
                                                    <option value="P01-Por definir">P01-Por definir</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-8 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Observaciones</span>
                                                </div>
                                                <textarea class="form-control rounded-0" style="resize: none;" rows="3" maxlength="256" v-model="observacion"></textarea>
                                            </div>
                                        </div>
                                        <div v-show="errorPersona" class="form-group row div-error">
                                            <div class="text-center text-error">
                                            <div v-for="error in errorMostrarMsjPersona" :key="error" v-text="error"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                    <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal agregar/actualizar-->

    <!--Inicio del modal Comentarios Cliente-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content content-comment">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal2()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3><i class="fa fa-user" aria-hidden="true"></i> {{ nombre }}</h3>
                            <p class="font-weight-bold" style="font-size: 15px;" v-text="tipo"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Comentario</span>
                                </div>
                                <textarea class="form-control rounded-1" style="resize: none;" rows="8" maxlength="400" v-model="descripcion_task"></textarea>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2">Fecha</span>
                                </div>
                                <input type="date" v-model="fecha_task" class="form-control" placeholder="Fecha" aria-label="Fecha" aria-describedby="Fecha">
                            </div>
                        </div>
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
                        <button type="button" class="btn btn-secondary" @click="cerrarModal2()">Cerrar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal2()">Cerrar</button>
                    <button type="button" v-if="tipoAccion==3" class="btn btn-primary" @click="registrarComment()">Guardar</button>
                    <button type="button" v-if="tipoAccion==4" class="btn btn-primary" @click="actualizarComment()">Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal Visualizar Cliente-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal3}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-credit">
                <div class="modal-body ">
                    <h3 class="mb-3">Registrar Nota de crédito</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <h5 for=""><strong>Forma de pago </strong><span style="color:red;" v-show="forma_pagonc==''">(*Seleccione)</span></h5>
                                <select class="form-control" v-model="forma_pagonc" v-if="otroFormPaync == false">
                                    <option value='' disabled>Seleccione la forma de pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Mixto">Mixto</option>
                                </select>
                                <div class="form-check float-left mt-1">
                                    <input class="form-check-input" type="checkbox" id="chkOtherPayab" v-model="otroFormPaync">
                                    <label class="form-check-label p-0 m-0" for="chkOtherPayab"><strong>Otro</strong></label>
                                </div>
                                <input class="form-control rounded-0"  maxlength="35" placeholder="Ingresa la forma de pago" v-model="forma_pagonc" v-if="otroFormPaync == true">
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> $ Monto: </strong></h5>
                            <input type="number" step="any" min="1" class="form-control" v-model="totalnc">
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> Observaciones: </strong></h5>
                            <textarea class="form-control rounded-1" rows="4" maxlength="400" v-model="descripcionnc"></textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-primary mr-2" @click="saveCredit(persona_id,totalnc)">Guardar</button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModalCredit()">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!--Inicio del modal Crear Nota de Credito -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal3}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-credit">
                <div class="modal-body ">
                    <h3 class="mb-3">Registrar Nota de crédito</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <h5 for=""><strong>Forma de pago </strong><span style="color:red;" v-show="forma_pagonc==''">(*Seleccione)</span></h5>
                                <select class="form-control" v-model="forma_pagonc" v-if="otroFormPaync == false">
                                    <option value='' disabled>Seleccione la forma de pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Mixto">Mixto</option>
                                </select>
                                <div class="form-check float-left mt-1">
                                    <input class="form-check-input" type="checkbox" id="chkOtherPayab" v-model="otroFormPaync">
                                    <label class="form-check-label p-0 m-0" for="chkOtherPayab"><strong>Otro</strong></label>
                                </div>
                                <input class="form-control rounded-0"  maxlength="35" placeholder="Ingresa la forma de pago" v-model="forma_pagonc" v-if="otroFormPaync == true">
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> $ Monto: </strong></h5>
                            <input type="number" step="any" min="1" class="form-control" v-model="totalnc">
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> Observaciones: </strong></h5>
                            <textarea class="form-control rounded-1" rows="4" maxlength="400" v-model="descripcionnc"></textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-primary mr-2" @click="saveCredit(persona_id,totalnc)">Guardar</button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModalCredit()">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
    <!--inicio del modal crear creditos-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal5}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-credit">
                <div class="modal-body ">
                    <h3 class="mb-3">Registrar Nota de crédito</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <h5 for=""><strong>Forma de pago </strong><span style="color:red;" v-show="forma_pagonc==''">(*Seleccione)</span></h5>
                                <select class="form-control" v-model="forma_pagonc" v-if="otroFormPaync == false">
                                    <option value='' disabled>Seleccione la forma de pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Mixto">Mixto</option>
                                </select>
                                <div class="form-check float-left mt-1">
                                    <input class="form-check-input" type="checkbox" id="chkOtherPayab" v-model="otroFormPaync">
                                    <label class="form-check-label p-0 m-0" for="chkOtherPayab"><strong>Otro</strong></label>
                                </div>
                                <input class="form-control rounded-0"  maxlength="35" placeholder="Ingresa la forma de pago" v-model="forma_pagonc" v-if="otroFormPaync == true">
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> $ Monto: </strong></h5>
                            <input type="number" step="any" min="1" class="form-control" v-model="totalnc">
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> Observaciones: </strong></h5>
                            <textarea class="form-control rounded-1" rows="4" maxlength="400" v-model="descripcionnc"></textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-primary mr-2" @click="saveCredit(persona_id,totalnc)">Guardar</button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModalCredit()">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Modal Estado de cuenta -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal4}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-cuenta">
                <div class="modal-body ">
                     <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-3">
                             <h4 class="mb-1">Exportar Estado de cuenta del cliente : {{ nombre }}</h4>
                             <div class="justify-content-center d-flex mt-5">
                                  <div>
                                    <label for=""><strong>Inicio: </strong></label>
                                    <input type="date" class="form-control" v-model="date1">
                                </div>
                                <div>
                                    <label for=""><strong>Fin: </strong></label>
                                    <input type="date" class="form-control" v-model="date2">
                                </div>
                             </div>
                            <div class="justify-content-center d-flex mt-2">
                                <button type="button" class="btn btn-success mr-2" @click="ventasClienteExcel(persona_id,date1,date2)"><i class="fa fa-file-excel-o"></i> Excel</button>
                                <button type="button" class="btn btn-danger" @click="ventasClientePDF(persona_id,date1,date2)"><i class="fa fa-file-pdf-o"></i> PDF</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 mb-0">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-secondary" @click="cerrarModalEstadoCR()">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin Modal Estado de cuenta -->


  </main>
</template>

<script>
import moment from 'moment';
import VueLightbox from 'vue-lightbox';
Vue.component("Lightbox",VueLightbox);
export default {
    data() {
        return {
            persona_id: 0,
            usrol : 0,
            nombre: "",
            tipo_documento: "",
            num_documento: "",
            ciudad: "",
            domicilio: "",
            telefono: "",
            email: "",
            rfc: "",
            tipo: "",
            observacion: "",
            cfdi : "",
            userid : "",
            userrol: "",
            company : "",
            tel_company : "",
            area : "",
            arrayPersona: [],
            modal  : 0,
            modal2 : 0,
            modal3 : 0,
            modal4 : 0,
            modal5 : 0,
            modal6 : 0,
            tituloModal: "",
            tipoAccion: 0,
            errorPersona: 0,
            errorMostrarMsjPersona: [],
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            offset : 3,
            criterio : 'nombre',
            zona : "",
            buscar : '',
            usuario: '',
            arrayVendedores : [],
            listado : 1,
            btnNewCliente : 1,
            vendedorD : "",
            nextTask : [],
            arrayTareaT : [],
            arrayVentasT : [],
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
            id_task : 0,
            nombre_task : "",
            descripcion_task : "",
            tipo_task : "Comentario",
            fecha_task : "",
            estado_task : "",
            CodeDate : "",
            errorTarea: 0,
            errorMostrarMsjTarea: [],
            dateAct : "",
            status : '',
            arrayFiles : [],
            docsArray : [],
            arrayComentarios : [],
            commentBody : "",
            CommentNew : 0,
            itsCommentUpd : 0,
            itsCommentNew : 0,
            comment_id : 0,
            actives: "",
            arrayCreditos : [],
            forma_pagonc : '',
            otroFormPaync : false,
            totalnc : 0,
            descripcionnc : '',
            active : 0,
            date1: '',
            date2: '',
            usuario : 0,
            personas:[],
            buscador:'',
            timeout:0,
            clave:'',
            setTimeoutBuscador:''

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

            },
            isActivedEvent: function(){
                return this.paginationEvent.current_page;
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
            randomNumber : function(){
            return Math.floor(Math.random() * (10 - 1 + 1)) + 1;
            }
        },
    methods: {
        listarPersona (page,buscar,criterio,status,zona,actives){
            let me=this;
            me.btnNewCliente = 1;
            var url= '/cliente?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&status='
                + status + '&zona='+ zona + '&actives='+ actives;
            axios.get(url, {
                params:{
                    buscador: this.buscador,
                }
            }).then(function (response) {

                var respuesta= response.data;
                me.arrayPersona = respuesta.personas.data;
                me.pagination= respuesta.pagination;
                me.userid = respuesta.userid;
                me.userrol = respuesta.userrol;
                /* console.log("Rol: " + respuesta.userrol);
                console.log("ID: " + respuesta.userid); */
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        listarPersonaPalabra(){

                axios.get('/cliente/listarClave',{
                    params:{
                        buscador: this.buscador,
                    }
                }).then( res =>{
                    this.personas = res.data;
                }).catch( error =>{
                    console.log(error.response)
                });

        },
        buscarClave(){

            clearTimeout( this.setTimeoutBuscador)
            this.setTimeoutBuscador = setTimeout(this.listarPersona(), 360);
        },
        abrirModal5(){
            this.modal = 1;
            this.tituloModal = "Busqueda Avanzada";

        },
        cerrarModal5(){
            let me = this;
            this.modal5 = 0;
            me.listarPersona(1,'','nombre',1,me.zona,'');
        },
        cambiarPagina(page,buscar,criterio,status,zona,actives){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarPersona(page,buscar,criterio,status,zona,actives);
        },
        registrarPersona() {
            if (this.validarPersona()) {
                return;
            }
            let me = this;
            let date = "";
            moment.locale('es');
            date = moment().format('YYMMDD');

            var newName = this.nombre;
            var rand = Math.round(Math.random() * (99 - 999));

            var areaVend="";
            for(var i=0;i<this.arrayVendedores.length;i++){
                if(this.arrayVendedores[i].id==this.userid){
                   /* console.log(this.arrayVendedores[i].area); */
                   areaVend = this.arrayVendedores[i].area;
                }
            }

            var numcomp = "C-".concat(date,rand);

           /*  console.log("Codigo: " + numcomp); */

            axios.post("/cliente/registrar", {
                'nombre': this.nombre,
                'tipo_documento': this.tipo_documento,
                'num_documento': numcomp,
                'ciudad': this.ciudad,
                'domicilio': this.domicilio,
                'telefono': this.telefono,
                'clave' : this.clave,
                'company' : this.company,
                'tel_company' : this.tel_company,
                'email': this.email,
                'rfc': this.rfc,
                'tipo': this.tipo,
                'idusuario' : this.userid,
                'observacion':this.observacion,
                'cfdi' : this.cfdi,
                'area' : areaVend
            })
            .then(function(response) {
                swal.fire(
                'Registrado!',
                'El cliente ' + '<b>' + newName + '</b>' + ' ha sido registrado con éxito.',
                'success');
                me.cerrarModal();
                me.listarPersona(1,'','nombre',1,me.zona,'');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarPersona() {
            var page = this.pagination.current_page;

            if (this.validarPersona()) {
                return;
            }
            let me = this;
            var clienteUp =  this.nombre;

            var areaVend="";
            for(var i=0;i<this.arrayVendedores.length;i++){
                if(this.arrayVendedores[i].id==this.userid){
                   areaVend = this.arrayVendedores[i].area;
                }
            }

            axios.put("/cliente/actualizar", {
                'nombre': this.nombre,
                'tipo_documento': this.tipo_documento,
                'num_documento': this.num_documento,
                'ciudad': this.ciudad,
                'domicilio': this.domicilio,
                'telefono': this.telefono,
                'company' : this.company,
                'tel_company' : this.tel_company,
                'email': this.email,
                'rfc': this.rfc,
                'clave':this.clave,
                'id': this.persona_id,
                'tipo': this.tipo,
                'cfdi' : this.cfdi,
                'idusuario' : this.userid,
                'observacion':this.observacion,
                'area' : areaVend
            })
            .then(function(response) {
                me.cerrarModal();
                swal.fire(
                'Actualizado!',
                'El cliente ' + '<b>' + clienteUp + '</b>' + ' ha sido actualizado con éxito.',
                'success')
                /* me.listarPersona(page,'','nombre',1,me.zona,''); */
                me.listarPersona(page,me.buscar,me.criterio,me.status,me.zona,me.actives);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        validarPersona() {
            this.errorPersona = 0;
            this.errorMostrarMsjPersona = [];

            if (!this.nombre)
                this.errorMostrarMsjPersona.push("El nombre del cliente no puede estar vacío.");

            if (this.errorMostrarMsjPersona.length) this.errorPersona = 1;

            return this.errorPersona;
        },
        cerrarModal() {
            var page = this.pagination.current_page;
            var stat = this.status;
            this.modal = 0;
            this.tituloModal = "";
            this.nombre = "";
            this.tipo_documento = "";
            this.num_documento = "";
            this.ciudad = "";
            this.domicilio = "";
            this.telefono = "";
            this.company = "";
            this.clave = "";
            this.tel_company = "";
            this.email = "";
            this.rfc = "";
            this.errorPersona = 0;
            this.tipo = "";
            this.observacion = "";
            this.listarPersona(page,this.buscar,this.criterio,this.status,this.zona,this.actives);
        },
        abrirModal(modelo, accion, data = []) {
            switch (modelo) {
                case "persona": {
                    switch (accion) {
                        case "registrar": {
                            this.modal = 1;
                            this.tituloModal = "Registrar Cliente";
                            this.nombre = "";
                            this.tipo_documento = "";
                            this.num_documento = "";
                            this.ciudad = "";
                            this.domicilio = "";
                            this.telefono = "";
                            this.company = "";
                            this.tel_company = "";
                            this.email = "";
                            this.clave = "";
                            this.rfc = "";
                            this.tipo = "";
                            this.observacion = "";
                            this.tipoAccion = 1;
                            break;
                        }
                        case "actualizar": {
                            this.modal = 1;
                            this.tituloModal = "Actualizar Cliente";
                            this.tipoAccion = 2;
                            this.persona_id = data["id"];
                            this.nombre = data["nombre"];
                            this.tipo_documento = data["tipo_documento"];
                            this.num_documento = data["num_documento"];
                            this.ciudad = data["ciudad"];
                            this.domicilio = data["domicilio"];
                            this.telefono = data["telefono"];
                            this.company = data["company"];
                            this.tel_company = data["tel_company"];
                            this.email = data["email"];
                            this.rfc = data["rfc"];
                            this.clave = data['clave'];
                            this.cfdi = data["cfdi"];
                            this.tipo = data["tipo"];
                            this.observacion = data["observacion"];
                            this.userid = data["idvendedor"];
                            break;
                        }
                    }
                }
            }
            this.selectVendedor();
        },
        abrirModal2(data = []){
            this.modal2 = 1;
            this.tituloModal = "Detalles Cliente: " + data["nombre"];
            this.tipoAccion = 2;
            this.persona_id = data["id"];
            this.nombre = data["nombre"];
            this.tipo_documento = data["tipo_documento"];
            this.clave = data["clave"];
            this.num_documento = data["num_documento"];
            this.ciudad = data["ciudad"];
            this.domicilio = data["domicilio"];
            this.telefono = data["telefono"];
            this.company = data["company"];
            this.tel_company = data["tel_company"];
            this.email = data["email"];
            this.rfc = data["rfc"];
            this.tipo = data["tipo"];
            this.observacion = data["observacion"];
            this.userid = data["idvendedor"];
            this.cfdi = data["cfdi"];
        },
        cerrarModal2(){
            var page = this.pagination.current_page;
            this.modal2 = 0;
            this.tituloModal = "";
            this.errorTarea =0;
            this.errorMostrarMsjTarea = [];
            this.obtenerTareas(this.persona_id);
        },
        selectVendedor(){
            let me=this;
            var url= '/user/selectUsuario';

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayVendedores = respuesta.usuarios;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        mostrarDetalle(data = []){
            this.listado = 0;
            this.btnNewCliente = 0;
            this.persona_id = data["id"];
            this.nombre = data["nombre"];
            this.tipo_documento = data["tipo_documento"];
            this.clave = data["clave"];
            this.num_documento = data["num_documento"];
            this.ciudad = data["ciudad"];
            this.domicilio = data["domicilio"];
            this.telefono = data["telefono"];
            this.company = data["company"];
            this.tel_company = data["tel_company"];
            this.email = data["email"];
            this.rfc = data["rfc"];
            this.tipo = data["tipo"];
            this.observacion = data["observacion"];
            this.userid = data["idvendedor"];
            this.persona_id =  data["id"];
            this.cfdi =  data["cfdi"];
            this.vendedorD = data["vendedor"];
            this.active = data["active"];
            var idcliente = data["id"];

            this.obtenerTareas(idcliente);
            this.obtenerVentas(idcliente);
            this.obtenerEventos(idcliente,1);
            this.getDocs(idcliente);
            this.getComments(idcliente);
            this.getCredits(data["id"]);

        },
        ocultarDetalle(){
            var page = this.pagination.current_page;
            var stat = this.status;
            this.listado = 1;
            this.btnNewCliente = 1;
            this.persona_id = 0;
            this.nombre = "";
            this.tipo_documento = "";
            this.num_documento = "";
            this.ciudad = "";
            this.domicilio = "";
            this.telefono = "";
            this.company = "";
            this.tel_company = "";
            this.email = "";
            this.rfc = "";
            this.clave= "";
            this.tipo = "";
            this.observacion = "";
            this.userid = "";
            this.persona_id = "";
            this.cfdi = "";
            this.vendedorD = "";
            this.nextTask = [];
            this.arrayTareaT = [];
            this.arrayVentasT = [];
            this.arrayCommentT = [];
            this.arrayActividadesT = [];
            this.arrayCreditos = [];
            this.forma_pagonc = '';
            this.otroFormPaync = false;
            this.totalnc = 0;
            this.active = 0;
            this.descripcionnc = '';
            this.listarPersona(page,this.buscar,this.criterio,this.status,this.zona,this.actives);
        },
        obtenerTareas(idcliente){
            let me = this;
            var url= '/tarea/obtenerTareas?idcliente=' + idcliente;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayTareaT = respuesta.tareas.data;
                me.nextTask = respuesta.siguiente.data;
                me.arrayCommentT = respuesta.comentarios.data;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        obtenerVentas(idcliente){
            let me = this;
            var url= '/venta/obtenerVentasCliente?idcliente=' + idcliente;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayVentasT = respuesta.ventas.data;
               /*  console.log(me.arrayVentasT); */
            })
            .catch(function (error) {
                console.log(error);
            });
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
        getDocs(idcliente){
            let me = this;
            var url= '/cliente/getDocs?id=' + idcliente;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.docsArray = respuesta.documentos;
                /* console.log(respuesta.documentos); */
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPaginaEvent(idcliente,page){
            let me = this;
            //Actualiza la página actual
            me.paginationEvent.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.obtenerEventos(idcliente,page);
        },
        convertDate(date){
            let me=this;
            moment.locale('es');
            var datec = moment(date).format('DD-MMMM-YYYY');
            /* console.log(datec); */
            return datec;
        },
        convertDateVenta(date){
            let me=this;
            var datec = moment(date).format('MMM DD YYYY HH:mm:ss');
            /* console.log(datec); */
            return datec;
        },
        UpdateTask(accion, data = []){
            switch (accion) {
                case "newComment" : {

                    var daten =  moment().format('YYYY-MM-DD');

                    this.modal2 = 1;
                    this.tipoAccion = 3;
                    this.cerrarDet = 1;
                    this.tituloModal = "Nuevo Comentario Para " + this.nombre;
                    this.isEdition = true;
                    this.iscompleted = false;
                    this.nombre_task = "";
                    this.descripcion_task = "";
                    this.tipo_task = "Comentario";
                    this.fecha_task = daten;
                    this.isComment = 1;
                    break;
                }
                case "comment": {

                    /* console.log(data); */

                    this.modal2 = 1;
                    this.tituloModal = "Modificar " + data["clase"] + " de " + data['cliente'];
                    this.tipoAccion = 4;

                    this.id_task = data['id'];
                    this.nombre_task = data['nombre'];
                    this.descripcion_task = data['descripcion'];
                    this.tipo_task = data['clase'];
                    this.fecha_task = data['fecha'];
                    this.estado_task =data['estado'];
                    this.isEdition = true;
                    this.iscompleted = false;
                    this.cerrarDet = 1;
                    this.isComment = 1;

                    break;
                }
            }
        },
        registrarComment(){
            if (this.validarTarea()) {
                return;
            }
            let me = this;
            var name = "T-".concat(me.CodeDate,"-",me.tipo_task);
            /* console.log(name); */
            me.nombre_task = name;
            axios.post('/tarea/registrar',{
                'idcliente': this.persona_id,
                'nombre': this.nombre_task,
                'descripcion' : this.descripcion_task,
                'tipo' : this.tipo_task,
                'fecha' : this.fecha_task
            }).then(function(response) {
                /* me.verTarea(me.idcliente); */
                me.cerrarModal2();
                swal.fire(
                'Completado!',
                'El comentario ha sido registrado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarComment(){

            if (this.validarTarea()) {
                return;
            }

            let me = this;
            axios.put('/tarea/actualizar',{
                'id' : this.id_task,
                'nombre' : this.nombre_task,
                'idcliente': this.persona_id,
                'nombre': this.nombre_task,
                'descripcion' : this.descripcion_task,
                'tipo' : this.tipo_task,
                'fecha' : this.fecha_task
            }).then(function(response) {
                /* me.verTarea(me.idcliente); */
                me.cerrarModal2();
                swal.fire(
                'Completado!',
                'El comentario ha sido actualizado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        validarTarea() {
            let me = this;
            me.errorTarea = 0;
            me.errorMostrarMsjTarea = [];

            if(me.descripcion_task == '') me.errorMostrarMsjTarea.push("El contenido no puede estar vacío");
            if (me.fecha_task == '') me.errorMostrarMsjTarea.push("Seleccione la fecha");
            if(me.fecha_task < me.dateAct) me.errorMostrarMsjTarea.push("La fecha del comentario no puede ser menos a la fecha actual");
            if (me.errorMostrarMsjTarea.length) me.errorTarea = 1;
            return me.errorTarea;
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
                        swal.fire(
                            'Eliminado!',
                            'El comentario ha sido eliminado con éxito.',
                            'success'
                        )
                        me.obtenerTareas(me.persona_id);
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        desactivarCliente(id,name) {
            var page = this.pagination.current_page;
            var stat = this.status;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de eliminar al cliente " + name +"?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    var upCliente = me.nombre;
                    axios.put('/cliente/desactivar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarPersona(page,me.buscar,me.criterio,me.status,me.zona,me.actives);
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El cliente " + "<b>" + name +"</b>" +" ha sido eliminado con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        activarCliente(id,name) {
            var page = this.pagination.current_page;
            var stat = this.status;
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de activar al cliente " + name + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/cliente/activar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarPersona(page,me.buscar,me.criterio,me.status,me.zona,me.actives);
                        swalWithBootstrapButtons.fire(
                            "Activado!",
                            "El cliente " + "<b>" + name +"</b>" +" ha sido activado con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        pdfVenta(id){
            window.open('/venta/pdf/'+id);
        },
        downloadDoc(file){
            window.open('clientesfiles/'+file);
        },
        fieldChange(e){
            /* console.log(e); */

            let selectedFilesTemp = e.target.files;

           /*  console.log(selectedFilesTemp); */

            for(var i=0;i<selectedFilesTemp.length;i++){

                /* console.log("Archivo " + i + ":" + selectedFilesTemp[i]['name']);

                console.log("Tipo  " + i + ":" + selectedFilesTemp[i]['type']); */

                let upFile = e.target.files[i];
                let type = e.target.files[i]["type"];
                this.cargarFiles(upFile,type);

                /* this.arrayFiles.push(selectedFilesTemp[i]); */
            }

        },
        cargarFiles(img,type){
            let reader = new FileReader();
            reader.onload = (e) => {
                this.arrayFiles.push({
                    url : e.target.result,
                    tipo : type
                });
            }
            reader.readAsDataURL(img);
        },
        guardarFiles(){
            let me = this;
            var cliente = this.persona_id;
            axios.put('/cliente/filesupplo',{
                'id' : this.persona_id,
                'filesdata': this.arrayFiles
            }).then(function(response) {
                swal.fire(
                'Completado!',
                'Los archivos fueron guardados con éxito.',
                'success');
                me.arrayFiles = [];
                me.getDocs(cliente);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        eliminarFile(id,idcliente){
            var cliente = idcliente;
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro eliminar este documento?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/cliente/eliminarDoc', {
                        'id' : id
                    }).then(function(response) {
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El documento ha sido eliminada con éxito.",
                            "success"
                        );
                        me.getDocs(cliente);
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
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
        },
        abrirModalCredit(){
            this.modal3 = 1;
            this.forma_pagonc = '';
            this.otroFormPaync = false;
            this.totalnc = 0;
            this.descripcionnc = '';
        },
        cerrarModalCredit(){
            this.modal3 = 0;
            this.forma_pagonc = '';
            this.otroFormPaync = false;
            this.totalnc = 0;
            this.descripcionnc = '';
        },
        saveCredit(id,total){
            let me = this;
            var numnota =  Math.floor(Math.random() * 9000000000) + 1000000000;
            let totalF = parseFloat(total);
            if(this.forma_pagonc == ''){
                swal.fire(
                'Atención!',
                'Ingrese la forma de pago.',
                'error');
            }else{
                if(totalF <= 0){
                    swal.fire(
                    'Error!',
                    'El monto no puede ser 0 o negativo.',
                    'error');
                    this.totalnc = 0;
                }else{
                    let me = this;
                    axios.post('/cliente/crearCredit',{
                        'id' : id,
                        'total' : totalF,
                        'num_notac' : numnota,
                        'forma_pago' : this.forma_pagonc,
                        'observacion' : this.descripcionnc
                    }).then(function(response) {
                        swal.fire(
                        'Completado!',
                        'La nota de credito ha sido registrado con éxito.',
                        'success');
                        me.getCredits(id);
                        me.cerrarModalCredit();
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
                }
            }
        },
        getCredits(id){
            let me = this;
            var url= '/cliente/getCredits?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayCreditos = respuesta.creditos;
            })
            .catch(function (error) {
                console.log(error);
            });
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
                        me.getCredits(idcliente);
                        swal.fire(
                            'Eliminado!',
                            'La nota de credito ha sido eliminada con éxito.',
                            'success'
                        );
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){

                }
            })
        },
        abrirModalEstadoCR(){
            this.modal4 = 1;
        },
        cerrarModalEstadoCR(){
            this.modal4 = 0;
            this.date1 = '';
            this.date2 = '';
        },
        ventasClienteExcel(id, date1, date2){
            window.open('/venta/ventasClienteExcel/' + id + '/'+ date1 + '/' + date2);
            this.cerrarModalEstadoCR();
        },
        ventasClientePDF(id, date1, date2){
            window.open('/venta/ventasClientePDF/'+id + '/'+ date1 + '/' + date2);
        },

    },
    mounted() {
        this.listarPersona(1,this.buscar, this.criterio,this.status,this.zona,this.actives,this.usuario);
        this.listarPersonaPalabra();



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
    .content-task{
        min-height: 652px !important;
    }
    .content-comment{
        height: 452px !important;
    }
    .div-error {
      display: flex;
      justify-content: center;
    }
    .text-error {
      color: red !important;
      font-weight: bold;
    }
    div.caja2{
        /* box-shadow: inset 0 0 2px black; */
        -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
        /* height: 150px; */
        height: auto !important;

    }
    div.divdocs{
        height: 300px !important;
        width: 100% !important;
        overflow-y: scroll;
        scrollbar-width: none;
    }
    .divdocs::-webkit-scrollbar {
        display: none;
    }
    .content-credit{
        height: 440px !important;
    }
    .content-cuenta {
        height: 340px !important;
    }

</style>
