<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Models\Document;
use App\Models\History;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Task $task)
    {
        $uploadedFiles = $request->file('uploadedFiles');

        foreach ($uploadedFiles as $file) {
            $fileName = auth()->id() . '-projects-' . Carbon::today()->format('Y-m-d_H-i'). '-' . $file->getClientOriginalName();

            $filePath = $file->storeAs('tasksFiles', $fileName, 'public');

            if ($filePath) {
                File::create([
                    'model_id' => $task->id,
                    'model_type' => Task::class,
                    'filename' => $fileName, 
                    'file_path' => $filePath,
                    'name' => $file->getClientOriginalName(), 
                    'type' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'user_id' => auth()->id(),
                    'department_id' => auth()->user()->department_id,
                ]);

                session()->flash('toast', [
                    'type' => 'success',
                    'title' => 'File Added!',
                    'description' => null,
                    'position' => 'toast-top toast-start',
                    'icon' => 'o-check-circle',
                    'css' => 'alert-success',
                    'timeout' => 3000,
                    'redirectTo' => null
                ]);
            } else {
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Error Uploading File!',
                    'description' => 'Could not move the file to the destination directory.',
                    'position' => 'toast-top toast-start',
                    'icon' => 'o-close-circle',
                    'css' => 'alert-danger',
                    'timeout' => 3000,
                    'redirectTo' => null
                ]);
            }
        }


        return redirect()->route('tasks.show', ['task' => $task->id]);
    
    }

    public function documentfile(Request $request, Document $document)
    {
        $uploadedFiles = $request->file('uploadedFiles');
        foreach ($uploadedFiles as $file) {
            $fileName = auth()->id() . '-document-' . Carbon::today()->format('Y-m-d_H-i'). '-' . $file->getClientOriginalName();

            $filePath = $file->storeAs('documents', $fileName, 'public');

            if ($filePath) {
                File::create([
                    'model_id' => $document->id,
                    'model_type' => Document::class,
                    'filename' => $fileName, 
                    'file_path' => $filePath,
                    'name' => $file->getClientOriginalName(), 
                    'type' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'user_id' => auth()->id(),
                    'department_id' => auth()->user()->department_id,
                ]);
                History::create([
                    'action' => 'file added',
                    'model_id' => $document->id,
                    'model_type' => Document::class,
                    'date' => now(),
                    'name' => $document->name, 
                    'department_id' => Auth::user()->department_id,
                    'user_id' => Auth::id(),
                ]);

                session()->flash('toast', [
                    'type' => 'success',
                    'title' => 'File Added!',
                    'description' => null,
                    'position' => 'toast-top toast-start',
                    'icon' => 'o-check-circle',
                    'css' => 'alert-success',
                    'timeout' => 3000,
                    'redirectTo' => null
                ]);
            } else {
                session()->flash('toast', [
                    'type' => 'error',
                    'title' => 'Error Uploading File!',
                    'description' => 'Could not move the file to the destination directory.',
                    'position' => 'toast-top toast-start',
                    'icon' => 'o-close-circle',
                    'css' => 'alert-danger',
                    'timeout' => 3000,
                    'redirectTo' => null
                ]);
            }
        }


        return redirect()->route('documents.show', ['document' => $document->id]);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        //
    }
}
