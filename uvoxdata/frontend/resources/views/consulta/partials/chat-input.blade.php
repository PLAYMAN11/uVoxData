<form id="consulta-form" class="bg-white rounded-xl shadow p-6">
    <label for="pregunta" class="block text-sm font-medium text-gray-700 mb-2">
        Escribe tu consulta electoral
    </label>
    <textarea
        id="pregunta"
        name="pregunta"
        rows="4"
        maxlength="2000"
        placeholder="Ej. ¿Cuál es el plazo para impugnar un resultado electoral según la LGSMIME?"
        class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
    ></textarea>
    <div class="flex justify-between items-center mt-3">
        <span id="char-count" class="text-xs text-gray-400">0 / 2000</span>
        <button
            type="submit"
            class="bg-blue-800 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition"
        >
            Consultar
        </button>
    </div>
</form>
