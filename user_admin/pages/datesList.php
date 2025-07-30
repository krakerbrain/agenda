<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$auth->validarTokenUsuario();

?>
<link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/table.css?v=<?php echo time(); ?>">

<div class="mx-auto px-2 w-full">
    <ul class="flex border-b border-gray-200 mb-4" id="myTab" role="tablist">
        <li class="flex-1 mr-2 text-center" role="presentation">
            <button
                class="inline-flex flex-col md:flex-row items-center justify-center py-2 md:md:px-4 text-sm font-medium text-gray-700 bg-white w-full gap-1 md:gap-2 cursor-pointer"
                data-bs-target="#unconfirmed">
                <span class="text-cyan-500 text-lg md:text-base"><i class="fa-solid fa-clock"></i></span>
                <span class="text-xs md:text-sm">Por Confirmar</span>
            </button>
        </li>
        <li class="flex-1 mr-2 text-center" role="presentation">
            <button
                class="inline-flex flex-col md:flex-row items-center justify-center py-2 md:px-4 text-sm font-medium text-gray-700 bg-white w-full gap-1 md:gap-2 cursor-pointer"
                data-bs-target="#confirmed">
                <span class="text-green-500 text-lg md:text-base"><i class="fa-solid fa-check-circle"></i></span>
                <span class="text-xs md:text-sm">Confirmadas</span>
            </button>
        </li>
        <li class="flex-1 mr-2 text-center" role="presentation">
            <button
                class="inline-flex flex-col md:flex-row items-center justify-center py-2 md:px-4 text-sm font-medium text-gray-700 bg-white w-full gap-1 md:gap-2 cursor-pointer"
                data-bs-target="#past">
                <span class="text-gray-400 text-lg md:text-base"><i class="fa fa-calendar-xmark"></i></span>
                <span class="text-xs md:text-sm">Pasadas</span>
            </button>
        </li>
        <li class="flex-1 mr-2 text-center" role="presentation">
            <button
                class="inline-flex flex-col md:flex-row items-center justify-center py-2 md:px-4 text-sm font-medium text-gray-700 bg-white w-full gap-1 md:gap-2 cursor-pointer"
                data-bs-target="#all">
                <span class="text-blue-500 text-lg md:text-base"><i class="fa-solid fa-list"></i></span>
                <span class="text-xs md:text-sm">Todas</span>
            </button>
        </li>
        <li class="flex-1 mr-2 text-center" role="presentation">
            <button
                class="inline-flex flex-col md:flex-row items-center justify-center py-2 md:px-4 text-sm font-medium text-gray-700 bg-white w-full gap-1 md:gap-2 cursor-pointer"
                data-bs-target="#events">
                <span class="text-purple-500 text-lg md:text-base"><i class="fa-solid fa-person-chalkboard"></i></span>
                <span class="text-xs md:text-sm">Eventos</span>
            </button>
        </li>
    </ul>


    <!-- Offcanvas para el formulario de búsqueda -->
    <div id="offcanvasSearch"
        class="fixed inset-x-0 top-0 z-50 h-auto bg-white shadow-lg transform -translate-y-full transition-transform duration-300 ease-in-out overflow-y-auto">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h5 class="text-lg font-medium">Filtro de búsqueda</h5>
            <button id="offcanvasSearchClose" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-4">
            <form id="searchForm" class="flex flex-col sm:flex-row gap-2 pt-2">
                <input class="form-input form-input-sm shadow p-1 w-full" type="text" id="service" name="service"
                    placeholder="Buscar servicio..." autocomplete="off">
                <input class="form-input form-input-sm shadow p-1 w-full" type="text" id="name" name="name"
                    placeholder="Buscar nombre..." autocomplete="off">
                <input class="form-input form-input-sm shadow p-1 w-full" type="text" id="phone" name="phone"
                    placeholder="Buscar teléfono..." autocomplete="off">
                <input class="form-input form-input-sm shadow p-1 w-full" type="text" id="mail" name="mail"
                    placeholder="Buscar correo..." autocomplete="off">
                <input class="form-input form-input-sm shadow p-1 w-full" type="date" id="date" name="date">
                <input class="form-input form-input-sm shadow p-1 w-full" type="time" id="hour" name="hour">
                <select class="form-select form-select-sm shadow p-1 w-full" id="status" name="status">
                    <option value="all">Seleccionar estado</option>
                    <option value="1">Confirmada</option>
                    <option value="0">Pendiente</option>
                </select>
                <button
                    class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2 px-4 rounded-md text-sm transition w-full sm:w-auto"
                    type="submit" id="searchButton">Buscar</button>
            </form>
        </div>
    </div>
    <!-- Backdrop -->
    <div id="offcanvasSearchBackdrop" class="fixed inset-0 z-40 bg-black opacity-50 hidden"></div>
    <div class="mt-2 rounded-lg">
        <div class="flex justify-between items-center bg-white p-4">
            <!-- Botón para abrir el offcanvas de búsqueda -->
            <div class="flex justify-end mb-2">
                <button
                    class="bg-cyan-500 hover:bg-cyan-600 text-white font-semibold py-2 px-4 rounded-md text-sm transition flex items-center gap-2"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#searchOffcanvas"
                    aria-controls="searchOffcanvas" id="offcanvasToggleSearch">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span class="hidden sm:inline">Filtros de búsqueda</span>
                </button>
            </div>
            <div id="paginator" class="flex justify-end items-center gap-2 py-2 pe-2">
                <button id="prevPage"
                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded disabled:opacity-50 transition"
                    title="Ir a la página anterior" disabled>
                    <i class="fas fa-arrow-left"></i>
                </button>
                <span id="currentPage" class="text-sm font-medium">Página 1</span>
                <button id="nextPage"
                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded disabled:opacity-50 transition">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
        <table class="min-w-full bg-white appointments-table">
            <thead>
                <tr class="bg-cyan-50 text-cyan-800 text-sm font-semibold head-table">
                    <!-- Columnas dinámicas -->
                </tr>
            </thead>
            <tbody id="appointmentsContent" class="divide-y divide-gray-100">
                <!-- Se llenará con JavaScript -->
            </tbody>
        </table>
    </div>
</div>
<?php include dirname(__DIR__, 2) . '/includes/modal-appointments.php'; ?>