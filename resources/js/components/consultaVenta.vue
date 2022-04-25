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
          <i class="fa fa-align-justify"></i> Ventas
        </div>

        <!-- Listado -->
        <template v-if="listado==1">
            <div class="card-body">
                <div class="form-inline">
                    <div class="form-group mb-2 col-11">
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="criterio">
                                <option value="cliente">Cliente</option>
                                <option value="num_comprobante">No° Comprobante</option>
                                <option value="fecha_hora">Fecha</option>
                                <option value="forma_pago">Forma de pago</option>
                            </select>
                            <input type="text" v-model="buscar" @keyup.enter="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo)" class="form-control mb-1" placeholder="Texto a buscar...">
                        </div>
                        <div class="input-group">
                            <select class="form-control mb-1" v-model="estadoVenta" @change="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo)">
                                <option value="">Activa</option>
                                <option value="Anulada">Cancelada</option>
                            </select>
                            <button type="submit" @click="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo)" class="btn btn-primary mb-1"><i class="fa fa-search"></i> Buscar</button>
                        </div>
                        <div class="input-group input-group-sm ml-sm-2 mr-sm-1 ml-md-2 ml-lg-5" v-if="estadoVenta!='Anulada'">
                            <select class="form-control" id="tipofact" name="tipofact" v-model="estadoEntrega" @change="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo)">
                                <option value="">Todo</option>
                                <option value="entregado">100%</option>
                                <option value="entrega_parcial">Parcial</option>
                                <option value="no_entregado">No entregado</option>
                            </select>
                            <button class="btn btn-sm btn-warning" type="button"><i class="fa fa-truck" aria-hidden="true"></i>&nbsp; Entregas </button>
                        </div>
                        <div class="input-group input-group-sm ml-sm-2 mr-sm-1 ml-md-2 ml-lg-5" v-if="estadoVenta!='Anulada'">
                            <select class="form-control" id="tipofact" name="tipofact" v-model="estadoAdeudo" @change="listarVenta(1,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo)">
                                <option value="">Todo</option>
                                <option value="Pagado">Pagado 100%</option>
                                <option value="Abonado">Pagado Parcial</option>
                                <!-- <option value="NoAbono">Sin Abono</option> -->
                            </select>
                            <button class="btn btn-sm btn-success" type="button"><i class="fa fa-money" aria-hidden="true"></i>&nbsp; Pagos </button>
                        </div>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover table-responsive-xl">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Atendió</th>
                                <th>Cliente</th>
                                <th>Tipo Comprobante</th>
                                <th>No° Comprobante</th>
                                <th>Fecha Hora</th>
                                <th>Total</th>
                                <th>Adeudo</th>
                                <th>Forma de pago</th>
                                <th>Facturación</th>
                                <th>Entregado</th>
                                <th>100% Pagado</th>
                                <th>Estado</th>

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
                                    </div>
                                </td>
                                <td v-text="venta.usuario"></td>
                                <td v-text="venta.nombre"></td>
                                <td v-text="venta.tipo_comprobante"></td>
                                <td v-text="venta.num_comprobante"></td>
                                <td>{{ convertDateVenta(venta.fecha_hora) }}</td>
                                <td v-text="venta.total"></td>
                                <td v-text="venta.adeudo"></td>
                                <td v-text="venta.forma_pago"></td>
                                <td v-text="venta.tipo_facturacion"></td>
                                <td v-if="venta.entregado">
                                    <span class="badge badge-success">100%</span>
                                </td>
                                <td v-else-if="venta.entrega_parcial">
                                    <span class="badge badge-warning">Parcial</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">No entregado</span>
                                </td>
                                 <td v-if="venta.adeudo == 0">
                                    <span class="badge badge-success">100% Pagado</span>
                                    <!-- <toggle-button :value="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled /> -->
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">No Pagado</span>
                                   <!--  <toggle-button :value="false" :labels="{checked: 'Si', unchecked: 'No'}" disabled /> -->
                                </td>
                                <td v-if="venta.estado =='Registrado'">
                                    <span class="badge badge-success">Activa</span>
                                </td>
                                <td v-else>
                                    <span class="badge badge-danger">Cancelada</span>
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
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar, criterio,estadoVenta,estadoEntrega,estadoAdeudo)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar, criterio,estadoVenta,estadoEntrega,estadoAdeudo)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar, criterio,estadoVenta,estadoEntrega,estadoAdeudo)">Sig</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </template>
        <!-- Fin Listado -->

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
                    <!-- Status Entregas -->
                    <template v-if="usrol != 1">
                        <div class="col-md-2" v-if="adeudo == 0 && entregado_parcial == 0">
                            <div class="form-group">
                                <label for=""><strong>Entregado 100%:</strong> </label>
                                <div v-if="pagado">
                                    <toggle-button @change="cambiarEstadoEntrega(venta_id)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Pendiente de pago</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="adeudo == 0  && entregado == 0">
                            <div class="form-group">
                                <label for=""><strong>Entregado Parcial:</strong> </label>
                                <div v-if="pagado">
                                    <toggle-button @change="cambiarEstadoEntregaParcial(venta_id)" v-model="btnEntregaParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Pendiente de pago</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-md-2" v-if="!entregado_parcial">
                            <div class="form-group">
                                <label for=""><strong>Entregado 100%:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntrega(venta_id)" v-model="btnEntrega" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="!entregado">
                            <div class="form-group">
                                <label for=""><strong>Entregado Parcial:</strong> </label>
                                <div>
                                    <toggle-button @change="cambiarEstadoEntregaParcial(venta_id)" v-model="btnEntregaParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                </div>
                            </div>
                        </div>
                    </template>
                    <!-- Status Pagos -->
                    <template v-if="usrol != 1">
                        <div class="col-md-2">
                            <div class="form-group" v-if="!pago_parcial">
                                <label for=""><strong>100% Pagado: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagado(venta_id)" v-model="btnPagado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}"  disabled/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" v-if="!pagado">
                                <label for=""><strong>Pagado Parcialmente: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagadoParcial(venta_id,adeudo)" v-model="btnPagadoParcial"
                                        :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" :disabled="pago_parcial == 1" disabled/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-md-2">
                            <div class="form-group" v-if="!pago_parcial">
                                <label for=""><strong>100% Pagado: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagado(venta_id)" v-model="btnPagado" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled />
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" v-if="!pagado">
                                <label for=""><strong>Pagado Parcialmente: </strong> </label>
                                <div v-if="estadoVn == 'Registrado'">
                                    <toggle-button @change="cambiarEstadoPagadoParcial(venta_id,adeudo)"
                                        v-model="btnPagadoParcial" :sync="true" :labels="{checked: 'Si', unchecked: 'No'}" disabled/>
                                </div>
                                <div v-else>
                                    <span class="badge badge-danger">Presupuesto cancelado</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div :class="{'col-md-7': pago_parcial,  'col-md-2': !pago_parcial}">
                        <template v-if="adeudo > 0">
                            <p class="float-right" style="font-size: 20px;"><span class="badge badge-danger">Adeudo: </span> {{ adeudo }}</p>
                        </template>
                        <template v-else>
                            <p class="float-right" style="font-size: 20px;"><span class="badge badge-success">Pagado al 100 %</span></p>
                        </template>
                    </div>
                    <div class="col-5">
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
                                        <div class="table-responsive col-12 p-0">
                                            <table class="table table-bordered table-striped table-sm table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="10px">No°</th>
                                                        <th>Fecha</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody v-if="arrayDepositos.length">
                                                    <tr v-for="(deposito,index) in arrayDepositos" :key="deposito.id">
                                                        <td width="10px" v-text="index + 1"></td>
                                                        <td>{{ convertDateVenta(deposito.fecha) }}</td>
                                                        <td v-text="deposito.total"></td>
                                                    </tr>
                                                    <tr style="background-color: #CEECF5;">
                                                        <td colspan="2" align="right"><strong>Abonado:</strong></td>
                                                        <td>$ {{ calcularAbonos }}</td>
                                                    </tr>
                                                    <tr style="background-color: #CEECF5;">
                                                        <td colspan="2" align="right"><strong>Adeudo:</strong></td>
                                                        <td>$ {{ adeudo }} </td>
                                                    </tr>
                                                </tbody>
                                                <tbody v-else>
                                                    <tr>
                                                        <td colspan="3" class="text-center">
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
                                    <th>Descuento </th>
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
                                    <td>$ {{ total}} </td>
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
            listado : 1,
            modal: 0,
            modal2: 0,
            modal3: 0,
            modal4: 0,
            modal5: 0,
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
            offset : 3,
            criterio : 'num_comprobante',
            buscar : '',
            buscarA : '',
            criterioA : 'sku',
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
            estadoAdeudo : ""
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
            }
        },
    methods: {
        listarVenta (page,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo){
            let me=this;
            var url= '/ventaDeposit?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio + '&estado='
                + estadoVenta + '&estadoEntrega=' + estadoEntrega + '&estadoAdeudo=' + estadoAdeudo;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                me.arrayVenta = respuesta.ventas.data;
                me.pagination= respuesta.pagination;
                me.usrol = respuesta.userrol;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        cambiarPagina(page,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo){
            let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarVenta(page,buscar,criterio,estadoVenta,estadoEntrega,estadoAdeudo);
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
            this.listarVenta(1,'','num_comprobante','',this.estadoEntrega,this.estadoAdeudo);
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
                me.factura_env = arrayVentaT[0]['factura_env'];

                moment.locale('es');
                me.fecha_llegada=moment(fechaform).format('dddd DD MMM YYYY hh:mm:ss a');

                 var imp =   parseFloat(me.impuesto = arrayVentaT[0]['impuesto']);

                me.divImp = imp + 1;

                if(me.entregado ==1){
                    me.btnEntrega = true;
                    me.btnEntregaParcial = false;
                }

                if(me.entregado_parcial ==1){
                    me.btnEntregaParcial = true;
                    me.btnEntrega = false;
                }

                if(me.pagado ==1){
                    me.btnPagado = true;
                    me.btnPagadoParcial = false;
                }

                if(me.pago_parcial == 1){
                    me.btnPagadoParcial = true;
                    me.btnPagado = false;
                    me.getDeposits(me.venta_id);
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
        convertDateVenta(date){
            moment.locale('es');
            let me=this;
            var datec = moment(date).format('DD MMM YYYY hh:mm:ss a');
            /* console.log(datec); */
            return datec;
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
        }

    },
    mounted() {
        this.listarVenta(1,this.buscar, this.criterio,this.estadoVenta,this.estadoEntrega,this.estadoAdeudo);
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
       /*  -moz-appearance:textfield !important; */
    }
    .sinpadding [class*="col-"] {
        padding: 0;
    }
    .content-export{
        /* width: auto !important; */
        height: 380px !important;
    }
</style>
