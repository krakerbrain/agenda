<div class="container mx-auto px-4 max-w-4xl">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4">
        <button
            class="px-4 py-2 border border-blue-500 text-blue-500 rounded hover:bg-blue-50 transition-colors mb-2 md:mb-0"
            id="mark-all-read">
            Marcar todas como leídas
        </button>
        <div id="toast-container" class="z-[1100]"></div>
    </div>

    <div class="space-y-2" id="all-notifications-list">
        <div class="text-center py-4">
            <div class="inline-block h-8 w-8 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin"
                role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>
    </div>
</div>