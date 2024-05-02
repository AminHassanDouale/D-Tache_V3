<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function reportdept(Request $request)
    {
         // Retrieve start date and due date from the request
         $startDate = $request->input('start_date');
         $dueDate = $request->input('due_date');
         
         // Query tasks based on start date and due date
         $tasks = Task::query();
 
         if ($startDate && $dueDate) {
             $tasks->whereBetween('start_date', [$startDate, $dueDate])
                   ->orWhereBetween('due_date', [$startDate, $dueDate]);
         } elseif ($startDate) {
             $tasks->where('start_date', '>=', $startDate)
                   ->orWhere('due_date', '>=', $startDate);
         } elseif ($dueDate) {
             $tasks->where('start_date', '<=', $dueDate)
                   ->orWhere('due_date', '<=', $dueDate);
         }
 
         // Retrieve and return the tasks
         $tasks = $tasks->get();
 
         return view('report.dept', compact('tasks', 'startDate', 'dueDate'));
    }

}