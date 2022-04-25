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
          <i class="fa fa-align-justify"></i> Ventas
          <button type="button" @click="mostrarDetalle()" class="btn btn-secondary" v-if="listado==1">
            <i class="icon-plus"></i>&nbsp;Nuevo
          </button>
           <template v-if="listado==2">
                <button type="button" class="btn btn-info float-right ml-2"
                    @click="sendMailPresupuesto(venta_id,email_cliente,cliente)" v-if="email_cliente && estadoVn == 'Registrado' ">
                    <i class="fa fa-share"></i> Enviar
                </button>&nbsp;
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
                                <option value="cliente">Cliente</option>
                                <option value="user">Usuario</option>
                                <option value="num_comprobante">No° Comprobante</option>
                                <option value="fecha_hora">Fecha</option>
                                <option value="forma_pago">Forma de pago</option>
                            </select>
                            <input type="text" v-model="buscar" @keyup.enter="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoPago)" class="form-control mb-1" placeholder="Texto a buscar...">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="estadoVenta" @change="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoPago)">
                                <option value="">Activa</option>
                                <option value="Anulada">Cancelada</option>
                            </select>
                            <button type="submit" @click="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoPago)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div class="input-group input-group-sm ml-sm-2 mr-sm-1 ml-md-2 ml-lg-5" v-if="estadoVenta!='Anulada'">
                            <select class="form-control" id="tipofact" name="tipofact" v-model="estadoEntrega" @change="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoPago)">
                                <option value="">Todo</option>
                                <option value="entregado">100%</option>
                                <option value="entrega_parcial">Parcial</option>
                                <option value="no_entregado">No entregado</option>
                            </select>
                            <button class="btn btn-sm btn-warning" type="button"><i class="fa fa-truck" aria-hidden="true"></i>&nbsp; Entregas </button>
                        </div>
                        <div class="input-group input-group-sm ml-sm-2 mr-sm-1 ml-md-2 ml-lg-5" v-if="estadoVenta!='Anulada'">
                            <select class="form-control" id="tipofact" name="tipofact" v-model="estadoPago" @change="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoPago)">
                                <option value="">Todo</option>
                                <option value="pagado">Pagado 100%</option>
                                <option value="parcial">Pagado Parcial</option>
                                <option value="nopagado">No Pagado</option>
                            </select>
                            <button class="btn btn-sm btn-success" type="button"><i class="fa fa-money" aria-hidden="true"></i>&nbsp; Pagos </button>
                        </div>
                        <div class="input-group input-group-sm mt-1 mt-sm-0 ml-md-2 ml-lg-5" v-if="estadoVenta!='Anulada' && usrol != 4">
                             <button @click="abrirModal5()" class="btn btn-success btn-sm">Reporte <i class="fa fa-file-excel-o"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Atendió</th>
                                <th>Área</th>
                                <th>Cliente</th>
                                <th>Tipo Comprobante</th>
                                <th>No° Comprobante</th>
                                <th>Fecha Hora</th>
                                <th>Total</th>
                                <th>Forma de pago</th>
                                <th>Facturación</th>
                                <th v-if="usrol == 1">Facturado</th>
                                <th>Entregado</th>
                                <th>100% Pagado</th>
                                <th>Estado</th>
                                <th>P. Especial</th>
                            </tr>
                        </thead>
                        <tbody v-if="arrayVenta.length">
                            <tr v-for="venta in arrayVenta" :key="venta.id">
                                <td>
                                    <div class="form-inline">
                                        <button type="button" class="btn btn-success btn-sm" @click="verVenta(venta.id)">
                                        <i class="icon-eye"></i>
                                        </button>&nbsp;
                                        <button type="button" class="btn btn-outline-danger btn-sm" @click="pdfVenta(venta.id)">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </button>&nbsp;
                                        <template v-if="usrol == 1">
                                           <template v-if="venta.total == venta.adeudo">
                                                <button type="button" v-if="venta.estado == 'Registrado'" class="btn btn-danger btn-sm" @click="desactivarVenta(venta.id, venta.adeudo)">
                                                    <i class="icon-trash"></i>
                                                </button>
                                           </template>
                                        </template>
                                    </div>
                                </td>
                                <td v-text="venta.usuario"></td>
                                <td v-text="venta.area"></td>
                                <td v-text="venta.nombre"></td>
                                <td v-text="venta.tipo_comprobante"></td>
                                <td v-text="venta.num_comprobante"></td>
                                <td>{{ convertDateVenta(venta.fecha_hora) }}</td>
                                <td v-text="venta.total"></td>
                                <td v-text="venta.forma_pago"></td>
                                <td v-text="venta.tipo_facturacion"></td>
                                <template v-if="usrol == 1">
                                    <td v-if="venta.facturado == 1">
                                        <span class="badge badge-success" v-if="venta.tipo_facturacion == 'Cliente'">Facturado</span>
                                    </td>
                                    <td v-else-if="venta.facturado == 0">
                                        <span class="badge badge-danger" v-if="venta.tipo_facturacion == 'Cliente'"> No Facturado</span>
                                         <span class="badge badge-info" v-if="venta.tipo_facturacion == 'Publico General'">Facturado General</span>
                                    </td>
                                </template>
                                <td v-if="venta.entregado">
                                    <span class="badge badge-success">100%</span>
                                </td>
                                <td v-else-if="venta.entrega_parcial">
                                    <span class="badge badge-warning">Parcial</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">No entregado</span>
                                </td>
                                <template v-if="venta.adeudo == 0">
                                    <td><span class="badge badge-success">100% Pagado</span></td>
                                </template>
                                <template v-else-if="venta.total == venta.adeudo">
                                    <td><span class="badge badge-danger">No Pagado</span></td>
                                </template>
                                <template v-else-if="(venta.total - venta.adeudo) < venta.total">
                                    <td><span class="badge badge-warning">Pagado Parcialmente</span></td>
                                </template>
                                <td v-if="venta.estado =='Registrado'">
                                    <span class="badge badge-success">Activa</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">Cancelada</span>
                                </td>
                                <td class="text-center">
                                    <span v-if="venta.special" class="badge badge-success">Si</span>
                                    <span v-else class="badge badge-danger">No</span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="13" class="text-center">
                                    <strong>NO hay presupuestos con ese criterio o estado...</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoVenta,estadoEntrega,estadoPago)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoVenta,estadoEntrega,estadoPago)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoVenta,estadoEntrega,estadoPago)">Sig</a>
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
                            <label for=""><strong>Cliente (*)</strong></label>
                                <v-select :on-search="selectCliente" label="nombre" :options="arrayCliente" placeholder="Buscar clientes..." :onChange="getDatosCliente">
                                </v-select>
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
                    </div>&nbsp;
                </div>
                <div class="form-group row border">
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante (*)</strong></label>
                            <select v-model="tipo_comprobante" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="PRESUPUESTO">PRESUPUESTO</option>
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
                    <div class="col-md-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Forma de pago</strong><span style="color:red;" v-show="forma_pago==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="forma_pago" v-if="otroFormPay == false">
                                <option value='' disabled>Seleccione la forma de pago</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Mixto">Mixto</option>
                            </select>
                            <div class="form-check float-left mt-1">
                                <input class="form-check-input" type="checkbox" id="chkOtherPay" v-model="otroFormPay">
                                <label class="form-check-label p-0 m-0" for="chkOtherPay"><strong>Otro</strong></label>
                            </div>
                            <textarea class="form-control rounded-0" rows="2" maxlength="256" v-model="forma_pago" v-if="otroFormPay == true"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3 text-center" v-if="forma_pago =='Cheque'">
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
                    <div class="col-md-3 text-center">
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
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Lugar de entrega</strong><span style="color:red;" v-show="lugar_entrega==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="lugar_entrega">
                                <option value='' disabled>Seleccione el lugar de entrega</option>
                                <option value="LAB TROYSTONE">LAB TROYSTONE</option>
                                <option value="LAB TROYSTONE S.L.P.">LAB TROYSTONE S.L.P.</option>
                                <option value="LAB TROYSTONE A.G.U.A.S.">LAB TROYSTONE A.G.S.</option>
                                <option value="LAB TROYSTONE P.U.E.B.L.A.">LAB TROYSTONE P.B.A.</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="form-group">
                            <label for=""><strong>Tipo de facturación</strong><span style="color:red;" v-show="tipo_facturacion==''">(*Seleccione)</span></label>
                            <select class="form-control" v-model="tipo_facturacion">
                                <option value='' disabled>Seleccione el tipo de facturación</option>
                                <option value="Publico General">Publico General</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 text-center" v-if="arrayDetalle.length">
                        <div class="form-group">
                            <label for=""><strong>Total M<sup>2</sup> </strong></label>
                            <p> {{ metrosTotales.toFixed(4) }} </p>
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
                            <label for=""><strong>Cantidad</strong> <span style="color:red;" v-show="cantidad==0">(*Ingrese)</span></label>
                            <input type="number" min="0" value="0"  class="form-control" v-model="cantidad">
                        </div>
                    </div>
                    <div class="col-sm-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Precio m<sup>2</sup></strong> <span style="color:red;" v-show="precio==0">(*Ingrese)</span></label>
                            <input type="number" min="0" value="0" step="any" class="form-control" v-model="precio">
                        </div>
                    </div>
                    <div class="col-sm-2 text-center">
                        <div class="form-group">
                            <label for=""><strong>Descuento ($)</strong></label>
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
                                       {{ ((( (detalle.precio * detalle.cantidad) * detalle.metros_cuadrados) - detalle.descuento)).toFixed(6) }}
                                    </td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="14" align="right"><strong>Total Parcial:</strong></td>
                                    <td>$ {{total_parcial=(total-total_impuesto).toFixed(6)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="14" align="right"><strong>Total IVA:</strong></td>
                                    <td>$ {{total_impuesto=((total * parseFloat(impuesto))/(1+parseFloat(impuesto))).toFixed(6)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="14" align="right"><strong>Total Neto:</strong></td>
                                    <td>$ {{total=(calcularTotal.toFixed(4))}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="14" align="right"><strong>Total Metros<sup>2</sup> : </strong></td>
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
                    <div class="col-md-4 text-center float-right">
                        <label for="exampleFormControlTextarea2"><strong>Observaciones Internas</strong></label>
                        <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacionpriv"></textarea>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button type="button" @click="ocultarDetalle()"  class="btn btn-secondary">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="registrarVenta()">Registrar Venta</button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Fin Nueva Venta -->
        <!-- Ver Venta -->
        <template v-else-if="listado==2">
            <div class="card-body">
                <div class="form-group row border">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h4>Cliente </h4>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column">
                            <div class="p-0"><p><strong v-text="cliente"></strong></p></div>
                            <div class="p-0"><p><u v-text="tipo_cliente"></u></p></div>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column">
                            <div class="p-0"><p> <strong>Teléfono:</strong> {{ telefono_cliente }}</p></div>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column" v-if="tipo_facturacion == 'Cliente'">
                            <div class="p-0">
                                <p> <strong>RFC:</strong> {{ rfc_cliente }}
                                <span style="color:red;" v-show="!rfc_cliente">(*Complete esta información)</span>
                                </p>
                            </div>
                            <div class="p-0">
                                <p> <strong>Uso CFDI: </strong> {{ cfdi_cliente }}
                                    <span style="color:red;" v-show="!cfdi_cliente">(*Complete esta información)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md">
                        <div class="d-flex flex-column" v-if="contacto_cliente">
                            <div class="p-0"><p> <strong>Contacto:</strong> {{ contacto_cliente }}</p></div>
                            <div class="p-0"><p> <strong>Tel Contacto: </strong> {{ telcontacto_cliente }}</p></div>
                        </div>
                    </div>
                </div>
                <div class="form-group row border">

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo Comprobante</strong></label>
                            <p v-text="tipo_comprobante"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Número de Presupuesto</strong></label>
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
                            <label for=""><strong>Fecha:</strong></label>
                            <p v-text="fecha_llegada"></p>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for=""><strong>Impuesto (%)</strong></label>
                            <p v-text="impuesto"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tipo de facturación</strong></label>
                            <p v-text="tipo_facturacion"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Total del Presuspuesto</strong></label>
                            <p v-text="total"></p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Forma de pago</strong></label>
                            <p v-text="forma_pago"></p>
                        </div>
                    </div>
                    <div class="col-md-2" v-if="forma_pago =='Cheque'">
                        <div class="form-group">
                            <label for=""><strong>No° de cheque</strong></label>
                            <p v-text="num_cheque"></p>
                        </div>
                    </div>
                    <div class="col-md-2" v-if="forma_pago =='Cheque'">
                        <div class="form-group">
                            <label for=""><strong>Banco</strong></label>
                            <p v-text="banco"></p>
                        </div>
                    </div>
                    <!-- Autorizar entrega -->
                    <template v-if="usrol != 4">
                        <div class="col-md-2" v-if="entregado == 0 && entregado_parcial == 0">
                            <div class="form-group">
                                <label for=""><strong>Autorizar Entrega:</strong> </label>
                                <div>
                                    <toggle-button @change="autorizarEntrega(venta_id,num_comprobante)" v-model="btnAutoEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-md-2" v-if="auto_entrega">
                            <div class="form-group">
                                <span class="badge badge-success">Entrega autorizada</span>
                            </div>
                        </div>
                    </template>
                    <!-- Status Entregas -->
                    <template v-if="usrol != 1">
                        <div class="col-md-2" v-if="adeudo == 0  && entregado == 0">
                            <div class="form-group">
                                <label for=""><strong>Entregado Parcial:</strong> </label>
                                <div v-if="pagado">
                                    <toggle-button @change="cambiarEstadoEntregaParcial(venta_id)" v-model="btnEntregaParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Pendiente de pago</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!--Cambios en pagos solo Admin puede cambiar a no entregado-->
                    <div class="col-md-2" v-if="!btnEntrega && usrol != 1">
                            <div class="form-group">
                                <label for=""><strong>Entregado 100%:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntrega(venta_id)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                            </div>
                    </div>
                    <div class="col-md-2" v-else-if="usrol == 1">
                            <div class="form-group">
                                <label for=""><strong>Entregado 100%:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntrega(venta_id)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                            </div>
                    </div>
                    <!--Fin de los cambios-->
                    <div class="col-md-2" v-if="!entregado">
                            <div class="form-group">
                                <label for=""><strong>Entregado Parcial:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntregaParcial(venta_id)" v-model="btnEntregaParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled/>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-2" v-if="!btnSpecial && usrol != 1">
                        <div class="form-group">
                            <label for=""><strong>Presupuesto Especial:</strong> </label>
                            <div>
                                <toggle-button @change="cambiarEstadoSpecial(venta_id)" v-model="btnSpecial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2" v-else-if="usrol == 1">
                        <div class="form-group">
                            <label for=""><strong>Presupuesto Especial:</strong> </label>
                            <div>
                                <toggle-button @change="cambiarEstadoSpecial(venta_id)" v-model="btnSpecial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                            </div>
                        </div>
                    </div>
                    <!-- Status Pagos -->
                    <template v-if="usrol != 1">
                        <div class="col-md-2">
                            <div class="form-group" v-if="!pagado">
                                <label for=""><strong>Pagado Parcialmente: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagadoParcial(venta_id,adeudo)" v-model="btnPagadoParcial"
                                        :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" :disabled="pago_parcial == 1"/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!--Cambios en pagos solo Admin puede cambiar a no entregado-->
                    <template v-if="usrol == 1">
                        <div class="col-md-2" v-if="!btnPagado && usrol != 1">
                            <div class="form-group" >
                                <label for=""><strong>100% Pagado: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagado(venta_id)" v-model="btnPagado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" v-else-if="usrol == 1">
                            <div class="form-group" >
                                <label for=""><strong>100% Pagado: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagado(venta_id)" v-model="btnPagado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!--Fin de los Cambios-->
                    <div class="col-md-2">
                            <div class="form-group" v-if="!pagado">
                                <label for=""><strong>Pagado Parcialmente: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagadoParcial(venta_id,adeudo)"
                                        v-model="btnPagadoParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" :disabled="pago_parcial == 1"/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                    </div>
                    <div v-if="tipo_facturacion == 'Cliente'">
                        <div class="col-md-2" v-if="usrol == 1">
                            <div class="col-md-2" v-if="!btnFactura && usrol != 1">
                                <div class="form-group">
                                        <label for=""><strong>Facturar:</strong> </label>
                                    <div>
                                        <toggle-button @change="cambiarFactura(venta_id)" v-model="btnFactura" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2" v-else-if="usrol == 1">
                                <div class="form-group">
                                        <label for=""><strong>Facturar:</strong> </label>
                                    <div>
                                        <toggle-button @change="cambiarEstadoFactura(venta_id)" v-model="btnFactura" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" :disabled="facturado == 1"  />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                            <div class="form-group" >
                                <label for=""><strong>Número de Facturación</strong></label>
                                <p v-text="num_factura"></p>
                            </div>
                    </div>
                    <div :class="{'col-md-7': pago_parcial,  'col-md-2': !pago_parcial}">
                        <template v-if="adeudo > 0">
                            <p class="float-right" style="font-size: 20px;"><span class="badge badge-danger">Adeudo: </span> {{ adeudo }}</p>
                        </template>
                        <template v-else>
                            <p class="float-right" style="font-size: 20px;"><span class="badge badge-success">Pagado al 100 %</span></p>
                        </template>
                    </div>
                    <div class="col-5"><!-- Abonos -->
                        <div id="accordion" v-if="pago_parcial" class="mb-2">
                            <div class="card m-0">
                                <div class="card-header" id="headingOne">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" style="text-decoration:none; color:#000;" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            <strong>Abonos</strong>
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                    <div class="card-body p-0">
                                        <button class="btn btn-primary btn-sm float-right m-2" @click="cambiarEstadoPagadoParcial(venta_id,adeudo)" v-if="adeudo > 0">Nuevo</button>
                                        <div class="table-responsive col-12 p-0">
                                            <table class="table table-bordered table-striped table-sm table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="10px">No°</th>
                                                        <th width="20px" v-if="usrol == 1">Opciones</th>
                                                        <th>Forma de pago</th>
                                                        <th>Fecha</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-if="arrayDepositos.length">
                                                    <tr v-for="(deposito,index) in arrayDepositos" :key="deposito.id">
                                                        <td width="10px" v-text="index + 1"></td>
                                                        <td v-if="usrol == 1">
                                                            <button type="button" class="btn btn-light btn-sm" @click="deleteDeposit(deposito.id,venta_id,deposito.total)">
                                                                <i class="icon-trash"></i>
                                                            </button> &nbsp;
                                                        </td>
                                                        <td v-text="deposito.forma_pago"></td>
                                                        <td>{{ convertDateVenta(deposito.fecha) }}</td>
                                                        <td v-text="deposito.total"></td>
                                                    </tr>
                                                    <tr style="background-color: #CEECF5;">
                                                        <td colspan="4" align="right"><strong>Abonado:</strong></td>
                                                        <td>$ {{ calcularAbonos }}</td>
                                                    </tr>
                                                    <tr style="background-color: #CEECF5;">
                                                        <td colspan="4" align="right"><strong>Adeudo:</strong></td>
                                                        <td>$ {{ adeudo }} </td>
                                                    </tr>
                                                </tbody>
                                                <tbody v-else>
                                                    <tr v-if="usrol == 1">
                                                        <td colspan="5" class="text-center">
                                                            <strong>NO hay artículos en este detalle...</strong>
                                                        </td>
                                                    </tr>
                                                    <tr v-else>
                                                        <td colspan="4" class="text-center">
                                                            <strong>NO hay artículos en este detalle...</strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
                                    <th>Código de material</th>
                                    <th>No° Placa</th>
                                    <th>Terminado</th>
                                    <th>Espesor</th>
                                    <th>largo</th>
                                    <th>Alto</th>
                                    <th>Metros <sup>2</sup></th>
                                    <th>Cantidad</th>
                                    <th>Precio m<sup>2</sup></th>
                                    <th>Descuento ($)</th>
                                    <th>Ubicacion</th>
                                    <th>SubTotal</th>

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
                                    <td v-text="detalle.precio"></td>
                                    <td v-text="detalle.descuento"></td>
                                    <td v-text="detalle.ubicacion"></td>
                                    <td>{{ ((( (detalle.precio * detalle.cantidad) * detalle.metros_cuadrados) - detalle.descuento)).toFixed(6) }}</td>
                                </tr>
                                 <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Parcial:</strong></td>
                                    <td>$ {{total_parcial = (total / divImp).toFixed(6) }}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Impuesto:</strong></td>
                                    <td>$ {{total_impuesto=((total * impuesto)/(divImp)).toFixed(6)}}</td>
                                </tr>
                                <tr style="background-color: #CEECF5;">
                                    <td colspan="13" align="right"><strong>Total Neto:</strong></td>
                                    <td>$ {{total=(calcularTotal.toFixed(4))}} </td>
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
                                    <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacion(venta_id)">
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
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for=""><strong>Lugar de entrega</strong></label>
                            <p v-text="lugar_entrega"></p>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><strong>Tiempo de entrega</strong></label>
                            <p v-text="tiempo_entrega"></p>
                        </div>
                    </div>&nbsp;
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col">
                                <label for="exampleFormControlTextarea2"><strong>Observaciones Internas</strong></label>
                            </div>
                            <div class="col-2">
                                <template v-if="obsprivEditable == 0">
                                    <button type="button" class="btn btn-warning btn-sm float-right" @click="editObservacionPriv()">
                                        <i class="icon-pencil"></i>
                                    </button>
                                </template>
                                <template v-else>
                                    <button type="button" class="btn btn-primary btn-sm float-right" @click="actualizarObservacionPriv(venta_id)">
                                        <i class="fa fa-floppy-o"></i>
                                    </button>
                                </template>&nbsp;
                            </div>
                        </div>&nbsp;
                        <template v-if="obsprivEditable == 0">
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" readonly v-model="observacionpriv"></textarea>
                        </template>
                        <template v-else>
                            <textarea class="form-control rounded-0" rows="3" maxlength="256" v-model="observacionpriv"></textarea>
                        </template>
                    </div>&nbsp;
                </div>
                <div class="form-group row">
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
                                    <template v-else-if="zona=='PBA'">
                                        <option value="Puebla">Puebla</option>
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
                                    <option value="PBA">Puebla</option>
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
                                <label class="col-md-4 mb-2 form-control-label" for="text-input">Metros <sup> 2</sup> Restantes</label>
                                <div class="col-md-4 mb-2 ">
                                    <input type="number" readonly :value="calcularMtsRestantes" class="form-control"/>
                                </div>&nbsp;

                                <span class="col-md-12 text-center" style="color:red;" v-show="metros_cuadradosA+metros_cuadradosB<metros_cuadrados">
                                    <strong>(*La distrubucion de medidas no completa el tamaño original de la placa)</strong>
                                </span>
                                <span class="col-md-12 text-center" style="color:red;" v-show="metros_cuadradosA+metros_cuadradosB>metros_cuadrados">
                                    <strong>(*La distrubucion de medidas supera el tamaño original de la placa)</strong>
                                </span>
                            <!-- ArticuloA -->
                                <div class="col-md-6 border">
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
                                <div class="col-md-6 border">
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
                            <button type="button" v-if="validatedA==1 || validatedB==1" class="btn btn-primary" @click="updateCorte(),eliminarDetalle(ind)">Actualizar</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-none d-sm-none d-md-block">
                    <button type="button" v-if="validatedA==0 && validatedB==0" class="btn btn-secondary" @click="cerrarModal4()">Cerrar</button>
                    <button type="button" v-if="validatedA==1 || validatedB==1" class="btn btn-primary" @click="updateCorte(),eliminarDetalle(ind)">Actualizar</button>
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

    <!-- Modal Seleccionar tipo de abono -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal7}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-askdep">
                <div class="modal-body ">
                    <div class="row d-flex justify-content-around">
                        <div class="col-12 mb-3">
                            <h3 class="text-center">Selecciona la forma de pago</h3>
                            <div class="justify-content-center d-flex mt-5">
                                <button type="button" class="btn btn-primary mr-2" @click="pagoCredit(venta_id,adeudo,idcliente)">Nota de crédito</button>
                                <button type="button" class="btn btn-primary" @click="pagoOtro(venta_id,adeudo)">Otros</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4 mb-0">
                        <div class="col-12 justify-content-center d-flex">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal7(venta_id)">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.modal-content -->
        </div>
    <!-- /.modal-dialog -->
    </div>
    <!-- Modal Seleccionar tipo de abono -->

    <!-- Modal crear abono -->
    <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal6}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md " role="document">
            <div class="modal-content content-deposit">
                <!-- <div class="modal-header">
                    <h4 class="modal-title" v-text="tituloModal"></h4>
                    <button type="button" class="close" @click="cerrarModal6(venta_id)" aria-label="Close">
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
                            <button type="button" class="btn btn-primary mr-2" @click="saveDeposit(venta_id,adeudo,totalab)">Guardar</button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModal6(venta_id)">Cancelar</button>
                        </div>
                    </div>
                </div>
               <!--  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal6(venta_id)">Cancelar</button>
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
                                @click="guardarAbonoCredit(venta_id,adeudo,totalab)" v-if="selectedCredits.length"> Guardar
                            </button>
                            <button type="button" class="btn btn-secondary" @click="cerrarModal8(venta_id)">Cancelar</button>
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
            venta_id: 0,
            idcliente: 0,
            cliente: '',
            user: '',
            estado :'Registrado',
            tipo_comprobante: "PRESUPUESTO",
            num_comprobante: "",
            impuesto: 0.16,
            descuento : 0,
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
            fecha_llegada : '',
            file : '',
            imagenMinatura : '',
            arrayCategoria : [],
            condicion : 0,
            precio_venta : 0,
            cantidad : 0,
            total_impuesto : 0.0,
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
            arrayVenta : [],
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
            errorVenta: 0,
            errorMostrarMsjVenta: [],
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
            estadoVenta : "",
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
        calcularTotal : function(){
            let me=this;
            let resultado = 0;
            let subtotal = 0;
            let iva = parseFloat(me.impuesto) + 1;
            for(var i=0;i<me.arrayDetalle.length;i++){
                subtotal += (((me.arrayDetalle[i].precio * me.arrayDetalle[i].metros_cuadrados)* me.arrayDetalle[i].cantidad)-me.arrayDetalle[i].descuento);
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
        listarVenta (page,buscar,criterio,estadoVenta,estadoEntrega,estadoPago){
            let me=this;
            var url= '/venta?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio +
                '&estado='+ estadoVenta + '&estadoEntrega=' + estadoEntrega + '&estadoPagado=' + estadoPago;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayVenta = respuesta.ventas.data;
                me.pagination= respuesta.pagination;
                me.usrol = respuesta.userrol;
                me.usid = respuesta.usid;
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
            me.rfc_cliente = val1.rfc;
            me.tipo_cliente = val1.tipo;
            me.telefono_cliente = val1.telefono;
            me.contacto_cliente = val1.company;
            me.telcontacto_cliente = val1.tel_company;
            me.obs_cliente = val1.observacion;
            me.cfdi_cliente =  val1.cfdi;

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
        cambiarPagina(page,buscar,criterio,estadoVenta,estadoEntrega,estadoPago){
            let me = this;
            me.pagination.current_page = page;
            me.listarVenta(page,buscar,criterio,estadoVenta,estadoEntrega,estadoPago);
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
        registrarVenta(){
            if (this.validarVenta()) {
                return;
            }
            if(this.forma_pago != 'Cheque'){
                this.num_cheque = 0;
                this.banco = '';
            }
            let me = this;
            var numcomp = "V-".concat(me.CodeDate,"-",me.num_comprobante);
            var totalDem = parseFloat(this.total).toFixed(4);
            var is_special = 0;
            if (me.ispecial) {
                is_special = 1;
            }
            //console.log(`Total : ${totalDem}`);
            axios.post('/venta/registrar',{
                'idcliente': this.idcliente,
                'tipo_comprobante': this.tipo_comprobante,
                'num_comprobante' : numcomp,
                'impuesto' : this.impuesto,
                'total' : totalDem,
                'forma_pago' : this.forma_pago,
                'tiempo_entrega' : this.tiempo_entrega,
                'lugar_entrega' : this.lugar_entrega,
                'moneda' : this.moneda,
                'tipo_cambio' : this.tipo_cambio,
                'observacion' : this.observacion,
                'observacionpriv' : this.observacionpriv,
                'num_cheque'  : this.num_cheque,
                'banco'       : this.banco,
                'tipo_facturacion' : this.tipo_facturacion,
                'special'   : is_special,
                'data': this.arrayDetalle,
            }).then(function(response) {
                me.ocultarDetalle();
                me.listarVenta(1,'','num_comprobante','','','');
                Swal.fire({
                    type: 'success',
                    title: 'Registrado...',
                    text: 'La venta ha sido registrada con éxito!!',
                });
                //console.log(`Resp : ${ response }`);
            })
            .catch(function(error) {
                console.log(error);
            });
        },
        desactivarVenta(id,adeudo) {

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
                        axios.put('/venta/desactivar',{
                            'id': id,
                            'genNc' : genNc
                        }).then(function (response) {
                            me.listarVenta(1,'','num_comprobante','','','');
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
                        axios.put('/venta/desactivar',{
                            'id': id,
                            'genNc' : genNc
                        }).then(function (response) {
                            me.listarVenta(1,'','num_comprobante','','','');
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
            if (!me.impuesto) me.errorMostrarMsjVenta.push("Ingrese el impuesto de la venta");
            if (me.arrayDetalle.length<=0) me.errorMostrarMsjVenta.push("Introdusca articulos para la venta");
            if (me.moneda != 'Peso Mexicano') me.errorMostrarMsjVenta.push("Seleccione el tipo de cambio de la moneda");

            if (me.errorMostrarMsjVenta.length) me.errorVenta = 1;

            return me.errorVenta;
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
            this.errorVenta =0;
            this.errorMostrarMsjVenta = [];
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
            this.tipo_comprobante = "PRESUPUESTO",
            this.impuesto = 0.16;
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
            this.listarVenta(this.pagination.current_page,
                this.buscar, this.criterio,this.estadoVenta,this.estadoEntrega,this.estadoPago);
        },
        verVenta(id){
            let me = this;
            me.listado = 2;

            //Obtener los datos del ingreso
            var arrayVentaT=[];
            var url= '/venta/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                arrayVentaT = respuesta.venta;

                var fechaform  = arrayVentaT[0]['fecha_hora'];

                var total_parcial = 0;

                me.venta_id = arrayVentaT[0]['id'];
                me.cliente = arrayVentaT[0]['cliente'];
                me.rfc_cliente = arrayVentaT[0]['rfc'];
                me.tipo_cliente = arrayVentaT[0]['tipo'];
                me.contacto_cliente = arrayVentaT[0]['contacto'];
                me.telcontacto_cliente = arrayVentaT[0]['tel_contacto'];
                me.telefono_cliente = arrayVentaT[0]['telefono'];
                me.cfdi_cliente = arrayVentaT[0]['cfdi'];
                me.tipo_comprobante=arrayVentaT[0]['tipo_comprobante'];
                me.num_comprobante=arrayVentaT[0]['num_comprobante'];
                me.user=arrayVentaT[0]['usuario'];
                me.impuesto = arrayVentaT[0]['impuesto'];
                me.total = arrayVentaT[0]['total'];
                me.adeudo = arrayVentaT[0]['adeudo'];
                me.forma_pago = arrayVentaT[0]['forma_pago'];
                me.lugar_entrega = arrayVentaT[0]['lugar_entrega'];
                me.tiempo_entrega = arrayVentaT[0]['tiempo_entrega'];
                me.entregado = arrayVentaT[0]['entregado'];
                me.entregado_parcial = arrayVentaT[0]['entrega_parcial'];
                me.moneda = arrayVentaT[0]['moneda'];
                me.tipo_cambio = arrayVentaT[0]['tipo_cambio'];
                me.observacion = arrayVentaT[0]['observacion'];
                me.observacionpriv = arrayVentaT[0]['observacionpriv'];
                me.estadoVn = arrayVentaT[0]['estado'];
                me.num_cheque = arrayVentaT[0]['num_cheque'];
                me.banco = arrayVentaT[0]['banco'];
                me.tipo_facturacion = arrayVentaT[0]['tipo_facturacion'];
                me.pagado = arrayVentaT[0]['pagado'];
                me.pago_parcial = arrayVentaT[0]['pago_parcial'];
                me.facturado = arrayVentaT[0]['facturado'];
                me.num_factura = arrayVentaT[0]['num_factura']
                me.factura_env = arrayVentaT[0]['factura_env'];
                me.auto_entrega = arrayVentaT[0]['auto_entrega'];
                me.idcliente = arrayVentaT[0]['idcliente'];
                me.email_cliente =  arrayVentaT[0]['EmailC'];
                moment.locale('es');
                me.fecha_llegada=moment(fechaform).format('dddd DD MMM YYYY hh:mm:ss a');

                 var imp =   parseFloat(me.impuesto = arrayVentaT[0]['impuesto']);

                me.divImp = imp + 1;

                if(me.entregado ==1){
                    me.btnEntrega = true;
                }else{
                     me.btnEntrega = false;
                }

                if(me.entregado_parcial ==1){
                    me.btnEntregaParcial = true;
                }else{
                    me.btnEntregaParcial = false;
                }

                if(arrayVentaT[0]['pagado'] ==1){
                    me.btnPagado = true;
                }else{
                    me.btnPagado = false;
                }

                if(arrayVentaT[0]['pago_parcial'] == 1){
                    me.btnPagadoParcial = true;
                    me.getDeposits(me.venta_id);
                }else{
                    me.btnPagadoParcial = false;
                }

                if(arrayVentaT[0]['auto_entrega'] == 1){
                    me.btnAutoEntrega = true;
                }else{
                    me.btnAutoEntrega = false;
                }

                if(arrayVentaT[0]['special'] == 1){
                    me.btnSpecial = true;
                }else{
                    me.btnSpecial = false;
                }
            })
            .catch(function (error) {
                console.log(error);
            });

            //Obtener los detalles del ingreso
            var url= '/venta/obtenerDetalles?id=' + id;
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

            if(me.zona == 'PBA'){
                bodega = 'Puebla';
                me.bodega = 'Puebla';

            }else{
                if(bodega == 'Puebla'){
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
        registrarArticuloA(){

            let me = this;
            if(me.largoA == 0 || me.altoA == 0 || me.codigoA == ""){
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
                    Swal.fire(
                        "Registrado!",
                        `La mitad A guardada con éxito`,
                        "success"
                    )
                    me.validatedA = 1;
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        registrarArticuloB(){

            let me = this;
            if(me.largoB == 0 || me.altoB == 0 || me.codigoB == ""){
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
                    Swal.fire(
                        "Registrado!",
                        `La mitad B guardada con éxito`,
                        "success"
                    )
                    me.validatedB = 1;
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        updateCorte(){

            let me = this;
            axios.put("/venta/actualizarCorte",{
                'id' : this.articulo_id,
                'stock':this.stock,

            }).then(function(response){
                swal.fire(
                    "Completado!",
                    `La placa ha sido cortada con éxito`,
                    "success"
                )

            }).catch(function(error){
                console.log(error);
            })

        },
        cambiarEstadoEntrega(id){
            let me = this;
            if(me.btnEntrega == true){
                me.entregado = 1;
            }else{
                me.entregado = 0;
            }
            axios.post('/venta/cambiarEntrega',{
                'id': id,
                'entregado' : this.entregado
            }).then(function (response){
                me.verVenta(id);
                if(me.entregado == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido registrado con entregado al 100%.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto ha sido registrado como no entregado.',
                    'warning')
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoEntregaParcial(id){
            let me = this;
            if(me.btnEntregaParcial == true){
                me.entregado_parcial = 1;
            }else{
                me.entregado_parcial = 0;
            }
            axios.post('/venta/cambiarEntregaParcial',{
                'id': id,
                'entrega_parcial' : this.entregado_parcial
            }).then(function (response) {
                me.verVenta(id);
                if(me.entregado_parcial == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido registrado con entrega parcial.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto ha sido registrado como no entregado.',
                    'warning')
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoPagado(id){
          let me = this;
            if(me.btnPagado == true){
                me.pagado = 1;
                me.btnPagadoParcial = false;
            }else{
                me.pagado = 0;
                me.btnEntrega = false;
                me.cambiarEstadoEntrega(id);
            }
            axios.post('/venta/cambiarPagado',{
                'id': id,
                'pagado' : this.pagado
            }).then(function (response) {
                me.verVenta(id);
                if(me.pagado == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido marcado como pagado con éxito.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto se registro como pendiente de pago.',
                    'warning')
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoFactura(id){
            let me = this;
            if(me.btnFactura == true){
                me.facturado = 1;
            }else{
                me.facturado = 0;
            }
            axios.post('/venta/cambiarFactura',{
                'id': id,
                'facturado' : this.facturado
            }).then(function (response){
                me.verVenta(id);
                if(me.facturado == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido registrado como Facturado.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto ha sido registrado como no Facturado.',
                    'warning')
                }
            }).catch(function (error) {
                console.log(error);
            });
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
            axios.post('/venta/actualizarObservacion',{
                'id': id,
                'observacion' : this.observacion
            }).then(function (response) {
                me.obsEditable = 0;
            }).catch(function (error) {
                console.log(error);
            });
        },
        actualizarObservacionPriv(id){
            let me = this;
            axios.post('/venta/actualizarObservacionPriv',{
                'id': id,
                'observacionpriv' : this.observacionpriv
            }).then(function (response) {
                me.obsprivEditable = 0;
            }).catch(function (error) {
                console.log(error);
            });
        },
        pdfVenta(id){
            window.open('/venta/pdf/'+id);
        },
        getLastNum(){
            let me=this;
            var url= '/venta/nextNum';
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
        abrirModal5(){
            this.modal5 = 1;
            this.tituloModal = "Generar Reporte de ventas";
        },
        cerrarModal5(){
            this.modal5 = 0;
            this.tituloModal = "";
            this.fecha1 = "";
            this.fecha2 = "";
            this.arrayReceptores = [];
            this.selectedUsers = [];
        },
        listarExcel(inicio, fin,selectedUsers){
            if (this.usrol != 1){
                var ArrUsuarios = [];
                ArrUsuarios.push(this.usid);
                window.open('/venta/ExportExcel?inicio=' + inicio + '&fin=' + fin + '&usuarios=' + ArrUsuarios);
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
                window.open('/venta/ExportExcel?inicio=' + inicio + '&fin=' + fin + '&usuarios=' + ArrUsuarios);
            }
            }
        },
        listarExcelDet(inicio, fin,selectedUsers){
            if (this.usrol != 1){
                var ArrUsuarios = [];
                ArrUsuarios.push(this.usid);
                window.open('/venta/ExportExcelDet?inicio=' + inicio + '&fin=' + fin + '&usuarios=' + ArrUsuarios);
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
                    window.open('/venta/ExportExcelDet?inicio=' + inicio + '&fin=' + fin + '&usuarios=' + ArrUsuarios);
                }
            }

        },
        getDeposits(id){
            let me = this;
            var url= '/venta/getDeposits?id=' + id;
            axios.get(url).then(function (response){
                var respuesta= response.data;
                me.arrayDepositos = respuesta.abonos;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        deleteDeposit(id,idventa,total){
            /* console.log(`Estas a punto de borrar el abono ${id} de la venta ${ventaid}`); */
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Esta seguro de eliminar este abono?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Aceptar!",
                cancelButtonText: "Cancelar!",
                reverseButtons: true
            })
            .then(result => {
                if (result.value) {
                    let me = this;
                    axios.put('/venta/eliminarDeposit',{
                        'id': id,
                        'idventa' : idventa,
                        'total' : total
                    }).then(function (response) {
                        me.verVenta(idventa);
                        Swal.fire({
                            type: 'success',
                            title: 'Eliminado...',
                            text: 'El abono ha sido eliminado con éxito!!',
                        });
                    }).catch(function (error) {
                        console.log(error);
                    });
                }else if (result.dismiss === swal.DismissReason.cancel){
                    //this.verVenta(idventa);
                }
            })
        },
        abrirModal10(){
            this.modal10 = 1;
            this.tituloModal = "Generar Reporte de Abonos";
        },
        cerrarModal10(){
            this.modal10 = 0;
            this.tituloModal = "";
            this.fecha1 = "";
            this.fecha2 = "";
        },
        listarAbonosExcel(inicio, fin){
              window.open('/venta/AbonoExportExcel?inicio=' + inicio + '&fin=' + fin);
        },
        cambiarEstadoPagadoParcial(id,adeudo){
            this.modal7 = 1;
            this.tituloModal = 'Crear Abono';
            this.forma_pagoab = '';
            this.totalab = 0;
        },
        cerrarModal6(id){
            this.modal6 = 0;
            this.forma_pagoab = '';
            this.verVenta(id);
            this.otroFormPayab =  false;
        },
        saveDeposit(id,adeudo,total){
            let abono = parseFloat(total);
            if(this.forma_pagoab == ''){
                swal.fire(
                'Atención!',
                'Ingrese la forma de pago.',
                'error');
            }else{
                if(abono > adeudo){
                    swal.fire(
                    'Error!',
                    'El abono no puede ser mayor que el adeudo.',
                    'error');
                    this.totalab = 0;
                }else{
                    let me = this;
                    axios.post('/venta/crearDeposit',{
                        'id' : id,
                        'total' : abono,
                        'forma_pago' : this.forma_pagoab
                    }).then(function(response) {
                        me.modal6 = 0;
                        me.forma_pagoab = '';
                        me.otroFormPayab =  false;
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

            }
        },
        autorizarEntrega(id,pr){
            let me = this;
            if(me.btnAutoEntrega == true){
                me.auto_entrega = 1;
            }else{
                me.auto_entrega = 0;
            }
            axios.put('/venta/autorizarEntrega',{
                'id': id,
                'auto_entrega' : this.auto_entrega
            }).then(function (response) {
                me.verVenta(id);
                if(me.auto_entrega == 1){
                    swal.fire(
                    'Completado!',
                    `El presupuesto ${ pr } ha sido autorizado para entrega con éxito.`,
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    `El presupuesto ${ pr } ha sido marcado como no autorizado para entrega.`,
                    'warning')
                }
            }).catch(function (error) {
                console.log(error);
            });
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
            var url= '/cliente/getCreditsPay?id=' + id + '&page=' + page;
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

            axios.put('/cliente/actualizarCredits', {

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

                axios.post('/venta/crearDepositCredit',{
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
        selectReceptor(){
            let me=this;
            var url= '/user/selectUsuario';

            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayReceptores = respuesta.usuarios;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        sendMailPresupuesto(id,mail,cliente){
            let me = this;
            axios.post('/venta/enviarPresupuestoMail',{
                'id': id,
                'mail' : mail,
                'name' : cliente
            }).then(function (response) {
                Swal.fire({
                    type: 'success',
                    title: 'Enviado...',
                    text: `El presupuesto de ${ cliente } se envio correctamente al correo ${ mail }`,
                })
            }).catch(function (error) {
                console.log(error);
            });
        },
        cambiarEstadoSpecial(id){
            let me = this;
            var is_special = 0;
            if(me.btnSpecial == true){
                is_special = 1;
            }
            axios.post('/venta/cambiarSpecial',{
                'id': id,
                'especial' : is_special
            }).then(function (response) {
                me.verVenta(id);
                if(is_special == 1){
                    swal.fire(
                    'Completado!',
                    'El presupuesto ha sido marcado como especial con éxito.',
                    'success')
                }else{
                    swal.fire(
                    'Atención!',
                    'El presupuesto fue desmarcado como especial',
                    'warning')
                }
            }).catch(function (error) {
                console.log(error);
            });
        },
    },
    mounted() {
        this.listarVenta(1,this.buscar, this.criterio,this.estadoVenta,this.estadoEntrega,this.estadoPago);
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
