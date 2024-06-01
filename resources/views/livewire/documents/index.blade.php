<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Project;
use App\Models\Document;
use App\Models\File; 
use App\Models\Priority;
use App\Models\Status;
use App\Traits\ClearsProperties;
use App\Traits\ResetsPaginationWhenPropsChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Mary\Traits\Toast;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use toast, WithPagination, ResetsPaginationWhenPropsChanges, ClearsProperties;

    #[Url]
    public string $name = '';



    #[Url]
    public ?int $category_id = 0;
    

    #[Url]
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public bool $showFilters = false;
  

    public function filterCount(): int
    {
        return ($this->category_id ? 1 : 0) + (strlen($this->name) ? 1 : 0);
    }
    public function statuses(): Collection
    {
        return Status::orderBy('name')->get();
    }
    public function documents(): LengthAwarePaginator
    {
        return Document::query()
            ->where('user_id', Auth::user()->id)
            ->with([ 'category'])
            ->withAggregate('category', 'name')
            ->when($this->name, fn(Builder $q) => $q->where('name', 'like', "%$this->name%"))
            ->when($this->category_id, fn(Builder $q) => $q->where('category_id', $this->category_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(30);
    }

//liked documents

public function likedDocuments(): Collection
{
    return Auth::user()->likes()->with('category')->get();
}

// document files
public function docFiles()
{
    return File::where('user_id', Auth::user()->id)
        ->where('model_type', Document::class)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get(); // Add get() to execute the query and retrieve the results
}


    public function headers(): array
    {
        return [
            ['key' => 'preview', 'label' => '', 'class' => 'w-14', 'sortable' => false],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'category.name', 'label' => 'Category', 'sortBy' => 'category_name', 'class' => 'hidden lg:table-cell'],
            ['key' => 'user.name', 'label' => 'Assignee', 'sortBy' => 'users.name']

        ];
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'documents' => $this->documents(),
            'files' => $this->docFiles(),
            'likedDocuments' => $this->likedDocuments(),
            'categories' => Category::all(),
            'filterCount' => $this->filterCount(),
        ];
    }
    protected $listeners = ['document-saved' => '$refresh'];
}; ?>
<div>
        <x-header title="Document" separator progress-indicator>
            {{--  SEARCH --}}
            <x-slot:middle class="!justify-end">
                <x-input placeholder="Name..." wire:model.live.debounce="name" icon="o-magnifying-glass" clearable />
            </x-slot:middle>
    
            {{-- ACTIONS  --}}
            <x-slot:actions>
                <x-button label="Filters" icon="o-funnel" :badge="$filterCount" badge-classes="font-mono"
                    @click="$wire.showFilters = true" class="bg-base-300" responsive />
    
                <livewire:documents.create />
            </x-slot:actions>
        </x-header>
    
        <!-- Liked Folders Section -->

<div class="mb-8">
    <h2 class="mb-2 text-xl font-semibold">Follow Documents</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 lg:grid-cols-4">
        @if($likedDocuments->isNotEmpty())
        @foreach($likedDocuments as $document)
        <a href="{{ route('documents.show', $document) }}">
            <div class="p-4 bg-white rounded shadow">
                <div class="flex items-start">
                    <svg class="w-6 h-6 mr-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2H9l-2-2H5c-1.1 0-2 .9-2 2z" />
                    </svg>
                </div>
                <div>
                    <div>{{ $document->name }}</div>
                    <div class="text-sm text-gray-500">{{ $document->category->name }}</div>
                </div>
            </div>
        </a>
        @endforeach
        @else
        <div class="text-center">oops, No Follow Document available.</div>
    @endif
    </div>
</div>

    
        <!-- Recent Section -->
        
        <div class="mb-8">
            <h2 class="mb-2 text-xl font-semibold">Recent Files</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
                @if($files->isNotEmpty())
        @foreach ($files as $file)
                <div class="p-4 bg-white rounded shadow">
                    <div>
                        @if ($file->type == 'pdf')
                        <img
                        width="50" class="mx-auto rounded-full"
                        src="/images/pdf.png"
                        alt=""
                    />  @elseif ($file->type == 'jpg')
                    <img
                    width="100" class="mx-auto rounded-full"
                    src="/images/jpg.png"
                    alt=""
                />
                        @elseif($file->type == 'docx')
                        <img
                        width="100" class="mx-auto rounded-full"
                        src="/images/docx.png"
                        alt=""
                    /> @elseif($file->type == 'png')
                        <img
                        width="100" class="mx-auto rounded-full"
                        src="/images/png.png"
                        alt=""
                    />@elseif($file->type == 'rar')
                    <img
                    width="100" class="mx-auto rounded-full"
                    src="/images/rar.png"
                    alt=""
                />
                @elseif($file->type == 'txt')
                        <img
                        width="100" class="mx-auto rounded-full"
                        src="/images/txt.png"
                        alt=""
                    />
                    @elseif($file->type == 'xlsx')
                        <img
                        width="100" class="mx-auto rounded-full"
                        src="/images/xlsx.png"
                        alt=""
                    />
                    @else
                    <img
                    width="100" class="mx-auto rounded-full"
                        src="/images/xlsx.png"
                        alt=""
                    />
                        @endif

                        <div class="text-center text-gray-700">{{ $file->name }}</div>
                        
                        <div class="text-sm text-center text-gray-500">{{ $file->created_at->diffForHumans() }} - {{ round($file->size / (1024 * 1024), 2) }} MB</div>
                    </div>
                </div>
                @endforeach
        @else
        <div class="text-center">oops, No File available.</div>
    @endif
            </div>
        </div>
        
        <!-- All Files Section -->
        <x-card>
            <h2 class="mb-2 text-xl font-semibold">List Documents</h2>

            @if ($documents->count() > 0)
                <x-table :headers="$headers" :rows="$documents" link="/documents/{id}/show"  :sort-by="$sortBy" with-pagination>
                    
                </x-table>
            @else
                <div class="flex items-center justify-center gap-10 mx-auto">
                    <div>
                        <img src="/images/no-results.png" width="300" />
                    </div>
                    <div class="text-lg font-medium">
                        Desole {{ Auth::user()->name }}, Pas des Documents.
                    </div>
                </div>
            @endif
        </x-card>
    {{-- FILTERS --}}
    <x-drawer wire:model="showFilters" title="Filters" class="lg:w-1/3" right separator with-close-button>
        <div class="grid gap-5" @keydown.enter="$wire.showFilters = false">
            <x-input label="Name ..." wire:model.live.debounce="name" icon="o-user" inline />
           
            <x-select label="Category" :options="$categories" wire:model.live="category_id" icon="o-flag" placeholder="All"
                placeholder-value="0" inline />
           
        </div>

        {{-- ACTIONS --}}
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.showFilters = false" />
        </x-slot:actions>
    </x-drawer>
    
    </div>