<div id="infoModal"
    class="fixed inset-0 z-50 items-center justify-center hidden pointer-events-none transition-opacity duration-300 opacity-0 p-4">
    <div class="absolute inset-0 bg-gray-500 opacity-50 transition-opacity"></div>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-md mx-auto overflow-hidden transform transition-all duration-300 scale-95 pointer-events-auto">
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#1B637F]/20">
            <h5 class="text-lg font-semibold" id="modalLabel"></h5>
            <button type="button"
                class="text-gray-400 hover:text-gray-700 text-2xl leading-none focus:outline-none close-modal"
                aria-label="Close">&times;</button>
        </div>
        <div class="px-4 py-4 text-gray-700" id="modalMessage">
        </div>
        <div class="flex justify-end px-4 py-3 border-t border-[#1B637F]/20">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded close-modal">Cerrar</button>
        </div>
    </div>
</div>