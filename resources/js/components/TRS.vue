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
          <i class="fa fa-align-justify"></i> Reclamos
          <button type="button" @click="mostrarDetalle()" class="btn btn-secondary" v-if="listado==1">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
           <template v-if="listado==2">
                <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary float-right">Volver</button>
          </template>
          <div class="form-inline ml-5 float-right" v-if="usrol== 1">
                <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5" >
                    <button @click="abrirModal10()" class="btn btn-outline-success btn-sm">Abonos<i class="fa fa-file-excel-o"></i></button>
                </div>
          </div>

        </div>
        <!-- Listado -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-11">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="num_comprobante">No° Comprobante</option>
                                <option value="fecha_hora">Fecha</option>
                                <option value="forma_pago">Forma de pago</option>
                            </select>
                            <input type="text" v-model="buscar" @keyup.enter="listarReclamo(1,buscar,criterio,estadoReclamo)" class="form-control mb-1" placeholder="Texto a buscar...">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="estadoReclamo" @change="listarReclamo(1,buscar,criterio,estadoReclamo)">
                                <option value="">Pacivo</option>
                                <option value="Activo">Activo</option>
                            </select>
                            <button type="submit" @click="listarReclamo(1,buscar,criterio,estadoReclamo)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5" v-if="estadoReclamo!='Anulada' && usrol != 4">
                             <button @click="abrirModal5()" class="btn btn-success btn-sm">Reporte <i class="fa fa-file-excel-o"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Registro</th>
                                <th>Tipo Comprobante</th>
                                <th>No° Comprobante</th>
                                <th>Fecha Hora</th>
                                <th>Estado</th>
                                <th>Validacion</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayReclamo.length">
                            <tr v-for="reclamo in arrayReclamo" :key="reclamo.id">
                                <td>
                                    <div class="form-inline">
                                        <button type="button" class="btn btn-success btn-sm" @click="verReclamo(reclamo.id)">
                                        <i class="icon-eye"></i>
                                        </button>&nbsp;
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfReclamo(reclamo.id)">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </button>&nbsp;
                                        <template v-if="usrol == 1">
                                                <button type="button" v-if="reclamo.estado == 'Pacivo'" class="btn btn-danger btn-sm" @click="desactivarVenta(reclamo.id)">
                                                    <i class="icon-trash"></i>
                                                </button>
                                        </template>
                                    </div>
                                </td>
                                <td v-text="reclamo.usuario"></td>
                                <td v-text="reclamo.tipo_comprobante"></td>
                                <td v-text="reclamo.num_comprobante"></td>
                                <td>{{ convertDateVenta(reclamo.fecha_hora) }}</td>
                                <td v-if="reclamo.estado =='Activo'">
                                    <span class="badge badge-success">Activa</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-warning">Pasivo</span>
                                </td>
                                <td v-if="reclamo.atendido == 1">
                                    <span class="badge badge-warning">Pendiente</span>
                                </td>
                                <td v-else-if="reclamo.atendido == 2">
                                    <span class="badge badge-success">Atendido</span>
                                </td>
                                <td v-else-if="reclamo.atendido == 3">
                                    <span class="badge badge-danger">No Procedio</span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <strong>NO hay presupuestos con ese criterio o estado...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoReclamo)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoReclamo)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoReclamo)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->
        <!-- Nueva Venta -->
        <template v-else-if="listado==0">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante (*)</strong></label>
                            <select v-model="tipo_comprobante" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="RECLAMO">RECLAMO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Número de presupuesto (*)</strong></label>
                            <div class="d-flex justify-content-center">
                                <div>
                                    <input type="number" readonly :value="getFechaCode" class="form-control col-md"/>
                                </div>
                                <div>
                                    <input type="text" class="form-control col-md" v-model="num_comprobante" placeholder="000xx">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center" v-if="arrayDetalle.length">
                        <div class="form-group">
                            <label for=""><strong>Total M<sup>2</sup> </strong></label>
                            <p> {{ metrosTotales.toFixed(4) }} </p>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <div v-show="errorReclamo" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjReclamo" :key="error" v-text="error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for=""><strong>Articulo</strong> <span style="color:red;" v-show="articulo==''">(*Seleccione)</span> </label>
                            <div class="form-inline">
                                <input type="text" class="form-control" v-model="codigo" @keyup.enter="buscarArticulo()"  placeholder="Ingrese el no° de placa" >
                                <button @click="abrirModal()" class="btn btn-primary">...</button>
                                <input type="text" readonly class="form-control" v-model="articulo">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Cantidad</strong> <span style="color:red;" v-show="cantidad==0">(*Ingrese)</span></label>
                            <input type="number" min="0" value="0"  class="form-control" v-model="cantidad">
                        </div>
                    </div>
                    <div class="col-sm-2 text-center">
                        <div class="form-group">
                            <button @click="agregarDetalle()" class="btn btn-success form-control btnagregar"><i class="icon-plus"></i></button>
                        </div>
                    </div>
                </div>

                <div class="form-group row border">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th width="10px">No°</th>
                                    <th>Opciones</th>
                                    <th>Material</th>
                                    <th>Código de material</th>
                                    <th>No° Placa</th>
                                    <th>Terminado</th>
                                    <th>Espesor</th>
                                    <th>largo</th>
                                    <th>Alto</th>
                                    <th>Metros <sup>2</sup></th>
                                    <th>Cantidad</th>
                                    <th>Ubicacion</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td width="10px" v-text="index + 1"></td>
                                    <td>
                                        <div class="form-inline">
                                            <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                                <i class="icon-close"></i>
                                            </button>&nbsp;
                                            <button type="button" @click="abrirModal2(index)" class="btn btn-success btn-sm">
                                                <i class="icon-eye"></i>
                                            </button> &nbsp;
                                            <button type="button" class="btn btn-warning btn-sm" @click="abrirModal4(index)">
                                                <i class="icon-crop"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td v-text="detalle.categoria"></td>
                                    <td v-text="detalle.articulo"></td>
                                    <td v-text="detalle.codigo"></td>
                                    <td v-text="detalle.terminado"></td>
                                    <td v-text="detalle.espesor"></td>
                                    <td v-text="detalle.largo"></td>
                                    <td v-text="detalle.alto"></td>
                                    <td v-text="detalle.metros_cuadrados"></td>
                                    <td>
                                        <span style="color:red;" v-show="detalle.cantidad>detalle.stock">Solo hay: {{detalle.stock}} disponibles</span>
                                        <input v-model="detalle.cantidad" min="1" type="number" class="form-control">
                                    </td>
                                    <td v-text="detalle.ubicacion"></td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="11" align="right"><strong>Total Metros<sup>2</sup> : </strong></td>
                                    <td> {{ metrosTotales.toFixed(4)}} </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="15" class="text-center">
                                        <strong>NO hay artículos agregados...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4 text-center">
                        <label for="exampleFormControlTextarea2"><strong>Observaciones</strong></label>
                        <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacion"></textarea>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="registrarReclamo()">Registrar Reclamo</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin Nueva Venta -->
        <!-- Ver Venta -->
        <template v-else-if="listado==2">
            <div class="card-body">
                <div class="form-group row border">
                        <div class="col-md-2">
                            <div class="form-group">
                                <h1><i class="fa fa-tasks" aria-hidden="true"></i>{{tipo_comprobante}}</h1>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for=""><strong>Numero de Comprobante </strong></label>
                                <p v-text="num_comprobante"> </p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for=""><strong>Fecha y Hora</strong></label>
                                <p v-text="fecha_hora"> </p>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for=""><strong>Registrado por</strong></label>
                                <p v-text="user"> </p>
                            </div>
                        </div>
                        <div class="col-md-2" >
                            <div class="form-group">
                                <label for=""><strong>Estado</strong></label>
                                <template v-if="estadoVn == 'Activo'">
                                    <p class="float-rigth" style="font-size: 20px;">
                                        <span class="badge badge-success">Activo</span>
                                    </p>
                                </template>
                                <template v-else>
                                    <p class="float-rigth" style="font-size: 20px;">
                                        <span class="badge badge-warning">Pasivo</span>
                                    </p>
                                </template>
                            </div>
                        </div>
                </div>
                <div class="form-group row border">
                         <div><h4>Articulos Añadidos: </h4>&nbsp;</div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th width="10px">No°</th>
                                        <th>Detalles</th>
                                        <th>Código de material</th>
                                        <th>No° Placa</th>
                                        <th>Terminado</th>
                                        <th>Espesor</th>
                                        <th>largo</th>
                                        <th>Alto</th>
                                        <th>Metros <sup>2</sup></th>
                                        <th>Cantidad</th>
                                        <th>Ubicacion</th>

                                    </tr>
                                </thead>
                                <tbody v-if="arrayDetalle.length">
                                    <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                        <td width="10px" v-text="index + 1"></td>
                                        <td>
                                            <button type="button" @click="abrirModal3(index)" class="btn btn-success btn-sm">
                                                <i class="icon-eye"></i>
                                            </button> &nbsp;
                                        </td>
                                        <td v-text="detalle.sku"></td>
                                        <td v-text="detalle.codigo"></td>
                                        <td v-text="detalle.terminado"></td>
                                        <td v-text="detalle.espesor"></td>
                                        <td v-text="detalle.largo"></td>
                                        <td v-text="detalle.alto"></td>
                                        <td v-text="detalle.metros_cuadrados"></td>
                                        <td v-text="detalle.cantidad"></td>
                                        <td v-text="detalle.ubicacion"></td>

                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="12" class="text-center">
                                            <strong>NO hay artículos en este detalle...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Notas de credito ventas -->
                        <div class="col-12 col-md-8 col-lg-8 col-xl-8 offset-md-2 offset-xl-0 mt-3 mt-sm-3 mt-lg-3 mt-xl-0">
                            <div class="d-flex flex-column">
                                <div class="border-bottom d-flex justify-content-between">
                                    <div><h4>Notas de crédito: </h4>&nbsp;</div>
                                    <div v-if="estadoVn == 'Pasivo'">
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
                                                            @click="eliminarCredito(credito.id,reclamo_id)" v-if="credito.estado != 'Abonada'">
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
                <div class="form-group row border">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col">
                                <label for="exampleFormControlTextarea2"><strong>Observaciones</strong></label>
                            </div>
                            <div class="col-2">
                                <template v-if="obsEditable == 0">
                                    <button type="button" class="btn btn-warning btn-sm float-right" @click="editObservacion()">
                                        <i class="icon-pencil"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacion(reclamo_id)">
                                        <i class="fa fa-floppy-o"></i>
                                    </button>
                                </template>&nbsp;
                            </div>
                        </div>&nbsp;
                        <template v-if="obsEditable == 0">
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" readonly v-model="observacion"></textarea>
                        </template>
                        <template v-else>
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacion"></textarea>
                        </template>
                    </div>&nbsp;
                    <!-- Files Upploader -->
                    <div class="col-md-6">
                        <div class="page-header">
                            <h3 id="timeline">Archivos adjuntos de {{ num_comprobante }} &nbsp;</h3>
                        </div>
                        <div class="divdocs form-inline" v-if="docsArray.length">
                            <div v-for="file in docsArray" :key="file.id" class="d-flex justify-content-around">
                                <div>
                                    <template v-if="file.tipo != 'pdf'">
                                        <div class="d-flex justify-content-center">
                                            <div>
                                                <lightbox class="m-0" album="" :src="'reclamosfiles/'+file.url">
                                                    <figure class="figure">
                                                        <img :src="'reclamosfiles/'+file.url" width="150" height="100" class="figure-img img-fluid rounded" alt="File CAPTION">
                                                        <figcaption class="figure-caption text-right" v-text="file.url"></figcaption>
                                                    </figure>
                                                </lightbox>&nbsp;
                                            </div>
                                            <div>
                                                <button @click="eliminarFile(file.id,reclamo_id)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
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
                                                <button @click="eliminarFile(file.id,reclamo_id)" class="btn btn-transparent text-danger rounded-circle"><i class="fa fa-times fa-2x"></i></button>
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
                    </div>&nbsp;
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin ver Venta-->
      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>

    <!--Inicio del modal listar articulos-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <!-- Filtros Modal Articulos -->
                    <div class="form-inline">
                        <div class="form-group col-12">
                            <div class="input-group input-group-sm col-12 col-lg-8 col-xl-8 mb-3 p-1">
                                <div class="input-group-append">
                                    <span class="input-group-text">Criterios</span>
                                </div>
                                <select class="form-control" v-model="criterioA">
                                    <option value="sku">Código de material</option>
                                    <option value="codigo">No° de placa</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="buscarA" @keyup.enter="listarArticulo(1,buscarA,criterioA,bodega,acabado,categoriaFilt)" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-4 col-xl-4 mb-3 p-1">
                                <div class="input-group-append">
                                    <span class="input-group-text">Material</span>
                                </div>
                                <select class="form-control" v-model="categoriaFilt" @change="listarArticulo(1,buscarA,criterioA,bodega,acabado,categoriaFilt)">
                                    <option value="0">Seleccione un material</option>
                                    <option v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-4 mb-3 p-1">
                                <div class="input-group-append">
                                    <span class="input-group-text">Terminado</span>
                                </div>
                                <input type="text" v-model="acabado" @keyup.enter="listarArticulo(1,buscarA,criterioA,bodega,acabado,categoriaFilt)" class="form-control" placeholder="Terminado">
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-4 mb-3 p-1">
                                <div class="input-group-append">
                                    <span class="input-group-text">Ubicación</span>
                                </div>
                                <select class="form-control" v-model="bodega" @change="listarArticulo(1,buscarA,criterioA,bodega,acabado,categoriaFilt)">
                                    <template v-if="zona=='GDL'">
                                        <option value="">Todas</option>
                                        <option value="Del Musico">Del Músico</option>
                                        <option value="Escultores">Escultores</option>
                                        <option value="Sastres">Sastres</option>
                                        <option value="Mecanicos">Mecánicos</option>
                                        <option value="Tractorista">Tractorista</option>
                                    </template>
                                    <template v-else-if="zona=='SLP'">
                                        <option value="San Luis">San Luis</option>
                                    </template>
                                    <template v-else-if="zona=='AGS'">
                                        <option value="Aguascalientes">Aguascalientes</option>
                                    </template>
                                </select>
                            </div>
                            <div class="input-group input-group-sm col-12 col-lg-6 col-xl-3 mb-3 p-1">
                                <div class="input-group-append">
                                    <span class="input-group-text" style="background-color:red;color:white;"><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp; Area</span>
                                </div>
                                <select class="form-control" v-model="zona" @change="listarArticulo(1,buscarA,criterioA,bodega,acabado,categoriaFilt)">
                                    <option value="GDL">Guadalajara</option>
                                    <option value="SLP">San Luis</option>
                                    <option value="AGS">Aguascalientes</option>
                                </select>
                            </div>
                                <button type="submit" @click="listarArticulo(1,buscarA,criterioA,bodega,acabado,categoriaFilt)" class="btn btn-sm btn-primary col-1 mb-3 p-1"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-center table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>Opciones</th>
                                <th>No° Placa</th>
                                <th>Código de material</th>
                                <th>Material</th>
                                <th>Largo</th>
                                <th>Alto</th>
                                <th>Metros<sup>2</sup></th>
                                <th>Stock</th>
                                <th>Ubicacion</th>
                                <th>Terminado</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayArticulo.length">
                            <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                <td>
                                    <button type="button" @click="agregarDetalleModal(articulo)" class="btn btn-success btn-sm">
                                    <i class="icon-check"></i>
                                    </button>
                                </td>
                                <td v-text="articulo.codigo"></td>
                                <td v-text="articulo.sku"></td>
                                <td v-text="articulo.nombre_categoria"></td>
                                <td v-text="articulo.largo"></td>
                                <td v-text="articulo.alto"></td>
                                <td v-text="articulo.metros_cuadrados"></td>
                                <td v-text="articulo.stock"></td>
                                <td v-text="articulo.ubicacion"></td>
                                <td v-text="articulo.terminado"></td>
                                <td>
                                <div v-if="articulo.condicion == 1">
                                    <span class="badge badge-success">Activo</span>
                                </div>
                                <div v-else-if="articulo.condicion == 3">
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
                                        <strong>NO hay artículos con ese criterio...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginacion MODAL -->
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="paginationart.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page - 1,buscarA,criterioA,bodega,acabado,categoriaFilt)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumberArt" :key="page" :class="[page == isActivedArt ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(page,buscarA,criterioA,bodega,acabado,categoriaFilt)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="paginationart.current_page < paginationart.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page + 1,buscarA,criterioA,bodega,acabado,categoriaFilt)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    <!--Inicio del modal Visualizar articulo Insertado-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal2}" data-spy="scroll"  role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-info modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal + sku"></h4>
                    <button type="button" class="close" @click="cerrarModal2()" aria-label="Close">
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
                    <table class="table table-bordered table-striped table-sm text-center table-hover table-responsive-sm">
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
                                <td v-text="categoria"></td>
                            </tr>
                            <tr >
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
                                <td v-text="calcularMts"></td>
                            </tr>
                            <tr >
                                <td><strong>ESPESOR</strong></td>
                                <td v-text="espesor"></td>
                            </tr>
                            <tr >
                                <td><strong>PRECIO</strong></td>
                                <td v-text="precio"></td>
                            </tr>
                            <tr >
                                <td><strong>BODEGA DE DESCARGA</strong></td>
                                <td v-text="ubicacion"></td>
                            </tr>
                            <tr >
                                <td><strong>Stock</strong></td>
                                <td v-text="stock"></td>
                            </tr>
                            <tr >
                                <td><strong>DESCRIPCION</strong></td>
                                <td v-text="descripcion"></td>
                            </tr>
                            <tr >
                                <td><strong>OBSERVACIONES</strong></td>
                                <td v-text="observacion"></td>
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
    <!--Fin del modal-->

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
                            <button type="button" class="btn btn-primary mr-2" @click="saveCredit(reclamo_id,totalnc)">Guardar</button>
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

    <!-- Modal exportar excel -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal5}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-success modal-md " role="document">
            <div class="modal-content content-exportUs">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal5()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body ">
                    <h3 class="mb-3">Generar reporte de ventas</h3>
                    <div class="row d-flex justify-content-center"  v-if="usrol == 1">
                        <div class="input-group input-group-sm col-12 mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text">Usuarios</span>
                            </div>
                            <v-select multiple v-model="selectedUsers" :on-search="selectReceptor" label="nombre" :options="arrayReceptores" placeholder="Buscar usuarios...">
                            </v-select>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Inicio: </strong></label>
                            <!-- <date-picker name="date" v-model="fecha1" class="form-control" :config="options"></date-picker> -->
                            <input type="date" class="form-control" v-model="fecha1">
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Fin: </strong></label>
                           <!--  <date-picker name="date" v-model="fecha2" :config="options"></date-picker> -->
                           <input type="date" class="form-control" v-model="fecha2">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        <div>
                            <button type="button" class="btn btn-primary mr-5" @click="listarExcel(fecha1,fecha2,selectedUsers)">Resumido</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary ml-5" @click="listarExcelDet(fecha1,fecha2,selectedUsers)">Detallado</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal5()">Cerrar</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin exportar excel -->

    <!-- Modal crear abono -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal6}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-deposit">
                <!-- <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal6(reclamo_id)" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div> -->
                <div class="modal-body ">
                    <h3 class="mb-3">Adeudo actual: {{ adeudo }}</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-2">
                            <!-- <label for=""><strong>Forma de pago: </strong></label>
                            <input type="date" class="form-control" v-model="fecha1"> -->
                            <div class="form-group">
                                <h5 for=""><strong>Forma de pago </strong><span style="color:red;" v-show="forma_pagoab==''">(*Seleccione)</span></h5>
                                <select class="form-control" v-model="forma_pagoab" v-if="otroFormPayab == false">
                                    <option value='' disabled>Seleccione la forma de pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Mixto">Mixto</option>
                                </select>
                                <div class="form-check float-left mt-1">
                                    <input class="form-check-input" type="checkbox" id="chkOtherPayab" v-model="otroFormPayab">
                                    <label class="form-check-label p-0 m-0" for="chkOtherPayab"><strong>Otro</strong></label>
                                </div>
                                <input class="form-control rounded-0"  maxlength="35" placeholder="Ingresa la forma de pago" v-model="forma_pagoab" v-if="otroFormPayab == true">
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <h5 for=""><strong> $ Abono: </strong></h5>
                            <input type="number" step="any" min="1" class="form-control" v-model="totalab">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-primary mr-2" @click="saveDeposit(reclamo_id,adeudo,totalab)">Guardar</button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModal6(reclamo_id)">Cancelar</button>
                        </div>
                    </div>
                </div>
               <!--  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal6(reclamo_id)">Cancelar</button>
                </div> -->
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin Modal crear abono -->

    <!-- Modal crear abono con nota credito -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal8}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content content-creditPay">
                <div class="modal-body">
                    <h3 class="mb-3">Adeudo actual: {{ adeudo }}</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-2">
                            <h4>Notas de crédito : </h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                                    <thead>
                                        <tr>
                                            <th>Opciones</th>
                                            <th>No° de Nota</th>
                                            <th>Monto</th>
                                            <th>Monto Gastado</th>
                                            <th>Forma de pago</th>
                                            <th>Fecha</th>
                                            <th>Observaciones</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayCreditos.length">
                                        <tr v-for="credito in arrayCreditos" :key="credito.id">
                                            <td v-if="credito.estado == 'Vigente'">
                                                <button type="button" class="btn btn-success btn-sm" @click="addDetalleCredito(credito)">
                                                    <i class="icon-plus"></i>
                                                </button>&nbsp;
                                            </td>
                                            <td v-text="credito.num_documento"></td>
                                            <td v-text="credito.total"></td>
                                            <td v-text="credito.nuevo_total"></td>
                                            <td v-text="credito.forma_pago"></td>
                                            <td>{{ convertDateVenta(credito.fecha_hora) }}</td>
                                            <td v-text="credito.observacion"></td>
                                            <td v-text="credito.estado"></td>
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
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" v-if="paginationcred.current_page > 1">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaCre(idcliente,paginationcred.current_page - 1)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="page in pagesNumberCre" :key="page" :class="[page == isActivedCre ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaCre(idcliente,page)" v-text="page"></a>
                                    </li>
                                    <li class="page-item" v-if="paginationcred.current_page < paginationcred.last_page">
                                        <a class="page-link" href="#" @click.prevent="cambiarPaginaCre(idcliente,paginationcred.current_page + 1)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-12">
                            <template v-if="selectedCredits.length"><h5>Notas de Credito seleccionadas: </h5></template>
                            <template v-else><h5 style="color:red;">Selecciona al menos una nota de crédito: </h5></template>
                            <div class="form-inline" v-if="selectedCredits.length">
                                <div v-for="(credit,index) in selectedCredits" :key="credit.id" class="d-flex justify-content-around mr-1">
                                    <table class="table table-bordered table-striped table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Opciones</th>
                                                <th>No° de Nota</th>
                                                <th>Monto</th>
                                                <th>Monto a Gastar</th>
                                                <th>Observacion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                 <td width="10px">
                                                    <button @click="eliminarDetalleCredito(index)" type="button" class="btn btn-danger btn-sm">
                                                        <i class="icon-close"></i>
                                                    </button>&nbsp;
                                                </td>
                                                <td v-text="credit.num_documento"></td>
                                                <td >{{credit.total - credit.nuevo_total}}</td>
                                                <td>
                                                    <span style="color:red" v-show="credit.nuevo_total > credit.total">No puede superar el Monto!</span>
                                                    <input v-model="credit.nuevo_total" min="0" step="any" type="number" class="form-control">
                                                </td>
                                                <td>
                                                    <input v-text="credit.observacion" type="text" class="form-control">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="form-group">
                                        <button type="button"  @click="updateCredits()" class="btn btn-success form-control btnagregar">
                                            <i class="icon-plus"></i>
                                        </button>&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="form-group">
                                <h5 for=""><strong>Forma de pago </strong></h5>
                                <select class="form-control" v-model="forma_pagoab">
                                    <option value="Nota de crédito">Nota de crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <h5 for=""><strong> $ Abono: </strong></h5>
                            <input type="number" step="any" min="1" class="form-control" disabled :value="TotalAbonoCredito">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-primary mr-2"
                                @click="guardarAbonoCredit(reclamo_id,adeudo,totalab)" v-if="selectedCredits.length"> Guardar
                            </button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModal8(reclamo_id)">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Fin Modal crear abono con nota credito -->

    <!-- Modal exportar excel -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal10}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-success modal-md " role="document">
            <div class="modal-content content-exportUs">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal10()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body ">
                    <h3 class="mb-3">Generar reporte de Abonos</h3>
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Inicio: </strong></label>
                            <!-- <date-picker name="date" v-model="fecha1" class="form-control" :config="options"></date-picker> -->
                            <input type="date" class="form-control" v-model="fecha1">
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for=""><strong>Fin: </strong></label>
                           <!--  <date-picker name="date" v-model="fecha2" :config="options"></date-picker> -->
                           <input type="date" class="form-control" v-model="fecha2">
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <div>
                            <button type="button" class="btn btn-primary mr-5" @click="listarAbonosExcel(fecha1,fecha2)">Reporte</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal10()">Cerrar</button>
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
import vSelect from 'vue-select';
import VueBarcode from 'vue-barcode';
import VueLightbox from 'vue-lightbox';
import moment from 'moment';
import ToggleButton from 'vue-js-toggle-button';
import datePicker from 'vue-bootstrap-datetimepicker';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton);
Vue.use(datePicker);
export default {
    data() {
        return {
            reclamo_id: 0,
            user: '',
            atendido:"",
            estado :'Activo',
            tipo_comprobante: "RECLAMO",
            num_comprobante: "",
            moneda : 'Peso Mexicano',
            tipo_cambio : 0,
            observacion : '',
            observacionpriv : '',
            categoria : '',
            idarticulo : 0,
            articulo : "",
            codigo: "",
            idcategoria :0,
            sku : '',
            terminado : '',
            largo : 0,
            alto : 0,
            metros_cuadrados : 0,
            espesor : 0,
            ubicacion : '',
            origen : '',
            contenedor : '',
            fecha_hora : '',
            file : '',
            arrayCreditos : [],
            forma_pagonc : '',
            otroFormPaync : false,
            totalnc : 0,
            descripcionnc : '',
            imagenMinatura : '',
            arrayCategoria : [],
            arrayFiles : [],
            docsArray : [],
            condicion : 0,
            precio_venta : 0,
            cantidad : 0,
            total_parcial : 0.0,
            divImp: 0.0,
            total: 0.0,
            forma_pago : "Efectivo",
            tiempo_entrega : "",
            lugar_entrega : "",
            precio: 0.0,
            entregado : 0,
            entregado_parcial: 0,
            stock : 0,
            descripcion : "",
            tipo_facturacion : "",
            num_cheque : 0,
            banco : "",
            pagado : 0,
            pago_parcial : 0,
            adeudo : 0,
            arrayArticulo : [],
            arrayReclamo : [],
            arrayDetalle : [],
            arrayCliente : [],
            arrayProject : [],
            detalleProjects : [],
            listado : 1,
            modal: 0,
            modal2: 0,
            modal3: 0,
            modal4: 0,
            modal5: 0,
            modal6: 0,
            modal7: 0,
            modal8: 0,
            modal10: 0,
            ind : '',
            tituloModal: "",
            tipoAccion: 0,
            errorReclamo: 0,
            errorMostrarMsjReclamo: [],
            pagination : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            paginationart : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
             paginationPre : {
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
            buscarA : '',
            criterioA : 'sku',
            categoriaFilt : 0,
            criterioPre : 'num_comprobante',
            buscarPre : '',
            zona : "GDL",
            codigoA : "",
            codigoB : "",
            largoA : 0,
            largoB : 0,
            altoA : 0,
            altoB : 0,
            metros_cuadradosA : 0,
            metros_cuadradosB : 0,
            precioA : 0,
            precioB : 0,
            ubicacionA : "",
            ubicacionB : "",
            validatedB : 0,
            validatedA : 0,
            btnEntrega : false,
            btnEntregaParcial : false,
            btnPagado : false,
            btnPagadoParcial : false,
            btnFactura : false,
            estadoVn : "",
            CodeDate : "",
            obsEditable : 0,
            obsprivEditable : 0,
            sigNum : 0,
            rfc_cliente: "",
            tipo_cliente : "",
            telefono_cliente : "",
            contacto_cliente : "",
            telcontacto_cliente : "",
            obs_cliente: "",
            cfdi_cliente: "",
            bodega : "",
            areaUs : "",
            acabado : "",
            estadoReclamo : "",
            estadoPago : "",
            usrol : 0,
            fecha1 : "",
            fecha2 : "",
            options: {
                format: 'YYYY-MM-DD',
                useCurrent: false,
                showClear: true,
                showClose: true,
                daysOfWeekDisabled: [0],
                minDate: moment().subtract(60, 'seconds'),
                maxDate: moment().add(60, 'days'),
            },
            facturado : 0,
            factura_env: 0,
            estadoEntrega : "",
            arrayDepositos : [],
            forma_pagoab : "",
            otroFormPay : false,
            otroFormPayab : false,
            totalab : 0,
            btnAutoEntrega : false,
            auto_entrega : 0,
            arrayCreditos : [],
            active : 0,
            paginationcred : {
                'total'        : 0,
                'current_page' : 0,
                'per_page'     : 0,
                'last_page'    : 0,
                'from'         : 0,
                'to'           : 0,
            },
            selectedCredits : [],
            selectedUsers : [],
            arrayReceptores : [],
            email_cliente : "",
            usid : 0,
            ispecial : false,
            btnSpecial : false,
            nuevo_total : 0,
            special : 0,
            num_factura: '',
            fecha_llegada: '',
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
        isActivedArt: function(){
            return this.paginationart.current_page;
        },
        pagesNumberArt: function() {
            if(!this.paginationart.to) {
                return [];
            }

            var from = this.paginationart.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationart.last_page){
                to = this.paginationart.last_page;
            }

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        isActivedCre: function(){
            return this.paginationcred.current_page;
        },
        pagesNumberCre: function() {
            if(!this.paginationcred.to) {
                return [];
            }

            var from = this.paginationcred.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationcred.last_page){
                to = this.paginationcred.last_page;
            }

            var pagesArray = [];
            while(from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        },
        isActivedPre: function(){
            return this.paginationPre.current_page;
        },
        pagesNumberPre: function() {
            if(!this.paginationPre.to) {
                return [];
            }

            var from = this.paginationPre.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationPre.last_page){
                to = this.paginationPre.last_page;
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
        calcularMts : function(){
            let me=this;
            let resultado = 0;
            resultado = resultado + (me.alto * me.largo);
            me.metros_cuadrados = resultado;
            return resultado;
        },
        calcularMtsA : function(){
            let me = this;
            let resultado = 0;
            resultado = resultado + (me.altoA * me.largoA);
            me.metros_cuadradosA = resultado;
            return resultado;
        },
        calcularMtsB : function(){
            let me=this;
            let resultado = 0;
            resultado = resultado + (me.altoB * me.largoB);
            me.metros_cuadradosB = resultado;
            return resultado;
        },
        calcularMtsRestantes : function(){
            let me=this;
            let resultado = 0;
            resultado = me.metros_cuadrados - (me.metros_cuadradosA + me.metros_cuadradosB);
            return resultado;
        },
        getFechaCode : function(){
            let me = this;
            let date = "";
            moment.locale('es');
            date = moment().format('YYMMDD');
            me.CodeDate = moment().format('YYMMDD');
            return date;
        },
        calcularAbonos : function(){
            let me=this;
            let resultado = 0;
            for(var i=0;i<me.arrayDepositos.length;i++){
                resultado += parseFloat(me.arrayDepositos[i].total);
            }
            return resultado;
        },
        metrosTotales : function(){
            let me=this;
            let resultado = 0;
            for(var i=0;i<me.arrayDetalle.length;i++){
                resultado += parseFloat(me.arrayDetalle[i].metros_cuadrados);
            }
            return resultado;
        },
        TotalAbonoCredito : function(){
            let me=this;
            let resultado = 0;
            for(var i=0;i<me.selectedCredits.length;i++){
                resultado += parseFloat(me.selectedCredits[i].nuevo_total);
            }
            me.totalab = resultado;
            return resultado;
        },
        calcularTotalN : function(){
            let me = this;
            let resultado = 0;
            for(var i=0;i<me.selectedCredits.length;i++){
                resultado += parseFloat(me.selectedCredits[i].total - me.selectedCredits[i].nuevo_total);
            }
            me.total = resultado;
            return resultado;


        }
    },
    methods: {
        listarReclamo (page,buscar,criterio,estadoReclamo){
            let me=this;
            var url= '/reclamo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio +
                '&estado='+ estadoReclamo;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayReclamo = respuesta.reclamos.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        buscarArticulo(){
            let me = this;
            var url= '/articulo/buscarArticuloVenta?filtro='+ me.codigo;

            axios.get(url).then(function (response) {
                let respuesta = response.data;
                me.arrayArticulo=respuesta.articulos;

                if(me.arrayArticulo.length > 0){
                    me.articulo = me.arrayArticulo[0]['sku'];
                    me.idarticulo = me.arrayArticulo[0]['id'];
                    me.precio = me.arrayArticulo[0]['precio_venta'];
                    me.stock = me.arrayArticulo[0]['stock'];
                    me.espesor = me.arrayArticulo[0]['espesor'];
                    me.largo = me.arrayArticulo[0]['largo'];
                    me.alto = me.arrayArticulo[0]['alto'];
                    me.metros_cuadrados = me.arrayArticulo[0]['metros_cuadrados'];
                    me.terminado =  me.arrayArticulo[0]['terminado'];
                    me.ubicacion =  me.arrayArticulo[0]['ubicacion'];
                    me.idcategoria = me.arrayArticulo[0]['idcategoria'];
                    me.categoria = me.arrayArticulo[0]['nombre_categoria'];
                    me.origen = me.arrayArticulo[0]['origen'];
                    me.contenedor = me.arrayArticulo[0]['contenedor'];
                    me.descripcion =  me.arrayArticulo[0]['descripcion'];
                    me.observacion = me.arrayArticulo[0]['observacion'];
                    me.file = me.arrayArticulo[0]['file'];
                    me.fecha_llegada = me.arrayArticulo[0]['fecha_llegada'];
                }else{
                    me.articulo = 'No existe este artículo';
                    me.idarticulo = 0;
                }
            })
            .catch(function (error) {
                console.log(error);
            });


        },
        downloadDoc(file){
            window.open('reclamosfiles/'+file);
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
            var reclamo = this.reclamo_id;
            axios.put('/reclamo/filesupplo',{
                'id' : this.reclamo_id,
                'filesdata': this.arrayFiles
            }).then(function(response) {
                swal.fire(
                'Completado!',
                'Los archivos fueron guardados con éxito.',
                'success');
                me.arrayFiles = [];
                me.getDocs(reclamo);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        eliminarFile(id,idreclamo){
            var reclamo = idreclamo;
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
                    axios.put('/reclamo/eliminarDoc', {
                        'id' : id
                    }).then(function(response) {
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El documento ha sido eliminada con éxito.",
                            "success"
                        );
                        me.getDocs(reclamo);
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        cambiarPagina(page,buscar,criterio,estadoReclamo){
            let me = this;
            me.pagination.current_page = page;
            me.listarReclamo(page,buscar,criterio,estadoReclamo);
        },
        encuentra(id){
            var sw=0;
            for(var i=0;i<this.arrayDetalle.length;i++){
                if(this.arrayDetalle[i].idarticulo==id){
                    sw=true;
                }
            }
            return sw;
        },
        eliminarDetalle(index){
            let me = this;
            me.arrayDetalle.splice(index,1);
        },
        agregarDetalle(){
            let me = this;
            if(me.idarticulo == 0 || me.cantidad == 0){
            }else{
                if(me.encuentra(me.idarticulo)){
                    Swal.fire({
                        type: 'error',
                        title: 'Error...',
                        text: 'Este No° de placa ya esta en el listado!',
                    })
                    me.codigo = "";
                    me.sku = "";
                    me.idarticulo = "";
                    me.articulo="";
                    me.cantidad = 0;
                    me.precio = 0;
                    me.descuento = 0;
                    me.idcategoria = 0;
                    me.largo = 0;
                    me.alto = 0;
                    me.metros_cuadrados = 0;
                    me.terminado = 0;
                    me.espesor = 0;
                    me.stock = 0;
                    me.ubicacion = "";
                    me.categoria = "";
                    me.idcategoria = 0;

                }else{
                    if(me.cantidad > me.stock){
                        Swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'La cantidad excede las placas disponibles de este material!',
                        });
                    }else{
                        me.arrayDetalle.push({
                            idarticulo       : me.idarticulo,
                            articulo         : me.articulo,
                            sku              : me.sku,
                            codigo           : me.codigo,
                            idcategoria      : me.idcategoria,
                            largo            : me.largo,
                            alto             : me.alto,
                            metros_cuadrados : me.metros_cuadrados,
                            terminado        : me.terminado,
                            espesor          : me.espesor,
                            precio           : me.precio,
                            cantidad         : me.cantidad,
                            stock            : me.stock,
                            ubicacion        : me.ubicacion,
                            descuento        : me.descuento,
                            categoria        : me.categoria,
                            origen           : me.origen,
                            contenedor       : me.contenedor,
                            file             : me.file,
                            descripcion      : me.descripcion,
                            observacion      : me.observacion,
                            fecha_llegada    : me.fecha_llegada
                        });
                        me.codigo = "";
                        me.sku = "";
                        me.idarticulo = "";
                        me.articulo="";
                        me.cantidad = 0;
                        me.precio = 0;
                        me.descuento = 0;
                        me.idcategoria = 0;
                        me.largo = 0;
                        me.alto = 0;
                        me.metros_cuadrados = 0;
                        me.terminado = 0;
                        me.espesor = 0;
                        me.stock = 0;
                        me.ubicacion = "";
                        me.categoria = "";
                        me.observacion = "";
                        me.origen = "";
                        me.contenedor  = "";
                        me.file  = "";
                        me.descripcion   = "";
                        me.fecha_llegada = "";
                    }
                }
            }
        },
        registrarReclamo(){
             if (this.validarReclamo()) {
                return;
            }
            let me = this;
            var numcomp = "RC-".concat(me.CodeDate,"-",me.num_comprobante);

            //console.log(`Total : ${totalDem}`);
            axios.post('/reclamo/registrar',{
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'observacion' : this.observacion,
                'data': this.arrayDetalle,
            }).then(function(response) {
                me.ocultarDetalle();
                me.listarReclamo(1,'','num_comprobante','');
                Swal.fire({
                    type: 'success',
                    title: 'Registrado...',
                    text: 'El reclamo ha sido registrada con éxito!!',
                });
                //console.log(`Resp : ${ response }`);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarReclamo(id) {

            if ( adeudo > 0 ){
                    const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                    confirmButton: "btn btn-success ml-3",
                    cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                    title: "¿Esta seguro de anular esta venta?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Aceptar!",
                    cancelButtonText: "Cancelar!",
                    reverseButtons: true
                })
                .then(result => {
                    if (result.value) {
                        let me = this;
                        let genNc = false;
                        axios.put('/reclamo/desactivar',{
                            'id': id,
                            'genNc' : genNc
                        }).then(function (response) {
                            me.listarReclamo(1,'','num_comprobante','','','');
                            Swal.fire({
                                type: 'success',
                                title: 'Anulado...',
                                text: 'La venta ha sido anulado con éxito!!',
                                footer: response.data
                            });
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }else if (result.dismiss === swal.DismissReason.cancel){
                    }
                });
            } else {
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success ml-3",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });
                swalWithBootstrapButtons.fire({
                    title: "¿Esta seguro de anular esta venta?",
                    type: "warning",
                    html:
                    '<div class="form-check" style="font-size: 20px;">' +
                        '<input class="form-check-input mt-2" type="checkbox" value="" id="chkGnc"><label for="chkGnc">Generar nota de crédito</label>'+
                    '</div>',
                    showCancelButton: true,
                    confirmButtonText: "Aceptar!",
                    cancelButtonText: "Cancelar!",
                    reverseButtons: true
                })
                .then(result => {
                    if (result.value) {
                        let genNc = document.getElementById('chkGnc').checked;
                        /* console.log(genNc); */
                        let me = this;
                        axios.put('/reclamo/desactivar',{
                            'id': id,
                            'genNc' : genNc
                        }).then(function (response) {
                            me.listarReclamo(1,'','num_comprobante','','','');
                            Swal.fire({
                                type: 'success',
                                title: 'Anulado...',
                                text: 'La venta ha sido anulado con éxito!!',
                                footer: response.data
                            });
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }else if (result.dismiss === swal.DismissReason.cancel){
                    }
                });
            }
        },
        validarReclamo() {
            let me = this;
            var art;

            me.errorReclamo = 0;
            me.errorMostrarMsjReclamo = [];

            me.arrayDetalle.map(function(x){
                if(x.cantidad > x.stock){
                    art ="La cantidad del articulo " + x.codigo + " supera las cantidades disponibles.";
                    me.errorMostrarMsjReclamo.push(art);
                }
            });

            if (me.tipo_comprobante==0) me.errorMostrarMsjReclamo.push("Seleccione un comprobante.");
            if (!me.num_comprobante) me.errorMostrarMsjReclamo.push("Ingrese el numero de comprobante");
            if (me.arrayDetalle.length<=0) me.errorMostrarMsjReclamo.push("Introdusca articulos para la venta");

            if (me.errorMostrarMsjReclamo.length) me.errorReclamo = 1;

            return me.errorReclamo;
        },
        mostrarDetalle(){
            this.getLastNum();
            this.listado = 0;
            this.codigo = "";
            this.idarticulo = 0;
            this.articulo = "";
            this.sku = "";
            this.idcategoria = 0;
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
            this.num_comprobante = (parseInt(this.sigNum)+1);
            this.selectCategoria();
        },
        ocultarDetalle(){
            this.listado = 1;
            this.codigo = "";
            this.idarticulo = 0;
            this.articulo = "";
            this.sku = "";
            this.idcategoria = 0;
            this.largo = 0;
            this.alto = 0;
            this.metros_cuadrados = 0;
            this.terminado = '';
            this.espesor = 0;
            this.precio_venta = 0;
            this.precio = 0;
            this.cantidad = 0;
            this.file = '';
            this.origen = '';
            this.contenedor = '';
            this.fecha_llegada = '';
            this.ubicacion = '';
            this.moneda = 'Peso Mexicano';
            this.tipo_cambio = '0';
            this.stock = 0;
            this.cliente = 0;
            this.categoria = 0;
            this.observacion = "";
            this.observacionpriv = "";
            this.arrayDetalle = [];
            this.errorReclamo =0;
            this.errorMostrarMsjReclamo = [];
            this.num_comprobante = 0;
            this.entregado = 0;
            this.entregado_parcial = 0;
            this.btnEntrega =  false;
            this.btnEntregaParcial = false;
            this.btnSpecial = false;
            this.ispecial = false;
            this.btnPagado = false;
            this.btnPagadoParcial = false;
            this.obsEditable = 0;
            this.obsprivEditable = 0;
            this.idcliente = 0;
            this.rfc_cliente = "";
            this.cfdi_cliente = "";
            this.tipo_cliente = "";
            this.telefono_cliente = "";
            this.contacto_cliente = "";
            this.telcontacto_cliente = "";
            this.obs_cliente = "";
            this.factura_env = 0;
            this.facturado = 0;
            this.arrayDepositos = [];
            this.getLastNum();
            this.tipo_comprobante = "RECLAMO",
            this.total = 0.0;
            this.descuento = 0;
            this.forma_pago = "Efectivo";
            this.tiempo_entrega = "";
            this.lugar_entrega = "";
            this.banco = "";
            this.num_cheque = 0;
            this.tipo_facturacion = "";
            this.otroFormPay = false;
            this.email_cliente = "";
            this.listarReclamo(this.pagination.current_page,
                this.buscar, this.criterio,this.estadoReclamo,this.estadoEntrega,this.estadoPago);
        },
        verReclamo(id,reclamo){
            let me = this;
            me.listado = 2;

            //Obtener los datos del ingreso

            var arrayReclamoT=[];
            var url= '/reclamo/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayReclamoT = respuesta.reclamo;

                var fechaform  = arrayReclamoT[0]['fecha_hora'];
                var reclamo = arrayReclamoT[0]['id'];

                var total_parcial = 0;

                me.reclamo_id = arrayReclamoT[0]['id'];
                me.tipo_comprobante=arrayReclamoT[0]['tipo_comprobante'];
                me.num_comprobante=arrayReclamoT[0]['num_comprobante'];
                me.user=arrayReclamoT[0]['usuario'];
                me.observacion = arrayReclamoT[0]['observacion'];
                me.estadoVn = arrayReclamoT[0]['estado'];
                moment.locale('es');
                me.fecha_hora=moment(fechaform).format('dddd DD MMM YYYY hh:mm:ss a');
                me.getDocs(reclamo);


            })
            .catch(function (error) {
                console.log(error);
            });

            //Obtener los detalles del ingreso
            var url= '/reclamo/obtenerDetalles?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayDetalle = respuesta.detalles;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cerrarModal() {
            this.modal = 0;
            this.buscarA = "";
            this.bodega = "";
            this.acabado = "";
        },
        abrirModal() {
            this.arrayArticulo=[];
            this.modal = 1;
            this.tituloModal = "Seleccionar Artículos";
            this.listarArticulo(1,'','sku','','',0);
        },
        listarArticulo(page,buscar,criterio,bodega,acabado,idcategoria){
            let me=this;

            if(me.zona == 'SLP'){
                bodega = 'San Luis';
                me.bodega = 'San Luis';

            }else{
                if(bodega == 'San Luis'){
                    bodega = "";
                    me.bodega = "";
                }
            }

            if(me.zona == 'AGS'){
                bodega = 'Aguascalientes';
                me.bodega = 'Aguascalientes';

            }else{
                if(bodega == 'Aguascalientes'){
                    bodega = "";
                    me.bodega = "";
                }
            }


            var url= '/articulo/listarArticuloVenta?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio
            + '&bodega=' + bodega + '&acabado=' + acabado + '&idcategoria=' + idcategoria;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayArticulo = respuesta.articulos.data;
                me.paginationart= respuesta.pagination;
                me.areaUs = respuesta.userarea;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPaginaArt(page,buscar,criterio,bodega,acabado,idcategoria){
            let me = this;
            //Actualiza la página actual
            me.paginationart.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticulo(page,buscar,criterio,bodega,acabado,idcategoria);
        },
        agregarDetalleModal(data =[]){
            let me=this;
            if(me.encuentra(data['id'])){
                Swal.fire({
                    type: 'error',
                    title: 'Lo siento...',
                    text: 'Este No° de placa ya esta en el listado!!',
                })
            }
            else{
                me.arrayDetalle.push({
                    idarticulo       : data['id'],
                    articulo         : data['sku'],
                    codigo           : data['codigo'],
                    idcategoria      : data['idcategoria'],
                    categoria        : data['nombre_categoria'],
                    largo            : data['largo'],
                    alto             : data['alto'],
                    metros_cuadrados : data['metros_cuadrados'],
                    terminado        : data['terminado'],
                    espesor          : data['espesor'],
                    precio           : data['precio_venta'],
                    stock            : data['stock'],
                    ubicacion        : data['ubicacion'],
                    categoria        : data['nombre_categoria'],
                    origen           : data['origen'],
                    contenedor       : data['contenedor'],
                    file             : data['file'],
                    descripcion      : data['descripcion'],
                    observacion      : data['observacion'],
                    fecha_llegada    : data['fecha_llegada'],
                    cantidad: 1,
                    descuento : 0

                });
                Swal.fire({
                    position: 'top-end',
                    type: 'success',
                    title: `${data['codigo']}`,
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        },
        abrirModal2(index){
            let me = this;
            me.ind = index;
            me.modal2 = 1;
            me.tituloModal      = "Detalles Artículo ";
            me.sku              = me.arrayDetalle[index]['articulo'];
            me.codigo           = me.arrayDetalle[index]['codigo'];
            me.categoria        = me.arrayDetalle[index]['categoria'];
            me.largo            = me.arrayDetalle[index]['largo'];
            me.alto             = me.arrayDetalle[index]['alto'];
            me.ubicacion        = me.arrayDetalle[index]['ubicacion'];
            me.terminado        = me.arrayDetalle[index]['terminado'];
            me.espesor          = me.arrayDetalle[index]['espesor'];
            me.precio_venta     = me.arrayDetalle[index]['precio_venta'];
            me.metros_cuadrados = me.arrayDetalle[index]['metros_cuadrados'];
            me.contenedor       = me.arrayDetalle[index]['contenedor'];
            me.fecha_llegada    = me.arrayDetalle[index]['fecha_llegada'];
            me.origen           = me.arrayDetalle[index]['origen'];
            me.stock            = me.arrayDetalle[index]['stock'];
            me.file             = me.arrayDetalle[index]['file'];
            me.origen           = me.arrayDetalle[index]['origen'];
            me.contenedor       = me.arrayDetalle[index]['contenedor'];
            me.descripcion      = me.arrayDetalle[index]['descripcion'];
            me.observacion      = me.arrayDetalle[index]['observacion'];
            me.selectCategoria();
        },
        cerrarModal2() {
            this.modal2 = 0;
            this.sku = '';
            this.codigo = '';
            this.categoria = 0;
            this.largo = 0;
            this.alto = 0;
            this.terminado = '';
            this.espesor = 0;
            this.ubicacion = '';
            this.precio_venta = 0;
            this.metros_cuadrados = 0;
            this.stock = 0;
            this.file = '';
            this.origen = '';
            this.observacion = '';
            this.contenedor = '';
            this.descripcion = '';
            this.ind = '';
            this.fecha_llegada = '';
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
        cerrarModal4() {
            this.modal4 = 0;
            this.idarticulo = 0;
            this.sku = '';
            this.codigo = '';
            this.categoria = 0;
            this.largo = 0;
            this.alto = 0;
            this.terminado = '';
            this.espesor = 0;
            this.ubicacion = '';
            this.precio_venta = 0;
            this.metros_cuadrados = 0;
            this.stock = 0;
            this.file = '';
            this.origen = '';
            this.observacion = '';
            this.contenedor = '';
            this.descripcion = '';
            this.ind = '';
            this.fecha_llegada = '';
        },
        abrirModal4(index) {
            let me = this;
            me.ind = index;
            me.arrayArticulo=[];
            me.modal4 = 1;
            me.tituloModal      = "Dividir Placa ";
            me.idarticulo       = me.arrayDetalle[index]['idarticulo'];
            me.sku              = me.arrayDetalle[index]['articulo'];
            me.codigo           = me.arrayDetalle[index]['codigo'];
            me.idcategoria      = me.arrayDetalle[index]['idcategoria'];
            me.categoria        = me.arrayDetalle[index]['categoria'];
            me.largo            = me.arrayDetalle[index]['largo'];
            me.alto             = me.arrayDetalle[index]['alto'];
            me.ubicacion        = me.arrayDetalle[index]['ubicacion'];
            me.terminado        = me.arrayDetalle[index]['terminado'];
            me.espesor          = me.arrayDetalle[index]['espesor'];
            me.precio           = me.arrayDetalle[index]['precio'];
            me.metros_cuadrados = me.arrayDetalle[index]['metros_cuadrados'];
            me.contenedor       = me.arrayDetalle[index]['contenedor'];
            me.fecha_llegada    = me.arrayDetalle[index]['fecha_llegada'];
            me.origen           = me.arrayDetalle[index]['origen'];
            me.stock            = me.arrayDetalle[index]['stock'];
            me.file             = me.arrayDetalle[index]['file'];
            me.origen           = me.arrayDetalle[index]['origen'];
            me.contenedor       = me.arrayDetalle[index]['contenedor'];
            me.descripcion      = me.arrayDetalle[index]['descripcion'];
            me.observacion      = me.arrayDetalle[index]['observacion'];
            me.codigoA          = me.codigo + '-A';
            me.codigoB          = me.codigo + '-B';
            me.largoA           = me.largo;
            me.largoB           = me.largo;
            me.altoA            = me.alto / 2;
            me.altoB            = me.alto / 2;
            me.precioA          = me.precio / 2;
            me.precioB          = me.precio / 2;
            me.selectCategoria();
        },
        editObservacion(){
            let me = this;
            me.obsEditable = 1;
        },
        editObservacionPriv(){
            let me = this;
            me.obsprivEditable = 1;
        },
        actualizarObservacion(id){
            let me = this;
            axios.post('/reclamo/actualizarObservacion',{
                'id': id,
                'observacion' : this.observacion
            }).then(function (response) {
                me.obsEditable = 0;
            }).catch(function (error) {
                console.log(error);
            });
        },
        pdfReclamo(id){
            window.open('/reclamo/pdf/'+id);
        },
        getLastNum(){
            let me=this;
            var url= '/reclamo/nextNum';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.sigNum = respuesta.SigNum;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        convertDateVenta(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            /* console.log(datec); */
            return datec;
        },
        listarExcel(inicio, fin,selectedUsers){
            if (this.usrol != 1){
                var ArrUsuarios = [];
                ArrUsuarios.push(this.usid);
                window.open('/reclamo/ExportExcel?inicio=' + inicio + '&fin=' + fin + '&usuarios=' + ArrUsuarios);
            }else{
                if(!selectedUsers.length){
                swal.fire(
                'Atencion!',
                `Seleccione los usuarios para el reporte`,
                'error');
            }else{
                var ArrUsuarios = [];
                for(let i = 0; i < selectedUsers.length; i++){
                    ArrUsuarios.push(selectedUsers[i]['id']);
                }
                window.open('/reclamo/ExportExcel?inicio=' + inicio + '&fin=' + fin + '&usuarios=' + ArrUsuarios);
            }
            }
        },
        cerrarModal6(id){
            this.modal6 = 0;
            this.forma_pagoab = '';
            this.verVenta(id);
            this.otroFormPayab =  false;
        },
        cerrarModal7(){
            this.modal7 = 0;
            if(this.pago_parcial == 0)
                this.btnPagadoParcial =  false;
            else
                this.btnPagadoParcial = true;

        },
        pagoOtro(id,adeudo){
            this.modal7 = 0;
            this.modal6 = 1;
            this.tituloModal = 'Crear Abono';
            this.forma_pagoab = '';
            this.totalab = 0;
        },
        pagoCredit(id,adeudo,idcliente){
            this.modal7 = 0;
            this.modal8 = 1;
            this.tituloModal = 'Crear Abono';
            this.forma_pagoab = 'Nota de crédito';
            this.totalab = 0;
            this.getCredits(idcliente,1);
        },
        getCredits(id,page){
            let me = this;
            var url= '/reclamo/getCreditsPay?id=' + id + '&page=' + page;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayCreditos = respuesta.creditos.data;
                me.paginationcred = respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        updateCredits(){
            let me = this;

            axios.put('/reclamo/actualizarCredits', {

                'num_documento': me.num_documento,
                'total'        : me.total,
                'forma_pago'   : me.forma_pago,
                'nuevo_total'  : me.nuevo_total,
                'observacion'  : me.observacion,

            }).then(function (response){
                 swal.fire(
                    'Completado!',
                    'La Nota de Credito a sido actualizada .',
                    'success');

            }).catch(function (error){
                console.log(error);
            })
        },
        cambiarPaginaCre(id,page){
            let me = this;
            me.pagination.current_page = page;
            me.getCredits(id,page);
        },
        cerrarModal8(id){
            this.modal8 = 0;
            this.forma_pagoab = '';
            this.verVenta(id);
            this.otroFormPayab =  false;
            this.arrayCreditos = [];
            this.selectedCredits = [];
            this.totalab = 0;
        },
        addDetalleCredito(data =[]){
            let me=this;
            if(me.encuentraCredit(data['id'])){
                Swal.fire({
                    type: 'error',
                    title: 'Lo siento...',
                    text: 'Esta Nota de crédito ya esta seleccionada!!',
                })
            }
            else{
                me.selectedCredits.push({
                    idcredit         : data['id'],
                    num_documento    : data['num_documento'],
                    total            : data['total'],
                    nuevo_total      : data['nuevo_total'],
                });
            }
        },
        encuentraCredit(id){
            var sw=0;
            for(var i=0;i<this.selectedCredits.length;i++){
                if(this.selectedCredits[i].idcredit==id){
                    sw=true;
                }
            }
            return sw;
        },
        eliminarDetalleCredito(index){
            let me = this;
            me.selectedCredits.splice(index,1);
        },
        guardarAbonoCredit(id,adeudo,total,nuevo_total){
            let abono2 = parseFloat(nuevo_total);
            let abono = parseFloat(total);
            if(abono2 > abono > adeudo){
                swal.fire(
                'Error!',
                'El abono no puede ser mayor que el adeudo.',
                'error');
                this.totalab = 0;
            }else{
                let me = this;

                var ArrCredits = [];
                for(let i = 0; i < this.selectedCredits.length; i++){
                    ArrCredits.push(this.selectedCredits[i]['idcredit']);
                }
                //console.log(ArrCredits);

                axios.post('/reclamo/crearDepositCredit',{
                    'id'         : id,
                    'total'      : abono,
                    'forma_pago' : this.forma_pagoab,
                    'nuevo_total': this.nuevo_total,
                    'creditos'   : ArrCredits
                }).then(function(response) {
                    me.cerrarModal8();
                    me.verVenta(id);
                    swal.fire(
                    'Completado!',
                    'El abono ha sido registrado con éxito.',
                    'success');
                })
                .catch(function(error) {
                    console.log(error);
                });
            }
        },
        downloadDoc(file){
            window.open('reclamosfiles/'+file);
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
            var reclamo = this.reclamo_id;
            axios.put('/reclamo/filesupplo',{
                'id' : this.reclamo_id,
                'filesdata': this.arrayFiles
            }).then(function(response) {
                swal.fire(
                'Completado!',
                'Los archivos fueron guardados con éxito.',
                'success');
                me.arrayFiles = [];
                me.getDocs(reclamo);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        getDocs(idreclamo){
            let me = this;
            var url= '/reclamo/getDocs?id=' + idreclamo;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.docsArray = respuesta.documentos;
                /* console.log(respuesta.documentos); */
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        eliminarFile(id,idreclamo){
            var reclamo = idreclamo;
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
                    axios.put('/reclamo/eliminarDoc', {
                        'id' : id
                    }).then(function(response) {
                        swalWithBootstrapButtons.fire(
                            "Eliminado!",
                            "El documento ha sido eliminada con éxito.",
                            "success"
                        );
                        me.getDocs(reclamo);
                    }).catch(function(error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
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
                    axios.post('/reclamo/crearCredit',{
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
            var url= '/reclamo/getCredits?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayCreditos = respuesta.creditos;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        eliminarCredito(id,idreclamo){

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
                    axios.put('/reclamo/deleteCredit',{
                        'id': id
                    }).then(function (response) {
                        me.getCredits(idreclamo);
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
    },
    mounted() {
        this.listarReclamo(1,this.buscar, this.criterio,this.estadoReclamo);
        this.getLastNum();
    }
};
</script>
<style>
    .modal-content {
        width: 100% !important;
        position: absolute !important;
        border: none !important;
        height: 860px !important;
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
    .selectDetalle {
        background: white;
        border: none;
        text-align: center;
    }
    input[type="number"]{
        -webkit-appearance: textfield !important;
        margin: 0;
    }
    .sinpadding [class*="col-"] {
        padding: 0;
    }
    .content-exportUs{
        height: 500px !important;
    }
    .content-deposit {
        height: 340px !important;
    }
    .content-askdep{
        height: 240px !important;
    }
    .content-creditPay{
        height: 570px !important;
        width: 100% !important;
    }

    @media only screen and (max-width: 440px) {}


/* Extra small devices (phones, 600px and down) */

@media only screen and (min-width: 440px) {
    .modal-lg {
        max-width: 95% !important;
    }
}


/* Extra small devices (phones, 600px and down) */

@media only screen and (max-width: 576px) {
    .modal-lg {
        max-width: 95% !important;
    }

    .btnagregar{
            margin-top: 2rem;
        }
}


/* Medium devices (landscape tablets, 768px and up) */

@media only screen and (min-width: 768px) {
    .modal-lg {
        max-width: 90% !important;
    }
}


/* Large devices (laptops/desktops, 992px and up) */

@media only screen and (min-width: 992px) {
    .modal-lg {
        max-width: 90% !important;
    }
}


/* Extra large devices (large laptops and desktops, 1200px and up) */

@media only screen and (min-width: 1200px) {
    .modal-lg {
        max-width: 80% !important;
    }
}
</style>
