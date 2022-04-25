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
                <i class="fa fa-align-justify"></i> Recadero
                <button type="button" @click="newCall()" class="btn btn-secondary" v-if="listado==0">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <!-- Listado de Llamadas -->
            <template v-if="listado == 0">
                <div class="card-body">
                    <div class="form-inline">
                        <div class="form-group mb-2 col-12">
                            <div class="input-group">
                                <select class="form-control mb-1" v-model="criterio">
                                    <option value="nombre">Cliente</option>
                                    <option value="tipo">Tipo Cliente</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <input type="text" v-model="buscar" @keyup.enter="listarLlamadas(1,buscar,criterio,estado,zona)" class="form-control mb-1" placeholder="Texto a buscar">
                            </div>
                            <div class="input-group">
                                <select class="form-control mb-1" v-model="estado" @change="listarLlamadas(1,buscar,criterio,estado,zona)">
                                    <option value="">Todo</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Atendido">Completada</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                                 <button type="submit" @click="listarLlamadas(1,buscar,criterio,estado,zona)" class="btn btn-primary mb-1"><i class="fa fa-search mb-1"></i>Filtro</button>
                            </div>
                            <div class="input-group ml-5">
                                <button class="btn btn-light mb-1"><i style="color:red;" class="fa fa-map-marker"></i> Area</button>
                                <select class="form-control mb-1" v-model="zona" @change="listarLlamadas(1,buscar,criterio,estado,zona)">
                                    <option value="">Todo</option>
                                    <option value="GDL">Guadalajara</option>
                                    <option value="SLP">San Luis</option>
                                </select>
                            </div>
                            <div class="input-group ml-0 ml-md-5">
                                <button class="btn btn-light mb-1" ><i style="color:red;" class="fa fa-search"></i> Filtro</button>
                                <input type="text" v-model="buscador" @keyup.enter="buscarPalabra()" class="form-control mb-1 " placeholder="Busqueda Anvanzada">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                            <tr>
                                <th class="opc-btns">Opciones</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Area</th>
                                <th>Tipo</th>
                                <th>Clave</th>
                                <th>Contacto</th>
                                <th>Tel Contacto</th>
                                <th>Domicilio</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Atendió</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayLlamada.length">
                                <tr v-for="llamada in arrayLlamada" :key="llamada.id">
                                    <td>
                                        <div class="form-inline">
                                            <button type="button" @click="verLlamada(llamada)" class="btn btn-success btn-sm">
                                                <i class="icon-eye"></i>
                                            </button> &nbsp;
                                            <template v-if="llamada.status == 'Pendiente'">
                                                <button type="button" @click="editTask(llamada)" class="btn btn-warning btn-sm">
                                                    <i class="icon-pencil"></i>
                                                </button> &nbsp;
                                                <button type="button" class="btn btn-danger btn-sm" @click="desactivarLlamada(llamada.id)">
                                                    <i class="icon-trash"></i>
                                                </button>&nbsp;
                                            </template>
                                        </div>
                                    </td>
                                    <td v-text="llamada.cliente"></td>
                                    <td v-text="llamada.telefono"></td>
                                    <td v-text="llamada.area"></td>
                                    <td v-text="llamada.tipo"></td>
                                    <td v-text="llamada.clave"></td>
                                    <td v-text="llamada.company"></td>
                                    <td v-text="llamada.tel_company"></td>
                                    <td> {{llamada.domicilio}} {{llamada.ciudad}}</td>
                                    <td> {{ convertDate(llamada.fecha) }} </td>

                                    <td class="text-center">
                                        <div v-if="llamada.status == 'Pendiente'">
                                            <span class="badge badge-warning">Pendiente</span>
                                        </div>
                                        <div v-else-if="llamada.status == 'Atendido'">
                                            <span class="badge badge-success">Atendido</span>
                                        </div>
                                        <div v-else-if="llamada.status == 'Cancelada'">
                                            <span class="badge badge-danger">Cancelada</span>
                                        </div>
                                    </td>
                                    <td v-text="llamada.usuario"></td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <strong>NO hay llamadas registradas o con ese criterio...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estado,zona)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estado,zona)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estado,zona)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </template>
            <!-- Fin Listado de  Llamadas -->

            <!-- Editar/Nueva Llamadas -->
            <template v-if="listado == 1">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12 text-center">
                           <h3 v-text="tituloDetalle"></h3>
                        </div>
                    </div>
                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="row">
                            <div class="col-12">
                                <h5>Información de la persona</h5>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="#tabCliente" class="nav-link active" data-toggle="tab">{{ tabTitle }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#tabProveedor" class="nav-link" data-toggle="tab" v-if="!CallEdit">Proveedor</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <!-- TAB CLIENTE -->
                                    <div class="tab-pane active" id="tabCliente" role="tab panel">
                                        <div class="row">
                                            <div class="col-12" v-if="!CallEdit"><h5>Cliente</h5></div>
                                        </div>
                                        <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                            <div class="row">
                                                <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Tipo</span>
                                                    </div>
                                                    <select class="form-control" v-model="tipo_cliente">
                                                        <option value="" disabled>Seleccione el tipo de cliente</option>
                                                        <option value="Proveedor">PROVEEDOR</option>
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
                                                    <input type="text" v-model="nombre_cliente" class="form-control" placeholder="Nombre del cliente"/>
                                                </div>
                                                <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Teléfono</span>
                                                    </div>
                                                    <input type="text" v-model="telefono_cliente" maxlength="13" class="form-control" placeholder="Teléfono del cliente"/>
                                                </div>
                                                <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Palabra Clave</span>
                                                    </div>
                                                    <input type="text" v-model="clave" class="form-control" placeholder="Palabra Clave"/>
                                                </div>
                                                <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Correo electrónico</span>
                                                    </div>
                                                    <input type="text" v-model="email_cliente" class="form-control" placeholder="Correo electrónico"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-group input-group-sm col-12  col-xl-8  mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Direccion</span>
                                                    </div>
                                                    <input type="text" v-model="domicilio_cliente" class="form-control" placeholder="Domicilio"/>
                                                    <input type="text" v-model="ciudad_cliente" class="form-control" placeholder="Ciudad y estado"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-group input-group-sm col-12 col-xl-8  mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Contacto</span>
                                                    </div>
                                                    <input type="text" v-model="company_cliente" class="form-control" placeholder="Nombre de contacto"/>
                                                    <input type="text" maxlength="13" v-model="tel_company_cliente" class="form-control" placeholder="Teléfono de contacto"/>
                                                </div>
                                            </div>
                                            <div v-show="errorCliente" class="form-group row div-error">
                                                <div class="text-center text-error">
                                                <div v-for="error in errorMostrarMsjCliente" :key="error" v-text="error"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between">
                                                    <div>
                                                        <h5>Observaciones</h5>
                                                    </div>
                                                    <div>
                                                        <div class="form-inline">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <h3><i style="color:red;" class="fa fa-map-marker"></i> Area: &nbsp; </h3>
                                                                    <select class="form-control" v-model="call_area">
                                                                        <option value="GDL">Guadalajara</option>
                                                                        <option value="SLP">San Luis</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div>
                                                        <template class="justify-content-center col-10">
                                                            <editor :options="editorOptions" mode="wysiwyg" v-model="body" Width="100%"/>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row d-flex justify-content-center mt-2">
                                                <div class="col-12 mb-3">
                                                    <button type="button" @click="cerrarDetalle()"  class="btn btn-secondary float-right">Cancelar</button>
                                                    <button v-if="CallNew" type="button" class="btn btn-primary float-right mr-2" @click="registrarLlamadaCliente()">Guardar</button>
                                                    <button v-if="CallEdit" type="button" class="btn btn-primary float-right mr-2" @click="actualizarLlamadaCliente()">Actualizar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- TAB PROVEEDOR -->
                                    <div class="tab-pane" id="tabProveedor" role="tabpanel" v-if="!CallEdit">
                                        <div class="row">
                                            <div class="col-12"><h5>Proveedor</h5></div>
                                        </div>
                                        <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                            <div class="row">
                                                <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Nombre</span>
                                                    </div>
                                                    <input type="text" v-model="nombre_proveedor" class="form-control" placeholder="Nombre del proveedor"/>
                                                </div>
                                                <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Teléfono</span>
                                                    </div>
                                                    <input type="text" v-model="telefono_proveedor" maxlength="13" class="form-control" placeholder="Teléfono del proveedor"/>
                                                </div>
                                                <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Correo electrónico</span>
                                                    </div>
                                                    <input type="text" v-model="email_proveedor" class="form-control" placeholder="Correo electrónico"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-group input-group-sm col-12  col-xl-8  mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Direccion</span>
                                                    </div>
                                                    <input type="text" v-model="domicilio_proveedor" class="form-control" placeholder="Domicilio"/>
                                                    <input type="text" v-model="ciudad_proveedor" class="form-control" placeholder="Ciudad y estado"/>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-group input-group-sm col-12 col-xl-8  mb-3">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">Contacto</span>
                                                    </div>
                                                    <input type="text" v-model="company_proveedor" class="form-control" placeholder="Nombre de contacto"/>
                                                    <input type="text" maxlength="13" v-model="tel_company_proveedor" class="form-control" placeholder="Teléfono de contacto"/>
                                                </div>
                                            </div>
                                            <div v-show="errorProveedor" class="form-group row div-error">
                                                <div class="text-center text-error">
                                                <div v-for="error in errorMostrarMsjProveedor" :key="error" v-text="error"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 d-flex justify-content-between">
                                                    <div>
                                                        <h5>Observaciones</h5>
                                                    </div>
                                                    <div>
                                                        <div class="form-inline">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <h3><i style="color:red;" class="fa fa-map-marker"></i> Area: &nbsp; </h3>
                                                                    <select class="form-control" v-model="call_area">
                                                                        <option value="GDL">Guadalajara</option>
                                                                        <option value="SLP">San Luis</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div>
                                                        <template class="justify-content-center col-10">
                                                            <editor :options="editorOptions" mode="wysiwyg" v-model="body" Width="100%"/>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row d-flex justify-content-center mt-2">
                                                <div class="col-12 mb-3">
                                                    <button type="button" @click="cerrarDetalle()"  class="btn btn-secondary float-right">Cancelar</button>
                                                    <button v-if="CallNew" type="button" class="btn btn-primary float-right mr-2" @click="registrarLlamadaProveedor()">Guardar</button>
                                                    <button v-if="CallEdit" type="button" class="btn btn-primary float-right mr-2" @click="actualizarLlamadaProveedor()">Actualizar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </template>
            <!-- Fin editar/nueva Llamadas -->

            <!-- Ver Llamada y segumiento -->
            <template v-if="listado == 2">
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-8 text-center mt-3 d-flex justify-content-end">
                            <div>
                                <h3><i style="color:red;" class="fa fa-map-marker"></i> Area: &nbsp; </h3>
                            </div>
                            <div>
                                <span style="font-size: 20px;" v-if="call_area == 'GDL'">
                                    Guadalajara
                                </span>
                                <span style="font-size: 20px;" v-else-if="call_area == 'SLP'">
                                    San Luís
                                </span>
                            </div>

                        </div>
                        <div class="col-md-1" v-if="status != 'Cancelada'">
                            <div class="form-group">
                                <label for=""><strong style="font-size: 20px;">¿Atendido?</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoCall(call_id, btnEstado)" v-model="btnEstado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 text-center">
                           <template v-if="status == 'Pendiente'">
                                <strong style="font-size: 20px;">Estado :</strong>
                                <span style="font-size: 20px;" class="badge badge-warning">
                                    Pendiente <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                </span>
                            </template>
                            <template v-else-if="status == 'Atendido'">
                                <span style="font-size: 20px;" class="badge badge-success">
                                    Atendido <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                </span>
                            </template>
                            <template v-else-if="status == 'Cancelado'">
                                <span style="font-size: 20px;" class="badge badge-danger">
                                    Cancelada <i class="fa fa-times-circle-o" aria-hidden="true"></i>
                                </span>
                            </template>
                        </div>&nbsp;
                        <div class="col-12 col-sm-6 col-lg-4 text-center mt-3">
                            <h2><i class="fa fa-user" aria-hidden="true"></i> {{ nombre_cliente}}</h2>
                            <p class="font-weight-bold" style="font-size: 20px;" v-text="tipo_cliente"></p>
                            <p class="font-weight-bold" style="font-size: 20px;" v-text="num_cliente"></p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-2 mt-3">
                            <label for=""><strong>Domicilio: </strong><span style="color:red;" v-show="!domicilio_cliente || !ciudad_cliente">(*Complete esta informacion)</span> </label>
                            <p> {{domicilio_cliente}} - {{ciudad_cliente}} </p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-2 mt-3">
                            <label for=""><strong>Telefono: </strong><span style="color:red;" v-show="!telefono_cliente">(*Complete esta informacion)</span></label>
                            <p v-text="telefono_cliente"> </p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-2 mt-3">
                            <label for=""><strong>Correo electrónico:</strong> </label>
                            <p v-text="email_cliente"> </p>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-8 mt-2 d-flex justify-content-start">
                            <!-- <h4>Datos de contacto: </h4> -->
                            <div class="mr-3">
                                <label for=""><strong>Nombre del contacto: </strong> </label>
                                <p v-text="company_cliente"></p>
                            </div>
                            <div>
                                <label for=""><strong>Telefono del contacto: </strong> </label>
                                <p v-text="tel_company_cliente"> </p>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-8 float-right">
                            <button type="button" @click="abrirModalAC(idcliente)" class="btn btn-primary float-right" v-if="active_cliente == 3">Activar Cliente</button>
                            <div v-else class="d-flex flex-column">
                                <span style="font-size: 15px;" class="badge badge-success float-right" >
                                Cliente Activado <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                </span>
                                <strong> Asignado a  : {{ agente }}</strong>
                            </div>
                        </div>
                        <div class="col-12 col-sm-8 mb-3">
                            <viewer :value="body" width="600px"/>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-8 text-center mt-3">
                           <span class="float-right text-muted" style="font-size: 12px;"><strong>Creado por: </strong> {{ user_name }} el dia {{ convertDate(call_date) }}</span>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center mt-3">
                        <div class="col-12 col-md-8 col-lg-8 col-xl-6 offset-md-2 offset-xl-0">
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
                                        <div class="col-12 col-sm-8" :class="{'showNewComment' : CommentNew}" style="display: none;">
                                            <!-- <form action method="post" enctype="multipart/form-data" class="form-horizontal"> -->
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Comentario</span>
                                                    </div>
                                                    <textarea class="form-control rounded-1" rows="8" maxlength="255" v-model="commentBody"></textarea>
                                                </div>
                                                <button class="btn btn-primary mt-2 float-right" @click="saveComment(call_id)" v-if="commentBody && itsCommentNew">Guardar</button>
                                                <button class="btn btn-primary mt-2 float-right" @click="updateComment(call_id)" v-if="commentBody && itsCommentUpd">Actualizar</button>
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
                                            <li class="col-md-6" style="list-style:none;">
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
                                                                    <button type="button" class="btn btn-sm btntask float-right" @click="deleteComentario(comment.id,call_id)">
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
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-12 col-lg-6 mb-3">
                            <button type="button" @click="cerrarVerLlamada()"  class="btn btn-secondary float-right">Cerrar</button>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Fin ver Llamada y segumiento -->

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
                            <h5>Complemente la información de la persona</h5>
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a href="#tabCliente" class="nav-link active" data-toggle="tab">Activar Persona</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <!-- TAB CLIENTE -->
                                <div class="tab-pane active" id="tabCliente" role="tab panel">
                                    <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Vendedor</span>
                                                </div>
                                                <select class="form-control" v-model="idvendedor">
                                                    <option value="0" disabled>Seleccione a quien asignara el cliente</option>
                                                    <option v-for="vendedor in arrayVendedores" :key="vendedor.id" :value="vendedor.id" v-text="vendedor.nombre"></option>
                                                </select>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Tipo</span>
                                                </div>
                                                <select class="form-control" v-model="tipo_cliente">
                                                    <option value="" disabled>Seleccione el tipo de cliente</option>
                                                    <option value="Proveedor">PROVEEDOR</option>
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
                                                <input type="text" v-model="nombre_cliente" class="form-control" placeholder="Nombre de la persona física"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Teléfono</span>
                                                </div>
                                                <input type="text" v-model="telefono_cliente" maxlength="13" class="form-control" placeholder="Teléfono de la persona física"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Correo electrónico</span>
                                                </div>
                                                <input type="text" v-model="email_cliente" class="form-control" placeholder="Correo electrónico"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12  col-xl-8  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Direccion</span>
                                                </div>
                                                <input type="text" v-model="domicilio_cliente" class="form-control" placeholder="Domicilio"/>
                                                <input type="text" v-model="ciudad_cliente" class="form-control" placeholder="Ciudad y estado"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-xl-8  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Contacto</span>
                                                </div>
                                                <input type="text" v-model="company_cliente" class="form-control" placeholder="Nombre de contacto"/>
                                                <input type="text" maxlength="13" v-model="tel_company_cliente" class="form-control" placeholder="Teléfono de contacto"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-group input-group-sm col-12 col-lg-6 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">RFC</span>
                                                </div>
                                                <input type="text" v-model="rfc_cliente" maxlength="13" class="form-control" placeholder="RFC persona física"/>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-6  mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Uso del CFDI</span>
                                                </div>
                                                <select class="form-control" v-model="cfdi_cliente">
                                                    <option value="" disabled>Seleccione</option>
                                                    <option value="G01-Adquisicion de mercacias">G01-Adquisicion de mercacias</option>
                                                    <option value="G02-Devoluciones,descuentos o bonificaciones">G02-Devoluciones,descuentos o bonificaciones</option>
                                                    <option value="G03-Gastos en general">G03-Gastos en general</option>
                                                    <option value="G04-Pago con tarjeta de credito">G04-Pago con tarjeta de credito</option>
                                                    <option value="G28-Pago con tarjeta de debito">G28-Pago con tarjeta de debito</option>
                                                    <option value="P01-Por definir">P01-Por definir</option>
                                                </select>
                                            </div>
                                            <div class="input-group input-group-sm col-12 col-lg-8 mb-3">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">Observaciones</span>
                                                </div>
                                                <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacion_cliente"></textarea>
                                            </div>
                                        </div>
                                        <div v-show="errorCliente" class="form-group row div-error">
                                            <div class="text-center text-error">
                                            <div v-for="error in errorMostrarMsjCliente" :key="error" v-text="error"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModalAC()">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="activarCliente()">Activar</button>

                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModalAC()">Cerrar</button>
                    <button type="button" class="btn btn-primary" @click="activarCliente()">Activar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal agregar/actualizar-->
  </main>
</template>

<script>
import moment from 'moment';
import datePicker from 'vue-bootstrap-datetimepicker';
import vSelect from 'vue-select';
import 'tui-editor/dist/tui-editor.css';
import 'tui-editor/dist/tui-editor-contents.css';
import 'codemirror/lib/codemirror.css';
import 'highlight.js/styles/github.css';
import Editor from '@toast-ui/vue-editor/src/Editor.vue';
import { Viewer } from '@toast-ui/vue-editor'

Vue.use(datePicker);
export default {
    data() {
        return {
            call_id : 0,
            usercall : 0,
            idcliente : 0,
            body : "",
            status: 0,
            call_area: 'GDL',
            call_date : "",
            user_name : "",
            btnEstado : false,
            tipo_cliente : "",
            nombre_cliente : "",
            telefono_cliente : "",
            email_cliente : "",
            domicilio_cliente : "",
            ciudad_cliente : "",
            company_cliente : "",
            num_cliente: "",
            tel_company_cliente : "",
            rfc_cliente : "",
            cfdi_cliente: "",
            observacion_cliente : "",
            active_cliente : 3,
            idvendedor : 0,
            errorCliente : 0,
            agente : "",
            errorMostrarMsjCliente : [],
            nombre_proveedor : "",
            telefono_proveedor : "",
            email_proveedor : "",
            domicilio_proveedor : "",
            ciudad_proveedor : "",
            company_proveedor : "",
            tel_company_proveedor : "",
            errorProveedor : "",
            errorMostrarMsjProveedor : [],
            arrayLlamada: [],
            errorLlamada: 0,
            errorMostrarMsjLlamada: [],
            arrayComentarios : [],
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
            buscar : '',
            estado : 'Pendiente',
            zona : "",
            listado : 0,
            tituloDetalle : "",
            OpenDet : false,
            CallEdit : false,
            CallNew : false,
            options: {
                format: 'YYYY-MM-DD HH:mm:ss',
                useCurrent: false,
                showClear: true,
                showClose: true,
                daysOfWeekDisabled: [0],
                minDate: moment().subtract(120, 'minutes'),
                maxDate: moment().add(90, 'days'),
            },
            tabTitle : "Cliente",
            nom_usuario : "",
            commentBody : "",
            CommentNew : 0,
            itsCommentUpd : 0,
            itsCommentNew : 0,
            comment_id : 0,
            user_id : 0,
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
            modal : 0,
            tituloModal : "",
            arrayVendedores : [],
            llamadas:[],
            buscador: '',
            timeout:0,
            clave:'',
            setTimeoutBuscador:''
        };
    },
    components: {
        vSelect,
        'editor': Editor,
        'viewer': Viewer
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
        listarLlamadas (page,buscar,criterio,estado,zona){
            let me=this;
            var url= '/call?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estado + '&zona='+ zona;
            axios.get(url, {
                params: {
                    buscador: this.buscador
                }
            }).then(function (response) {
                var respuesta= response.data;
                me.arrayLlamada = respuesta.llamadas.data;
                me.pagination= respuesta.pagination;
                me.user_id = respuesta.userid;
                //me.zona = respuesta.userarea;

            })
            .catch(function (error) {
                console.log(error);
            });
        },
        buscarPalabra(){

            clearTimeout( this.setTimeoutBuscador)
            this.setTimeoutBuscador = setTimeout(this.listarLlamadas(), 360);
        },
        listarLlamadaClave(){
             axios.get('/call/listarClave',{
                    params:{
                        buscador: this.buscador,
                    }
                }).then( res =>{
                    this.llamadas= res.data;
                }).catch( error =>{
                    console.log(error.response)
                });
        },
        cambiarPagina(page,buscar,criterio,estado,zona){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarLlamadas(page,buscar,criterio,estado,zona);
        },
        registrarLlamadaCliente() {
            if (this.validarCliente()) {
                return;
            }
            let me = this;
            var newName = this.nombre_cliente;
            let date = "";

            moment.locale('es');
            date = moment().format('YYMMDD');
            var rand = Math.round(Math.random() * (99 - 999));
            var numcomp = "C-".concat(date,rand);

            axios.post("/call/registrarCliente", {
                nombre: this.nombre_cliente,
                num_documento: numcomp,
                ciudad : this.ciudad_cliente,
                domicilio : this.domicilio_cliente,
                telefono : this.telefono_cliente,
                company : this.company_cliente,
                tel_company : this.tel_company_cliente,
                email: this.email_cliente,
                tipo : this.tipo_cliente,
                body : this.body,
                area : this.call_area
            })
            .then(function(response) {
                me.cerrarDetalle();
                swal.fire(
                'Registrado!',
                'La llamada de  ' + '<b>' + newName + '</b>' + ' ha sido registrado con éxito.',
                'success')
                me.listarLlamadas(1,'','nombre','Pendiente',this.zona);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        actualizarLlamadaCliente() {
            if (this.validarCliente()) {
                return;
            }
            let me = this;

            axios.put("/call/actualizar", {
                idcliente : this.idcliente,
                nombre: this.nombre_cliente,
                num_documento: this.num_cliente,
                ciudad : this.ciudad_cliente,
                domicilio : this.domicilio_cliente,
                telefono : this.telefono_cliente,
                clave    : this.clave,
                company : this.company_cliente,
                tel_company : this.tel_company_cliente,
                email: this.email_cliente,
                tipo : this.tipo_cliente,
                active : this.active_cliente,
                idvendedor : this.idvendedor,
                idcall : this.call_id,
                body : this.body,
                area : this.call_area,
                usercall : this.usercall
            })
            .then(function(response) {
                me.cerrarDetalle();
                swal.fire(
                'Compleado!',
                'La llamada ha sido actualizada con éxito.',
                'success')
                me.listarLlamadas(1,'','nombre','Pendiente',me.zona);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarLlamada(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de cancelar este registro?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/call/desactivar', {
                        'id' : id
                    }).then(function(response) {
                        me.listarLlamadas(1,'','nombre','Pendiente',me.zona);
                        swalWithBootstrapButtons.fire(
                            "Desactivado!",
                            "La actividad ha sido cancelada con éxito.",
                            "success"
                        )
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        validarCliente() {
            this.errorCliente = 0;
            this.errorMostrarMsjCliente = [];
            if (!this.nombre_cliente) this.errorMostrarMsjCliente.push("Ingresa el nombre del cliente.");
            if (!this.telefono_cliente) this.errorMostrarMsjCliente.push("Ingresa el teléfono del cliente");
            if (!this.tipo_cliente) this.errorMostrarMsjCliente.push("Selecciona el tipo de cliente");
            if (!this.body) this.errorMostrarMsjCliente.push("Ingresa las observaciones de la llamada");
            if (this.errorMostrarMsjCliente.length) this.errorCliente = 1;
            return this.errorCliente;
        },
        registrarLlamadaProveedor() {
            if (this.validarProveedor()) {
                return;
            }
            let me = this;
            var newName = this.nombre_proveedor;
            let date = "";

            axios.post("/call/registrarProveedor", {
                nombre: this.nombre_proveedor,
                ciudad : this.ciudad_proveedor,
                domicilio : this.domicilio_proveedor,
                telefono : this.telefono_proveedor,
                company : this.company_proveedor,
                tel_company : this.tel_company_proveedor,
                email: this.email_proveedor,
                body : this.body,
                area : this.call_area
            })
            .then(function(response) {
                me.cerrarDetalle();
                swal.fire(
                'Registrado!',
                'La llamada de  ' + '<b>' + newName + '</b>' + ' ha sido registrado con éxito.',
                'success')
                me.listarLlamadas(1,'','nombre','Pendiente',me.zona);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        validarProveedor() {
            this.errorProveedor = 0;
            this.errorMostrarMsjProveedor = [];
            if (!this.nombre_proveedor) this.errorMostrarMsjProveedor.push("Ingresa el nombre del cliente.");
            if (!this.telefono_proveedor) this.errorMostrarMsjProveedor.push("Ingresa el teléfono del cliente");
            if (!this.body) this.errorMostrarMsjProveedor.push("Ingresa las observaciones de la llamada");
            if (this.errorMostrarMsjProveedor.length) this.errorProveedor = 1;
            return this.errorProveedor;
        },
        newCall(){
            this.listado = 1;
            this.title = "";
            this.content = "";
            this.idreceptor = 0;
            this.end = "";
            this.OpenDet = true;
            this.CallNew = true;
            this.CallEdit = false;
            this.tituloDetalle = 'Registrar llamada';
            //this.selectReceptor();
        },
        cerrarDetalle(){
            this.listado = 0;
            this.nombre_cliente = "";
            this.tipo_cliente = "";
            this.idcliente = "";
            this.telefono_cliente = "";
            this.email_cliente = "";
            this.domicilio_cliente = "";
            this.ciudad_cliente = "";
            this.company_cliente = "";
            this.tel_company_cliente = "";
            this.num_cliente =  "";
            this.call_id = "";
            this.body = "";
            this.status = "";
            this.call_area = "GDL";
            this.OpenDet = false;
            this.TaskNew = false;
            this.TaskEdit = false;
            this.tabTitle = "Cliente";
            this.listarLlamadas(1,'','nombre',this.estado,this.zona);

        },
        editTask(data = []){
            this.listado = 1;
            this.idcliente = data['idcliente'];
            this.nombre_cliente = data['cliente'];
            this.tipo_cliente = data['tipo'];
            this.telefono_cliente = data['telefono'];
            this.email_cliente = data['email'];
            this.domicilio_cliente = data['domicilio'];
            this.ciudad_cliente = data['ciudad'];
            this.company_cliente = data['company'];
            this.tel_company_cliente = data['tel_company'];
            this.num_cliente =  data['num_documento'];
            this.call_id = data['id'];
            this.body = data['body'];
            this.status = data['status'];
            this.call_area = data['area'];
            this.active_cliente = data['active'];
            this.idvendedor =  data['idvendedor'];
            this.usercall = data['usercall'];
            this.OpenDet = true;
            this.CallNew = false;
            this.CallEdit = true;
            this.tituloDetalle = 'Editar Llamada';
            this.tabTitle = "Editar";

        },
        verLlamada(data = []){
            this.listado = 2;
            this.idcliente = data['idcliente'];
            this.nombre_cliente = data['cliente'];
            this.tipo_cliente = data['tipo'];
            this.telefono_cliente = data['telefono'];
            this.clave = data['clave'];
            this.email_cliente = data['email'];
            this.domicilio_cliente = data['domicilio'];
            this.ciudad_cliente = data['ciudad'];
            this.company_cliente = data['company'];
            this.tel_company_cliente = data['tel_company'];
            this.num_cliente =  data['num_documento'];
            this.call_id = data['id'];
            this.body = data['body'];
            this.status = data['status'];
            this.call_area = data['area'];
            this.active_cliente = data['active'];
            this.idvendedor =  data['idvendedor'];
            this.usercall = data['usercall'];
            this.call_date = data['fecha'];
            this.user_name = data['usuario'];
            this.agente = data['agente'];
            this.OpenDet = true;

            let estadoCall =  data['status'];

            if(estadoCall == 'Pendiente'){
                this.btnEstado =  false;
            }else if(estadoCall == 'Cancelada'){
                this.btnEstado =  false;
            }else{
                this.btnEstado =  true;
            }

            this.getComments(data['id']);
        },
        cerrarVerLlamada(){
            this.listado = 0;
            this.nombre_cliente = "";
            this.tipo_cliente = "";
            this.idcliente = "";
            this.telefono_cliente = "";
            this.email_cliente = "";
            this.domicilio_cliente = "";
            this.ciudad_cliente = "";
            this.company_cliente = "";
            this.tel_company_cliente = "";
            this.num_cliente =  "";
            this.call_id = "";
            this.clave = "";
            this.body = "";
            this.status = "";
            this.agente = "";
            this.call_area = "GDL";
            this.tabTitle = "Cliente";
            this.arrayComentarios = [];
            this.comment_id = 0;
            this.itsCommentUpd = 0;
            this.itsCommentNew = 0;
            this.OpenDet = false;
            this.listarLlamadas(1,'','nombre',this.estado,this.zona);
        },
        abrirModalAC(){
            this.modal = 1;
            this.tituloModal = "Activar Cliente";
            this.selectVendedor();
        },
        cerrarModalAC(){
            this.modal = 0;
        },
        activarCliente(id){

            if (this.validarCliente()) {
                return;
            }

            let me = this;
            var clienteUp =  this.nombre_cliente;

            var agent="";
            var areaVend="";

            for(var i=0;i<this.arrayVendedores.length;i++){
                if(this.arrayVendedores[i].id==this.idvendedor){
                   //console.log(`Usuario ${this.arrayVendedores[i].nombre} -  Area : ${this.arrayVendedores[i].area}`);
                   agent = this.arrayVendedores[i].nombre;
                   areaVend = this.arrayVendedores[i].area;
                }
            }

            me.agente = agent;

            axios.put("/cliente/actualizar", {
                'nombre': this.nombre_cliente,
                'num_documento': this.num_cliente,
                'ciudad': this.ciudad_cliente,
                'domicilio': this.domicilio_cliente,
                'telefono': this.telefono_cliente,
                'clave'   : this.clave,
                'company' : this.company_cliente,
                'tel_company' : this.tel_company_cliente,
                'email': this.email_cliente,
                'rfc': this.rfc_cliente,
                'id': this.idcliente,
                'tipo': this.tipo_cliente,
                'cfdi' : this.cfdi_cliente,
                'idusuario' : this.idvendedor,
                'area' : areaVend,
                'observacion' : this.observacion_cliente
            })
            .then(function(response) {
                me.cerrarModalAC();
                me.active_cliente = 1;
                swal.fire(
                'Activado!',
                'El cliente ' + '<b>' + clienteUp + '</b>' + ' ha sido activado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });

        },
        convertDate(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            return datec;
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
        newComment(data = []){
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

            var callid = id;

            axios.post('/call/crearComment',{
                'id' : id,
                'body' : this.commentBody
            }).then(function(response) {
                me.cancelComment();
                me.getComments(callid);
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
            var url= '/call/getComments?id=' + id;
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
            var callid = id;

            axios.put("/call/editComment", {
                id : this.comment_id,
                body : this.commentBody
            })
            .then(function(response) {
                me.cancelComment();
                me.getComments(callid);
                swal.fire(
                'Completado!',
                'El comentario ha sido actualizado con éxito.',
                'success')
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        deleteComentario(id,actividad){
            let me = this;
            var callid = actividad;
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
                    axios.put('/call/deleteComment', {
                        'id' : id
                    }).then(function(response) {
                         me.getComments(callid);
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
        cambiarEstadoCall(id,estado){
            let me = this;

            if(estado == true){
                me.estado = 'Atendido';
            }else{
                me.estado = 'Pendiente';
            }

            var estadoAct = me.estado;

            console.log(`Estado: ${ estadoAct }`);

            axios.put('/call/cambiarEstado',{
                'id': id,
                'status' : this.estado
            }).then(function (response) {
                if(estadoAct == 'Pendiente'){
                    swal.fire(
                    'Atención!',
                    'La actividad ha sido registrado como no atendida.',
                    'warning')
                    me.btnEstado = false;
                }else{
                    swal.fire(
                    'Completado!',
                    'La llamada ha sido registrado como atendida.',
                    'success')
                    me.btnEstado = true;
                }
                me.status = estadoAct;
            }).catch(function (error) {
                console.log(error);
            });
        }

    },
    mounted() {
        this.listarLlamadas(1,this.buscar, this.criterio, this.estado, this.zona);

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
    .content-category{
        height: 350px !important;
    }
    .showNewComment {
      display:block !important;
    }
    ul#involvedUsers li{
        display:inline !important;
    }
    .opc-btns {
        min-width: 130px !important;
    }
</style>
