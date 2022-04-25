<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=>['guest']],function(){
    //LOGIN
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::get('/login', 'Auth\LoginController@showLoginForm');
});

Route::group(['middleware'=>['auth']],function(){

    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/dashboard','DashboardController');
    Route::post('/notification/get', 'NotificationController@get');

    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');




    Route::group(['middleware'=>['Almacenero']],function(){
        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::put('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
        Route::post('/articulo/registrarDetalle', 'ArticuloController@storeDetalle');
        Route::get('/articulo/listarPdf','ArticuloController@listarPdf');

        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');


    });

    Route::group(['middleware'=>['Vendedor']],function(){

        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');


    });

    Route::group(['middleware'=>['Administrador']],function(){

        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::put('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/actualizarDetalle', 'ArticuloController@updateDetalle');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/ocultar', 'ArticuloController@ocultar');
        Route::put('/articulo/estadoMaterial', 'ArticuloController@estadoMaterial');
        Route::put('/articulo/cambiarDetalle', 'ArticuloController@cambiarDetalle');
        Route::put('/articulo/malPulido', 'ArticuloController@malPulido');
        Route::put('/articulo/anularRelice', 'ArticuloController@anularRelice');
        Route::put('/articulo/anularDetalle', 'ArticuloController@anularDetalle');
        Route::put('/articulo/anularPulido', 'ArticuloController@anularPulido');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
        Route::post('/articulo/registrarDetalle', 'ArticuloController@storeDetalle');
        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');
        Route::put('/articulo/actualizarCortes' , 'ArticuloController@updateCorte');
        Route::get('/articulo/listarPdf','ArticuloController@listarPdf');
        Route::get('/articulo/listarExcel','ArticuloController@listarExcel');
        Route::get('/articulo/listarExcelOculto','ArticuloController@listarExcelOculto');
        Route::put('/articulo/cambiarComprometido', 'ArticuloController@cambiarComprometido');
        Route::get('/articulo/listarArticuloCotizado', 'ArticuloController@listarArticuloCotizado');
        Route::put('/articulo/eliminarImg', 'ArticuloController@eliminarImagen');
        Route::get('/articulo/selectBodega', 'ArticuloController@selectBodega');
        Route::get('/articulo/listarExcelVenta','ArticuloController@listarExcelVenta');
        Route::get('/articulo/getCodesSku','ArticuloController@getCodesSku');
        Route::get('/articulo/listByCategory','ArticuloController@listByCategory');
        Route::get('/articulo/listBySku','ArticuloController@listBySku');
        Route::get('/articulo/listarExcelFiltros','ArticuloController@listarExcelFiltros');
        Route::get('/articulo/getLinks','ArticuloController@getLinks');
        Route::put('/articulo/deleteLink','ArticuloController@deleteLink');
        Route::post('/articulo/cambiarEstadoIngreso', 'ArticuloController@cambiarEstadoIngreso');
        Route::get('/articulo/listarArticuloOcultos', 'ArticuloController@listarArticuloOcultos');
        Route::put('/articulo/cambiarSalida', 'ArticuloController@cambiarSalida');
        Route::put('/articulo/anularSalida', 'ArticuloController@anularSalida');
        Route::get('/articulo/edicionArticulos/{id}', 'ArticuloController@edicionArticulos');
        Route::get('/articulo/desocultar', 'ArticuloController@desocultar');
        Route::get('/articulo/listarArticuloDetalle', 'ArticuloController@listarArticuloDetalle');
        Route::get('/articulo/listarArticuloActualizado', 'ArticuloController@listarArticuloActualizado');
        Route::get('/articulo/Detalletraslado','ArticuloController@Detalletraslado');

        Route::get('/java', 'JavaController@index');
        Route::post('/java/registrar', 'JavaController@store');
        Route::put('/java/actualizar', 'JavaController@update');
        Route::put('/java/desactivar', 'JavaController@desactivar');
        Route::put('/java/ocultar', 'JavaController@ocultar');
        Route::put('/java/activar', 'JavaController@activar');
        Route::get('/java/buscarArticulo', 'JavaController@buscarArticulo');
        Route::get('/java/listarArticulo', 'JavaController@listarArticulo');
        Route::post('/java/registrarDetalle', 'JavaController@storeDetalle');
        Route::get('/java/buscarArticuloVenta', 'JavaController@buscarArticuloVenta');
        Route::get('/java/listarArticuloVenta', 'JavaController@listarArticuloVenta');
        Route::get('/java/listarPdf','JavaController@listarPdf');
        Route::get('/java/listarExcel','JavaController@listarExcel');
        Route::get('/java/listarExcelOculto','JavaController@listarExcelOculto');
        Route::put('/java/cambiarComprometido', 'JavaController@cambiarComprometido');
        Route::get('/java/listarArticuloCotizado', 'JavaController@listarArticuloCotizado');
        Route::put('/java/eliminarImg', 'JavaController@eliminarImagen');
        Route::get('/java/selectBodega', 'JavaController@selectBodega');
        Route::get('/java/listarExcelVenta','JavaController@listarExcelVenta');
        Route::get('/java/getCodesSku','JavaController@getCodesSku');
        Route::get('/java/listByCategory','JavaController@listByCategory');
        Route::get('/java/listBySku','JavaController@listBySku');
        Route::get('/java/listarExcelFiltros','JavaController@listarExcelFiltros');
        Route::get('/java/getLinks','JavaController@getLinks');
        Route::put('/java/deleteLink','JavaController@deleteLink');
        Route::post('/java/cambiarEstadoIngreso', 'JavaController@cambiarEstadoIngreso');
        Route::get('/java/listarArticuloOcultos', 'JavaController@listarArticuloOcultos');
        Route::get('/java/desocultar', 'JavaController@desocultar');

        Route::get('/abrasivo', 'AbrasivoController@index');
        Route::post('/abrasivo/registrar', 'AbrasivoController@store');
        Route::put('/abrasivo/actualizar', 'AbrasivoController@update');
        Route::put('/abrasivo/desactivar', 'AbrasivoController@desactivar');
        Route::put('/abrasivo/ocultar', 'AbrasivoController@ocultar');
        Route::put('/abrasivo/activar', 'AbrasivoController@activar');
        Route::get('/abrasivo/buscarArticulo', 'AbrasivoController@buscarArticulo');
        Route::get('/abrasivo/listarArticulo', 'AbrasivoController@listarArticulo');
        Route::post('/abrasivo/registrarDetalle', 'AbrasivoController@storeDetalle');
        Route::post('/abrasivo/registrarStock', 'AbrasivoController@storeStock');
        Route::get('/abrasivo/buscarArticuloVenta', 'AbrasivoController@buscarArticuloVenta');
        Route::get('/abrasivo/listarArticuloVenta', 'AbrasivoController@listarArticuloVenta');
        Route::get('/abrasivo/listarPdf','AbrasivoController@listarPdf');
        Route::get('/abrasivo/listarExcel','AbrasivoController@listarExcel');
        Route::get('/abrasivo/listarExcelOculto','AbrasivoController@listarExcelOculto');
        Route::put('/abrasivo/cambiarComprometido', 'AbrasivoController@cambiarComprometido');
        Route::get('/abrasivo/listarArticuloCotizado', 'AbrasivoController@listarArticuloCotizado');
        Route::put('/abrasivo/eliminarImg', 'AbrasivoController@eliminarImagen');
        Route::get('/abrasivo/selectBodega', 'AbrasivoController@selectBodega');
        Route::get('/abrasivo/listarExcelVenta','AbrasivoController@listarExcelVenta');
        Route::get('/abrasivo/getCodesSku','AbrasivoController@getCodesSku');
        Route::get('/abrasivo/listByCategory','AbrasivoController@listByCategory');
        Route::get('/abrasivo/listBySku','AbrasivoController@listBySku');
        Route::get('/abrasivo/listarExcelFiltros','AbrasivoController@listarExcelFiltros');
        Route::get('/abrasivo/getLinks','AbrasivoController@getLinks');
        Route::put('/abrasivo/deleteLink','AbrasivoController@deleteLink');
        Route::post('/abrasivo/cambiarEstadoIngreso', 'AbrasivoController@cambiarEstadoIngreso');
        Route::get('/abrasivo/listarArticuloOcultos', 'AbrasivoController@listarArticuloOcultos');
        Route::get('/abrasivo/desocultar', 'AbrasivoController@desocultar');
        Route::get('/abrasivo/listarArticuloStock', 'AbrasivoController@listarArticuloStock');

        Route::get('/bloque', 'BloqueController@index');
        Route::post('/bloque/registrar', 'BloqueController@store');
        Route::put('/bloque/actualizar', 'BloqueController@update');
        Route::put('/bloque/desactivar', 'BloqueController@desactivar');
        Route::put('/bloque/ocultar', 'BloqueController@ocultar');
        Route::put('/bloque/estadoMaterial', 'BloqueController@estadoMaterial');
        Route::put('/bloque/cambiarDetalle', 'BloqueController@cambiarDetalle');
        Route::put('/bloque/malPulido', 'BloqueController@malPulido');
        Route::put('/bloque/anularRelice', 'BloqueController@anularRelice');
        Route::put('/bloque/anularDetalle', 'BloqueController@anularDetalle');
        Route::put('/bloque/anularPulido', 'BloqueController@anularPulido');
        Route::put('/bloque/activar', 'BloqueController@activar');
        Route::get('/bloque/buscarArticulo', 'BloqueController@buscarArticulo');
        Route::get('/bloque/listarArticulo', 'BloqueController@listarArticulo');
        Route::post('/bloque/registrarDetalle', 'BloqueController@storeDetalle');
        Route::get('/bloque/buscarArticuloVenta', 'BloqueController@buscarArticuloVenta');
        Route::get('/bloque/listarArticuloVenta', 'BloqueController@listarArticuloVenta');
        Route::put('/bloque/actualizarCortes' , 'BloqueController@updateCorte');
        Route::get('/bloque/listarPdf','BloqueController@listarPdf');
        Route::get('/bloque/listarExcel','BloqueController@listarExcel');
        Route::get('/bloque/listarExcelOculto','BloqueController@listarExcelOculto');
        Route::put('/bloque/cambiarComprometido', 'BloqueController@cambiarComprometido');
        Route::get('/bloque/listarArticuloCotizado', 'BloqueController@listarArticuloCotizado');
        Route::put('/bloque/eliminarImg', 'BloqueController@eliminarImagen');
        Route::get('/bloque/selectBodega', 'BloqueController@selectBodega');
        Route::get('/bloque/listarExcelVenta','BloqueController@listarExcelVenta');
        Route::get('/bloque/getCodesSku','BloqueController@getCodesSku');
        Route::get('/bloque/listByCategory','BloqueController@listByCategory');
        Route::get('/bloque/listBySku','BloqueController@listBySku');
        Route::get('/bloque/listarExcelFiltros','BloqueController@listarExcelFiltros');
        Route::get('/bloque/getLinks','BloqueController@getLinks');
        Route::put('/bloque/deleteLink','BloqueController@deleteLink');
        Route::post('/bloque/cambiarEstadoIngreso', 'BloqueController@cambiarEstadoIngreso');
        Route::get('/bloque/listarArticuloOcultos', 'BloqueController@listarArticuloOcultos');
        Route::put('/bloque/cambiarSalida', 'BloqueController@cambiarSalida');
        Route::put('/bloque/anularSalida', 'BloqueController@anularSalida');
        Route::get('/bloque/edicionArticulos/{id}', 'BloqueController@edicionArticulos');
        Route::get('/bloque/desocultar', 'BloqueController@desocultar');
        Route::get('/bloque/listarArticuloDetalle', 'BloqueController@listarArticuloDetalle');
        Route::get('/bloque/listarArticuloActualizado', 'BloqueController@listarArticuloActualizado');
        Route::get('/bloque/Detalletraslado','BloqueController@Detalletraslado');


        Route::get('/proveedor', 'ProveedorController@index');
        Route::post('/proveedor/registrar', 'ProveedorController@store');
        Route::put('/proveedor/actualizar', 'ProveedorController@update');
        Route::get('/proveedor/selectProveedor', 'ProveedorController@selectProveedor');

        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
        Route::put('/cliente/desactivar', 'ClienteController@desactivarCliente');
        Route::put('/cliente/activar', 'ClienteController@activarCliente');
        Route::put('/cliente/filesupplo', 'ClienteController@filesUppload');
        Route::get('/cliente/getDocs', 'ClienteController@getDocs');
        Route::put('/cliente/eliminarDoc', 'ClienteController@eliminarDoc');

        Route::post('/cliente/crearComment', 'ClienteController@crearComment');
        Route::get('/cliente/getComments', 'ClienteController@getComments');
        Route::put('/cliente/editComment','ClienteController@editComment');
        Route::put('/cliente/deleteComment','ClienteController@deleteComment');
        Route::post('/cliente/crearCredit', 'ClienteController@crearCredit');
        Route::get('/cliente/getCredits', 'ClienteController@getCredits');
        Route::put('/cliente/deleteCredit','ClienteController@deleteCredit');
        Route::put('/cliente/actualizarCredits','ClienteController@updateCredits');
        Route::get('/cliente/getCreditsPay', 'ClienteController@getCreditsPay');
        Route::get('/cliente/listarClave', 'ClienteController@listarClave');

        Route::get('/rol', 'RolController@index');
        Route::get('/rol/selectRol', 'RolController@selectRol');

        Route::get('/user', 'UserController@index');
        Route::post('/user/registrar', 'UserController@store');
        Route::put('/user/actualizar', 'UserController@update');
        Route::put('/user/desactivar', 'UserController@desactivar');
        Route::put('/user/activar', 'UserController@activar');
        Route::get('/user/selectUsuario', 'UserController@selectUsuario');
        Route::get('/user/selectUsuarioAct', 'UserController@selectUsuarioAct');
        Route::put('/user/autoIngreso','UserController@autoIngreso');
        Route::put('/user/editarArticulos','UserController@editarArticulos');
        Route::put('/user/setLastConnection','UserController@setLastConnection');
        Route::get('/user/getUserPerms/{id}', 'UserController@getUserPerms');
        Route::get('/user/getUserEditar/{id}', 'UserController@getUserEditar');
        Route::put('/user/cambiarPassword','UserController@cambiarPassword');

        Route::get('/ingreso', 'IngresoController@index');
        Route::post('ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');
        Route::get('/ingreso/nextNum','IngresoController@getLastNum');
        Route::get('/ingreso/pdf/{id}','IngresoController@pdf');
        Route::get('/ingreso/pdfcontenedor/{id}','IngresoController@pdfContenedor');
        Route::post('/ingreso/cambiarEstadoIngreso', 'IngresoController@cambiarEstadoIngreso');

        Route::get('/ingresojava', 'IngresoJavaController@index');
        Route::post('ingresojava/registrar', 'IngresoJavaController@store');
        Route::put('/ingresojava/desactivar', 'IngresoJavaController@desactivar');
        Route::get('/ingresojava/obtenerCabecera', 'IngresoJavaController@obtenerCabecera');
        Route::get('/ingresojava/obtenerDetalles', 'IngresoJavaController@obtenerDetalles');
        Route::get('/ingresojava/nextNum','IngresoJavaController@getLastNum');
        Route::get('/ingresojava/pdf/{id}','IngresoJavaController@pdf');
        Route::get('/ingresojava/pdfcontenedor/{id}','IngresoJavaController@pdfContenedor');
        Route::post('/ingresojava/cambiarEstadoIngreso', 'IngresoJavaController@cambiarEstadoIngreso');

        Route::get('/ingresoabrasivo', 'IngresoAbrasivoController@index');
        Route::get('/ingresoabrasivo/indexEntradas', 'IngresoAbrasivoController@indexEntradas');
        Route::post('ingresoabrasivo/registrar', 'IngresoAbrasivoController@store');
        Route::put('/ingresoabrasivo/desactivar', 'IngresoAbrasivoController@desactivar');
        Route::get('/ingresoabrasivo/obtenerCabecera', 'IngresoAbrasivoController@obtenerCabecera');
        Route::get('/ingresoabrasivo/obtenerDetalles', 'IngresoAbrasivoController@obtenerDetalles');
        Route::get('/ingresoabrasivo/nextNum','IngresoAbrasivoController@getLastNum');
        Route::get('/ingresoabrasivo/pdf/{id}','IngresoAbrasivoController@pdf');
        Route::get('/ingresoabrasivo/pdfcontenedor/{id}','IngresoAbrasivoController@pdfContenedor');
        Route::post('/ingresoabrasivo/cambiarEstadoIngreso', 'IngresoAbrasivoController@cambiarEstadoIngreso');

        Route::get('/ingresobloque', 'IngresoBloqueController@index');
        Route::post('ingresobloque/registrar', 'IngresoBloqueController@store');
        Route::put('/ingresobloque/desactivar', 'IngresoBloqueController@desactivar');
        Route::get('/ingresobloque/obtenerCabecera', 'IngresoBloqueController@obtenerCabecera');
        Route::get('/ingresobloque/obtenerDetalles', 'IngresoBloqueController@obtenerDetalles');
        Route::get('/ingresobloque/nextNum','IngresoBloqueController@getLastNum');
        Route::get('/ingresobloque/pdf/{id}','IngresoBloqueController@pdf');
        Route::get('/ingresobloque/pdfcontenedor/{id}','IngresoBloqueController@pdfContenedor');
        Route::post('/ingresobloque/cambiarEstadoIngreso', 'IngresoBloqueController@cambiarEstadoIngreso');


        Route::get('/venta','VentaController@index');
        Route::post('/venta/registrar','VentaController@store');
        Route::get('/ventaInv','VentaController@indexInvo');
        Route::put('/venta/desactivar','VentaController@desactivar');
        Route::get('/venta/obtenerCabecera','VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles','VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}','VentaController@pdf');
        Route::post('/venta/cambiarEntrega','VentaController@cambiarEntrega');
        Route::post('/venta/cambiarEntregaParcial','VentaController@cambiarEntregaParcial');
        Route::post('/venta/cambiarPagado','VentaController@cambiarPagado');
        Route::post('/venta/actualizarObservacion','VentaController@actualizarObservacion');
        Route::post('/venta/actualizarObservacionPriv','VentaController@actualizarObservacionPriv');
        Route::get('/venta/nextNum','VentaController@getLastNum');
        Route::get('/venta/obtenerVentasCliente','VentaController@obtenerVentasCliente');
        Route::put('/venta/cambiarFacturacion','VentaController@cambiarFacturacion');
        Route::put('/venta/cambiarFacturacionEnv','VentaController@cambiarFacturacionEnv');
        Route::get('/ventaDeposit','VentaController@indexDeposit');
        Route::put('/venta/autorizarEntrega','VentaController@autorizarEntrega');
        Route::get('/entrega','VentaController@indexEntregas');
        Route::get('/entrega/pdf/{id}','VentaController@pdfEntrega');
        Route::put('/entrega/updDetalle','VentaController@updDetalle');
        Route::get('/venta/obtenerDetallesEntrega','VentaController@obtenerDetallesEntrega');
        Route::put('/entrega/updImagen','VentaController@updImage');
        Route::put('/entrega/eliminarImg', 'VentaController@eliminarImagen');
        Route::get('/venta/ExportExcel','VentaController@listarExcel');
        Route::get('/venta/ExportExcelDet','VentaController@listarExcelDet');
        Route::get('/venta/AbonoExportExcel','VentaController@listarAbonosExcel');
        Route::post('/venta/crearDeposit', 'VentaController@crearDeposit');
        Route::get('/venta/getDeposits', 'VentaController@getDeposits');
        Route::put('/venta/eliminarDeposit','VentaController@deleteDeposit');
        Route::get('/venta/getVentasClienteProject','VentaController@getVentasClienteProject');
        Route::put('/venta/filesupplo', 'VentaController@filesUppload');
        Route::get('/venta/getDocs', 'VentaController@getDocs');
        Route::put('/venta/eliminarDoc', 'VentaController@eliminarDoc');
        Route::post('/venta/crearDepositCredit', 'VentaController@crearDepositCredit');
        Route::put('/venta/updateCredit', 'VentaController@updateCredit');
        Route::get('/venta/ventasClienteExcel/{id}/{date1}/{date2}','VentaController@ventasClienteExcel');
        Route::get('/venta/ventasClientePDF/{id}/{date1}/{date2}','VentaController@ventasClientePDF');
        Route::get('/venta/ventasUsuariosExcel','VentaController@ventasUsuariosExcel');
        Route::post('/venta/enviarPresupuestoMail', 'VentaController@enviarPresupuestoMail');
        Route::post('/venta/enviarFacturaMail', 'VentaController@enviarFacturaMail');
        Route::post('/venta/cambiarSpecial', 'VentaController@cambiarSpecial');
        Route::post('/venta/cambiarFactura', 'VentaController@cambiarFactura');
        Route::get('/venta/getSalesprojects', 'VentaController@getProject');
        Route::put('/venta/actualizar', 'VentaController@update');

        Route::get('/ventajava','VentaJavaController@index');
        Route::get('/ventajavaInvo','VentaJavaController@facturacionInvo');
        Route::post('/ventajava/registrar','VentaJavaController@store');
        Route::put('/ventajava/desactivar','VentaJavaController@desactivar');
        Route::get('/ventajava/obtenerCabecera','VentaJavaController@obtenerCabecera');
        Route::get('/ventajava/obtenerDetalles','VentaJavaController@obtenerDetalles');
        Route::get('/ventajava/pdf/{id}','VentaJavaController@pdf');
        Route::post('/ventajava/cambiarEntrega','VentaJavaController@cambiarEntrega');
        Route::post('/ventajava/cambiarEntregaParcial','VentaJavaController@cambiarEntregaParcial');
        Route::post('/ventajava/cambiarPagado','VentaJavaController@cambiarPagado');
        Route::post('/ventajava/actualizarObservacion','VentaJavaController@actualizarObservacion');
        Route::post('/ventajava/actualizarObservacionPriv','VentaJavaController@actualizarObservacionPriv');
        Route::get('/ventajava/nextNum','VentaJavaController@getLastNum');
        Route::get('/ventajava/obtenerVentasCliente','VentaJavaController@obtenerVentasCliente');
        Route::put('/ventajava/cambiarFacturacion','VentaJavaController@cambiarFacturacion');
        Route::put('/ventajava/cambiarFacturacionEnv','VentaJavaController@cambiarFacturacionEnv');
        Route::get('/ventajavaDeposit','VentaJavaController@indexDeposit');
        Route::put('/ventajava/autorizarEntrega','VentaJavaController@autorizarEntrega');
        Route::get('/ventajava/obtenerDetallesEntrega','VentaJavaController@obtenerDetallesEntrega');
        Route::get('/ventajava/ExportExcel','VentaJavaController@listarExcel');
        Route::get('/ventajava/ExportExcelDet','VentaJavaController@listarExcelDet');
        Route::get('/ventajava/AbonoExportExcel','VentaJavaController@listarAbonosExcel');
        Route::post('/ventajava/crearDeposit', 'VentaJavaController@crearDeposit');
        Route::get('/ventajava/getDeposits', 'VentaJavaController@getDeposits');
        Route::put('/ventajava/eliminarDeposit','VentaJavaController@deleteDeposit');
        Route::get('/ventajava/getVentasClienteProject','VentaJavaController@getVentasClienteProject');
        Route::put('/ventajava/filesupplo', 'VentaJavaController@filesUppload');
        Route::get('/ventajava/getDocs', 'VentaJavaController@getDocs');
        Route::put('/ventajava/eliminarDoc', 'VentaJavaController@eliminarDoc');
        Route::post('/ventajava/crearDepositCredit', 'VentaJavaController@crearDepositCredit');
        Route::put('/ventajava/updateCredit', 'VentaJavaController@updateCredit');
        Route::get('/ventajava/ventasClienteExcel/{id}/{date1}/{date2}','VentaJavaController@ventasClienteExcel');
        Route::get('/ventajava/ventasClientePDF/{id}/{date1}/{date2}','VentaJavaController@ventasClientePDF');
        Route::get('/ventajava/ventasUsuariosExcel','VentaJavaController@ventasUsuariosExcel');
        Route::post('/ventajava/enviarPresupuestoMail', 'VentaJavaController@enviarPresupuestoMail');
        Route::post('/ventajava/enviarFacturaMail', 'VentaJavaController@enviarFacturaMail');
        Route::post('/ventajava/cambiarSpecial', 'VentaJavaController@cambiarSpecial');
        Route::post('/ventajava/cambiarFactura', 'VentaJavaController@cambiarFactura');
        Route::get('/ventajava/getSalesprojects', 'VentaJavaController@getProject');
        Route::put('/ventajava/actualizar', 'VentaJavaController@update');

        Route::get('/ventaabrasivo','VentaAbrasivoController@index');
        Route::get('/ventaabrasivoInvo','VentaAbrasivoController@facturacionInvo');
        Route::post('/ventaabrasivo/registrar','VentaAbrasivoController@store');
        Route::put('/ventaabrasivo/desactivar','VentaAbrasivoController@desactivar');
        Route::get('/ventaabrasivo/obtenerCabecera','VentaAbrasivoController@obtenerCabecera');
        Route::get('/ventaabrasivo/obtenerDetalles','VentaAbrasivoController@obtenerDetalles');
        Route::get('/ventaabrasivo/pdf/{id}','VentaAbrasivoController@pdf');
        Route::post('/ventaabrasivo/cambiarEntrega','VentaAbrasivoController@cambiarEntrega');
        Route::post('/ventaabrasivo/cambiarEntregaParcial','VentaAbrasivoController@cambiarEntregaParcial');
        Route::post('/ventaabrasivo/cambiarPagado','VentaAbrasivoController@cambiarPagado');
        Route::post('/ventaabrasivo/actualizarObservacion','VentaAbrasivoController@actualizarObservacion');
        Route::post('/ventaabrasivo/actualizarObservacionPriv','VentaAbrasivoController@actualizarObservacionPriv');
        Route::get('/ventaabrasivo/nextNum','VentaAbrasivoController@getLastNum');
        Route::get('/ventaabrasivo/obtenerVentasCliente','VentaAbrasivoController@obtenerVentasCliente');
        Route::put('/ventaabrasivo/cambiarFacturacion','VentaAbrasivoController@cambiarFacturacion');
        Route::put('/ventaabrasivo/cambiarFacturacionEnv','VentaAbrasivoController@cambiarFacturacionEnv');
        Route::get('/ventaDeposit','VentaAbrasivoController@indexDeposit');
        Route::put('/ventaabrasivo/autorizarEntrega','VentaAbrasivoController@autorizarEntrega');
        Route::get('/ventaabrasivo/ExportExcel','VentaAbrasivoController@listarExcel');
        Route::get('/ventaabrasivo/ExportExcelDet','VentaAbrasivoController@listarExcelDet');
        Route::get('/ventaabrasivo/AbonoExportExcel','VentaAbrasivoController@listarAbonosExcel');
        Route::post('/ventaabrasivo/crearDeposit', 'VentaAbrasivoController@crearDeposit');
        Route::get('/ventaabrasivo/getDeposits', 'VentaAbrasivoController@getDeposits');
        Route::put('/ventaabrasivo/eliminarDeposit','VentaAbrasivoController@deleteDeposit');
        Route::get('/ventaabrasivo/getVentasClienteProject','VentaAbrasivoController@getVentasClienteProject');
        Route::put('/ventaabrasivo/filesupplo', 'VentaAbrasivoController@filesUppload');
        Route::get('/ventaabrasivo/getDocs', 'VentaAbrasivoController@getDocs');
        Route::put('/ventaabrasivo/eliminarDoc', 'VentaAbrasivoController@eliminarDoc');
        Route::post('/ventaabrasivo/crearDepositCredit', 'VentaAbrasivoController@crearDepositCredit');
        Route::put('/ventaabrasivo/updateCredit', 'VentaAbrasivoController@updateCredit');
        Route::get('/ventaabrasivo/ventasClienteExcel/{id}/{date1}/{date2}','VentaAbrasivoController@ventasClienteExcel');
        Route::get('/ventaabrasivo/ventasClientePDF/{id}/{date1}/{date2}','VentaAbrasivoController@ventasClientePDF');
        Route::get('/ventaabrasivo/ventasUsuariosExcel','VentaAbrasivoController@ventasUsuariosExcel');
        Route::post('/ventaabrasivo/enviarPresupuestoMail', 'VentaAbrasivoController@enviarPresupuestoMail');
        Route::post('/ventaabrasivo/enviarFacturaMail', 'VentaAbrasivoController@enviarFacturaMail');
        Route::post('/ventaabrasivo/cambiarSpecial', 'VentaAbrasivoController@cambiarSpecial');
        Route::post('/ventaabrasivo/cambiarFactura', 'VentaAbrasivoController@cambiarFactura');
        Route::get('/ventaabrasivo/getSalesprojects', 'VentaAbrasivoController@getProject');
        Route::put('/ventaabrasivo/actualizar', 'VentaAbrasivoController@update');

        Route::get('/cotizacion', 'CotizacionController@index');
        Route::post('/cotizacion/registrar', 'CotizacionController@store');
        Route::put('/cotizacion/desactivar', 'CotizacionController@desactivar');
        Route::get('/cotizacion/obtenerCabecera','CotizacionController@obtenerCabecera');
        Route::get('/cotizacion/obtenerDetalles','CotizacionController@obtenerDetalles');
        Route::get('/cotizacion/pdf/{id}','CotizacionController@pdf');
        Route::put('/cotizacion/aceptarCotizacion', 'CotizacionController@aceptarCotizacion');
        Route::get('/cotizacion/nextNum','CotizacionController@getLastNum');
        Route::post('/cotizacion/actualizarObservacion', 'CotizacionController@actualizarObservacion');
        Route::put('/cotizacion/desactivarVenta', 'CotizacionController@desactivarVenta');
        Route::post('/cotizacion/enviarCotizacionMail', 'CotizacionController@enviarCotizacionMail');

        Route::get('/cotizacionproyecto', 'CotizacionProyectoController@index');
        Route::post('/cotizacionproyecto/registrar', 'CotizacionProyectoController@store');
        Route::put('/cotizacionproyecto/desactivar', 'CotizacionProyectoController@desactivar');
        Route::get('/cotizacionproyecto/obtenerCabecera','CotizacionProyectoController@obtenerCabecera');
        Route::get('/cotizacionproyecto/obtenerDetalles','CotizacionProyectoController@obtenerDetalles');
        Route::get('/cotizacionproyecto/pdf/{id}','CotizacionProyectoController@pdf');
        Route::put('/cotizacionproyecto/aceptarCotizacion', 'CotizacionProyectoController@aceptarCotizacion');
        Route::get('/cotizacionproyecto/nextNum','CotizacionProyectoController@getLastNum');
        Route::post('/cotizacionproyecto/actualizarObservacion', 'CotizacionProyectoController@actualizarObservacion');
        Route::put('/cotizacionproyecto/desactivarVenta', 'CotizacionProyectoController@desactivarVenta');
        Route::post('/cotizacionproyecto/enviarCotizacionMail', 'CotizacionProyectoController@enviarCotizacionMail');

        Route::get('/cotizacionjava', 'CotizacionJavaController@index');
        Route::post('/cotizacionjava/registrar', 'CotizacionJavaController@store');
        Route::put('/cotizacionjava/desactivar', 'CotizacionJavaController@desactivar');
        Route::get('/cotizacionjava/obtenerCabecera','CotizacionJavaController@obtenerCabecera');
        Route::get('/cotizacionjava/obtenerDetalles','CotizacionJavaController@obtenerDetalles');
        Route::get('/cotizacionjava/pdf/{id}','CotizacionJavaController@pdf');
        Route::put('/cotizacionjava/aceptarCotizacion', 'CotizacionJavaController@aceptarCotizacion');
        Route::get('/cotizacionjava/nextNum','CotizacionJavaController@getLastNum');
        Route::post('/cotizacionjava/actualizarObservacion', 'CotizacionJavaController@actualizarObservacion');
        Route::put('/cotizacionjava/desactivarVenta', 'CotizacionJavaController@desactivarVenta');
        Route::post('/cotizacionjava/enviarCotizacionMail', 'CotizacionJavaController@enviarCotizacionMail');

        Route::get('/cotizacionabrasivo', 'CotizacionAbrasivoController@index');
        Route::post('/cotizacionabrasivo/registrar', 'CotizacionAbrasivoController@store');
        Route::put('/cotizacionabrasivo/desactivar', 'CotizacionAbrasivoController@desactivar');
        Route::get('/cotizacionabrasivo/obtenerCabecera','CotizacionAbrasivoController@obtenerCabecera');
        Route::get('/cotizacionabrasivo/obtenerDetalles','CotizacionAbrasivoController@obtenerDetalles');
        Route::get('/cotizacionabrasivo/pdf/{id}','CotizacionAbrasivoController@pdf');
        Route::put('/cotizacionabrasivo/aceptarCotizacion', 'CotizacionAbrasivoController@aceptarCotizacion');
        Route::get('/cotizacionabrasivo/nextNum','CotizacionAbrasivoController@getLastNum');
        Route::post('/cotizacionabrasivo/actualizarObservacion', 'CotizacionAbrasivoController@actualizarObservacion');
        Route::put('/cotizacionabrasivo/desactivarVenta', 'CotizacionAbrasivoController@desactivarVenta');
        Route::post('/cotizacionabrasivo/enviarCotizacionMail', 'CotizacionAbrasivoController@enviarCotizacionMail');

        Route::get('/tarea', 'TareaController@index');
        Route::post('/tarea/registrar', 'TareaController@store');
        Route::put('/tarea/actualizar', 'TareaController@update');
        Route::put('/tarea/desactivar', 'TareaController@desactivar');
        Route::put('/tarea/completar', 'TareaController@completar');
        Route::get('/tarea/obtenerTareas','TareaController@obtenerTareasCliente');

        Route::get('/event', 'EventController@index');
        Route::post('/event/registrar', 'EventController@store');
        Route::put('/event/actualizar', 'EventController@update');
        Route::delete('/event/{id}','EventController@destroy');
        Route::put('/event/completar', 'EventController@completar');
        Route::get('/event/obtenerEventsCliente','EventController@obtenerEventsCliente');
        Route::get('/event/listarEventos', 'EventController@listarEventos');
        Route::get('/event/ExportExcel','EventController@listarExcel');
        Route::get('/event/listarProyectos', 'EventController@listarProyectos');

        Route::get('/traslado', 'TrasladoController@index');
        Route::post('/traslado/registrar', 'TrasladoController@store');
        Route::put('/traslado/desactivar', 'TrasladoController@desactivar');
        Route::get('/traslado/obtenerCabecera', 'TrasladoController@obtenerCabecera');
        Route::get('/traslado/obtenerDetalles', 'TrasladoController@obtenerDetalles');
        Route::get('/traslado/nextNum','TrasladoController@getLastNum');
        Route::get('/traslado/pdf/{id}','TrasladoController@pdf');
        Route::post('/traslado/cambiarEntrega','TrasladoController@cambiarEntrega');
        Route::post('/traslado/actualizarObservacion','TrasladoController@actualizarObservacion');
        Route::put('/traslado/updImagen','TrasladoController@updImage');
        Route::put('/traslado/eliminarImg', 'TrasladoController@eliminarImagen');
        Route::get('/traslado/excel/{id}','TrasladoController@excelTraslado');
        Route::post('/traslado/actualizar','TrasladoController@update');

        Route::get('/trasladojava', 'TrasladoJavaController@index');
        Route::post('/trasladojava/registrar', 'TrasladoJavaController@store');
        Route::put('/trasladojava/desactivar', 'TrasladoJavaController@desactivar');
        Route::get('/trasladojava/obtenerCabecera', 'TrasladoJavaController@obtenerCabecera');
        Route::get('/trasladojava/obtenerDetalles', 'TrasladoJavaController@obtenerDetalles');
        Route::get('/trasladojava/nextNum','TrasladoJavaController@getLastNum');
        Route::get('/trasladojava/pdf/{id}','TrasladoJavaController@pdf');
        Route::post('/trasladojava/cambiarEntrega','TrasladoJavaController@cambiarEntrega');
        Route::post('/trasladojava/actualizarObservacion','TrasladoJavaController@actualizarObservacion');
        Route::put('/trasladojava/updImagen','TrasladoJavaController@updImage');
        Route::put('/trasladojava/eliminarImg', 'TrasladoJavaController@eliminarImagen');
        Route::get('/trasladojava/excel/{id}','TrasladoJavaController@excelTraslado');
        Route::post('/trasladojava/actualizar','TrasladoJavaController@update');

        Route::get('/trasladoabrasivo', 'TrasladoAbrasivoController@index');
        Route::post('/trasladoabrasivo/registrar', 'TrasladoAbrasivoController@store');
        Route::put('/trasladoabrasivo/desactivar', 'TrasladoAbrasivoController@desactivar');
        Route::get('/trasladoabrasivo/obtenerCabecera', 'TrasladoAbrasivoController@obtenerCabecera');
        Route::get('/trasladoabrasivo/obtenerDetalles', 'TrasladoAbrasivoController@obtenerDetalles');
        Route::get('/trasladoabrasivo/nextNum','TrasladoAbrasivoController@getLastNum');
        Route::get('/trasladoabrasivo/pdf/{id}','TrasladoAbrasivoController@pdf');
        Route::post('/trasladoabrasivo/cambiarEntrega','TrasladoAbrasivoController@cambiarEntrega');
        Route::post('/trasladoabrasivo/actualizarObservacion','TrasladoAbrasivoController@actualizarObservacion');
        Route::put('/trasladoabrasivo/updImagen','TrasladoAbrasivoController@updImage');
        Route::put('/trasladoabrasivo/eliminarImg', 'TrasladoAbrasivoController@eliminarImagen');
        Route::get('/trasladoabrasivo/excel/{id}','TrasladoAbrasivoController@excelTraslado');
        Route::post('/trasladoabrasivo/actualizar','TrasladoAbrasivoController@update');


        Route::get('/actividad', 'ActivityController@index');
        Route::post('/actividad/registrar', 'ActivityController@store');
        Route::put('/actividad/actualizar', 'ActivityController@update');
        Route::put('/actividad/desactivar', 'ActivityController@desactivar');
        Route::put('/actividad/cambiarEstado','ActivityController@cambiarEstado');
        Route::get('/actividad/getActivitiesUser','ActivityController@getActivitiesUser');
        Route::post('/actividad/crearComment', 'ActivityController@crearComment');
        Route::get('/actividad/getComments', 'ActivityController@getComments');
        Route::put('/actividad/editComment','ActivityController@editComment');
        Route::put('/actividad/deleteComment','ActivityController@deleteComment');
        Route::get('/actividad/getUsers', 'ActivityController@getUsers');

        Route::get('/call','CallController@index');
        Route::post('call/registrarCliente', 'CallController@storeCliente');
        Route::post('call/registrarProveedor', 'CallController@storeProveedor');
        Route::put('/call/actualizar', 'CallController@update');
        Route::put('/call/desactivar', 'CallController@desactivar');
        Route::post('/call/crearComment', 'CallController@crearComment');
        Route::get('/call/getComments', 'CallController@getComments');
        Route::put('/call/editComment','CallController@editComment');
        Route::put('/call/deleteComment','CallController@deleteComment');
        Route::put('/call/cambiarEstado','CallController@cambiarEstado');
        Route::get('/call/listarClave', 'CallController@listarClave');

        Route::get('/project', 'ProjectController@index');
        Route::get('/projectInvo', 'ProjectController@indexProject');
        Route::post('/project/registrar', 'ProjectController@store');
        Route::get('/project/getSales', 'ProjectController@getVentas');
        Route::put('/project/desactivar','ProjectController@desactivar');
        Route::post('/project/cambiarEntrega','ProjectController@cambiarEntrega');
        Route::post('/project/cambiarEntregaParcial','ProjectController@cambiarEntregaParcial');
        Route::post('/project/cambiarPagado','ProjectController@cambiarPagado');
        Route::post('/project/actualizarObservacion','ProjectController@actualizarObservacion');
        Route::post('/project/actualizarObservacionPriv','ProjectController@actualizarObservacionPriv');
        Route::post('/project/crearDeposit', 'ProjectController@crearDeposit');
        Route::get('/project/getDeposits', 'ProjectController@getDeposits');
        Route::put('/project/eliminarDeposit','ProjectController@deleteDeposit');
        Route::get('/project/refreshProject', 'ProjectController@refreshProject');
        Route::put('/project/actualizar', 'ProjectController@update');
        Route::put('/project/filesupplo', 'ProjectController@filesUppload');
        Route::get('/project/getDocs', 'ProjectController@getDocs');
        Route::put('/project/eliminarDoc', 'ProjectController@eliminarDoc');
        Route::post('/project/crearDepositCredit', 'ProjectController@crearDepositCredit');
        Route::get('/project/ExportExcel','ProjectController@listarExcel');
        Route::get('/project/pdf/{id}','ProjectController@pdf');
        Route::get('/project/getVentasClienteProject','ProjectController@getVentasClienteProject');
        Route::put('/project/cambiarFacturacion','ProjectController@cambiarFacturacion');
        Route::put('/project/cambiarFacturacionEnv','ProjectController@cambiarFacturacionEnv');
        Route::get('/project/obtenerCabecera','ProjectController@obtenerCabecera');
        Route::get('/project/AbonoProjectExportExcel','ProjectController@ListarAbonosProjectExcel');


        Route::get('/gallery', 'GalleryController@index');
        Route::post('/gallery/registrar', 'GalleryController@store');
        Route::get('/gallery/getLinks','GalleryController@getLinks');
        Route::put('/gallery/deleteLink','GalleryController@deleteLink');
        Route::put('/gallery/actualizar', 'GalleryController@update');
        Route::put('/gallery/desactivar', 'GalleryController@desactivar');
        Route::put('/gallery/activar', 'GalleryController@activar');
        Route::get('/gallery/refreshGallery', 'GalleryController@refreshGallery');

        Route::get('/credito', 'CreditController@index');
        Route::put('/credito/cambiarEstado','CreditController@cambiarEstado');

        Route::get('/oferta', 'OfertaController@index');
        Route::post('/oferta/registrar', 'OfertaController@store');
        Route::put('/oferta/desactivar', 'OfertaController@desactivar');
        Route::get('/oferta/obtenerCabecera','OfertaController@obtenerCabecera');
        Route::get('/oferta/obtenerDetalles','OfertaController@obtenerDetalles');
        Route::get('/oferta/pdf/{id}','OfertaController@pdf');
        Route::put('/oferta/aceptarCotizacion', 'OfertaController@aceptarCotizacion');
        Route::get('/oferta/nextNum','OfertaController@getLastNum');
        Route::post('/oferta/actualizarObservacion', 'OfertaController@actualizarObservacion');
        Route::put('/oferta/desactivarVenta', 'OfertaController@desactivarVenta');

        Route::get('/plantilla', 'PlantillaController@index');
        Route::post('/plantilla/registrar', 'PlantillaController@store');
        Route::put('/plantilla/filesupplo', 'PlantillaController@filesUppload');
        Route::get('/plantilla/getDocs', 'PlantillaController@getDocs');
        Route::put('/plantilla/eliminarDoc', 'PlantillaController@eliminarDoc');
        Route::get('/plantilla/obtenerCabecera', 'PlantillaController@obtenerCabecera');

        Route::get('/reclamo', 'ReclamoController@index');
        Route::post('/reclamo/registrar', 'ReclamoController@store');
        Route::put('/reclamo/desactivar', 'ReclamoController@desactivar');
        Route::get('/reclamo/obtenerCabecera', 'ReclamoController@obtenerCabecera');
        Route::get('/reclamo/obtenerDetalles', 'ReclamoController@obtenerDetalles');
        Route::get('/reclamo/nextNum','ReclamoController@getLastNum');
        Route::get('/reclamo/pdf/{id}','ReclamoController@pdf');
        Route::post('/reclamo/cambiarEntrega','ReclamoController@cambiarEntrega');
        Route::post('/reclamo/actualizarObservacion','ReclamoController@actualizarObservacion');
        Route::put('/reclamo/updImagen','ReclamoController@updImage');
        Route::put('/reclamo/eliminarImg', 'ReclamoController@eliminarImagen');
        Route::get('/reclamo/excel/{id}','ReclamoController@excelReclamo');
        Route::post('/reclamo/actualizar','ReclamoController@update');
        Route::put('/reclamo/updProceso', 'ReclamoController@updProceso');
        Route::put('/reclamo/atendido', 'ReclamoController@atendido');
        Route::put('/reclamo/noProcedio', 'ReclamoController@noProcedio');
        Route::put('/reclamo/storeFolio', 'ReclamoController@guardarFolio');



    });
});

