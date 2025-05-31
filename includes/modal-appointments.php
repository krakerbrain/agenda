<style>
#infoAppointment {
    z-index: 50;
}
</style>

<!-- Google Authenticate Modal -->
<div id="googleAuthenticateModal"
    class="fixed inset-0 z-50 overflow-y-auto hidden opacity-0 transition-all duration-300 ease-in-out">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="border-b pb-2 text-lg leading-6 font-medium text-gray-900"
                            id="googleAuthenticateModalLabel">
                            Autenticación necesario</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Autenticación inválida. Por favor, vuelve a iniciar sesión
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                    id="confirmAuthenticate">
                    Aceptar
                </button>
                <button type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    id="cancelAutoOpen">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Info Appointment Modal -->
<div id="infoAppointment"
    class="fixed inset-0 z-50 overflow-y-auto hidden opacity-0 transition-all duration-300 ease-in-out">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="border-b pb-2 text-lg leading-6 font-medium text-gray-900" id="infoAppointmentLabel">
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="infoAppointmentMessage"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal"
    class="fixed inset-0 z-50 overflow-y-auto hidden opacity-0 transition-all duration-300 ease-in-out">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-[#6b72808c] bg-opacity-0 transition-opacity duration-300 ease-in-out"
            aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="border-b pb-2 text-lg leading-6 font-medium text-gray-900" id="deleteModalLabel">¿Por
                            qué deseas
                            eliminar esta reserva?</h3>
                        <div class="mt-2">
                            <form id="reason-form">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input id="reason-no-show" name="reason" type="radio"
                                            value="El cliente no asistió."
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="reason-no-show" class="ml-2 block text-sm text-gray-700">El cliente
                                            no asistió.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="reason-last-minute" name="reason" type="radio"
                                            value="El cliente canceló a última hora."
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="reason-last-minute" class="ml-2 block text-sm text-gray-700">El
                                            cliente canceló a última hora.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="reason-no-confirmation" name="reason" type="radio"
                                            value="El cliente no confirmó la reserva."
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="reason-no-confirmation" class="ml-2 block text-sm text-gray-700">El
                                            cliente no confirmó la reserva.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="reason-early-cancel" name="reason" type="radio"
                                            value="El cliente canceló con anticipación."
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="reason-early-cancel" class="ml-2 block text-sm text-gray-700">El
                                            cliente canceló con anticipación.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="reason-booking-error" name="reason" type="radio"
                                            value="Error en la reserva."
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="reason-booking-error" class="ml-2 block text-sm text-gray-700">Error
                                            en la reserva.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="reason-business-cancel" name="reason" type="radio"
                                            value="El negocio canceló la reserva."
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="reason-business-cancel" class="ml-2 block text-sm text-gray-700">El
                                            negocio canceló la reserva.</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="reason-other" name="reason" type="radio" value="Otro"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="reason-other" class="ml-2 block text-sm text-gray-700">Otro</label>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notas
                                        adicionales:</label>
                                    <textarea id="notes" name="notes" rows="3"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="delete-button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    <span class="spinner hidden" aria-hidden="true"></span>
                    <span>Eliminar cita</span>
                </button>
                <button type="button" id="delete-and-incident-button"
                    class="my-2 md:my-0 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-500 text-base font-medium text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                    <span class="spinner hidden" aria-hidden="true"></span>
                    <span>Eliminar y generar incidencia</span>
                </button>
                <button type="button"
                    class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-modal">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Warning Modal -->
<div id="warningModal"
    class="fixed inset-0 z-50 overflow-y-auto hidden opacity-0 transition-all duration-300 ease-in-out">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="border-b pb-2 text-lg leading-6 font-medium text-gray-900" id="warningModalLabel">
                            Advertencia</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Es obligatorio seleccionar una de las incidencias
                                previamente mencionadas para generar una incidencia.</p>
                            <p class="text-sm text-gray-500 mt-2">Si considera que no hubo problemas, haga clic en
                                "Regresar" y luego en "Eliminar cita".</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    data-modal-target="#deleteModal">
                    Regresar
                </button>
            </div>
        </div>
    </div>
</div>