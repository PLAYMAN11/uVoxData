<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-sm font-medium text-gray-700 mb-3">Subir documento (opcional)</h2>
    <form id="upload-form" enctype="multipart/form-data" class="flex items-center gap-4">
        <label class="flex-1 cursor-pointer border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition">
            <input type="file" name="documento" accept=".pdf" class="hidden" id="file-input">
            <span id="file-label" class="text-sm text-gray-500">Selecciona un PDF o arrástralo aquí</span>
        </label>
        <button
            type="submit"
            class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-600 transition"
        >
            Indexar
        </button>
    </form>
    <p id="upload-status" class="mt-2 text-xs text-gray-400"></p>
</div>
