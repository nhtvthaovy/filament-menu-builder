<div wire:key="create-custom-link">
    <x-filament::section :heading="__('filament-menu-builder::menu-builder.custom_link')" :collapsible="true" :persist-collapsed="true" id="create-custom-link">
        <form wire:submit.prevent="save">
            {{ $this->form }}

            <div class="mt-6 mb-6">
                <label class="block text-sm font-medium text-black mb-2">Ảnh</label>
                <input type="file" wire:model="image" class="block w-full text-sm text-gray-900">
                @error('image')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror

                @if ($image)
                    <div class="mt-3 w-12 h-12 overflow-hidden rounded-md border border-gray-200">
                        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-full h-full object-contain">
                    </div>
                @endif

                <div wire:loading wire:target="image" class="text-blue-600 text-sm mt-2">
                    Đang tải ảnh..., Đợi chút
                </div>

                <div wire:loading.remove wire:target="image" class="text-green-600 text-sm mt-2"
                    @if (!$image) style="display:none;" @endif>
                    Upload xong!
                </div>
            </div>

            {{-- <div class="pt-4">
                <x-filament::button type="submit">
                    {{ __('filament-menu-builder::menu-builder.actions.add.label') }}
                </x-filament::button>
            </div> --}}
            <div class="pt-4 relative">
                <x-filament::button type="submit" wire:loading.attr="disabled">
                    {{ __('filament-menu-builder::menu-builder.actions.add.label') }}
                </x-filament::button>

                <span wire:loading wire:target="save" class="text-blue-600 text-sm ml-2">
                    Đợi chút...
                </span>
            </div>

        </form>
    </x-filament::section>
</div>
