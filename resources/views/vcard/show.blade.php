<x-layouts.app :title="$vCard->title . ' | Smart VCard'">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="memphis-card max-w-md w-full p-8 text-center relative">
            <div class="absolute -top-4 -right-4 w-10 h-10 bg-acid-lime border-4 border-black rounded-full"></div>
            <div class="absolute -bottom-3 -left-3 w-8 h-8 bg-acid-pink border-4 border-black rotate-45"></div>

            @if($vCard->avatar_path)
                <div class="relative inline-block mb-6">
                    <img
                        src="{{ Storage::url($vCard->avatar_path) }}"
                        alt="{{ $vCard->title }}"
                        class="w-32 h-32 rounded-full border-4 border-black object-cover mx-auto"
                    />
                </div>
            @else
                <div class="w-32 h-32 rounded-full border-4 border-black bg-gradient-acid flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl font-black text-white">{{ substr($vCard->title, 0, 1) }}</span>
                </div>
            @endif

            <h1 class="text-4xl font-black mb-2">
                {{ $vCard->title }}
            </h1>

            @if($vCard->bio)
                <p class="text-gray-600 mb-8 text-lg font-medium">{{ $vCard->bio }}</p>
            @endif

            @if($vCard->social_links->isNotEmpty())
                <div class="space-y-3">
                    @foreach($vCard->social_links as $platform => $url)
                        <a
                            href="{{ $url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="social-btn block py-3 px-6 hover:bg-acid-lime transition-colors"
                        >
                            <span class="flex items-center justify-center gap-3">
                                <span>{{ $platform }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0 0L10 14" />
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 pt-6 border-t-4 border-black">
                <a
                    href="{{ route('vcard.qr.download', $vCard->slug) }}"
                    class="memphis-btn-secondary inline-block py-2 px-6 text-sm"
                >
                    Скачать QR-код ↓
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
