<?php

use App\Models\VCard;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    #[Rule('required|min:2|max:100')]
    public string $title = '';

    #[Rule('nullable|max:500')]
    public string $bio = '';

    #[Rule('nullable|image|max:2048')]
    public $avatar = null;

    #[Rule('nullable|array')]
    public array $socialLinks = [
        'telegram' => '',
        'github' => '',
        'linkedin' => '',
        'twitter' => '',
        'instagram' => '',
        'website' => '',
    ];

    public ?string $savedSlug = null;

    public function save()
    {
        $this->validate();

        $avatarPath = null;

        if ($this->avatar) {
            $avatarPath = $this->avatar->store('avatars', 'public');
        }

        $filteredSocial = collect($this->socialLinks)
            ->filter(fn($value) => !empty(trim($value)))
            ->toArray();

        $vCard = VCard::create([
            'title' => $this->title,
            'bio' => $this->bio ?: null,
            'avatar_path' => $avatarPath,
            'social_links' => $filteredSocial,
        ]);

        $this->savedSlug = $vCard->slug;

        $this->dispatch('vcard-created', slug: $this->savedSlug);
    }

    #[Computed]
    public function qrCode(): string
    {
        if (empty($this->title)) {
            return '';
        }

        $tempVCard = new VCard([
            'title' => $this->title,
            'bio' => $this->bio,
            'social_links' => collect($this->socialLinks)->filter(fn($v) => !empty(trim($v)))->toArray(),
            'slug' => 'preview-' . uniqid(),
        ]);

        if ($this->avatar) {
            $tempPath = 'livewire-tmp/' . uniqid() . '.' . $this->avatar->getClientOriginalExtension();
            Storage::disk('public')->put($tempPath, $this->avatar->get());
            $tempVCard->avatar_path = $tempPath;
        }

        try {
            return app(QrCodeService::class)->generate($tempVCard);
        } finally {
            if (isset($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
        }
    }

    public function render()
    {
        return view('components.⚡v-card-constructor');
    }
};
?>

<div class="max-w-7xl mx-auto px-4">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="memphis-card p-8 space-y-6">
            <h2 class="text-2xl font-black uppercase tracking-wide">Заполни данные</h2>

            <div>
                <label class="block text-sm font-bold mb-2 uppercase tracking-wider">Имя / Название</label>
                <input
                    type="text"
                    wire:model.live="title"
                    placeholder="John Doe"
                    class="memphis-input w-full px-4 py-3"
                />
                @error('title') <span class="text-acid-pink text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 uppercase tracking-wider">Био</label>
                <textarea
                    wire:model.live="bio"
                    rows="3"
                    placeholder="Разработчик. Дизайнер. Мечтатель."
                    class="memphis-input w-full px-4 py-3 resize-none"
                ></textarea>
                @error('bio') <span class="text-acid-pink text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 uppercase tracking-wider">Аватар</label>
                <div class="flex items-center gap-4">
                    <input
                        type="file"
                        wire:model.live="avatar"
                        accept="image/*"
                        class="memphis-input w-full px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-2 file:border-black file:bg-acid-purple file:text-white file:font-bold hover:file:bg-acid-pink transition-all"
                    />
                    @if($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" class="w-20 h-20 rounded-full border-4 border-black object-cover" />
                    @endif
                </div>
                @error('avatar') <span class="text-acid-pink text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold mb-3 uppercase tracking-wider">Соцсети</label>
                <div class="space-y-3">
                    @foreach($socialLinks as $platform => $url)
                        <div class="flex items-center gap-3">
                            <span class="w-24 text-xs font-black uppercase text-acid-pink">{{ $platform }}</span>
                            <input
                                type="url"
                                wire:model.live="socialLinks.{{ $platform }}"
                                placeholder="https://..."
                                class="memphis-input flex-1 px-3 py-2 text-sm"
                            />
                        </div>
                    @endforeach
                </div>
            </div>

            <button
                wire:click="save"
                wire:loading.attr="disabled"
                class="memphis-btn w-full py-4 text-lg disabled:opacity-50"
            >
                <span wire:loading.remove>Создать визитку 🚀</span>
                <span wire:loading class="animate-pulse">Генерация...</span>
            </button>

            @if($savedSlug)
                <div class="p-4 bg-acid-lime border-4 border-black rounded-xl font-bold">
                    Визитка создана! <a href="{{ route('vcard.show', $savedSlug) }}" class="underline hover:no-underline">Открыть публичную страницу →</a>
                </div>
            @endif
        </div>

        <div class="flex flex-col items-center justify-start space-y-6">
            <div class="text-center">
                <h3 class="text-xl font-black uppercase tracking-wide mb-1">QR-код превью</h3>
                <p class="text-xs text-gray-500 font-medium">Обновляется в реальном времени</p>
            </div>

            <div class="memphis-card p-8">
                @if($this->qrCode)
                    <div class="w-64 h-64 mx-auto rounded-xl overflow-hidden border-4 border-black" style="background: #0a0a12;">
                        {!! $this->qrCode !!}
                    </div>
                @else
                    <div class="w-64 h-64 flex items-center justify-center bg-gray-100 rounded-xl border-4 border-dashed border-gray-300">
                        <span class="text-gray-400 text-sm text-center font-bold">Введите имя<br>для генерации QR</span>
                    </div>
                @endif
            </div>

            @if($savedSlug)
                <a
                    href="{{ route('vcard.qr.download', $savedSlug) }}"
                    class="memphis-btn-secondary inline-block py-3 px-8"
                >
                    Скачать QR-код ↓
                </a>
            @endif
        </div>
    </div>
</div>
