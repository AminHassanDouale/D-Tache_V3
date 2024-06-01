<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Note;
use App\Models\Task;
use App\Models\Status;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use App\Models\Document;
use App\Models\File;
use App\Models\History;
use App\Models\Category;
use App\Mail\TaskCreatedMail;
use App\Traits\TraitsDocument;

use App\Models\Priority;
use Illuminate\Support\Collection;
use Mary\Traits\Toast;
use App\Mail\TaskCommented;
use Illuminate\Support\Facades\Mail;

new class extends Component {
    use Toast, TraitsDocument;
    
    public Document $document;
    public $name;
    public $description;
   
    public $category_id;
   
    public $files = []; 
    public $tags = [];
    
    public function mount(Document $document)
    {
        $this->document = $document->load('likers');

        $this->document = $document;
        $this->name = $document->name;
        $this->description = $document->description;
        $this->category_id = $document->category_id;
        //$this->file = $task->file;
    }


    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }
     #[Computed]
     public function isLiked(): bool
    {
        return (bool) $this->user()
            ->likes()
            ->where('document_id', $this->document->id)
            ->first();
    }


public function delete($fileId): void
    {
    
        $file = File::find($fileId);

if ($file) {
    // Delete the file from storage
    Storage::delete($file->file_path);

    // Delete the file record from the database
    $file->delete();

    $this->toast(
        type: 'success',
        title: 'File Deleted!',
        description: null,
        position: 'toast-bottom toast-start',
        icon: 'o-trash',
        timeout: 3000
    );
} else {
    $this->toast(
        type: 'error',
        title: 'Error!',
        description: 'File not found.',
        position: 'toast-bottom toast-start',
        icon: 'o-alert-circle',
        timeout: 3000
    );
}
    }

//  update task name //
public function changeDocumentName()
{
    

    $this->document->update(['name' => $this->name]);

    

    $this->toast(
            type: 'warning',
            title: 'Mise A jour, Nom Du Document!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Name updated', $this->document->id, Document::class);

}

// update Task Description 
public function changeDocumentDescription()
{
    $this->document->update(['description' => $this->description]);


    $this->toast(
            type: 'warning',
            title: 'Mise A jour, Description!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Description updated', $this->document->id, Document::class);

}

// update task Category_id //

public function changeCategory()
    {
        $this->document->update(['category_id' => $this->category_id]);

        $this->toast(
            type: 'warning',
            title: 'Mise A jour, Category!',
            description: null,                  // optional (text)
            position: 'toast-bottom toast-end',    // optional (daisyUI classes)
            icon: 'o-information-circle',       // Optional (any icon)
            css: 'alert-warning',                  // Optional (daisyUI classes)
            timeout: 3000,                      // optional (ms)
            redirectTo: null                    // optional (uri)
        );
        $this->logHistory('Category updated', $this->document->id, Document::class);


    }


private function logHistory($action, $modelId, $modelType)
    {
        History::create([
            'action' => $action,
            'model_id' => $modelId,
            'model_type' => $modelType,
            'date' => now(),
            'name' => $this->name, 
            'department_id' => Auth::user()->department_id,
            'user_id' => Auth::id(),
        ]);
    }
