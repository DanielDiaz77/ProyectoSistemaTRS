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
          <i class="fa fa-align-justify"></i> Ingreso Abrasivos
          <button type="button" @click="mostrarDetalle()" class="btn btn-secondary">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
          <button type="button" @click="añadirStock()" class="btn btn-secondary">
            <i class="icon-plus"></i>&nbsp;Añadir
          </button>
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
                            <input type="text" v-model="buscar" @keyup.enter="listarIngreso(1,buscar,criterio,estadoIn)" class="form-control mb-1" placeholder="Texto a buscar">
                            <button type="submit" @click="listarIngreso(1,buscar,criterio,estadoIn)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                            <input type="text" v-model="buscar" @keyup.enter="listarEntrada(1,buscar,criterio,estadoIn)" class="form-control mb-1" placeholder="Texto a buscar">
                            <button type="submit" @click="listarEntrada(1,buscar,criterio,estadoIn)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div class="input-group ml-2">
                            <button type="submit" @click="listarIngreso(1,buscar,criterio,estadoIn)" class="btn btn-outline-info mb-1"><i class="fa fa-question"></i> Estado</button>
                            <button type="submit" @click="listarEntrada(1,buscar,criterio,estadoIn)" class="btn btn-outline-info mb-1"><i class="fa fa-question"></i> Estado</button>
                        </div>
                        <select class="form-control mb-1" v-model="estadoIn" @change="listarEntrada(1,buscar,criterio,estadoIn)">
                            <option value="">Todo</option>
                            <option value="Registrado">Registrados</option>
                            <option value="Anulado">Anulados</option>
                        </select>
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
                                <th>Activo</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayIngreso.length">
                            <tr v-for="ingreso in arrayIngreso" :key="ingreso.id">
                                <td>
                                    <div class="form-inline">
                                        <button type="button" class="btn btn-success btn-sm" @click="verIngreso(ingreso.id)">
                                            <i class="icon-eye"></i>
                                        </button>&nbsp;
                                        <template v-if="ingreso.estado=='Registrado'">
                                            <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfIngreso(ingreso.id)">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </button>&nbsp;
                                            <button type="button" class="btn btn-danger btn-sm" @click="desactivarIngreso(ingreso.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                                <td v-text="ingreso.usuario"></td>
                                <td v-text="ingreso.nombre"></td>
                                <td v-text="ingreso.tipo_comprobante"></td>
                                <td v-text="ingreso.num_comprobante"></td>
                                <td v-text="ingreso.fecha_hora"></td>
                                <td v-text="ingreso.estado "></td>
                                <td>
                                    <div v-if="ingreso.active == 1">
                                        <span class="badge badge-success">Ingresado</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">No Ingresado</span>
                                    </div>
                                </td>
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
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoIn)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoIn)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio, estadoIn)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->
        <!-- Detalle -->
        <template v-else-if="listado==0">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><strong>Proveedor (*)</strong></label>
                                <v-select :on-search="selectProveedor" label="nombre" :options="arrayProveedor"
                                    placeholder="Buscar Proveedores..." :onChange="getDatosProveedor"></v-select>
                        </div>
                    </div>&nbsp;
                     <div class="col-md-3 text-center" v-if="rfc_proveedor">
                        <div class="form-group">
                            <label for=""><strong>RFC</strong></label>
                            <p v-text="rfc_proveedor"></p>
                        </div>
                    </div>
                     <div class="col-md-3 text-center" v-if="contacto_prov">
                        <div class="form-group">
                            <label for=""><strong>Contacto</strong></label>
                            <p style="font-size: 15px;">{{ contacto_prov  }} - {{ tel_contacto }}</p>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante (*)</strong> </label>
                            <select v-model="tipo_comprobante" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="INGRESO">Ingreso</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Número de ingreso (*)</strong></label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" readonly :value="getFechaCode" class="form-control col-md"/>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control col-md" v-model="num_comprobante" placeholder="000xx">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""><strong>Bodega de descarga</strong><span style="color:red;" v-show="ubicacion==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="ubicacion">
                                <option value='' disabled>Seleccione una bodega de descarga</option>
                                <option value="Del Musico">Del Músico</option>
                                <option value="Escultores">Escultores</option>
                                <option value="Sastres">Sastres</option>
                                <option value="Mecanicos">Mecánicos</option>
                                <option value="Tractorista">Tractorista</option>
                                <option value="San Luis">San Luis</option>
                                <option value="Aguascalientes">Aguascalientes</option>
                                <option value="Puebla">Puebla</option>
                                <option value="Oficina">Oficina Lazaro</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""><strong>Código</strong> <span style="color:red;" v-show="codigo==0">(*Ingrese el Código)</span></label>
                            <input type="text" class="form-control" v-model="codigo" placeholder="Código">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for=""><strong>SKU</strong><span style="color:red;" v-show="sku==''">(*Ingrese)</span></label>
                            <input type="text" class="form-control" v-model="sku" placeholder="SKU del material">
                        </div>
                    </div>
                     <div class="col-sm-2 text-center">
                        <div class="form-group row">
                            <label for="inputFile" class="text-center"><strong>Descripción</strong></label>&nbsp;
                            <input id="inputFile" type="text" v-model="descripcion_r" class="form-control" placeholder="Descripción del material">
                        </div>
                    </div>
                    <div class="col-sm-2 mt-3">
                        <div class="form-group">
                            <label class="float-right" for=""><strong>Consultar Artículos</strong></label>
                            <div class="form-inline float-right">
                                <button @click="abrirModal()" class="btn btn-primary">...</button>&nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <label for=""><strong>Cantidad</strong> <span style="color:red;" v-show="cantidad==0">(*Ingrese)</span></label>
                            <input type="number" min="0" value="0" step="any" class="form-control" v-model="cantidad">
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <label for=""><strong>Precio </strong><span style="color:red;" v-show="precio_venta==0">(*Ingrese)</span></label>
                            <input type="number" min="0" value="0" class="form-control" v-model="precio_venta">
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <button @click="agregarDetalle()" class="btn btn-success form-control btnagregar"><i class="icon-plus"></i></button>
                        </div>
                    </div>

                    <div class="col-sm-1 text-center mt-4 ">
                        <p class="mt-2 mb-1" style="font-size: 15px;"> Abrasivos añadidos  <strong> {{ arrayDetalle.length }} </strong>  </p>
                    </div>
                    <div class="col-sm-2 text-center mt-4">
                        <div class="form-group">
                            <label for=""><strong>Activar Ingreso:</strong> </label>
                            <div>
                                <toggle-button v-model="active" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div v-show="errorIngreso" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjIngreso" :key="error" v-text="error"></div>
                            </div>
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
                                    <th>SKU</th>
                                    <th>Código</th>
                                    <th>Cantidad</th>
                                    <th>Ubicación</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td v-text="(index + 1)"></td>
                                    <td>
                                        <div class="form-inline">
                                            <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                            <i class="icon-close"></i>
                                            </button>&nbsp;
                                            <button type="button" @click="abrirModal2(index)" class="btn btn-warning btn-sm">
                                                <i class="icon-pencil"></i>
                                            </button> &nbsp;
                                        </div>
                                    </td>
                                    <td>
                                        <input v-model="detalle.sku" type="text" class="form-control">
                                    </td>
                                    <td>
                                        <input v-model="detalle.codigo" type="text" class="form-control">
                                    </td>

                                    <td v-text="detalle.cantidad"></td>
                                   <td>
                                       <select class="form-control" v-model="detalle.ubicacion">
                                            <option value='' disabled>Seleccione una bodega de descarga</option>
                                            <option value="Del Musico">Del Músico</option>
                                            <option value="Escultores">Escultores</option>
                                            <option value="Sastres">Sastres</option>
                                            <option value="Mecanicos">Mecánicos</option>
                                            <option value="Tractorista">Tractorista</option>
                                            <option value="San Luis">San Luis</option>
                                            <option value="Aguascalientes">Aguascalientes</option>
                                            <option value="Puebla">Puebla</option>
                                            <option value="Oficina">Oficina Lazaro</option>
                                        </select>
                                   </td>
                                    <td>
                                        <input v-model="detalle.descripcion" type="text" class="form-control" placeholder="Descripcion gral">
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <strong>NO hay artículos agregados...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="registrarArticulos()">Registrar Ingreso</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin detalle -->
        <!-- Ver ingreso -->
        <template v-else-if="listado==2">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><strong>Proveedor</strong> </label>
                            <p v-text="proveedor"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante</strong></label>
                            <p v-text="tipo_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Número de Comprobante</strong></label>
                            <p v-text="num_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for=""><strong>Registrado por:</strong></label>
                            <p v-text="user"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Activar Ingreso:</strong> </label>
                            <div v-if="!active">
                                <toggle-button @change="cambiarEstadoIngreso(idingresoabrasivo, arrayDetalle)" v-model="btnActive" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                            </div>
                            <div v-else>
                                <span class="badge badge-success">Activado</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th width="10px">No°</th>
                                    <th>Detalles</th>
                                    <th>SKU</th>
                                    <th>Código</th>
                                    <th>Cantidad</th>
                                    <th>Ubicacion</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td v-text="(index + 1)"></td>
                                    <td>
                                        <button type="button" @click="abrirModal3(index)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                    </td>
                                    <td v-text="detalle.sku"></td>
                                    <td v-text="detalle.codigo"></td>
                                    <td v-text="detalle.cantidad"></td>
                                    <td v-text="detalle.ubicacion"></td>
                                    <td v-text="detalle.descripcion"></td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="8" class="text-center">
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
        <!--Añadir Stock -->
        <template v-else-if="listado==3">
            <div class="card-body">
                <!-- Proveedor Section -->
                <div class="form-group row border">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><strong>Proveedor (*)</strong></label>
                                <v-select @search="selectProveedor" label="nombre" :options="arrayProveedor"
                                    placeholder="Buscar Proveedores..." @input="getDatosProveedor"></v-select>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center" v-if="rfc_proveedor">
                        <div class="form-group">
                            <label for=""><strong>RFC</strong></label>
                            <h6 for=""><strong v-text="rfc_proveedor"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center" v-if="contacto_prov">
                        <div class="form-group">
                            <label for=""><strong>Contacto</strong></label>
                            <h6 for=""><strong v-text="contacto_prov"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center" v-if="tel_contacto">
                        <div class="form-group">
                            <label for=""><strong>Telefono de contacto</strong></label>
                            <h6 for=""><strong v-text="tel_contacto"></strong></h6>
                        </div>
                    </div>&nbsp;
                </div>
                <div class="form-group row border">
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante (*)</strong></label>
                            <select v-model="tipo_comprobante" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="INGRESO">INGRESO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Número de Ingreso(*)</strong></label>
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
                    <div class="col-sm-4 mt-1">
                        <div class="form-group">
                            <label for=""><strong>Articulo</strong> <span style="color:red;" v-show="articulo==''">(*Seleccione)</span> </label>
                            <div class="form-inline">
                                <input type="text" class="form-control" v-model="codigo_art" @keyup.enter="buscarArticulo()"  placeholder="Ingrese código del artículo" >
                                <button @click="abrirModal4()" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                                <input type="text" readonly class="form-control" v-model="articulo" v-if="articulo">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 text-center mt-1">
                        <div class="form-group">
                            <label for=""><strong>Cantidad</strong> <span style="color:red;" v-show="cantidad==0">(*Ingrese la cantidad)</span></label>
                            <input type="number" min="0" value="0"  class="form-control" v-model="cantidad">
                        </div>
                    </div>
                    <div class="col-sm-2 text-center mt-1">
                        <div class="form-group">
                            <button @click="agregarDetalle()" class="btn btn-block btn-success mt-4"><i class="icon-plus"></i></button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div v-show="errorIngreso" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjIngreso" :key="error" v-text="error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="table-responsive col-md-12 d-none d-sm-none d-md-block">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Código</th>
                                    <th>SKU</th>
                                    <th>Descripción</th>
                                    <th>Ubicación</th>
                                    <th>Stock</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td>
                                        <div class="form-inline">
                                            <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                            <i class="icon-close"></i>
                                            </button>&nbsp;
                                        </div>
                                    </td>
                                    <td v-text="detalle.codigo"></td>
                                    <td v-text="detalle.sku"></td>
                                    <td v-text="detalle.descripcion"></td>
                                    <td v-text="detalle.ubicacion"></td>
                                    <td v-text="detalle.stock"></td>
                                    <td>
                                        <input v-model="detalle.cantidad" min="0" type="number" value="2" class="form-control">
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <strong>NO hay artículos agregados...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="cerrarNuevoIngreso()"  class="btn btn-secondary">Cerrar</button>
                        <button type="button" @click="registarAbrasivos()" class="btn btn-primary">Registrar Ingreso</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin Añadir stock-->
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
                    <!-- Filtros -->
                    <div class="form-group row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <select class="form-control" v-model="criterioA">
                                    <option value="sku">SKU</option>
                                    <option value="codigo">Código</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="buscarA" @keyup.enter="listarArticulo(1,buscarA,criterioA,bodegaA)" class="form-control" placeholder="Texto a buscar">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <button type="submit" @click="listarArticulo(1,buscarA,criterioA,bodegaA)" class="btn btn-primary"><i class="fa fa-search"></i>Buscar</button>&nbsp;
                                <select class="form-control" v-model="bodegaA" @change="listarArticulo(1,buscarA,criterioA,bodegaA)">
                                    <option value="" disabled>Ubicacion</option>
                                    <option value="">Todas</option>
                                    <option value="Del Musico">Del Músico</option>
                                    <option value="Escultores">Escultores</option>
                                    <option value="Sastres">Sastres</option>
                                    <option value="Mecanicos">Mecánicos</option>
                                    <option value="Tractorista">Tractorista</option>
                                    <option value="San Luis">San Luis</option>
                                    <option value="Aguascalilentes">Aguascalientes</option>
                                    <option value="Puebla">Puebla</option>
                                    <option value="Oficina">Oficina Lazaro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Listado -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-center table-hover">
                            <thead>
                            <tr class="text-center">
                                <!-- <th>Opciones</th> -->
                                <th>Código</th>
                                <th>SKU</th>
                                <th>Ubicacion</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayArticulo.length">
                                <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                    <td v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td>
                                    <div v-if="articulo.condicion == 1">
                                        <span class="badge badge-success">Activo</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Desactivado</span>
                                    </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="9" class="text-center">
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
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page - 1,buscarA,criterioA,bodegaA)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumberArt" :key="page" :class="[page == isActivedArt ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(page,buscarA,criterioA,bodegaA)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="paginationart.current_page < paginationart.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page + 1,buscarA,criterioA,bodegaA)">Sig</a>
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
        <div class="modal-dialog modal-warning modal-lg" role="document">
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
                            <lightbox class="m-0" album="" :src="file">
                                <img class="img-responsive imgcenter" width="500px" :src="file">
                            </lightbox>&nbsp;
                    </template>
                    <div class="text-center">
                        <label class="text-left" for=""><strong>Actualizar Imagen</strong></label>
                        <input type="file" :src="imagen" @change="obtenerImagen" class="form-control-file">
                    </div>
                    <table class="table table-bordered table-striped table-sm text-center table-hover">
                        <thead>
                            <tr class="text-center">
                                <th class="text-center" colspan="2">Detalle del artículo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><strong>NO° DE PLACA</strong></td>
                            <td>
                                <input type="text" class="form-control" v-model="codigo" readonly>
                            </td>
                        </tr>
                        <tr >
                            <td><strong>SKU</strong></td>
                            <td>
                                <input type="text" class="form-control" v-model="sku">
                            </td>
                        </tr>
                        <tr >
                            <td><strong>PRECIO</strong></td>
                            <td>
                                <input type="number" min="0" value="0" class="form-control" v-model="precio_venta">
                            </td>
                        </tr>
                        <tr >
                            <td><strong>BODEGA DE DESCARGA</strong></td>
                            <td>
                                <select class="form-control" v-model="ubicacion">
                                    <option value='' disabled>Seleccione una bodega de descarga</option>
                                    <option value="Del Musico">Del Músico</option>
                                    <option value="Escultores">Escultor</option>
                                    <option value="Sastres">Sastre</option>
                                    <option value="Mecanicos">Mecánicos</option>
                                    <option value="Tractorista">Tractorista</option>
                                    <option value="San Luis">San Luis</option>
                                    <option value="Aguascalientes">Aguascalientes</option>
                                    <option value="Puebla">Puebla</option>
                                    <option value="Oficina">Ofina Lazaro</option>
                                </select>
                            </td>
                        </tr>
                        <tr >
                            <td><strong>Cantidad</strong></td>
                            <td>
                                <input type="number" min="0" value="0" step="any" class="form-control" v-model="cantidad">
                            </td>
                        </tr>
                        <tr >
                            <td><strong>DESCRIPCION</strong></td>
                            <td>
                                <input type="text" class="form-control" v-model="descripcion_r">
                            </td>
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
                        <button type="button" class="btn btn-ligth" @click="actualizarDetalle()">Actualizar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal2()">Cerrar</button>
                    <button type="button" class="btn btn-ligth" @click="actualizarDetalle()">Actualizar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->
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
                            <lightbox class="m-0" album="" :src="'http://localhost:8000/images/'+file">
                                <img class="img-responsive imgcenter" width="500px" :src="'http://localhost:8000/images/'+file">
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
                                <td><strong>CODIGO</strong></td>
                                <td v-text="codigo"></td>
                            </tr>
                            <tr >
                                <td><strong>SKU</strong></td>
                                <td v-text="sku"></td>
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
    <!--Inicio del modal listar articulos-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal4}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal4()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <!-- Filtros -->
                    <div class="form-group row">
                        <div class="col-md-8">
                            <div class="input-group">
                                <select class="form-control" v-model="criterioA">
                                    <option value="sku">SKU</option>
                                    <option value="codigo">Código de material</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="buscarA" @keyup.enter="listarArticulo(1,buscarA,criterioA,bodegaA)" class="form-control" placeholder="Texto a buscar">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <button type="submit" @click="listarArticulo(1,buscarA,criterioA,bodegaA)" class="btn btn-primary"><i class="fa fa-search"></i>Buscar</button>&nbsp;
                                <select class="form-control" v-model="bodegaA" @change="listarArticulo(1,buscarA,criterioA,bodegaA)">
                                    <option value="" disabled>Ubicacion</option>
                                    <option value="">Todas</option>
                                    <option value="Del Musico">Del Músico</option>
                                    <option value="Escultores">Escultores</option>
                                    <option value="Sastres">Sastres</option>
                                    <option value="Mecanicos">Mecánicos</option>
                                    <option value="Tractorista">Tractorista</option>
                                    <option value="San Luis">San Luis</option>
                                    <option value="Aguascalientes">Aguascalientes</option>
                                    <option value="Puebla">Puebla</option>
                                    <option value="Oficina">Oficina Lazaro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Listado -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-center table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>Opciones</th>
                                <th>SKU</th>
                                <th>Código de material</th>
                                <th>Stock</th>
                                <th>Ubicacion</th>
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
                                    <td v-text="articulo.stock"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td>
                                    <div v-if="articulo.condicion == 1">
                                        <span class="badge badge-success">Activo</span>
                                    </div>
                                    <div v-else-if="articulo.condicion == 0">
                                        <span class="badge badge-danger">Desactivado</span>
                                    </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="9" class="text-center">
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
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page - 1,buscarA,criterioA,bodegaA)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumberArt" :key="page" :class="[page == isActivedArt ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(page,buscarA,criterioA,bodegaA)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="paginationart.current_page < paginationart.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArt(paginationart.current_page + 1,buscarA,criterioA,bodegaA)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                    <hr class="d-block d-sm-block d-md-none">
                    <div class="float-right d-block d-sm-block d-md-none">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal4()">Cerrar</button>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal4()">Cerrar</button>
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
            rfc_proveedor : "",
            contacto_prov : "",
            tel_contacto : "",
            proveedor: '',
            user: '',
            nombre: "",
            tipo_comprobante: "INGRESO",
            num_comprobante: "",
            impuesto: 0.16,
            idabrasivo : 0,
            articulo : "",
            codigo: "",
            condicion : 0,
            precio_venta : 0,
            cantidad : 0,
            total_impuesto : 0.0,
            total_parcial : 0.0,
            total: 0.0,
            arrayArticulo : [],
            arrayIngreso : [],
            arrayEntrada : [],
            arrayDetalle : [],
            arrayProveedor : [],
            listado : 1,
            modal: 0,
            modal2: 0,
            modal3: 0,
            modal4: 0,
            ind : '',
            tituloModal: "",
            tipoAccion: 0,
            errorIngreso: 0,
            errorMostrarMsjIngreso: [],
            codigo_art:"",
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
            offset : 3,
            criterio : 'num_comprobante',
            estadoIn : '',
            buscar : '',
            buscarA : '',
            criterioA : 'sku',
            bodegaA : '',
            sku : '',
            ubicacion : '',
            stock : 0,
            descripcion_r: '',
            file : '',
            imagenMinatura : '',
            arrayCategoria : [],
            CodeDate : "",
            sigNum : 0,
            arrayCodigos : [],
            active : false,
            btnActive : false,
            idingresoabrasivo : 0,
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
            calcularTotal : function(){
                let me=this;
                let resultado = 0;
                for(var i=0;i<me.arrayDetalle.length;i++){
                    resultado = resultado + (me.arrayDetalle[i].precio_venta * me.arrayDetalle[i].cantidad)
                }
                return resultado;
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
            getFechaCode : function(){
                let me = this;
                let date = "";
                moment.locale('es');
                date = moment().format('YYMMDD');
                me.CodeDate = moment().format('YYMMDD');
                return date;
            }
        },
    methods: {
        listarIngreso (page,buscar,criterio,estado){
            let me=this;
            var url= '/ingresoabrasivo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado=' + estado;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayIngreso = respuesta.ingresos.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        listarEntrada(page,buscar,criterio){
            let me=this;
            var url= '/ingresoabrasivo/indexEntradas?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayEntrada = respuesta.entradas.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        selectProveedor(search,loading){
            let me=this;
            loading(true)
            var url= '/proveedor/selectProveedor?filtro='+search;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                q: search
                me.arrayProveedor=respuesta.proveedores;
                loading(false)
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        getDatosProveedor(val1){
            let me = this;
            me.loading = true;
            me.idproveedor = val1.id;
            me.rfc_proveedor = val1.rfc;
            me.contacto_prov = val1.contacto;
            me.tel_contacto = val1.telefono_contacto;
        },
        buscarArticulo(){
            let me = this;
            var url= '/abrasivo/buscarArticulo?filtro='+ me.codigo;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                me.arrayArticulo=respuesta.articulos;
                if(me.arrayArticulo.length > 0){
                    me.articulo = me.arrayArticulo[0]['nombre'];
                    me.idabrasivo = me.arrayArticulo[0]['id'];
                }else{
                    me.articulo = 'No existe este abrasivo';
                    me.idabrasivo = 0;
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        registarAbrasivos(){
            if (this.validarEntrada()) {
                return;
            }
            let me = this;
            let active = 1;
                active = 1;
            axios.post("/abrasivo/registrarDetalle", {
                'data' : this.arrayDetalle,
                'active' : active
            })
            .then(function(response) {
                me.registrarStock();
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        registrarStock(){
            let me = this;
            var numcomp = "E-".concat(me.CodeDate,"-",me.num_comprobante);
            let active = 0;
            if (this.active == true)
                active = 1;
            axios.post('/ingresoabrasivo/registrar',{
                'idproveedor': this.idproveedor,
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'impuesto' : this.impuesto,
                'total' : this.total,
                'data': this.arrayDetalle,
                'active' : active
            }).then(function(response) {
                me.cerrarNuevoIngreso();
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Se ha registrado el ingreso con éxito!',
                });
                me.listarEntrada(1,'','num_comprobante','Registrado');

            })
            .catch(function(error) {
                console.log(error);
            });
        },
        agregarDetalleModal(data =[]){
            let me=this;
            if(me.encuentra(data['id'])){
                swal.fire(
                    'Error!',
                    'El artículo ya esta en el listado.',
                    'warning'
                );
            }
            else{
                me.arrayDetalle.push({
                    idabrasivo       : data['id'],
                    codigo           : data['codigo'],
                    sku              : data['sku'],
                    descripcion      : data['descripcion'],
                    ubicacion        : data['ubicacion'],
                    precio           : data['precio_venta'],
                    stock            : data['stock'],
                    cantidad         : 1
                });
               Swal.fire({
                    type: 'success',
                    title: 'Añadido..',
                    text: 'Se añadio un abrasivo!',
                    timer: 1000
                });
            }
        },
        cambiarPagina(page,buscar,criterio,estado){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarIngreso(page,buscar,criterio,estado);
        },
        cambiarPaginaArt(page,buscar,criterio,bodega){
            let me = this;
            //Actualiza la página actual
            me.paginationart.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticulo(page,buscar,criterio,bodega);
        },
        encuentra(codigo){
            let sw = 0;
            for(var i=0; i<this.arrayDetalle.length;i++){
                if(this.arrayDetalle[i].codigo==codigo){
                    sw = true;
                }
            }
            return sw;
        },
        encuentraCodigoSku(codigo){
            let sw = 0;
            for(var i=0; i<this.arrayCodigos.length;i++){
                if(this.arrayCodigos[i].codigo==codigo){
                    sw = true;
                }
            }
            return sw;
        },
        eliminarDetalle(index){
            let me = this;
           /*  console.log(`Index : ${ index }`); */
            me.arrayDetalle.splice(index,1);
        },
        agregarDetalle(){
            let me = this;
            if(me.codigo == 0){
            }else{
                if(me.encuentraCodigoSku(me.codigo)){
                    Swal.fire({
                        type: 'error',
                        title: 'Error...',
                        text: 'Este Código ya esta en el sistema!',
                    });
                    me.codigo = "";
                }
                else if(me.encuentra(me.codigo)){
                    Swal.fire({
                        type: 'error',
                        title: 'Error...',
                        text: 'Este Código ya esta en el listado del detalle!',
                    });
                    me.codigo = "";
                }else{
                    me.arrayDetalle.push({
                        ubicacion        : me.ubicacion,
                        articulo         : me.articulo,
                        sku              : me.sku,
                        codigo           : me.codigo,
                        precio_venta     : me.precio_venta,
                        cantidad         : me.cantidad,
                        stock            : me.cantidad,
                        imagen           : me.file,
                        descripcion      : me.descripcion_r,
                    });
                    me.codigo = "";
                    me.file = "";
                }
            }
        },
        actualizarDetalle(){
            let me = this;
            if(me.codigo == 0 || me.precio_venta == 0 || me.cantidad == 0){
            }else{
                me.eliminarDetalle(me.ind);
                me.arrayDetalle.push({
                    ubicacion        : me.ubicacion,
                    articulo         : me.articulo,
                    sku              : me.sku,
                    codigo           : me.codigo,
                    precio_venta     : me.precio_venta,
                    cantidad         : me.cantidad,
                    stock            : me.cantidad,
                    imagen           : me.file,
                    descripcion      : me.descripcion_r,
                });
                me.codigo = "";
                me.file = "";
                me.modal2 = 0;
            }
        },
        registrarArticulos() {
            if (this.validarIngreso()) {
                return;
            }
            let me = this;
            let active = 5;
            if (this.active == true)
                active = 1;
            axios.post("/abrasivo/registrarDetalle", {
                'data' : this.arrayDetalle,
                'active' : active
            })
            .then(function(response) {
                me.registrarIngreso();
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        registrarIngreso(){
            let me = this;
            var numcomp = "IA-".concat(me.CodeDate,"-",me.num_comprobante);
            let active = 0;
            if (this.active == true)
                active = 1;
            axios.post('/ingresoabrasivo/registrar',{
                'idproveedor': this.idproveedor,
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'impuesto' : this.impuesto,
                'total' : this.total,
                'data': this.arrayDetalle,
                'active' : active
            }).then(function(response) {
                me.ocultarDetalle();
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Se ha registrado el ingreso con éxito!',
                });
                me.listarIngreso(1,'','num_comprobante','Registrado');
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarIngreso(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de anular este ingreso?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/ingresoabrasivo/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarIngreso(1,'','num_comprobante','Anulado');
                        swal(
                        'Anulado!',
                        'El ingreso ha sido anulado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        validarIngreso() {
            this.errorIngreso = 0;
            this.errorMostrarMsjIngreso = [];
            if (this.idproveedor==0) this.errorMostrarMsjIngreso.push("Seleccione un proveedor");
            if (this.tipo_comprobante==0) this.errorMostrarMsjIngreso.push("Seleccione un comprobante.");
            if (!this.num_comprobante) this.errorMostrarMsjIngreso.push("Ingrese el numero de comprobante");
            if (!this.impuesto) this.errorMostrarMsjIngreso.push("Ingrese el impuesto de la compra");
            if (this.arrayDetalle.length<=0) this.errorMostrarMsjIngreso.push("Introduzca abrasivos para registrar");
            if (this.errorMostrarMsjIngreso.length) this.errorIngreso = 1;
            return this.errorIngreso;
        },
        validarEntrada() {
            this.errorIngreso = 0;
            this.errorMostrarMsjIngreso = [];
            if (this.idproveedor==0) this.errorMostrarMsjIngreso.push("Seleccione un proveedor");
            if (this.tipo_comprobante==0) this.errorMostrarMsjIngreso.push("Seleccione un comprobante.");
            if (!this.num_comprobante) this.errorMostrarMsjIngreso.push("Ingrese el numero de comprobante");
            if (!this.impuesto) this.errorMostrarMsjIngreso.push("Ingrese el impuesto de la compra");
            if (this.arrayDetalle.length<=0) this.errorMostrarMsjIngreso.push("Introduzca abrasivos para registrar");
            if (this.errorMostrarMsjIngreso.length) this.errorIngreso = 1;
            return this.errorIngreso;
        },
        mostrarDetalle(){
            this.getLastNum();
            this.listado = 0;
            this.codigo = "";
            this.idabrasivo = 0;
            this.articulo = "";
            this.sku = "";
            this.precio_venta = 0;
            this.cantidad = 1;
            this.file = '';
            this.ubicacion = '';
            this.arrayDetalle = [];
            this.idproveedor = 0;
            this.num_comprobante = (parseInt(this.sigNum));
            this.getCodesSku();
        },
        añadirStock(){
            this.getLastNum();
            this.listado = 3;
            this.codigo = "";
            this.idabrasivo = 0;
            this.articulo = "";
            this.sku = "";
            this.precio_venta = 0;
            this.cantidad = 1;
            this.file = '';
            this.ubicacion = '';
            this.arrayDetalle = [];
            this.idproveedor = 0;
            this.num_comprobante = (parseInt(this.sigNum));
            this.getCodesSku();
        },
        getCodesSku(){
            let me=this;
            var url= '/abrasivo/getCodesSku';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayCodigos = respuesta.codigos;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        ocultarDetalle(){
            this.listado = 1;
            this.codigo = "";
            this.idabrasivo = 0;
            this.articulo = "";
            this.sku = "";
            this.precio_venta = 0;
            this.precio_venta = 0;
            this.cantidad = 0;
            this.file = '';
            this.ubicacion = '';
            this.arrayDetalle = [];
            this.errorMostrarMsjIngreso = [];
            this.idproveedor = 0;
            this.num_comprobante = 0;
            this.descripcion_r = "";
            this.getLastNum();
        },
        cerrarNuevoIngreso(){
            this.listado = 1;
            this.codigo = "";
            this.idabrasivo = 0;
            this.articulo = "";
            this.sku = "";
            this.precio_venta = 0;
            this.precio_venta = 0;
            this.cantidad = 0;
            this.file = '';
            this.ubicacion = '';
            this.arrayDetalle = [];
            this.errorMostrarMsjIngreso = [];
            this.idproveedor = 0;
            this.num_comprobante = 0;
            this.descripcion_r = "";
            this.getLastNum();
        },
        verIngreso(id){
            let me = this;
            me.listado = 2;
            //Obtener los datos del ingreso
            var arrayIngresoT=[];
            var url= '/ingresoabrasivo/obtenerCabecera?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayIngresoT = respuesta.ingreso;
                var fechaform  = arrayIngresoT[0]['fecha_hora'];
                console.log(arrayIngresoT);
                me.proveedor = arrayIngresoT[0]['nombre'];
                me.tipo_comprobante=arrayIngresoT[0]['tipo_comprobante'];
                me.num_comprobante=arrayIngresoT[0]['num_comprobante'];
                me.user=arrayIngresoT[0]['usuario'];
                moment.locale('es');
                me.fecha_llegada=moment(fechaform).format('llll');
                me.idingresoabrasivo = arrayIngresoT[0]['id'];
                if (arrayIngresoT[0]['active'] != 0) {
                    me.active = true
                } else {
                    me.active = false;
                }
            })
            .catch(function (error) {
                console.log(error);
            });
            //Obtener los detalles del ingreso
            var url= '/ingresoabrasivo/obtenerDetalles?id=' + id;
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
            this.bodegaA = "";
            this.criterioA = "sku";
        },
        abrirModal() {
            this.arrayArticulo=[];
            this.modal = 1;
            this.tituloModal = "Seleccionar Abrasivos";
            this.listarArticulo(1,'','sku','','');
        },
        cerrarModal4() {
            this.modal4 = 0;
            this.bodegaA = "";
            this.criterioA = "sku";
        },
        abrirModal4() {
            this.arrayArticulo=[];
            this.modal4 = 1;
            this.tituloModal = "Seleccionar Abrasivos";
            this.listarArticulo(1,'','sku','','');
        },
        listarArticulo (page,buscar,criterio,bodega){
            let me=this;
            var url= '/abrasivo/listarArticulo?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&bodega=' + bodega;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.paginationart= respuesta.pagination;
                me.arrayArticulo = respuesta.articulos.data;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        abrirModal2(index){
            let me = this;
            me.ind = index;
            me.modal2 = 1;
            me.tituloModal      = "Editar Artículo ";
            me.sku              = me.arrayDetalle[index]['sku'];
            me.codigo           = me.arrayDetalle[index]['codigo'];
            me.ubicacion        = me.arrayDetalle[index]['ubicacion'];
            me.precio_venta     = me.arrayDetalle[index]['precio_venta'];
            me.cantidad         = me.arrayDetalle[index]['cantidad'];
            me.file             = me.arrayDetalle[index]['imagen'];
            me.descripcion_r      = "";
        },
        cerrarModal2() {
            this.modal2 = 0;
            this.sku = '';
            this.codigo = '';
            this.precio_venta = 0;
            this.stock = 0;
            this.file = '';
            this.descripcion_r = '';
            this.ind = '';
        },
        obtenerImagen(e){
            let img = e.target.files[0];
            this.file = img;
            this.cargarImagen(img);
        },
        cargarImagen(img){
            let reader = new FileReader();
            reader.onload = (e) => {
                this.imagenMinatura = e.target.result;
                this.file =  e.target.result;
            }
            reader.readAsDataURL(img);
        },
        abrirModal3(index){
            let me = this;
            me.ind = index;
            me.modal3 = 1;
            me.tituloModal      = "Artículo ";
            me.sku              = me.arrayDetalle[index]['sku'];
            me.codigo           = me.arrayDetalle[index]['codigo'];
            me.ubicacion        = me.arrayDetalle[index]['ubicacion'];
            me.precio_venta     = me.arrayDetalle[index]['precio_compra'];
            me.cantidad         = me.arrayDetalle[index]['cantidad'];
            me.file             = me.arrayDetalle[index]['file'];
            me.descripcion_r    = me.arrayDetalle[index]['descripcion'];
            me.condicion    = me.arrayDetalle[index]['condicion'];
        },
        cerrarModal3() {
            this.modal3 = 0;
            this.sku = '';
            this.codigo = '';
            this.precio_venta = 0;
            this.stock = 0;
            this.file = '';
            this.descripcion_r = '';
            this.ind = '';
        },
        getLastNum(){
            let me=this;
            var url= '/ingresoabrasivo/nextNum';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.num_comprobante = respuesta;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        pdfIngreso(id){
            window.open('/ingresoabrasivo/pdf/'+id);
        },
        cambiarEstadoIngreso(id, detalles) {
            let data = [];
            detalles.forEach(element => {
                data.push(element['codigo']);
            });
            if (this.btnActive) {
                let me = this;
                axios.post('/ingresoabrasivo/cambiarEstadoIngreso',{
                'id': id,
                }).then(function(response) {
                   me.cambiarEstadoArticulos(data);
                })
                .catch(function(error) {
                    console.log(error);
                });
            }
        },
        cambiarEstadoArticulos(data) {
            let me = this;
            console.log(data);
            axios.post('/abrasivo/cambiarEstadoIngreso',{
                'data': data,
            }).then(function(response) {
                me.ocultarDetalle();
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Se ha activado el ingreso con éxito!',
                });
                me.listarIngreso(1,'','num_comprobante','Registrado');
            })
            .catch(function(error) {
                console.log(error);
            });
        }
    },
    mounted() {
        this.listarIngreso(1,this.buscar, this.criterio,this.estadoIn);
        this.listarEntrada(1,this.buscar, this.criterio);
        this.getLastNum();
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
    input[type="number"]
    {
        -webkit-appearance: textfield !important;
        margin: 0;
        /* -moz-appearance:textfield !important; */
    }
</style>

