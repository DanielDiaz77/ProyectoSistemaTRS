/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

window.$ = window.jQuery = require('jquery');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('categoria', require('./components/Categoria.vue').default);
Vue.component('articulo', require('./components/Articulo.vue').default);
Vue.component('articuloeditar', require('./components/ArticuloEditar.vue').default);
Vue.component('cliente', require('./components/Cliente.vue').default);
Vue.component('proveedor', require('./components/Proveedor.vue').default);
Vue.component('rol', require('./components/Rol.vue').default);
Vue.component('user', require('./components/User.vue').default);
Vue.component('ingreso', require('./components/Ingreso.vue').default);
Vue.component('venta', require('./components/Venta.vue').default);
Vue.component('dashboard', require('./components/Dashboard.vue').default);
Vue.component('consultaingreso', require('./components/ConsultaIngreso.vue').default);
Vue.component('consultaventa', require('./components/ConsultaVenta.vue').default);
Vue.component('notification', require('./components/Notification.vue').default);
Vue.component('cotizacion', require('./components/Cotizacion.vue').default);
Vue.component('entrega', require('./components/Entrega.vue').default);
Vue.component('ayuda', require('./components/Ayuda.vue').default);
Vue.component('acerca', require('./components/Acerca.vue').default);
Vue.component('tarea', require('./components/Tarea.vue').default);
Vue.component('calendario', require('./components/Calendario.vue').default);
Vue.component('traslado', require('./components/Traslado.vue').default);
Vue.component('facturacion', require('./components/Facturacion.vue').default);
Vue.component('consultaactividad', require('./components/ConsultaActividad.vue').default); //Consulta de eventos
Vue.component('actividad', require('./components/Actividad.vue').default);
Vue.component('recadero', require('./components/Call.vue').default);
Vue.component('project', require('./components/Project.vue').default);
Vue.component('galeria', require('./components/Galeria.vue').default);
Vue.component('credito', require('./components/Creditos.vue').default);
Vue.component('entrada', require('./components/Entrada.vue').default);
Vue.component('facturacionpe', require('./components/FacturacionPE.vue').default);
Vue.component('cotizacionp', require('./components/CotizacionP.vue').default);
Vue.component('articuloeditado', require('./components/ArticuloEditado.vue').default);
Vue.component('detalles', require('./components/Detalles.vue').default);
Vue.component('javas' ,require('./components/Javas.vue').default);
Vue.component('ingresojava' ,require('./components/IngresoJava.vue').default);
Vue.component('ventajava' ,require('./components/VentaJava.vue').default);
Vue.component('cotizacionjava', require('./components/CotizacionJava.vue').default);
Vue.component('trasladojava', require('./components/TrasladoJava.vue').default);
Vue.component('abrasivos', require('./components/Abrasivos.vue').default);
Vue.component('ingresoabrasivo', require('./components/IngresoAbrasivo.vue').default);
Vue.component('ventaabrasivo', require('./components/VentaAbrasivo.vue').default);
Vue.component('cotizacionabrasivo', require('./components/CotizacionAbrasivo.vue').default);
Vue.component('trasladoabrasivo', require('./components/TrasladoAbrasivo.vue').default);
Vue.component('bloques', require('./components/Bloques.vue').default);
Vue.component('ingresobloque', require('./components/IngresoBloque.vue').default);
Vue.component('facturacionjava', require('./components/FacturacionJava.vue').default);
Vue.component('facturacionabrasivo', require('./components/FacturacionAbrasivo.vue').default);
Vue.component('plantilla', require('./components/Plantilla.vue').default);
Vue.component('reclamo', require('./components/Reclamo.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data: {
        menu: 0,
        notifications: [],
        numActivities: 0,
        userId: 0,
        autoIngresos: 0,
        usedit: 0,

    },
    created() {
        let me = this;

        me.userId = $('meta[name="userId"]').attr('content');

        axios.post('notification/get').then(function(response) {
            me.notifications = response.data;
        }).catch(function(error) {
            console.log(error);
        });

        var userId = $('meta[name="userId"]').attr('content');
        Echo.private('App.User.' + userId).notification((notification) => {
            me.notifications.unshift(notification);
        });

        axios.get('/actividad/getActivitiesUser').then(function(response) {
            var respuesta = response.data;
            let numAct = respuesta.total;
            me.numActivities = respuesta.total;
            if (numAct != 0) {
                if (numAct == 1) {
                    swal.fire(
                        'Atención',
                        `Tienes ${numAct} tarea pendiente por realizar!`,
                        'warning'
                    )
                } else {
                    swal.fire(
                        'Atención',
                        `Tienes ${numAct} tareas pendientes por realizar!`,
                        'warning'
                    )
                }
            }

        }).catch(function(error) {
            console.log(error);
        });

        axios.put('/user/setLastConnection', {
            'id': this.userId
        }).then(function(response) {}).catch(function(error) {
            console.log(error);
        });

        axios.get('/user/getUserPerms/' + this.userId).then(function(resp) {
            me.autoIngresos = resp.data.Permiso;
        }).catch(function(error) {
            console.log(error);
        });

        axios.get('/user/getUserEditar/' + this.userId).then(function(resp) {
            me.usedit = resp.data.Permiso;
        }).catch(function(error) {
            console.log(error);
        });
    }
});
