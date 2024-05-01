<x-layouts.app title="Smart VCard Generator">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="text-center max-w-2xl mx-auto">
            <div class="inline-block memphis-badge bg-acid-lime mb-6 text-sm">
                GENERATOR v1.0
            </div>

            <h1 class="text-6xl md:text-8xl font-black mb-2 leading-none">
                <span class="text-outline">SMART</span><br>
                <span class="text-gradient-acid">V-CARD</span>
            </h1>

            <p class="text-gray-600 text-xl mb-12 max-w-md mx-auto font-medium">
                Создай цифровую визитку с кастомным QR-кодом за пару кликов
            </p>

            <a
                href="{{ route('vcard.create') }}"
                class="memphis-btn inline-block py-5 px-12 text-xl"
            >
                Создать визитку →
            </a>

            <div class="mt-16 grid grid-cols-3 gap-6 max-w-lg mx-auto">
                <div class="memphis-card p-5 text-center">
                    <div class="text-3xl font-black text-acid-pink">QR</div>
                    <div class="text-xs font-bold text-gray-500 mt-2 uppercase">Кастомный код</div>
                </div>
                <div class="memphis-card p-5 text-center">
                    <div class="text-3xl font-black text-acid-cyan">SVG</div>
                    <div class="text-xs font-bold text-gray-500 mt-2 uppercase">Векторный формат</div>
                </div>
                <div class="memphis-card p-5 text-center">
                    <div class="text-3xl font-black text-acid-lime">LIVE</div>
                    <div class="text-xs font-bold text-gray-500 mt-2 uppercase">Реальное время</div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
