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
          <i class="fa fa-align-justify"></i> Cotizacion Proyectos
          <button type="button" @click="mostrarDetalle()" class="btn btn-secondary" v-if="listado==1">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
          <template v-if="listado==2">
                <button type="button" class="btn btn-info float-right ml-2"
                    @click="sendMailCot(cotizacion_id,email_cliente,cliente)" v-if="email_cliente && estadoVn == 'Registrado' ">
                    <i class="fa fa-share"></i> Enviar
                </button>&nbsp;
                <button type="button" @click="desactivarCotizacionEditar(cotizacion_id)" class="btn btn-warning float-right">
                    <i class="icon-pencil"></i>&nbsp;<strong>Editar</strong>
                </button>
          </template>
        </div>
        <!-- Listado -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-sm-10">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="cliente">Cliente</option>
                                <option value="num_comprobante">No° Comprobante</option>
                                <option value="fecha_hora">Fecha</option>
                            </select>
                            <input type="text" v-model="buscar" @keyup.enter="listarCotizacion(1,buscar,criterio,estadoCotizacion)" class="form-control mb-1" placeholder="Texto a buscar">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="estadoCotizacion" @change="listarCotizacion(1,buscar,criterio,estadoCotizacion)">
                                <option value="">Pendiente</option>
                                <option value="Vendida">Vendida</option>
                                <option value="Anulada">Cancelada</option>
                            </select>
                            <button type="submit" @click="listarCotizacion(1,buscar,criterio,estadoCotizacion)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                    </div>
                    <div class="form-group mb-2 col-sm-2 float-right">
                        <button @click="abrirModal5()" class="btn btn-primary">...</button>&nbsp;<strong>Buscar Articulos cotizados</strong>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Usuario</th>
                                <th>Cliente</th>
                                <th>Tipo de documento</th>
                                <th>No° Documento</th>
                                <th>Fecha de creacion</th>
                                <th>Vigencia</th>
                                <th>Impuesto</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayCotizacion.length">
                            <tr v-for="cotizacion in arrayCotizacion" :key="cotizacion.id">
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" @click="verCotizacion(cotizacion.id)">
                                        <i class="icon-eye"></i>
                                    </button>&nbsp;

                                    <template v-if="cotizacion.estado=='Registrado'">
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfCotizacion(cotizacion.id)">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </button>&nbsp;
                                        <button type="button" class="btn btn-danger btn-sm" @click="desactivarCotizacion(cotizacion.id)">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </template>
                                </td>
                                <td v-text="cotizacion.usuario"></td>
                                <td v-text="cotizacion.nombre"></td>
                                <td v-text="cotizacion.tipo_comprobante"></td>
                                <td v-text="cotizacion.num_comprobante"></td>
                                <td>{{ convertDateCotizacion(cotizacion.fecha_hora) }}</td>
                                <td>{{ convertDateVigencia(cotizacion.vigencia) }}</td>
                                <td v-text="cotizacion.impuesto"></td>
                                <td v-text="cotizacion.total"></td>
                                <!-- <td v-text="cotizacion.estado "></td> -->
                                <td>
                                    <div v-if="cotizacion.estado == 'Registrado'">
                                        <span class="badge badge-warning">En espera</span>
                                    </div>
                                    <div v-else-if="cotizacion.estado == 'Vendida'">
                                        <span class="badge badge-success">Vendida</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">Cancelada</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <td colspan="10" class="text-center">
                                <strong>NO hay cotizaciones registradas o con ese criterio...</strong>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoCotizacion)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoCotizacion)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoCotizacion)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->

        <!-- Nueva Cotizacion -->
        <template v-else-if="listado==0">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Cliente (*)</strong></label>
                                <v-select :on-search="selectCliente" label="nombre" :options="arrayCliente" placeholder="Buscar clientes..." :onChange="getDatosCliente">
                                </v-select>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="cliente">
                        <div class="form-group">
                            <label for=""><strong>Cliente</strong></label>
                            <h6 for=""><strong v-text="cliente"></strong></h6>
                        </div>
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
                    <div class="col-md-2 text-center sinpadding" v-if="cfdi_cliente">
                        <div class="form-group">
                            <label for=""><strong>Uso del CFDI</strong></label>
                            <h6 for=""><strong v-text="cfdi_cliente"></strong></h6>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2 text-center sinpadding" v-if="contacto_cliente">
                        <div class="form-group">
                            <label for=""><strong>Contacto</strong></label>
                            <h6 for=""><strong> {{contacto_cliente}} | {{telcontacto_cliente}}</strong></h6>
                        </div>
                    </div>&nbsp;

                    <div class="col-md-3 text-center sinpadding" v-if="obs_cliente">
                        <div class="form-group">
                            <label for=""><strong>Observaciones</strong></label>
                            <h6 for=""><strong> {{obs_cliente}}</strong></h6>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo de documento (*) </strong></label>
                            <select v-model="tipo_comprobante" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="COTIZACION">COTIZACION</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Número de cotizacion (*)</strong></label>
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

                    <div class="col-md-2 text-center">
                        <label for=""><strong>Impuesto (*)</strong></label>
                        <input type="number" class="form-control" v-model="impuesto">
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Forma de pago</strong><span style="color:red;" v-show="forma_pago==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="forma_pago">
                                <option value='' disabled>Seleccione la forma de pago</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Mixto">Mixto</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><i style="color:green;" class="fa fa-money"></i><strong> Valor del proyecto</strong><span style="color:red;" v-show="neto==''">(*Ingrese)</span></label>
                            <input type="number" step="any" class="form-control" v-model="neto"  placeholder="Escriba el valor del proyecto">
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tiempo de entrega</strong><span style="color:red;" v-show="tiempo_entrega==''"> (*Seleccione)</span></label>
                            <select class="form-control" v-model="tiempo_entrega">
                                <option value='' disabled>Seleccione el tiempo de entrega</option>
                                <option value="Inmediata">Inmediata</option>
                                <option value="de 2 a 10 dias">de 2 a 10 dias</option>
                                <option value="de 10 a 20 dias">de 10 a 20 dias</option>
                                <option value="de 21 a 40 dias">de 10 a 20 dias</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Lugar de entrega</strong><span style="color:red;" v-show="lugar_entrega==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="lugar_entrega">
                                <option value='' disabled>Seleccione el lugar de entrega</option>
                                <option value="LAB TROYSTONE">LAB TROYSTONE</option>
                                <option value="LAB TROYSTONE S.L.P.">LAB TROYSTONE S.L.P.</option>
                                <option value="LAB TROYSTONE A.G.S.">LAB TROYSTONE A.G.S.</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Vigencia</strong><span style="color:red;" v-show="vigencia==''">(*Seleccione)</span> </label>
                             <input type="date" v-model="vigencia" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                          <div class="form-check float-left mr-2">
                            <input class="form-check-input" type="checkbox" id="chkInsta" v-model="instalacion">
                            <label class="form-check-label p-0 m-0" for="chkInsta"><strong>Instalación</strong></label>
                        </div>
                        <div class="form-check float-left">
                            <input class="form-check-input" type="checkbox" id="chkFlete" v-model="flete">
                            <label class="form-check-label p-0 m-0" for="chkFlete"><strong>Flete corre por cuenta del cliente</strong></label>
                        </div>
                    </div>
                    <div class="col-md-3 text-center" v-if="arrayDetalle.length">
                        <div class="form-group">
                            <label for=""><strong>Total M<sup>2</sup> </strong></label>
                            <p> {{ metrosTotales.toFixed(4) }} </p>
                        </div>
                    </div>

                    <div class="col-md-12 text-center">
                        <div v-show="errorCotizacion" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjCotizacion" :key="error" v-text="error"></div>
                            </div>
                        </div>
                    </div>
                        <div class="col-md-6 d-flex flex-column">
                        <h3><strong> Proyecto </strong></h3>
                        <div class="form-group">
                            <label for=""><strong> Nombre </strong><span style="color:red;" v-show="title==''">(*Ingrese)</span></label>
                            <input type="text" class="form-control" v-model="title" placeholder="Escriba el nombre del proyecto">
                        </div>
                        <div class="form-group">
                            <label for=""><strong> Descripción </strong></label>
                             <template class="justify-content-center">
                                <editor :options="editorOptions" mode="wysiwyg" v-model="content" Width="100%"/>
                            </template>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 d-flex flex-column">
                        <div class="form-group">
                            <h3><strong> Articulos </strong></h3>
                            <button class="btn btn-primary btn-sm float-right" v-if="idcliente" @click="abrirModal()">
                                <i class="fa fa-search" aria-hidden="true"></i> Seleccionar Articulos
                            </button>
                        </div>
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th width="10px">No°</th>
                                    <th>Material</th>
                                    <th>Código de material</th>
                                    <th>No° Placa</th>
                                    <th>Terminado</th>
                                    <th>largo</th>
                                    <th>Alto</th>
                                    <th>Metros <sup>2</sup></th>
                                    <th>Cantidad</th>
                                    <th v-if="moneda!='Peso Mexicano'">
                                        Precio m<sup>2</sup> {{moneda}}
                                    </th>
                                    <th v-else>
                                        Precio m<sup>2</sup>
                                    </th>
                                    <th>Ubicacion</th>
                                    <th>SubTotal</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td>
                                        <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                            <i class="icon-close"></i>
                                        </button>&nbsp;
                                        <button type="button" @click="abrirModal2(index)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                        <button type="button" class="btn btn-warning btn-sm" @click="abrirModal4(index)">
                                            <i class="icon-crop"></i>
                                        </button>
                                    </td>
                                    <td >{{ index + 1 }} </td>
                                    <td v-text="detalle.categoria"></td>
                                    <template v-if="detalle.articulo">
                                        <td v-text="detalle.articulo"></td>
                                    </template>
                                    <template v-else>
                                        <td v-text="detalle.sku"></td>
                                    </template>
                                    <td v-text="detalle.codigo"></td>
                                    <td v-text="detalle.terminado"></td>
                                    <td v-text="detalle.largo"></td>
                                    <td v-text="detalle.alto"></td>
                                    <td v-text="detalle.metros_cuadrados"></td>
                                    <td>
                                        <span style="color:red;" v-show="detalle.cantidad>detalle.stock">Solo hay: {{detalle.stock}} disponibles</span>
                                        <input v-model="detalle.cantidad" min="1" type="number" class="form-control">
                                    </td>
                                   <td>
                                       <input v-model="detalle.precio" min="0" step="any" type="number" class="form-control">
                                   </td>
                                    <td v-text="detalle.ubicacion"></td>
                                    <td>
                                       {{ (( (detalle.precio * detalle.cantidad) * detalle.metros_cuadrados) - detalle.descuento) }}
                                    </td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="12" align="right"><strong>Total Parcial:</strong></td>
                                    <td>$ {{total_parcial=(total-total_impuesto).toFixed(2)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="12" align="right"><strong>Total IVA:</strong></td>
                                    <td>$ {{total_impuesto=((total * parseFloat(impuesto))/(1+parseFloat(impuesto))).toFixed(2)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="12" align="right"><strong>Total Neto:</strong></td>
                                    <td>$ {{total=(calcularTotal.toFixed(2))}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="12" align="right"><strong>Total Metros<sup>2</sup> : </strong></td>
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
                        <button type="button" class="btn btn-primary" @click="registrarCotizacion()">Registrar Cotizacion</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin Nueva Cotizacion -->

         <!-- Ver Cotizacion -->
        <template v-else-if="listado==2">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><strong>Cliente</strong></label>
                            <p v-text="cliente"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante</strong></label>
                            <p v-text="tipo_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><strong>Número de Comprobante</strong></label>
                            <p v-text="num_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Registrado por:</strong></label>
                            <p v-text="user"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""> <strong>Fecha:</strong></label>
                            <p v-text="fecha_llegada"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong> Total del material:</strong></label>
                            <p v-text="total"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong> Valor del Proyecto:</strong></label>
                            <p v-text="neto"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong> Impuesto </strong></label>
                            <p v-text="impuesto"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""> <strong>Moneda</strong></label>
                            <p v-text="moneda"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong> Tipo de cambio</strong></label>
                            <p v-text="tipo_cambio"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""> <strong>Forma de pago </strong></label>
                            <p v-text="forma_pago"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""> <strong>Estado: </strong></label>
                            <div v-if="estadoVn == 'Registrado'">
                                <span class="badge badge-warning">En espera</span>
                            </div>
                            <div v-else-if="estadoVn == 'Vendida'">
                                <span class="badge badge-success">Aceptada</span>
                            </div>
                            <div v-else>
                                <span class="badge badge-danger">Cotizacion cancelada</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check float-left mr-2" >
                                <input class="form-check-input" type="checkbox" id="chkInsta" v-model="instalacion" >
                                <label class="form-check-label p-0 m-0" for="chkInsta"><strong>Instalación</strong></label>
                        </div>
                        <div class="form-check float-left" >
                                <input class="form-check-input" type="checkbox" id="chkFlete" v-model="flete">
                                <label class="form-check-label p-0 m-0" for="chkFlete"><strong>Flete corre por cuenta del cliente</strong></label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex flex-column">
                            <div>
                                <h4 v-text="title"></h4>
                            </div>
                            <div>
                                <viewer :value="content"/>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="table-responsive col-md-12">
                        <table class="table table-bordered table-striped table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Detalles</th>
                                    <th width="10px">No°</th>
                                    <th>Código de material</th>
                                    <th>No° Placa</th>
                                    <th>Terminado</th>
                                    <th>Espesor</th>
                                    <th>largo</th>
                                    <th>Alto</th>
                                    <th>Metros <sup>2</sup></th>
                                    <th>Cantidad</th>
                                    <th>Precio m<sup>2</sup></th>
                                    <th>Descuento </th>
                                    <th>Ubicacion</th>
                                    <th>SubTotal</th>

                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td>
                                        <button type="button" @click="abrirModal3(index)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                    </td>
                                    <td >{{ index + 1 }} </td>
                                    <td v-text="detalle.sku"></td>
                                    <td v-text="detalle.codigo"></td>
                                    <td v-text="detalle.terminado"></td>
                                    <td v-text="detalle.espesor"></td>
                                    <td v-text="detalle.largo"></td>
                                    <td v-text="detalle.alto"></td>
                                    <td v-text="detalle.metros_cuadrados"></td>
                                    <td v-text="detalle.cantidad"></td>
                                    <td v-text="detalle.precio"></td>
                                    <td v-text="detalle.descuento"></td>
                                    <td v-text="detalle.ubicacion"></td>
                                    <td>{{ (( (detalle.precio * detalle.cantidad) * detalle.metros_cuadrados) - detalle.descuento) }}</td>
                                </tr>
                                 <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Parcial:</strong></td>
                                    <td>$ {{total_parcial = (total / divImp).toFixed(2) }}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Impuesto:</strong></td>
                                    <td>$ {{total_impuesto=((total * impuesto)/(divImp)).toFixed(2)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Neto:</strong></td>
                                    <td>$ {{ total }} </td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Metros<sup>2</sup> : </strong></td>
                                    <td> {{ metrosTotales.toFixed(4)}} </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="14" class="text-center">
                                        <strong>NO hay artículos en este detalle...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group row">
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
                                    <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacion(cotizacion_id)">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Lugar de entrega</strong></label>
                            <p v-text="lugar_entrega"></p>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""> <strong>Tiempo de entrega </strong></label>
                            <p v-text="tiempo_entrega"></p>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""> <strong>Vigencia:</strong></label>
                            <p v-text="vigencia"></p>
                        </div>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                        <template v-if="estadoVn =='Registrado'">
                            <button type="button" @click="crearVenta()"  class="btn btn-primary">Pasar a Venta</button>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin ver Cotizacion-->

        <!-- Nueva Venta -->
        <template v-else-if="listado==3">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Cambiar cliente (*)</strong></label>
                                <v-select :on-search="selectCliente" label="nombre" :options="arrayCliente" placeholder="Buscar clientes..." :onChange="getDatosCliente">
                                </v-select>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Cliente</strong></label>
                            <h4 v-text="cliente"></h4>
                        </div>
                    </div>&nbsp;
                     <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo de cliente</strong></label>
                            <p v-text="tipo_cliente"></p>
                           <!--  <input type="text" readonly :value="tipo_cliente" class="form-control col-md"> -->
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>RFC</strong></label>
                            <p v-text="rfc_cliente"></p>
                            <!-- <input type="text" readonly :value="rfc_cliente" class="form-control col-md"> -->
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Uso de CFDI</strong></label>
                            <p v-text="cfdi_cliente"></p>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante (*)</strong></label>
                            <select v-model="tipo_comprobante" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="PRESUPUESTO">PRESUPUESTO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Número de presupuesto (*)</strong></label>
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
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Forma de pago</strong><span style="color:red;" v-show="forma_pago==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="forma_pago">
                                <option value='' disabled>Seleccione la forma de pago</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Mixto">Mixto</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-center" v-if="forma_pago =='Cheque'">
                        <div class="form-group">
                            <label for=""><strong>No° de Cheque</strong><span style="color:red;" v-show="num_cheque==0">(*Ingrese)</span></label>
                            <input type="number" min="0" class="form-control" v-model="num_cheque" placeholder="000xx">
                        </div>
                    </div>
                    <div class="col-md-2 text-center" v-if="forma_pago =='Cheque'">
                        <div class="form-group">
                            <label for=""><strong>Banco</strong><span style="color:red;" v-show="banco==''">(*Ingrese)</span></label>
                            <input type="text" class="form-control" v-model="banco" placeholder="Banco del cheque">
                        </div>
                    </div>
                    <div class="col-md-1 text-center">
                        <label for=""><strong>IVA (*)</strong></label>
                        <input type="text" class="form-control" v-model="impuesto">
                    </div>
                    <div class="col-md-1 text-center">
                        <div class="form-group">
                            <label for=""><strong>Moneda</strong><span style="color:red;" v-show="moneda==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="moneda">
                                <option value='' disabled>Seleccione la moneda del cobro</option>
                                <option value="Peso Mexicano">Peso Mexicano</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tiempo de entrega</strong><span style="color:red;" v-show="tiempo_entrega==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="tiempo_entrega">
                                <option value='' disabled>Seleccione el tiempo de entrega</option>
                                <option value="Inmediata">Inmediata</option>
                                <option value="de 2 a 10 dias">de 2 a 10 dias</option>
                                <option value="de 10 a 20 dias">de 10 a 20 dias</option>
                                <option value="de 21 a 40 dias">de 10 a 20 dias</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Lugar de entrega</strong><span style="color:red;" v-show="lugar_entrega==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="lugar_entrega">
                                <option value='' disabled>Seleccione el lugar de entrega</option>
                                <option value="LAB TROYSTONE">LAB TROYSTONE</option>
                                <option value="LAB TROYSTONE S.L.P.">LAB TROYSTONE S.L.P.</option>
                                <option value="LAB TROYSTONE A.G.S.">LAB TROYSTONE A.G.S.</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo de facturación</strong><span style="color:red;" v-show="tipo_facturacion==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="tipo_facturacion">
                                <option value='' disabled>Seleccione el tipo de facturación</option>
                                <option value="Publico General">Publico General</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Presupuesto especial:</strong> </label>
                            <div>
                                <toggle-button v-model="ispecial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <div v-show="errorVenta" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in errorMostrarMsjVenta" :key="error" v-text="error"></div>
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
                            <label for=""><strong>Cantidad</strong> <span style="color:red;" v-show="cantidad==0">(*Ingrese la cantidad)</span></label>
                            <input type="number" min="0" value="0"  class="form-control" v-model="cantidad">
                        </div>
                    </div>
                    <div class="col-sm-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Precio m<sup>2</sup></strong> <span style="color:red;" v-show="precio==0">(*Ingrese el precio)</span></label>
                            <input type="number" min="0" value="0" step="any" class="form-control" v-model="precio">
                        </div>
                    </div>
                    <div class="col-sm-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Descuento (%)</strong></label>
                            <input type="number" min="0" value="0" class="form-control" v-model="descuento">
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
                                    <th v-if="moneda!='Peso Mexicano'">
                                        Precio m<sup>2</sup> {{moneda}}
                                    </th>
                                    <th v-else>
                                        Precio m<sup>2</sup>
                                    </th>
                                    <th>Descuento </th>
                                    <th>Ubicacion</th>
                                    <th>SubTotal</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="(detalle,index) in arrayDetalle" :key="detalle.id">
                                    <td>
                                        <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                            <i class="icon-close"></i>
                                        </button>&nbsp;
                                        <button type="button" @click="abrirModal2(index)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;
                                        <button type="button" class="btn btn-warning btn-sm" @click="abrirModal4(index)">
                                            <i class="icon-crop"></i>
                                        </button>
                                    </td>
                                    <td v-text="detalle.categoria"></td>
                                    <td v-text="detalle.sku"></td>
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
                                   <td>
                                        <span style="color:red;" v-show="detalle.precio<=0">Ingrese el precio</span>
                                        <input v-model="detalle.precio" min="0" step="any" type="number" class="form-control">
                                   </td>
                                    <td>
                                        <span style="color:red" v-show="detalle.descuento>(detalle.precio * detalle.cantidad)">Descuento superior al subtotal!</span>
                                        <input v-model="detalle.descuento" min="0" step="any" type="number" class="form-control">
                                    </td>
                                    <td v-text="detalle.ubicacion"></td>
                                    <td>
                                       {{ (( (detalle.precio * detalle.cantidad) * detalle.metros_cuadrados) - detalle.descuento) }}
                                    </td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Parcial:</strong></td>
                                    <td>$ {{total_parcial=(total-total_impuesto).toFixed(2)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total IVA:</strong></td>
                                    <td>$ {{total_impuesto=((total * parseFloat(impuesto))/(1+parseFloat(impuesto))).toFixed(2)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Neto:</strong></td>
                                    <td>$ {{total=(calcularTotal.toFixed(2))}}</td>
                                </tr>
                               <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Metros<sup>2</sup> : </strong></td>
                                    <td> {{ metrosTotales.toFixed(4)}} </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="14" class="text-center">
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
                    <div class="col-md-4 text-center float-right">
                        <label for="exampleFormControlTextarea2"><strong>Observaciones Internas</strong></label>
                        <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacionpriv"></textarea>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="verCotizacion(cotizacion_id)"  class="btn btn-secondary">Cancelar</button>
                        <button type="button" class="btn btn-primary" @click="registrarVenta()">Registrar Venta</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin Nueva Venta -->

      </div>
      <!-- Fin ejemplo de tabla Listado -->
    </div>
    <!-- VENTANAS MODAL -->
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
                                    </template>
                                    <template v-else-if="zona== 'SLP'">
                                        <option value="San Luis">San Luis</option>
                                    </template>
                                    <template v-else-if="zona== 'AGS'">
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
                                <th>Terminado</th>
                                <th>Ubicacion</th>
                                <th>Comprometido</th>
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
                                <td v-text="articulo.terminado"></td>
                                <td v-text="articulo.ubicacion"></td>
                                <td>
                                <div v-if="articulo.comprometido">
                                    <span class="badge badge-success">SI</span>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">NO</span>
                                </div>
                                </td>
                            </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="10" class="text-center">
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
                            <td v-text="categoria"></td>
                            <!-- <select disabled class="form-control selectDetalle" v-model="idcategoria">
                                <option value="0" disabled>Seleccione un material</option>
                                <option class="text-center" v-for="categoria in arrayCategoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                            </select> -->

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
                            <td v-text="metros_cuadrados"></td>
                        </tr>
                        <tr >
                            <td><strong>ESPESOR</strong></td>
                            <td v-text="espesor"> </td>
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
                            <td><strong>Cantidad</strong></td>
                            <td v-text="cantidad"></td>
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

    <!--Inicio del modal-cortar placa articulos-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal4}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-warning modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal + sku"></h4>
                    <button type="button" v-if="validatedA==0 && validatedB==0" class="close" @click="cerrarModal4()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md">
                            <h1 class="text-center" v-text="sku"></h1>
                            <template v-if="file">
                                <lightbox class="m-0" album="" :src="'http://inventariostroystone.com/images/'+file">
                                    <img class="img-responsive imgcenter" width="250px" :src="'http://inventariostroystone.com/images/'+file">
                                </lightbox>&nbsp;
                            </template>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-center table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No° Placa</th>
                                <th>Código de material</th>
                                <th>Material</th>
                                <th>Largo</th>
                                <th>Alto</th>
                                <th>Metros<sup>2</sup></th>
                                <th>Espesor</th>
                                <th>Terminado</th>
                                <th>Stock</th>
                                <th>Precio m<sup>2</sup></th>
                                <th>Ubicacion</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td v-text="codigo"></td>
                                <td v-text="sku"></td>
                                <td v-text="categoria"></td>
                                <td v-text="largo"></td>
                                <td v-text="alto"></td>
                                <td v-text="metros_cuadrados"></td>
                                <td v-text="espesor"></td>
                                <td v-text="terminado"></td>
                                <td v-text="stock"></td>
                                <td v-text="precio"></td>
                                <td v-text="ubicacion"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="container">
                        <form action method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-4 form-control-label mb-3" for="text-input">Metros <sup> 2</sup> Restantes</label>
                                <div class="col-md-4 mb-3">
                                    <input type="number" readonly :value="calcularMtsRestantes" class="form-control"/>
                                </div>&nbsp;

                                <span class="col-md-12 text-center" style="color:red;" v-show="metros_cuadradosA+metros_cuadradosB<metros_cuadrados">
                                    <strong>(*La distrubucion de medidas no completa el tamaño original de la placa)</strong>
                                </span>
                                <span class="col-md-12 text-center" style="color:red;" v-show="metros_cuadradosA+metros_cuadradosB>metros_cuadrados">
                                    <strong>(*La distrubucion de medidas supera el tamaño original de la placa)</strong>
                                </span>
                            <!-- ArticuloA -->
                                <div class="col-md-6 border mb-2">
                                    <h3 class="text-center" v-text="sku + ' A'"></h3>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="codigoA==''">(*Ingrese el código, recuerde debe ser único)</span>

                                        <label class="col-md-4 form-control-label" for="text-input">No° de placa</label>
                                        <div class="col-md-8">
                                            <input type="text" v-model="codigoA" :disabled="validatedA == 1" class="form-control" placeholder="Código de barras"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="largoA==0">(*Ingrese el largo de la mitad A)</span>
                                        <label class="col-md-4 form-control-label" for="text-input">Largo</label>
                                        <div class="col-md-8">
                                            <input type="number" v-model="largoA" :disabled="validatedA == 1" min="1" class="form-control" placeholder=""/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="altoA==0">(*Ingrese el alto de la mitad A)</span>
                                        <label class="col-md-4 form-control-label" for="text-input">Alto</label>
                                        <div class="col-md-8">
                                            <input type="number" v-model="altoA" :disabled="validatedA == 1" min="1" class="form-control" placeholder=""/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-4 form-control-label" for="text-input">Metros<sup>2</sup></label>
                                        <div class="col-md-8">
                                            <input type="number" readonly :value="calcularMtsA" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="precioA==0">(*Ingrese el precio de la mitad A)</span>
                                        <label class="col-md-4 form-control-label" for="text-input">Precio m<sup>2</sup></label>
                                        <div class="col-md-8">
                                            <input type="number" v-model="precioA" :disabled="validatedA == 1"  min="1" class="form-control" placeholder=""/>
                                        </div>
                                    </div>
                                    <button type="button" v-if="validatedA==0" class="btn btn-primary float-right mb-2" @click="registrarArticuloA()">Guardar</button>&nbsp;
                                </div>
                            <!-- ArticuloB -->
                                <div class="col-md-6 border mb-2">
                                    <h3 class="text-center" v-text="sku + ' B'"></h3>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="codigoB==''">(*Ingrese el código, recuerde debe ser único)</span>
                                        <label class="col-md-4 form-control-label"  for="text-input">No° de placa</label>
                                        <div class="col-md-8">
                                            <input type="text" v-model="codigoB" :disabled="validatedB == 1" class="form-control" placeholder="Código de barras"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="largoB==0">(*Ingrese el largo de la mitad B)</span>
                                        <label class="col-md-4 form-control-label" for="text-input">Largo</label>
                                        <div class="col-md-8">
                                            <input type="number" v-model="largoB" :disabled="validatedB == 1" min="1" class="form-control" placeholder=""/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="altoB==0">(*Ingrese el alto de la mitad B)</span>
                                        <label class="col-md-4 form-control-label" for="text-input">Alto</label>
                                        <div class="col-md-8">
                                            <input type="number" v-model="altoB" :disabled="validatedB == 1" min="1" class="form-control" placeholder=""/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-4 form-control-label" for="text-input">Metros<sup>2</sup></label>
                                        <div class="col-md-8">
                                            <input type="number" readonly :value="calcularMtsB" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <span class="col-md-12" style="color:red;" v-show="precioB==0">(*Ingrese el precio de la mitad B)</span>
                                        <label class="col-md-4 form-control-label" for="text-input">Precio m <sup>2</sup></label>
                                        <div class="col-md-8">
                                            <input type="number" v-model="precioB" :disabled="validatedB == 1" min="1" class="form-control" placeholder=""/>
                                        </div>
                                    </div>
                                    <button type="button" v-if="validatedB==0" class="btn btn-primary float-right mb-2" @click="registrarArticuloB()">Guardar</button>&nbsp;&nbsp;
                                </div>
                            </div>
                        </form>
                        <hr class="d-block d-sm-block d-md-none">
                        <div class="float-right d-block d-sm-block d-md-none">
                            <button type="button" v-if="validatedA==0 && validatedB==0" class="btn btn-secondary" @click="cerrarModal4()">Cerrar</button>
                            <button type="button" v-if="validatedA==1 || validatedB==1" class="btn btn-primary" @click="actualizarArticulo(),eliminarDetalle(ind)">Actualizar</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" v-if="validatedA==0 && validatedB==0" class="btn btn-secondary" @click="cerrarModal4()">Cerrar</button>
                    <button type="button" v-if="validatedA==1 || validatedB==1" class="btn btn-primary" @click="actualizarArticulo(),eliminarDetalle(ind)">Actualizar</button>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
      <!-- /.modal-dialog -->
    </div>
    <!--Fin del modal-->

    <!--Inicio del modal listar articulos-cotizados-->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal5}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal5()" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterioA">
                                    <option value="sku">Código de material</option>
                                    <option value="codigo">No° de placa</option>
                                    <option value="descripcion">Descripción</option>
                                </select>
                                <input type="text" v-model="buscarA" @keyup.enter="listarArticuloCotizado(1,buscarA,criterioA)" class="form-control" placeholder="Texto a buscar">
                                <button type="submit" @click="listarArticuloCotizado(1,buscarA,criterioA)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>&nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-center table-hover">
                            <thead>
                            <tr class="text-center">
                                <th>No° Placa</th>
                                <th>Código de material</th>
                                <th>Material</th>
                                <th>Metros<sup>2</sup></th>
                                <th>Terminado</th>
                                <th>Ubicacion</th>
                                <th>No° Cotización</th>
                                <th>Cliente</th>
                                <th>Comprometido</th>
                            </tr>
                            </thead>
                            <tbody v-if="arrayArticulo.length">
                                <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                    <td v-text="articulo.codigo"></td>
                                    <td v-text="articulo.sku"></td>
                                    <td v-text="articulo.nombre_categoria"></td>
                                    <td v-text="articulo.metros_cuadrados"></td>
                                    <td v-text="articulo.terminado"></td>
                                    <td v-text="articulo.ubicacion"></td>
                                    <td v-text="articulo.cotizacion"></td>
                                    <td v-text="articulo.cliente"></td>
                                    <td>
                                    <div v-if="articulo.comprometido">
                                        <span class="badge badge-success">SI</span>
                                    </div>
                                    <div v-else>
                                        <span class="badge badge-danger">NO</span>
                                    </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <strong>NO hay artículos cotizados o con ese criterio...</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Paginacion MODAL Cotizados -->
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="paginationartcot.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtCot(paginationartcot.current_page - 1,buscarA,criterioA)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumberArtCot" :key="page" :class="[page == isActivedArtCot ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtCot(page,buscarA,criterioA)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="paginationartcot.current_page < paginationartcot.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPaginaArtCot(paginationartcot.current_page + 1,buscarA,criterioA)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal5()">Cerrar</button>
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
import VueLightbox from 'vue-lightbox';
import moment from 'moment';
import ToggleButton from 'vue-js-toggle-button';
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import 'tui-editor/dist/tui-editor.css';
import 'tui-editor/dist/tui-editor-contents.css';
import 'codemirror/lib/codemirror.css';
import 'highlight.js/styles/github.css';
import Editor from '@toast-ui/vue-editor/src/Editor.vue';
import VueBarcode from 'vue-barcode';
import { Viewer } from '@toast-ui/vue-editor';
Vue.component("Lightbox",VueLightbox);
Vue.use(ToggleButton);
Vue.use(datePicker);
export default {
    data() {
        return {
            cotizacion_id: 0,
            idcliente: 0,
            rfc_cliente : "",
            cfdi_cliente : "",
            tipo_cliente : "",
            telefono_cliente : "",
            contacto_cliente : "",
            telcontacto_cliente : "",
            obs_cliente: "",
            cliente: '',
            user: '',
            tipo_comprobante: "COTIZACION",
            num_comprobante: "",
            title : '',
            content : '',
            impuesto: 0.16,
            totalImpuesto : 0,
            totalParcial : 0,
            descuento : 0,
            flete : 0,
            instalacion : 0,
            moneda : 'Peso Mexicano',
            tipo_cambio : 0,
            observacion : '',
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
            fecha_llegada : '',
            vigencia : '',
            file : '',
            imagenMinatura : '',
            arrayCategoria : [],
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
            condicion : 0,
            precio_venta : 0,
            cantidad : 0,
            total_impuesto : 0.0,
            total_parcial : 0.0,
            divImp: 0.0,
            total: 0.0,
            neto: 0.0,
            forma_pago : "Efectivo",
            tiempo_entrega : "",
            lugar_entrega : "",
            precio: 0.0,
            aceptado : 0,
            stock : 0,
            comprometido : 0,
            descripcion : "",
            arrayArticulo : [],
            arrayCotizacion : [],
            arrayDetalle : [],
            arrayCliente : [],
            listado : 1,
            modal: 0,
            modal2: 0,
            modal3: 0,
            modal4: 0,
            modal5: 0,
            ind : '',
            tituloModal: "",
            tipoAccion: 0,
            errorCotizacion: 0,
            errorMostrarMsjCotizacion: [],
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
            paginationartcot : {
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
            bodega : '',
            categoriaFilt : 0,
            zona : "GDL",
            areaUs : "",
            areaUsC : "",
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
            estadoVn : "",
            CodeDate : "",
            vig : "",
            pastDays : 0,
            obsEditable : 0,
            acabado: "",
            observacionpriv : "",
            num_cheque : "",
            banco : "",
            tipo_facturacion : "",
            errorVenta: 0,
            errorMostrarMsjVenta: [],
            sigNum : 0,
            sigNumV : 0,
            estadoCotizacion : "",
            email_cliente : "",
            ispecial : false,
        };
    },
    components: {
        vSelect,
        'barcode': VueBarcode,
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
        isActivedArtCot: function(){
            return this.paginationartcot.current_page;
        },
        pagesNumberArtCot: function() {
            if(!this.paginationartcot.to) {
                return [];
            }

            var from = this.paginationartcot.current_page - this.offset;
            if(from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if(to >= this.paginationartcot.last_page){
                to = this.paginationartcot.last_page;
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
            let subtotal = 0;
            let iva = parseFloat(me.impuesto) + 1;
            for(var i=0;i<me.arrayDetalle.length;i++){
                subtotal += (((me.arrayDetalle[i].precio * me.arrayDetalle[i].metros_cuadrados) * me.arrayDetalle[i].cantidad)-me.arrayDetalle[i].descuento);
                resultado = subtotal * iva;
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
        calcularMtsA : function(){
            let me=this;
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
        cacularPrecioExtranjero : function(){
            let me=this;
            let precioExt = 0;

            if(me.moneda != 'Peso Mexicano'){
                precioExt = (precioExt + (me.precio / me.tipo_cambio));
                me.precio = Math.ceil(precioExt);
            }else{
                precioExt = me.precio;
            }
            return Math.ceil(precioExt);
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
        metrosTotales : function(){
            let me=this;
            let resultado = 0;
            for(var i=0;i<me.arrayDetalle.length;i++){
                resultado += parseFloat(me.arrayDetalle[i].metros_cuadrados);
            }
            return resultado;
        }
    },
    methods: {
        listarCotizacion (page,buscar,criterio,estadoCotizacion){
            let me=this;
            var url= '/cotizacionproyecto?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='+ estadoCotizacion;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayCotizacion = respuesta.cotizaciones.data;
                me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
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
            me.rfc_cliente =  val1.rfc;
            me.tipo_cliente = val1.tipo;
            me.telefono_cliente = val1.telefono;
            me.contacto_cliente = val1.company;
            me.telcontacto_cliente = val1.tel_company;
            me.obs_cliente = val1.observacion;
            me.cfdi_cliente =  val1.cfdi;
            me.cliente =  val1.nombre;
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
                    me.comprometido = me.arrayArticulo[0]['comprometido'];
                }else{
                    me.articulo = 'No existe este artículo';
                    me.idarticulo = 0;
                }
            })
            .catch(function (error) {
                console.log(error);
            });


        },
        cambiarPagina(page,buscar,criterio,estadoCotizacion){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarCotizacion(page,buscar,criterio,estadoCotizacion);
        },
        cambiarPaginaArtCot(page,buscar,criterio){
            let me = this;
            //Actualiza la página actual
            me.paginationartcot.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticuloCotizado(page,buscar,criterio);
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
            if(me.idarticulo == 0 || me.precio == 0 || me.cantidad == 0){
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
                    me.comprometido = 0;

                }else{
                    if(me.cantidad > me.stock){
                        Swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'La cantidad excede las placas disponibles de este material!',
                        });
                    }else{
                        if(me.comprometido == 1){
                            Swal.fire({
                                type: 'error',
                                title: 'Error...',
                                text: 'Este articulo esta comprometido!',
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
                            Swal.fire({
                                type: 'success',
                                title: `Añadido... ${ me.codigo } `,
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
                            me.observaciondescripcion   = "";
                            me.observacion = "";
                            me.fecha_llegada = "";
                            me.comprometido = 0;

                        }
                    }
                }
            }
        },
        registrarArticulos() {
            let me = this;

            axios.post("/articulo/registrarDetalle", {
                'data' : this.arrayDetalle
            })
            .then(function(response) {
                me.registrarIngreso();
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        registrarCotizacion(){

            if (this.validarCotizacion()) {
                return;
            }
            let me = this;
            var flet = 0;
            var insta = 0;

            var numcomp = "C-".concat(me.CodeDate,"-",me.num_comprobante);

            if(this.flete == true){
                flet = 1;
            }

            if(this.instalacion == true){
                insta = 1;
            }

            axios.post('/cotizacionproyecto/registrar',{
                'idcliente': this.idcliente,
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'impuesto' : this.impuesto,
                'total' : this.total,
                'forma_pago' : this.forma_pago,
                'title'      : this.title,
                'content'    : this.content,
                'flet'       : flet,
                'insta'      : insta,
                'neto'       : this.neto,
                'vigencia'   : this.vigencia,
                'tiempo_entrega' : this.tiempo_entrega,
                'lugar_entrega' : this.lugar_entrega,
                'moneda' : this.moneda,
                'tipo_cambio' : this.tipo_cambio,
                'observacion' : this.observacion,
                'data': this.arrayDetalle
            }).then(function(response) {
                me.ocultarDetalle();
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Cotización registrada con éxito!!',
                });
                me.listarCotizacion(1,'','num_comprobante','');

            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarCotizacion(id) {

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de anular esta cotizacion?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/cotizacionproyecto/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarCotizacion(1,'','num_comprobante','');
                        swal.fire(
                        'Anulado!',
                        'La cotizacion ha sido anulado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        desactivarCotizacionAnulada(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "Esta cotizacion ya se vencio será cancelada",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/cotizacionproyecto/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.ocultarDetalle();
                        me.listarCotizacion(1,'','num_comprobante','');
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        validarCotizacion() {
            let me = this;
            var art;

            me.errorCotizacion = 0;
            me.errorMostrarMsjCotizacion = [];

            me.arrayDetalle.map(function(x){
                if(x.cantidad > x.stock){
                    art ="La cantidad del articulo " + x.codigo + " supera las cantidades disponibles.";
                    me.errorMostrarMsjCotizacion.push(art);
                }
            });

            if (me.idcliente==0) me.errorMostrarMsjCotizacion.push("Seleccione un cliente");
            if (me.tipo_comprobante==0) me.errorMostrarMsjCotizacion.push("Seleccione un tipo de documento.");
            if (!me.num_comprobante) me.errorMostrarMsjCotizacion.push("Ingrese el numero de cotizacion");
            if (!me.impuesto) me.errorMostrarMsjCotizacion.push("Ingrese el impuesto de la cotizacion");
            if (me.arrayDetalle.length<=0) me.errorMostrarMsjCotizacion.push("Introdusca articulos para la cotizacion");
            if (me.vigencia == '') me.errorMostrarMsjCotizacion.push("Seleccione la vigencia de la cotizacion");

            if (me.errorMostrarMsjCotizacion.length) me.errorCotizacion = 1;

            return me.errorCotizacion;
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
            this.cantidad = 0;
            this.file = '';
            this.origen = '';
            this.contenedor = '';
            this.fecha_llegada = '';
            this.ubicacion = '';
            this.arrayDetalle = [];
            this.idproveedor = 0;
            this.num_comprobante = (parseInt(this.sigNum)+1);
            this.comprometido = 0;
            this.lugar_entrega = '';
            this.tiempo_entrega = '';
            this.forma_pago = 'Efectivo';
            this.total_impuesto = 0;
            this.total_parcial = 0;
            this.vigencia = '';
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
            this.vigencia = "";
            this.cantidad = 0;
            this.file = '';
            this.origen = '';
            this.contenedor = '';
            this.fecha_llegada = '';
            this.vigencia = '';
            this.ubicacion = '';
            this.moneda = 'Peso Mexicano';
            this.tipo_cambio = '0';
            this.stock = 0;
            this.cliente = 0;
            this.categoria = 0;
            this.observacion = "";
            this.arrayDetalle = [];
            this.errorCotizacion =0;
            this.errorMostrarMsjCotizacion = [];
            this.num_comprobante = 0;
            this.aceptado = 0;
            this.comprometido = 0;
            this.btnEntrega =  false;
            this.total = 0;
            this.totalImpuesto = 0;
            this.totalParcial = 0;
            this.arrayDetalle = [];
            this.total_impuesto = 0;
            this.total_parcial = 0;
            this.impuesto = 0.16;
            this.observacion = "";
            this.rfc_cliente = "";
            this.cfdi_cliente = "";
            this.tipo_cliente = "";
            this.telefono_cliente = "";
            this.contacto_cliente = "";
            this.telcontacto_cliente = "";
            this.obs_cliente = "";
            this.listarCotizacion(1,this.buscar, this.criterio,this.estadoCotizacion);
            this.getLastNum();
            this.email_cliente = "";
        },
        verCotizacion(id){

            let me = this;
            me.listado = 2;

            //Obtener los datos del ingreso
            var arrayCotizacionT=[];
            var url= '/cotizacionproyecto/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayCotizacionT = respuesta.cotizacion;

                var fechaform  = arrayCotizacionT[0]['fecha_hora'];
                var vigeformt  = arrayCotizacionT[0]['vigencia'];
                var total_parcial = 0;

                me.cotizacion_id = arrayCotizacionT[0]['id'];
                me.cliente = arrayCotizacionT[0]['nombre'];
                me.tipo_comprobante=arrayCotizacionT[0]['tipo_comprobante'];
                me.num_comprobante=arrayCotizacionT[0]['num_comprobante'];
                me.user=arrayCotizacionT[0]['usuario'];
                me.impuesto = arrayCotizacionT[0]['impuesto'];
                me.title = arrayCotizacionT[0]['title'];
                me.neto = arrayCotizacionT[0]['neto'];
                me.content = arrayCotizacionT[0]['content'];
                me.total = arrayCotizacionT[0]['total'];
                me.flete = arrayCotizacionT[0]['flete'];
                me.instalacion = arrayCotizacionT[0]['instalacion'];
                me.forma_pago = arrayCotizacionT[0]['forma_pago'];
                me.lugar_entrega = arrayCotizacionT[0]['lugar_entrega'];
                me.tiempo_entrega = arrayCotizacionT[0]['tiempo_entrega'];
                me.aceptado = arrayCotizacionT[0]['aceptado'];
                me.moneda = arrayCotizacionT[0]['moneda'];
                me.tipo_cambio = arrayCotizacionT[0]['tipo_cambio'];
                me.observacion = arrayCotizacionT[0]['observacion'];
                me.estadoVn = arrayCotizacionT[0]['estado'];
                me.rfc_cliente = arrayCotizacionT[0]['rfc'];
                me.cfdi_cliente = arrayCotizacionT[0]['cfdi'];
                me.tipo_cliente = arrayCotizacionT[0]['tipo'];
                me.idcliente = arrayCotizacionT[0]['idcliente'];
                me.email_cliente =  arrayCotizacionT[0]['EmailC'];
                moment.locale('es');
                me.fecha_llegada=moment(fechaform).format('dddd DD MMM YYYY hh:mm:ss a');
                me.vigencia=moment(vigeformt).format('dddd DD MMM YYYY');
                me.vig = moment(vigeformt).format('YY-MM-DD');

                me.getPastDays(me.vig,me.cotizacion_id,me.estadoVn);

                var imp =   parseFloat(me.impuesto = arrayCotizacionT[0]['impuesto']);

                me.divImp = imp + 1;

                if(me.aceptado ==1){
                    me.btnEntrega = true;
                }
            })
            .catch(function (error) {
                console.log(error);
            });

            //Obtener los detalles del ingreso
            var url= '/cotizacionproyecto/obtenerDetalles?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayDetalle = respuesta.detalles;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        getPastDays(dateVig,id,estado){
            let me = this;
            let vdate = dateVig;
            let date = "";
            moment.locale('es');
            date = moment().format('YY-MM-DD');

            if(date > vdate){
                console.log("Cotizacion vencida");
                if(estado == 'Registrado'){
                    me.desactivarCotizacionAnulada(id);
                }else{
                    Swal.fire({
                    type: 'error',
                    title: 'Atención!...',
                    text: 'Esta cotizacion esta vencida!!',
                })
                }

            }else if(date == vdate){
               /*  console.log("La cotizacion vence hoy!"); */
            }else{
                /* console.log("Disponible"); */
            }

        },
        cerrarModal() {
            this.modal = 0;
            this.buscarA = "";
            this.acabado = "";
        },
        abrirModal() {
            this.arrayArticulo=[];
            this.modal = 1;
            this.tituloModal = "Seleccionar Artículos";
            this.listarArticulo(1,'','sku','','',0);
        },
        listarArticulo (page,buscar,criterio,bodega,acabado,idcategoria){
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
                if(data['comprometido'] ==1){
                    Swal.fire({
                        type: 'error',
                        title: 'Lo siento...',
                        text: 'Esta placa esta comprometida!',
                    })
                }else{
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
                        type: 'success',
                        title: `Añadido... ${ data['codigo'] } `,
                    });
                }
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
            me.cantidad         = me.arrayDetalle[index]['cantidad'];
            me.file             = me.arrayDetalle[index]['file'];
            me.descripcion      = me.arrayDetalle[index]['descripcion'];
            me.observacion      = me.arrayDetalle[index]['observacion'];
            me.condicion        = me.arrayDetalle[index]['condicion'];
            me.selectCategoria();
        },
        cerrarModal3() {
            this.modal3 = 0;
            this.sku = '';
            this.codigo = '';
            this.idcategoria = 0;
            this.largo = 0;
            this.alto = 0;
            this.terminado = '';
            this.espesor = 0;
            this.precio_venta = 0;
            this.metros_cuadrados = 0;
            this.stock = 0;
            this.file = '';
            this.descripcion = '';
            this.ind = '';
            this.categoria = '';
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
        cerrarModal5() {
            this.modal5 = 0;
        },
        abrirModal5() {
            this.arrayArticulo=[];
            this.modal5 = 1;
            this.tituloModal = "Artículos Cotizados";
            this.listarArticuloCotizado(1,'','sku');
        },
        listarArticuloCotizado(page,buscar,criterio){
            let me=this;
            var url= '/articulo/listarArticuloCotizado?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayArticulo = respuesta.articulos.data;
                me.paginationartcot= respuesta.pagination;
                me.areaUsC = respuesta.userarea;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        registrarArticuloA(){
            let me = this;
            if(me.largoA == 0 || me.altoA == 0 || me.precioA == 0 || me.codigoA == ""){
            }else{
                axios.post("/articulo/registrar",{
                    'idcategoria': this.idcategoria,
                    'codigo': this.codigoA,
                    'sku' : this.sku,
                    'terminado' : this.terminado,
                    'largo' : this.largoA,
                    'alto' : this.altoA,
                    'metros_cuadrados' : this.metros_cuadradosA,
                    'espesor' : this.espesor,
                    'precio_venta' : this.precioA,
                    'ubicacion' : this.ubicacion,
                    'stock': this.stock,
                    'descripcion': this.descripcion,
                    'observacion' : this.observacion,
                    'origen' : this.origen,
                    'contenedor' : this.contenedor,
                    'fecha_llegada' : this.fecha_llegada
                }).then(function (response) {
                    me.validatedA = 1;
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        registrarArticuloB(){
            let me = this;
            if(me.largoB == 0 || me.altoB == 0 || me.precioB == 0 || me.codigoB == ""){
            }else{
                axios.post("/articulo/registrar",{
                    'idcategoria': this.idcategoria,
                    'codigo': this.codigoB,
                    'sku' : this.sku,
                    'terminado' : this.terminado,
                    'largo' : this.largoB,
                    'alto' : this.altoB,
                    'metros_cuadrados' : this.metros_cuadradosB,
                    'espesor' : this.espesor,
                    'precio_venta' : this.precioB,
                    'ubicacion' : this.ubicacion,
                    'stock': this.stock,
                    'descripcion': this.descripcion,
                    'observacion' : this.observacion,
                    'origen' : this.origen,
                    'contenedor' : this.contenedor,
                    'fecha_llegada' : this.fecha_llegada
                }).then(function (response) {
                    me.validatedB = 1;
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        actualizarArticulo() {
            if (this.validatedA == 0 || this.validatedB == 0) {
                Swal.fire({
                    type: 'error',
                    title: 'Error...',
                    text: 'Se deben guardar cambios en la placa A Y B!',
                })
                return;
            }
            let me = this;
            axios.put("/articulo/actualizarCorte", {
                'stock': this.stock,
                'id': this.idarticulo
            })
            .then(function(response) {
                me.buscarA = me.codigoA;
                me.criterioA = 'codigo';
                me.bodega = '';
                me.acabado = '';
                me.cerrarModal4();
                me.abrirModal();
                me.listarArticulo(1,me.buscarA,me.criterioA,me.bodega ,me.acabado,me.categoriaFilt);
                me.validatedA = 0;
                me.validatedB = 0;

            })
            .catch(function(error) {
                console.log(error);
            });
        },
        aceptarCotizacion(id){
          let me = this;
            if(me.btnEntrega == true){
                me.aceptado = 1;
            }else{
                me.aceptado = 0;
            }
            axios.put('/cotizacionproyecto/aceptarCotizacion',{
                'id': id,
                'aceptado' : this.aceptado
            }).then(function (response) {
                me.listarCotizacion(1,'','num_comprobante','');
            }).catch(function (error) {
                console.log(error);
            });
        },
        pdfCotizacion(id){
            window.open('/cotizacionproyecto/pdf/'+id,'_blank');
        },
        getLastNum(){
            let me=this;
            var url= '/cotizacionproyecto/nextNum';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.sigNum = respuesta.SigNum;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        editObservacion(){
            let me = this;
            me.obsEditable = 1;
        },
        actualizarObservacion(id){
            let me = this;
            axios.post('/cotizacionproyecto/actualizarObservacion',{
                'id': id,
                'observacion' : this.observacion
            }).then(function (response) {
                me.obsEditable = 0;
            }).catch(function (error) {
                console.log(error);
            });
        },
        convertDateCotizacion(date){
            let me=this;
            moment.locale('es');
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            return datec;
        },
        convertDateVigencia(date){
            let me=this;
            moment.locale('es');
            var datec = moment(date).format('DD MMM YYYY');
            return datec;
        },
        crearVenta(){
            let me = this;
            var comp = this.num_comprobante;
            this.listado = 3;
            this.tipo_comprobante = "PRESUPUESTO";
            this.observacionpriv = "Venta de la cotización " + comp;
            this.observacion = "";
            this.getLastNumVenta();
            this.num_comprobante = (parseInt(this.sigNumV)+1);
        },
        getLastNumVenta(){
            let me=this;
            var url= '/venta/nextNum';
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.sigNumV = respuesta.SigNum;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        registrarVenta(){

            if (this.validarVenta()) {
                return;
            }

            if(this.forma_pago != 'Cheque'){
                this.num_cheque = 0;
                this.banco = '';
            }

            let me = this;
            var is_special = 0;
            if (me.ispecial) {
                is_special = 1;
            }

            var numcomp = "V-".concat(me.CodeDate,"-",me.num_comprobante);
            console.log(numcomp);

            axios.post('/venta/registrar',{
                'idcliente': this.idcliente,
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'impuesto' : this.impuesto,
                'total' : this.total,
                'forma_pago' : this.forma_pago,
                'tiempo_entrega' : this.tiempo_entrega,
                'lugar_entrega' : this.lugar_entrega,
                'moneda' : this.moneda,
                'tipo_cambio' : this.tipo_cambio,
                'observacion' : this.observacion,
                'observacionpriv' : this.observacionpriv,
                'num_cheque'  : this.num_cheque,
                'banco'       : this.banco,
                'special'   : is_special,
                'tipo_facturacion' : this.tipo_facturacion,
                'data': this.arrayDetalle
            }).then(function(response) {
                Swal.fire({
                    type: 'success',
                    title: 'Completado...',
                    text: 'Venta registrada con el comprobante: ' + numcomp,
                })
                me.desactivarCotizacionVenta(me.cotizacion_id);
                me.idcliente = 0;
                me.tipo_comprobante = "Cotizacion";
                me.num_comprobante = 0;
                me.impuesto = 0.16;
                me.total = 0.0;
                me.idarticulo = 0;
                me.articulo = "";
                me.cantidad = 0;
                me.vigencia = "";
                me.precio = 0;
                me.stock = 0;
                me.observacion = "";
                me.descuento = 0;
                me.vigencia = "";
                me.forma_pago = "Efectivo";
                me.tiempo_entrega = "";
                me.lugar_entrega = "";
                me.aceptado = 0;
                me.moneda = "Peso Mexicano";
                me.tipo_cambio = "";
                me.comprometido = 0;
                me.arrayDetalle = [];
                me.obsEditable = 0;
                me.total_impuesto = 0;
                me.total_parcial = 0;
                me.tipo_facturacion = "";

            })
            .catch(function(error) {
                console.log(error);
            });
        },
        validarVenta() {
            let me = this;
            var art;

            me.errorVenta = 0;
            me.errorMostrarMsjVenta = [];

            me.arrayDetalle.map(function(x){
                if(x.cantidad > x.stock){
                    art ="La cantidad del articulo " + x.codigo + " supera las cantidades disponibles.";
                    me.errorMostrarMsjVenta.push(art);
                }
            });

            if (me.idcliente==0) me.errorMostrarMsjVenta.push("Seleccione un cliente");
            if (me.tipo_comprobante==0) me.errorMostrarMsjVenta.push("Seleccione un comprobante.");
            if (!me.num_comprobante) me.errorMostrarMsjVenta.push("Ingrese el numero de comprobante");
            if (!me.tipo_facturacion) me.errorMostrarMsjVenta.push("Seleccione el tipo de facturación");
            if (me.arrayDetalle.length<=0) me.errorMostrarMsjVenta.push("Introdusca articulos para la venta");
            if (me.moneda != 'Peso Mexicano') me.errorMostrarMsjVenta.push("Seleccione el tipo de cambio de la moneda");

            if (me.errorMostrarMsjVenta.length) me.errorVenta = 1;

            return me.errorVenta;
        },
        desactivarCotizacionVenta(id) {

            let me = this;
            axios.put('/cotizacionproyecto/desactivarVenta',{
                'id': id
            }).then(function (response) {
                me.ocultarDetalle();
                me.listarCotizacion(1,'','num_comprobante','');
            }).catch(function (error) {
                console.log(error);
            });
        },
        desactivarCotizacionEditar(id){

            let me = this;

            var numComp = me.num_comprobante;
            var NewComp = numComp.split("-");

            var Vigen = moment(me.vigencia).format('YYYY-MM-DD');

            /* console.log(`Vigencia =  ${ Vigen }`); */

            /* console.log(`New compNum =  ${ NewComp[2] }`); */

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de editar esta cotizacion? \n La original será cancelada y generara una nueva!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/cotizacionproyecto/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.getLastNum();
                        me.num_comprobante =   NewComp[2] + "B";
                        me.vigencia =  Vigen;
                        me.listado = 0;

                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                }
            })
        },
        sendMailCot(id,mail,cliente){
            let me = this;
            axios.post('/cotizacionproyecto/enviarCotizacionMail',{
                'id': id,
                'mail' : mail,
                'name' : cliente
            }).then(function (response) {
                Swal.fire({
                    type: 'success',
                    title: 'Enviado...',
                    text: `La cotizacion del cliente ${ cliente } se envio correctamente al correo ${ mail }`,
                })
            }).catch(function (error) {
                console.log(error);
            });
        }
    },
    mounted() {
        this.listarCotizacion(1,this.buscar, this.criterio,this.estadoCotizacion);
        this.getLastNum();
        this.getLastNumVenta();
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
