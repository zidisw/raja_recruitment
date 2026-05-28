<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('News Management') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Publish and organize company articles') }}</flux:subheading>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus" class="w-full md:w-auto">
            {{ __('Create Article') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" />

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('success') }}</flux:callout.heading>
        </flux:callout>
    @endif

    @if ($articles->isEmpty())
        <div
            class="flex flex-col items-center justify-center p-16 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <flux:icon.newspaper class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mb-6" />
            <flux:heading size="lg" class="mb-2">{{ __('No Articles Found') }}</flux:heading>
            <flux:text class="text-center max-w-md">
                {{ __('Click the button above to publish your first news article.') }}
            </flux:text>
        </div>
    @else
        <div class="glass-card-static overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm modern-table">
                    <thead>
                        <tr>
                            <th class="w-12 text-center!">{{ __('No.') }}</th>
                            <th>{{ __('Article') }}</th>
                            <th class="hidden md:table-cell">{{ __('Category') }}</th>
                            <th class="hidden lg:table-cell">{{ __('Author') }}</th>
                            <th class="text-center!">{{ __('Status') }}</th>
                            <th class="text-center! hidden lg:table-cell">{{ __('Published At') }}</th>
                            <th class="text-center! whitespace-nowrap w-px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                        @foreach ($articles as $article)
                            <tr wire:key="news-row-{{ $article->id }}" class="cursor-pointer">
                                <td class="px-4 py-4 text-center text-zinc-500 font-medium">
                                    {{ $articles->firstItem() + $loop->index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($article->featuredImage)
                                            <div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-zinc-100 dark:bg-zinc-800">
                                                <img src="{{ Storage::url($article->featuredImage->path) }}"
                                                    alt="{{ $article->title }}" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                                                <flux:icon.newspaper class="w-5 h-5 text-zinc-400" />
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-zinc-900 dark:text-white line-clamp-1">
                                                {{ $article->title }}
                                            </p>
                                            <p class="text-xs text-zinc-400 mt-0.5">/articles/{{ $article->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    @if ($article->category)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                                            {{ $article->category }}
                                        </span>
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400 hidden lg:table-cell">
                                    {{ $article->author?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($article->is_published && $article->published_at <= now())
                                        <flux:badge color="green" size="sm">{{ __('Published') }}</flux:badge>
                                    @elseif ($article->is_published)
                                        <flux:badge color="blue" size="sm">{{ __('Scheduled') }}</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">{{ __('Draft') }}</flux:badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-zinc-500 dark:text-zinc-400 hidden lg:table-cell text-sm">
                                    {{ $article->published_at?->format('d M Y, H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap w-px">
                                    <div class="inline-flex flex-nowrap items-center justify-center gap-2 whitespace-nowrap">
                                        <flux:button wire:click="openEdit({{ $article->id }})"
                                            wire:target="openEdit({{ $article->id }})" size="sm" variant="ghost" icon="pencil"
                                            class="app-action-btn">{{ __('Edit') }}</flux:button>
                                        <flux:button @click="$dispatch('confirm-action', {
                                                                    title: 'Hapus Artikel?',
                                                                    description: 'Artikel ini akan dihapus secara permanen. Aksi ini tidak dapat dibatalkan.',
                                                                    variant: 'danger',
                                                                    method: 'delete',
                                                                    args: [{{ $article->id }}]
                                                                })" size="sm" variant="ghost" icon="trash"
                                            class="app-action-btn-danger">
                                            {{ __('Hapus') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($articles->hasPages())
            <div>{{ $articles->links() }}</div>
        @endif
    @endif

    {{-- Create / Edit Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-3xl">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $editingId ? __('Edit Article') : __('New Article') }}
            </flux:heading>

            <form wire:submit="save" class="space-y-5">
                {{-- Title + Slug --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Title') }} *</flux:label>
                        <flux:input wire:model.live.debounce.400ms="title" placeholder="Article title..." />
                        <flux:error name="title" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Slug') }} *</flux:label>
                        <flux:input wire:model="slug" placeholder="article-slug" />
                        <flux:error name="slug" />
                    </flux:field>
                </div>

                {{-- Category + Published At --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Category') }}</flux:label>
                        <x-custom-select wire:model="category" placeholder="{{ __('No category') }}" :options="['' => __('No category')] + collect($categories)->mapWithKeys(fn($c) => [$c => $c])->toArray()" />
                        <flux:error name="category" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Publish Date') }}</flux:label>
                        <x-date-picker wire:model="published_at" mode="datetime"
                            placeholder="{{ __('Select publish date & time...') }}" />
                        <flux:error name="published_at" />
                    </flux:field>
                </div>

                {{-- Content --}}
                <flux:field>
                    <flux:label>{{ __('Content') }} *</flux:label>
                    <flux:textarea wire:model="content" rows="8" placeholder="Write the article content here..." />
                    <flux:error name="content" />
                </flux:field>

                {{-- Featured Image --}}
                <div class="space-y-3">
                    <flux:label>{{ __('Featured Image') }}</flux:label>
                    <div class="flex flex-col sm:flex-row gap-4 items-start">
                        @if ($featuredImage)
                            <div
                                class="relative w-32 h-24 rounded-xl overflow-hidden shrink-0 bg-zinc-100 dark:bg-zinc-800">
                                <img src="{{ $featuredImage->temporaryUrl() }}" class="w-full h-full object-cover">
                                <button wire:click="$set('featuredImage', null)" type="button"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 hover:bg-red-600">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @elseif ($editingId)
                            @php $existingFeatured = $this->existingFeaturedImage; @endphp
                            @if ($existingFeatured)
                                <div
                                    class="relative w-32 h-24 rounded-xl overflow-hidden shrink-0 bg-zinc-100 dark:bg-zinc-800 group">
                                    <img src="{{ Storage::url($existingFeatured->path) }}" class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button wire:click="deleteImage({{ $existingFeatured->id }})" type="button"
                                            class="text-white text-xs font-semibold bg-red-500 px-2 py-1 rounded-lg hover:bg-red-600">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="flex-1">
                            <input type="file" wire:model="featuredImage"
                                wire:key="news-featured-image-{{ $editingId ?? 'new' }}" accept="image/*" class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                                    file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                                    file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700
                                    dark:file:bg-zinc-800 dark:file:text-zinc-300
                                    hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 file:cursor-pointer
                                    focus:outline-none">
                            <p class="text-xs text-zinc-400 mt-1.5">JPG, PNG or WebP. Max 5MB.</p>
                            <div wire:loading wire:target="featuredImage"
                                class="mt-2 text-xs text-zinc-400 flex items-center gap-2">
                                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Uploading...
                            </div>
                        </div>
                    </div>
                    <flux:error name="featuredImage" />
                </div>

                {{-- Gallery Images --}}
                <div class="space-y-3">
                    <flux:label>{{ __('Gallery Images') }}</flux:label>

                    @if ($editingId)
                        @php $galleryExisting = $this->existingGalleryImages; @endphp
                        @if ($galleryExisting->isNotEmpty())
                            <div class="flex flex-wrap gap-3">
                                @foreach ($galleryExisting as $img)
                                    <div class="relative w-20 h-16 rounded-xl overflow-hidden group bg-zinc-100 dark:bg-zinc-800">
                                        <img src="{{ Storage::url($img->path) }}" class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <button wire:click="deleteImage({{ $img->id }})" type="button" class="text-white">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    @if (!empty($galleryImages))
                        <div class="flex flex-wrap gap-3">
                            @foreach ($galleryImages as $img)
                                <div class="relative w-20 h-16 rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                    <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover">
                                    <span
                                        class="absolute bottom-0 left-0 right-0 bg-green-500/80 text-white text-[10px] text-center py-0.5">New</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <input type="file" wire:model="galleryImages"
                        wire:key="news-gallery-images-{{ $editingId ?? 'new' }}" accept="image/*" multiple class="block w-full text-sm text-zinc-500 dark:text-zinc-400
                            file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0
                            file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700
                            dark:file:bg-zinc-800 dark:file:text-zinc-300
                            hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 file:cursor-pointer
                            focus:outline-none">
                    <p class="text-xs text-zinc-400">Select multiple photos for the gallery. Max 5MB each.</p>
                    <div wire:loading wire:target="galleryImages" class="text-xs text-zinc-400 flex items-center gap-2">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                            </path>
                        </svg>
                        Uploading gallery images...
                    </div>
                    <flux:error name="galleryImages.*" />
                </div>

                {{-- Published toggle --}}
                <flux:field>
                    <div class="flex items-center gap-3">
                        <flux:checkbox wire:model="is_published" id="is_published_news" />
                        <flux:label for="is_published_news">{{ __('Published (visible to the public)') }}</flux:label>
                    </div>
                </flux:field>

                <div class="flex justify-end gap-3 pt-2">
                    <flux:button type="button" variant="ghost" wire:click="$set('showModal', false)">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="save,featuredImage,galleryImages">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? __('Update Article') : __('Publish Article') }}
                        </span>
                        <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <x-confirm-action />
</div>