//display the history
public function allHistories()
{
    return History::where('model_id', $this->document->id)
        ->where('model_type', Document::class)
        ->orderBy('created_at', 'desc')
        ->paginate(4);
}

    public function with(): array
    {
        return [
            'categories' => $this->categories(),
            'histories' => $this->allHistories(),

        ];
    }

}; ?>
<div>
        <x-header title="Document" separator>
            <x-slot:actions>
                <x-button
                    wire:click="toggleLike({{ $document->id }})"
                    icon="o-heart"
                    tooltip="Wishlist"
                    spinner
                    @class(["btn-square btn-sm", "text-pink-500" => $document->likers->count() > 0])
                />
            </x-slot:actions>
        </x-header>
    
    <div class="flex flex-col gap-2 lg:flex-row">
        {{-- DETAILS --}}
        <div class="flex flex-col gap-5 lg:px-3" wire:key="details">
            <!-- Name update view -->
            <x-card>
                <x-input label="Name" wire:model.defer="name" placeholder="Enter Document name" :value="$document->name" wire:keydown.enter="changeDocumentName" />
            </x-card>
            <!-- Description update view -->
            <form wire:submit.prevent="changeDocumentDescription">
                <x-textarea
                    label="Description"
                    wire:model="description"
                    rows="5"
                    inline
                />
                <div class="mt-4">
                    <x-button type="submit" class="bg-blue-500">Update Description</x-button>
                </div>
            </form>
            <!-- Category update view -->
            <label for="category">Category</label>
            <select wire:model="category_id" label="Category" wire:change="changeCategory" id="category" name="category" class="block w-full p-4 border-0 border-solid divide-y divide-blue-200 rounded shadow hover:border-dotted">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            
        </div>
        <div class="flex flex-col lg:w-1/2">
            <x-card title="Files" separator>
                <form action="{{ route('file.documentfile', $document) }}" class="p-4 mb-6 bg-white rounded-lg shadow" enctype="multipart/form-data" method="post">
                    @csrf
                    <input wire:model="uploadedFiles" type="file" name="uploadedFiles[]" multiple>
                    <div>
                        <button type="submit" class="p-2 bg-green-200 rounded">Upload Files</button>
                    </div>
                </form>
    
                @if($document->file->isNotEmpty())
                    @foreach ($document->file as $file)
                        <div class="flex w-full items-center justify-between rounded-2xl bg-white p-3 shadow-3xl shadow-shadow-500 dark:!bg-navy-700 dark:shadow-none">
                            <div class="flex items-center">
                                @if ($file->type == 'pdf')
                                <img
                                class="h-[83px] w-[83px] rounded-lg"
                                src="/images/pdf.png"
                                alt=""
                            />  @elseif ($file->type == 'jpg')
                            <img
                            class="h-[83px] w-[83px] rounded-lg"
                            src="/images/jpg.png"
                            alt=""
                        />
                                @elseif($file->type == 'docx')
                                <img
                                class="h-[83px] w-[83px] rounded-lg"
                                src="/images/docx.png"
                                alt=""
                            /> @elseif($file->type == 'png')
                                <img
                                class="h-[83px] w-[83px] rounded-lg"
                                src="/images/png.png"
                                alt=""
                            />@elseif($file->type == 'rar')
                            <img
                            class="h-[83px] w-[83px] rounded-lg"
                            src="/images/rar.png"
                            alt=""
                        />
                        @elseif($file->type == 'txt')
                                <img
                                class="h-[83px] w-[83px] rounded-lg"
                                src="/images/txt.png"
                                alt=""
                            />
                            @elseif($file->type == 'xlsx')
                                <img
                                class="h-[83px] w-[83px] rounded-lg"
                                src="/images/xlsx.png"
                                alt=""
                            />
                            @else
                            <img
                                class="h-[83px] w-[83px] rounded-lg"
                                src="/images/xlsx.png"
                                alt=""
                            />
                                @endif
                                <div class="ml-4">
                                    <p class="text-base font-medium text-navy-700 dark:text-white ">
                                        {{ $file->name }}
                                    </p>
                                    @php
                                        $sizeInMB = round($file->size / (1024 * 1024), 2);
                                    @endphp
                                    <p class="mt-2 text-sm text-gray-600">
                                        {{ $sizeInMB }} MB
                                        <a class="ml-1 font-medium text-brand-500 hover:text-brand-500 dark:text-white" href=" ">
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center justify-center mr-4 text-gray-600 dark:text-white">
                                <a href="{{ Storage::url($file->file_path) }}" download="{{ $file->name }}" class="bg-white">
                                    <x-icon name="m-arrow-down-on-square" />
                                </a>|
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="bg-white">
                                    <x-icon name="o-eye" />
                                </a>|
                                <x-button
                                    wire:click="delete({{ $file->id }})"
                                    class="btn-error"
                                    wire:confirm="Are you sure?"
                                    icon="o-trash"
                                    spinner
                                    responsive
                                />
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center">No files available.</div>
                @endif
            </x-card>
        </div>
        <div class="flex flex-col lg:w-1/2">
            <x-card title="Histories" separator with-pagination>
                <div class="relative">
                    <div class="border-l-2">
                        @if($histories->isNotEmpty())
                        @foreach ($histories as $history)

                        <!-- Card 1 -->
                        <div class="relative flex flex-col items-center px-1 py-3 mb-6 ml-6 space-y-3 text-white transition transform bg-blue-600 rounded cursor-pointer hover:-translate-y-1 md:flex-row md:space-y-0">
                            <!-- Dot Following the Left Vertical Line -->
                            <div class="absolute z-10 w-4 h-4 mt-1 transform bg-blue-600 rounded-full -left-8 -translate-x-2/4 md:mt-0"></div>
        
                            <!-- Line connecting the box with the vertical line -->
                            <div class="absolute z-0 w-8 h-1 bg-blue-300 -left-8"></div>
        
                            <!-- Content in the box -->
                            <div class="flex-auto">
                                <h1 class="text-sm">{{ $history->date }}</h1>
                                <h1 class="text-lg font-bold">{{ $history->action }}</h1>
                                <h3 class="text-xs">{{ $history->user->name }}</h3>
                            </div>
                            <a href="#" class="text-xs text-center text-white hover:text-gray-300"></a>
                        </div>
                        @endforeach
                        @else
                        <div class="text-center">No History available.</div>
                    @endif

                    </div>
                </div>
            </x-card>
        </div>
        
    </div>
    
</div>
