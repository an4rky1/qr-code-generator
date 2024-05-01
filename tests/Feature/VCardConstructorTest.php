<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VCardConstructorTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders(): void
    {
        Livewire::test('v-card-constructor')
            ->assertSet('title', '')
            ->assertSet('bio', '')
            ->assertSee('Имя / Название')
            ->assertSee('Соцсети');
    }

    public function test_title_validation(): void
    {
        Livewire::test('v-card-constructor')
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    }

    public function test_save_creates_vcard(): void
    {
        Livewire::test('v-card-constructor')
            ->set('title', 'Test User')
            ->set('bio', 'Developer')
            ->set('socialLinks.telegram', 'https://t.me/test')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('savedSlug', fn($value) => $value !== null);
    }
}
