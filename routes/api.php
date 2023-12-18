<?php

/*Rutas login*/
use Illuminate\Http\Request;
use App\Http\Resources\EmpleadoResource;
use Illuminate\Support\Facades\Route;



Route::post('/login', 'AuthController@login')->name('login');
Route::post('/loginApp', 'AuthController@loginApp')->name('loginApp');
Route::post('/logout', 'AuthController@logout');
Route::post('/forgot-password','AuthenticationController@recoverPassword');
Route::get('test-notificaciones','PushController@sendTest');
//Notificationes
Route::post('/all-notification', 'FCMNotificationController@sendAllNotification')->name('allNotification');
Route::post('/send-notification', 'FCMNotificationController@sendNotificationToUser')->name('sendNotification');
Route::post('/store-fcmToken', 'FCMNotificationController@StoreTokenApi')->name('storeFCMToken');

Route::get('/imagenrotada','FacturaController@textrotatedImg');
Route::get('/generar-factura-mensual','FacturaController@GenerarFacturaProyectosMensuales');
Route::get('get-contenido-pendiente-redes/{token}', 'ContenidoRedSocialController@getPendienteByToken');

Route::post('aceptar-contenido-redes/{token}', 'ContenidoRedSocialController@Aceptar');
Route::post('rechazar-contenido-redes/{token}', 'ContenidoRedSocialController@Rechazar');
Route::post('fichar-esp', 'FicharController@ficharEsp');
Route::get('get-empleados-token-fichaje', 'FicharController@getEmpleadosToken');
Route::get('test-whatsapp/{to}','WhatsAppController@sendMesssage');
Route::post('webhook-save-venta-tfg','VentasWebhookController@SaveVentaTFG');
Route::post('import-save-venta-tfg','VentasWebhookController@importfromjson');
Route::get('send-notificaiton','PushController@checkTime');
Route::get('send-notification-seguimiento','PushController@checkTimeSeguimientos');
Route::post('upload-ventas-kit','VentasKitDigitalController@importVentasDiarias');
Route::get('get-estadisticas-ventas-diarias', 'VentasDiariasController@EstadisticasController');
Route::get('image-pdf/{nombre}','ContenidoRedSocialController@getPdfFirstPageImage');
Route::get('get-consumo-proyectos', 'ProyectoController@getConsumoTareas');
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/current-user',function(Request $request){
        return (new EmpleadoResource( $request->user()))->toArray($request);
    });
});
    Route::group(['middleware' => ['auth:sanctum','user-mutation']], function () {
    Route::post('webpush-subscribe', 'PushController@Subscribe');

   
    Route::get('/current-user-test',function(Request $request){
        return $request->user();
    });
    /*rutas para plantillas de whatsapp*/
    Route::get('/roles-tfg', 'AuthController@getRolesTFG');


    Route::get('/index-app/{idUser}', 'AppController@main');
    
    /*Ruta para el chat*/
    Route::get('get-chats', 'ChatController@getAllChats');
    Route::get('get-chat/{id?}', 'ChatController@getChat');
    Route::post('send-mensaje/{id?}', 'ChatController@guardarMensaje');
    Route::get('set-seen-messages/{chat_id?}', 'ChatController@setSeen');

    /*Ruta para obtener las provincias*/
    Route::get('get-provincias', 'ProvinciaController@getProvincias');
    /*Ruta para obtener los servicios*/
    Route::get('get-servicios', 'ServicioController@getServicios');
    /*Ruta para obtener los estados*/
    Route::get('get-estados', 'EstadoController@getEstados');

    /*Rutas Proyectos*/
    Route::get('get-all-proyectos/', 'ProyectoController@getAllProyectos');
    Route::get('get-cliente-proyectos/', 'ProyectoController@getAllProyectosCliente');

    Route::get('get-proyectos-activos', 'ProyectoController@getProyectosActivos');
    Route::get('get-proyectos-by-user/', 'ProyectoController@getProyectosByUser');
    Route::get('get-proyecto-by-id/{proyecto_id}', 'ProyectoController@getProyectoByid');
    Route::get('get-proyectos-by-user-id/{userid}/{tipo}', 'ProyectoController@getProyectosByUserId');
    Route::post('save-proyecto', 'ProyectoController@saveProyecto');
    
    Route::get('get-proyecto-estados', 'ProyectoController@getProyectoEstados'); // Pedimos al api que actualice el campo finalizado del estado de este Proyecto
    Route::post('save-proyecto-estados','ProyectoController@saveProyectoEstado');
    Route::post('delete-proyecto-estados','ProyectoController@deleteProyectoEstado');

    Route::get('delete-proyecto/{proyecto_id}', 'ProyectoController@deleteProyecto');// Tiene softdelete ( de elimina de la app pero se quedan los datos en BD )
    Route::get('update-project-status/{proyecto_estado_id}', 'ProyectoController@updateProyectoEstado'); // Pedimos al api que actualice el campo finalizado del estado de este Proyecto
    Route::get('delete-proyecto-estado/{proyecto_estado_id}', 'ProyectoController@deleteEstadoProyecto'); //Borramos un estado de proyecto existente
    Route::get('delete-file/{tipo}/{id}', 'ProyectoController@deleteFile');
    /*Rutas Reuniones*/
    Route::get('get-reuniones','ReunionController@GetReuniones');
    Route::get('get-reunion','ReunionController@GetReunion');

    Route::post('save-reunion','ReunionController@SaveReunion');
    Route::post('delete-reunion','ReunionController@cancelReunion');

    /*Rutas Proyectos Tareas*/
    Route::get('get-tareas-proyecto','ProyectoTareaController@GetTareas');
    Route::get('get-tareas-unicas-proyecto','ProyectoTareaController@GetTareasUnicas');

    Route::get('get-tarea-proyecto','ProyectoTareaController@GetTarea');
    Route::post('save-tareas-proyecto','ProyectoTareaController@SaveTarea');
    Route::post('cancel-tareas-proyecto','ProyectoTareaController@CancelTarea');
    Route::get('get-proyectos-for-tareas','ProyectoTareaController@GetProyectosForTareas');

    /*Rutas Estados de Proyecto*/
    Route::get('get-all-estados-potencial', 'ProyectoController@getEstados');
    Route::get('delete-estado-potencial/{id}', 'ProyectoController@DeleteEstados');
    Route::post('save-estado-potencial', 'ProyectoController@SaveEstados');
    /*Rutas Potenciales*/
    Route::get('get-all-potenciales/', 'ProyectoController@getAllPotenciales');
    
    Route::post('filtro-get-all-potenciales/', 'ProyectoController@getAllPotencialesFiltro');
    Route::get('get-potencial-by-id/{potencial_id}', 'ProyectoController@getPotencialByid');
    Route::post('save-potencial', 'ProyectoController@savePotencial');
    Route::get('delete-potencial/{potencial_id}', 'ProyectoController@deletePotencial');// Tiene softdelete ( de elimina de la app pero se quedan los datos en BD )
    Route::post('tiempo-tarea-proyecto', 'ProyectoController@getMinutosUtilizados');// Tiene softdelete ( de elimina de la app pero se quedan los datos en BD )

    /*Rutas Password*/
    Route::get('get-all-passwords/','PasswordController@getAllPasswords');
    Route::get('get-password-by-id/{password_id}', 'PasswordController@getPasswordByid');
    Route::post('save-password', 'PasswordController@savePassword');
    Route::get('delete-password/{password_id}', 'PasswordController@deletePassword');// Tiene softdelete ( de elimina de la app pero se quedan los datos en BD )

    /*Rutas Usuarios*/
    Route::get('get-usuarios', 'UsuarioController@getUsuarios');
    Route::post('get-usuarios-empleados', 'UsuarioController@getUsuariosEmpleados');
    Route::post('get-usuarios-empleados-all', 'UsuarioController@getUsuariosEmpleadosAll');

    // 
    Route::get('get-all-clientes', 'UsuarioController@getAllClientes');
    Route::get('get-all-clientes-inactive-proyectos/', 'UsuarioController@getAllInactiveClientes');
    Route::get('get-all-clientes-active-proyectos/', 'UsuarioController@getAllActiveClientes');
    Route::get('get-all-clientes-proyectos/', 'UsuarioController@getClientes');

    // 
    Route::get('get-usuario-by-id/{usuario_id}', 'UsuarioController@getUsuarioByid');
    Route::post('save-usuario', 'UsuarioController@saveUsuario');
    Route::post('update-usuario/{id}', 'UsuarioController@updateUsuario');
    Route::get('delete-usuario/{usuario_id}', 'UsuarioController@deleteUsuario');
    Route::get('get-methods-form', 'UsuarioController@getMethodsForm');

    /*Rutas Empresa*/
    Route::post('save-empresa','EmpresaController@saveEmpresa');
    Route::get('get-empresa/{id}','EmpresaController@getEmpresa');
    Route::get('delete-empresa/{id}','EmpresaController@deleteEmpresa');
    Route::get('get-empresas','EmpresaController@getEmpresas');
    
    /*Rutas Fichar*/
    Route::get('comprobar-fichaje/{status?}', 'FicharController@comprobarFichaje');

    Route::post('fichar/{id}', 'FicharController@fichar');
    Route::post('get-fichajes', 'FicharController@getFichajes');
    Route::get('get-fichaje/{id}', 'FicharController@getFichaje');
    Route::get('delete-fichaje/{id}', 'FicharController@deleteFichaje');
    Route::post('save-fichaje', 'FicharController@saveFichaje');

    Route::post('get-fichajes-by-user/{id}', 'FicharController@getFichajesByUser');
    Route::post('crear-pdf-fichajes', 'FicharController@crearPdfFichajes');

    /*Rutas Tareas*/
    Route::post('save-tarea', 'TareasController@saveTarea');
    Route::post('get-tareas', 'TareasController@getTareas');
    Route::post('buscar-tareas', 'TareasController@buscarTareas');
    Route::post('exportar-tareas', 'TareasController@exportarTareas');

    Route::post('delete-tarea/{tarea_id}', 'TareasController@eliminarTarea');

    /*Rutas Promociones*/
    Route::get('get-all-promociones', 'PromocionController@getAllpromociones');

    /* END Funcionan bien PROBADOS OK */
    Route::get('user',function(Request $request){
        return $request->user();
    });
    /*Rutas Facebook*/
    Route::post('/get-long-lived-token','FacebookWebhookController@FacebookGetLongLivedToken');
    Route::post('/get-page-access-token','FacebookWebhookController@FacebookGetPageAccessToken');
    Route::post('/get-add-app','FacebookWebhookController@FacebookAddAppRequest');
    /*Rutas Proveedor*/
    Route::get('get-proveedores/{user_id}', 'ProveedorController@getProveedores');
    Route::get('get-proveedor-by-id/{proveedor_id}', 'ProveedorController@getProveedorByid');
    Route::post('save-proveedor', 'ProveedorController@saveProveedor');
    Route::get('delete-proveedor/{proveedor_id}', 'ProveedorController@deleteProveedor');

    /*Rutas Albaranes*/
    Route::get('get-albaranes/{user_id}', 'AlbaranController@getAlbaranes');
    Route::get('get-albaran-by-id/{albaran_id}', 'AlbaranController@getAlbaranById');
    Route::post('save-albaran', 'AlbaranController@saveAlbaran');
    Route::post('update-albaran/{id}', 'AlbaranController@updatelbaran');
    Route::get('get-albaranes-enviados/{user_id}', 'AlbaranController@getnviados');
    Route::get('get-albaranes-enviados-show/{albaranId}', 'AlbaranController@albaranEnvidoShow');
    Route::post('save-albaran-enviado', 'AlbaranController@albaranesEnviadosF');
    Route::post('save-albaran-recibido', 'AlbaranController@albaranesRecibido');
    Route::post('delete-albaran-enviados/{albaran_id}', 'AlbaranController@deleteAlbaranEnviado');
    Route::post('save-factura-albaran', 'AlbaranController@generarFactura');
    Route::post('save-nota-albaran', 'AlbaranController@generarNota');
    Route::post('update-albaran-enviados/{id}', 'AlbaranController@updatealbaranesEnviadosF');
    Route::post('update-albaran-enviados-factura/{id}', 'AlbaranController@updateFactAlbaran');
    Route::get('delete-albaran/{albaran_id}', 'AlbaranController@deleteAlbaran');

    /*Rutas Recibo*/
    Route::get('get-recibos/{user_id}', 'PresupuestoController@getRecibos');
    Route::post('save-recibo/{tipo}/{convertir_factura}', 'ReciboController@saveRecibo');
    Route::get('get-recibo-by-id/{recibo_id}', 'ReciboController@getReciboById');
    Route::get('delete-recibo/{recibo_id}', 'PresupuestoController@deletePresupuesto');
    Route::get('remove-contabilizado/{elemento}/{idServicio}/{idRecibo}', 'ReciboController@removeContabilizado');
    Route::get('get-recibo-by-name/{elemento}', 'ReciboController@getReciboByName');

    /*Rutas factura*/
    Route::get('get-facturas/{user_id}', 'FacturaController@getFacturas');
    Route::get('delete-facturas/{id}', 'FacturaController@deleteFacturas');

    Route::get('get-facturas-by-proyecto/{id_proyecto}', 'FacturaController@getFacturasByProyecto');
    Route::get('get-facturas-by-cliente/{id_cliente}', 'FacturaController@getFacturasByCliente');

    Route::get('delete-factura/{factura_id}', 'FacturaController@deleteFactura');
    Route::get('facturas-recibidas/{idUser}', 'FacturaRecibidasController@index');
    Route::get('facturas-recibidas-show/{idFac}', 'FacturaRecibidasController@show');
    Route::post('facturas-recibidas/{idUser}', 'FacturaRecibidasController@store');
    Route::post('facturas-recibidas-update/{idFac}', 'FacturaRecibidasController@update');
    Route::post('facturas-recibidas-delete/{idFactRec}', 'FacturaRecibidasController@destroy');
    Route::get('get-data-albaranes/{user_id}', 'FacturaController@getDataAlbaranes');
    Route::get('cambiar-anio-fiscal', 'FacturaController@CambiarAnioFiscal');
    Route::get('duplicar-factura/{id}/{tipo}', 'FacturaController@DuplicateFactura');
    
    /*Rutas Notas*/
    Route::get('get-notas/{user_id}', 'NotaController@getNotas');
    Route::get('delete-nota/{recibo_id}', 'NotaController@deleteNota');

    /*Rutas Parte Trabajo*/
    Route::get('get-parte-trabajo/{user_id}', 'ParteTrabajoController@getParteTrabajo');
    Route::get('get-presupuestos-for-parte-trabajo/{user_id}', 'ParteTrabajoController@getPrespuestos');
    Route::get('get-presupuesto-asociado/{recibo_id}', 'ParteTrabajoController@getPresupuestoAsociado');
    Route::get('delete-parte-trabajo/{recibo_id}', 'ParteTrabajoController@deleteParteTrabajo');

    /*Rutas Ingreso*/
    Route::get('getAllingresos', 'IngresoController@getAllIngresos');
    Route::get('get-ingresos/{user_id}', 'IngresoController@getIngresos');
    Route::get('get-ingreso-by-id/{ingreso_id}', 'IngresoController@getIngresoById');
    Route::get('get-ingreso-by-pro_id/{proyecto_id}', 'IngresoController@getIngresoByProyectoId');
    Route::post('save-ingreso', 'IngresoController@saveIngreso');
    Route::get('delete-ingreso/{ingreso_id}', 'IngresoController@deleteIngreso');

    /*Rutas Gastos*/
    Route::get('get-gastos', 'GastosController@index');
    Route::get('get-gasto-by-id/{gasto_id}', 'GastosController@getGastoById');
    Route::post('save-gasto', 'GastosController@store');
    Route::post('update-gasto', 'GastosController@updateGasto');
    Route::get('delete-gasto/{gasto_id}', 'GastosController@destroy');
    Route::get('get-dashboard-gastos', 'GastosController@Dashboard');


    /*Rutas estadisticas ingreso*/
    Route::get('get-ingreso-bruto/{user_id}/{desde}/{hasta}', 'EstadisticasContabilidadController@getIngresoBruto');

    /*Rutas Morosos*/
    Route::get('get-morosos/{user_id}', 'MorososController@getMorosos');

    /*Rutas Email*/
    Route::post('send-email', 'MailController@sendEmail');

    /*Lote facturas*/
    Route::get('get-lote-facturas/{user_id}', 'LoteController@getFacturasByRango');
    Route::post('enviar-lote-facturas/{user_id}', 'LoteController@enviarFacturas');

    /*Rutas Gestor Documental*/
    Route::get('get-documentos/{user_id}', 'GestorDocumentalController@getDocumentos');
    Route::post('delete-documentos', 'GestorDocumentalController@deleteDocument');
    Route::post('create-folder', 'GestorDocumentalController@createFolder');
    Route::post('save-documents', 'GestorDocumentalController@saveDocuments');
    Route::post('download-document/{user_id}', 'GestorDocumentalController@downloadDocuments');
    Route::get('gestor-admin', 'GestorDocumentalController@adminGestionDocumental');
    Route::post('view-document', 'GestorDocumentalController@viewDocument');
    Route::get('get-tasks/{user_id}', 'GestorTareasController@index');
    Route::post('new-drag/{user_id}', 'GestorTareasController@newDrag');
    Route::put('update-drag/{drag_id}', 'GestorTareasController@updateDrag');
    Route::delete('delete-drag/{drag_id}', 'GestorTareasController@deleteDrag');
    Route::post('save-tasks/', 'GestorTareasController@store');
    Route::put('update-tasks/{tarea_id}', 'GestorTareasController@update');
    Route::put('update-tasks-status/{tarea_id}', 'GestorTareasController@updateStatus');
    Route::delete('delete-tasks/{tarea_id}', 'GestorTareasController@destroy');
    /*Rutas Tipos Gastos*/
    Route::get('get-tipos-gasto/{user_id}', 'TiposGastoController@getTiposGasto');
    Route::post('save-tipos-gasto', 'TiposGastoController@saveTiposGasto');
    Route::get('delete-tipos-gasto/{tipos_gasto_id}', 'TiposGastoController@deleteTiposGasto');

    /*Route::get('show-presupuestos/{proyecto_id}', 'PresupuestoController@showPresupuesto');
    Route::post('update-presupuestos/{presupuesto_id}', 'PresupuestoController@updatePresupuesto');
    Route::post('store-presupuestos/{proyecto_id}', 'PresupuestoController@storePresupuesto');
    Route::post('send-mail-presupuesto/{presupuesto_id}', 'PresupuestoController@sendMail');*/
    
    Route::post('change-factura-type','FacturaController@ChangeFacturaType');
    Route::get('index-facturas/{tipo}', 'FacturaController@indexFactura');
    Route::get('all-facturas', 'FacturaController@getAllFacturas');

    Route::get('show-facturas/{factura_id}', 'FacturaController@showFactura');
    Route::post('update-facturas/{factura_id}', 'FacturaController@updateFactura');
    Route::post('store-facturas', 'FacturaController@storeFactura');
    Route::post('delete-items-facturas', 'FacturaController@deleteItemFactura');
    //rutas de presupuesto
    Route::get('index-presupuestos', 'NewPresupuestoController@indexPresupuesto');
    Route::get('show-presupuestos/{presupuesto_id}', 'NewPresupuestoController@showPresupuesto');
    Route::post('update-presupuestos/{presupuesto_id}', 'NewPresupuestoController@updatePresupuesto');
    Route::post('store-presupuestos', 'NewPresupuestoController@storePresupuesto');
    Route::post('delete-items-presupuestos', 'NewPresupuestoController@deleteItemPresupuesto');
    //rutas de ventas diarias
    Route::post('save-venta-diaria', 'VentasDiariasController@saveVentaDiaria');
    Route::post('delete-venta-diaria', 'VentasDiariasController@deleteVentaDiaria');
    Route::get('get-ventas-diarias', 'VentasDiariasController@getVentasDiarias');
    Route::get('get-facturas-ventas-diarias', 'VentasDiariasController@getFacturas');
    Route::get('get-dashboard-ventas-diarias', 'VentasDiariasController@Dashboard');

    Route::get('get-resumen-ventas-diarias/{year}', 'VentasDiariasController@getResumenVentas');
    //rutas de ventas kit digital
    Route::post('save-venta-kit', 'VentasKitDigitalController@saveVentaKitDigital');
    Route::post('delete-venta-kit', 'VentasKitDigitalController@deleteVentaKitDigital');
    Route::get('get-ventas-kit', 'VentasKitDigitalController@getVentasKitDigital');
    Route::get('pagar-ventas-kit/{id}', 'VentasKitDigitalController@pagada');

    Route::get('get-ventas-kit-resumen/{year}', 'VentasKitDigitalController@getResumenVentas');

    Route::get('get-categorias-kit-digital', 'VentasKitDigitalController@getCategorias');

    //Rutas de promociones
    Route::get('add-token-redes/{id}', 'ContenidoRedSocialController@addtoken');
    Route::get('get-estados-contenido', 'ContenidoRedSocialController@getEstados');

    Route::get('get-contenido-redes/{token}', 'ContenidoRedSocialController@getAllbyToken');
    Route::get('get-contenido-redes-by-id/{id}', 'ContenidoRedSocialController@getContenidoById');
    Route::get('delete-contenido-redes/{id}', 'ContenidoRedSocialController@deleteContenido');
    Route::post('change-contenido-redes-estado', 'ContenidoRedSocialController@changeContenidoActive');
    Route::post('save-contenido-redes', 'ContenidoRedSocialController@saveContenido');

    //Rutas de promociones
    Route::get('get-all-promociones', 'PromocionController@getAllpromociones');
    Route::get('get-promocion-by-id/{promocion_id}', 'PromocionController@getPromocionById');
    Route::get('delete-promocion/{promocion_id}', 'PromocionController@deletePromocion');
    Route::get('change-promocion-active/{promocion_id}', 'PromocionController@changePromocionActive');
    Route::post('save-promocion', 'PromocionController@savePromocion');
    //Rutas Vacaciones
    Route::get('/get-vacaciones','VacacionesController@getVacaciones');
    Route::get('/get-vacaciones/{fecha_desde}/{fecha_hasta}','VacacionesController@getVacacionesCalendario');
    Route::get('/get-vacaciones/{year}','VacacionesController@getVacacionesYear');

    Route::post('/save-vacaciones','VacacionesController@saveVacaciones');
    Route::get('/delete-vacaciones/{id}','VacacionesController@deleteVacaciones');

    //Rutas Reservas de Sala
    Route::get('get-all-reservas', 'ReservasSalaController@getAllReservas');
    Route::get('get-desde-horas/{fecha}', 'ReservasSalaController@getDesdeHoras');
    Route::get('get-hasta-horas/{fecha}/{desdeHora}', 'ReservasSalaController@getHastaHoras');
    Route::post('save-reserva', 'ReservasSalaController@saveReunion');
    Route::get('delete-reserva/{reserva_id}', 'ReservasSalaController@deleteReserva');

    /*Rutas estadisticas*/
    Route::get('get-proyectos-data/{desde}/{hasta}', 'EstadisticasApiController@getProyectosData');
    Route::get('detalle-data/{desde}/{hasta}/{servicio_id}', 'EstadisticasApiController@getDetalleData');
    /* Rutas seguimiento */
    Route::get('get-seguimiento/{id}','SeguimientoController@getSeguimiento');
    Route::get('get-seguimientos','SeguimientoController@getSeguimientos');
    Route::get('get-ventas-seguimientos','SeguimientoController@getVentas');

    Route::get('delete-seguimiento/{id}','SeguimientoController@deleteSeguimiento');
    Route::post('save-seguimiento','SeguimientoController@saveSeguimiento');
    /* Rutas seguimiento Fase */
    Route::get('get-fase-seguimiento/{id}','SeguimientoController@getSeguimientoFase');
    Route::get('get-fases-seguimiento/{id}','SeguimientoController@getSeguimientoFases');
    Route::get('delete-fase-seguimiento/{id}','SeguimientoController@deleteSeguimientoFase');
    Route::post('save-fase-seguimiento','SeguimientoController@saveSeguimientoFase');

    
    /*Ruta para obtener las cuentas*/
    Route::get('get-cuentas-banco', 'CuentasController@getCuentas');
    /*Rutas de productos*/
    Route::get('get-productos-modulos','ProductoController@getModulos');

    Route::get('get-productos','ProductoController@getProductos');
    Route::get('get-producto/{id}','ProductoController@getProducto');
    Route::post('delete-producto','ProductoController@deleteProducto');
    Route::post('save-producto','ProductoController@saveProducto');
    /*Rutas de plantilla whatsapp*/
    Route::get('get-plantillas','WhatsAppController@getPlantillas');
    Route::get('get-plantilla/{id}','WhatsAppController@getPlantilla');
    Route::post('delete-plantilla','WhatsAppController@deletePlantilla');
    Route::post('save-plantilla','WhatsAppController@savePlantilla');
    /* Rutas TFG Gastos*/
    Route::get('get-tfg-gastos','TFGGastosController@getGastos');
    Route::get('get-tfg-gasto/{id}','TFGGastosController@getGasto');
    Route::post('delete-tfg-gasto','TFGGastosController@deleteGasto');
    Route::post('save-tfg-gasto','TFGGastosController@saveGasto');
    Route::get('get-dashboard-tfg-gastos','TFGGastosController@Dashboard');
    Route::get('get-dashboard-tfg-gastos-neto', 'TFGGastosController@DashboardNetos');
    Route::get('get-dashboard-tfg-gastos-caja', 'TFGGastosController@DashboardCaja');
    Route::get('get-tfg-gastos-tipos','TFGGastosController@getTipos');
    Route::post('delete-tfg-gasto-tipo','TFGGastosController@deleteTipo');
    Route::post('save-tfg-gasto-tipo','TFGGastosController@saveTipo');
    /*Rutas TFG Potenciales*/
    Route::get('get-potenciales-tfg-dia/{dia}','PotencialTfgController@getPotencialesDia');
    Route::get('get-potenciales-tfg-headers','PotencialTfgController@getPotencialesHeaders');
    Route::post('save-potenciales-tfg-headers','PotencialTfgController@savePotencial');
    Route::get('get-resumen-potenciales-tfg','PotencialTfgController@getResumenMes');
    Route::get('get-empresas-tfg','PotencialTfgController@getEmpresas');

    /*Rutas TFG Ventas Diarias*/
    Route::get('get-ventas-tfg','VentasDiariasTFGController@getVentasDiarias');
    Route::post('save-ventas-tfg','VentasDiariasTFGController@saveVentaDiaria');
    Route::post('delete-venta-tfg','VentasDiariasTFGController@deleteVentaDiaria');
    /*Rutas TFG Ventas Diarias*/
    Route::get('get-marketing-tfg','MarketingTFGController@getMarketingData');
    Route::get('get-marketing-tfg-dashboard','MarketingTFGController@getMarketingDataDahsboard');

    Route::post('save-marketing-tfg','MarketingTFGController@saveMarketingData');
    Route::post('delete-marketing-tfg','MarketingTFGController@deleteMarketingData');
    /*Rutas Marketing*/
    Route::get('get-marketing','MarketingController@getMarketingData');
    Route::post('save-marketing','MarketingController@saveMarketingData');
    Route::post('delete-marketing','MarketingController@deleteMarketingData');

    // Rutas Tickets
    Route::get('get-tickets', 'TicketsController@getAllTickets');
    Route::get('get-ticket/{id}', 'TicketsController@getTicket');
    Route::post('update-ticket', 'TicketsController@updateTicket');
    Route::get('delete-ticket/{id}', 'TicketsController@deleteTicket');
    Route::get('get-estado-tickets', 'TicketsController@getEstadoTickets');
});
Route::get('dashboard-ventas-tfg','VentasDiariasTFGController@Dashboard');
Route::get('dashboard-ventas-tfg-year','VentasDiariasTFGController@ResumenVentasByYear');

Route::get('get-ventas-tfg/{year}','VentasDiariasTFGController@getResumenVentas');
Route::get('get-stats-ventas-tfg','VentasDiariasTFGController@getEstadisticaVentas');
Route::get('get-stats-years-ventas-tfg','VentasDiariasTFGController@getEstadisticas');
Route::get('get-stats-years-ventas','VentasDiariasController@getEstadisticas');

Route::post('send-notification-social-mail','ContenidoRedSocialController@SendNotificationMail');
Route::post('envio-potencial','PotencialWebhookController@FormularioFidias');

// Rutas Tickets sin hacer login
Route::get('get-departamentos', 'TicketsController@getDepartamentos');
Route::get('get-urgencia', 'TicketsController@getUrgencia');
Route::get('get-usuario-by-attribute/{attribute}', 'UsuarioController@getUsersByAttribute');
Route::post('create-ticket', 'TicketsController@createTicket');