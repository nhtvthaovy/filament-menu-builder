<?php

declare(strict_types=1);

namespace nhtvthaovy\FilamentMenuBuilder\Livewire;

use nhtvthaovy\FilamentMenuBuilder\Enums\LinkTarget;
use nhtvthaovy\FilamentMenuBuilder\Models\Menu;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;

class CreateCustomLink extends Component implements HasForms
{
    use InteractsWithForms, WithFileUploads;

    public Menu $menu;
    public ?array $data = [];
    public $image;

    public function mount(Menu $menu): void
    {
        $this->menu = $menu;

        $this->form->fill([
            'title' => '',
            'url' => '',
            'description' => '',
            'target' => LinkTarget::Self->value,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label(__('filament-menu-builder::menu-builder.form.title'))
                    ->required(),
                TextInput::make('url')
                    ->label(__('filament-menu-builder::menu-builder.form.url'))
                    ->required(),
                Select::make('target')
                    ->label(__('filament-menu-builder::menu-builder.open_in.label'))
                    ->options(LinkTarget::class)
                    ->default(LinkTarget::Self),
                Textarea::make('description')
                    ->label(__('Mô tả'))
                    ->rows(2)
                    ->nullable(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->validate([
            'data.title' => 'required|string',
            'data.url' => 'required|string',
            'data.target' => ['required', Rule::in(array_map(fn($case) => $case->value, LinkTarget::cases()))],
            'data.description' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        $imagePath = null;
        if ($this->image) {
            $filename = 'menu-images/' . uniqid() . '.webp';
            $img = Image::make($this->image->getRealPath())->encode('webp', 100);
            Storage::disk('public')->put($filename, $img);
            $imagePath = $filename;
        }

        $this->menu->menuItems()->create([
            'title' => $this->data['title'],
            'url' => $this->data['url'],
            'target' => $this->data['target'],
            'description' => $this->data['description'] ?? null,
            'image' => $imagePath,
            'order' => ($this->menu->menuItems()->max('order') ?? 0) + 1,
        ]);

        Notification::make()
            ->title('Đã thêm liên kết tùy chỉnh!')
            ->success()
            ->send();

        $this->form->fill([
            'title' => '',
            'url' => '',
            'description' => '',
            'target' => LinkTarget::Self->value,
        ]);

        $this->reset('image');
        $this->dispatch('menu:created');
    }

    public function render(): View
    {
        return view('filament-menu-builder::livewire.create-custom-link');
    }
}
