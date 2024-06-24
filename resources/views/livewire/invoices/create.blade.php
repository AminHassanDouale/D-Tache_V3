<?php

use App\Models\Invoice;
use App\Models\Student;
use App\Models\Status;
use App\Models\BillMethodQuantity;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Mary\Traits\Toast;
use App\Traits\CalculatesInvoice;
use App\Traits\SavesInvoice;
use App\Traits\UpdatesRemainingAmount;


new class extends Component {
    use Toast;

public $student_id;
public $bill_method_quantity_id;
public $student_name;
public $studentId;
public $student_email;
public $subject;
public $start_date;
public $due_date;
public $amount;
public $user_id;
public $status_id = 1;

public function mount($student_id, $bill_method_quantity_id)
{
    $student = Student::findOrFail($student_id);
    $this->student_id = $student_id;
    $this->bill_method_quantity_id = $bill_method_quantity_id;
    $this->student_name = $student->name;
    $this->studentId = $student->studentId;
    $this->student_email = $student->email;
    $this->user_id = Auth::id();
    $this->amount = BillMethodQuantity::findOrFail($bill_method_quantity_id)->amount;
    $this->start_date = Carbon::now()->format('Y-m-d');
    $this->due_date = Carbon::now()->addDays(30)->format('Y-m-d');
}

public function createInvoice()
{
    $this->validate([
        'student_id' => 'required|exists:students,id',
        'bill_method_quantity_id' => 'required|exists:bill_method_quantities,id',
        'student_name' => 'required|string|max:255',
        'studentId' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'start_date' => 'required|date',
        'due_date' => 'required|date',
        'amount' => 'required|numeric|min:0',
        'user_id' => 'required|exists:users,id',
        'status_id' => 'required|exists:statuses,id',
    ]);

    $invoiceData = [
        'student_id' => $this->student_id,
        'bill_method_quantities_id' => $this->bill_method_quantity_id,
        'student_name' => $this->student_name,
        'studentId' => $this->studentId,
        'subject' => $this->subject,
        'start_date' => $this->start_date,
        'due_date' => $this->due_date,
        'amount' => $this->amount,
        'user_id' => $this->user_id,
        'status_id' => $this->status_id,
    ];

    Invoice::create($invoiceData);

    $this->toast(
        type: 'success',
        title: 'Invoice Created!',
        description: 'The invoice was created successfully.',
        position: 'toast-bottom toast-start',
        icon: 'o-check-circle',
        timeout: 3000
    );

    return back();
}


    public function with(): array
    {
        return [
            'statuses' => Status::all(),
        ];
    }





}; ?>
<div>
    <div class="p-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <h2 class="mb-4 text-2xl font-bold">Create New Invoice</h2>
                <form wire:submit.prevent="createInvoice">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Student Name</label>
                        <input type="text" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="student_name" readonly />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Student ID</label>
                        <input type="text" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="studentId" readonly />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Student Email</label>
                        <input type="text" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="student_email" readonly />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="subject" />
                    </div>
                    @php
                    $config1 = ['altFormat' => 'd/m/Y'];
                    @endphp
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Issue Date</label>
                        <x-datepicker type="date" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="start_date" icon-right="o-calendar" :config="$config1" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Due Date</label>
                        <x-datepicker type="date" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="due_date" icon-right="o-calendar" :config="$config1" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <input type="number" class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="amount" readonly />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" wire:model="status_id">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-4">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove> Create Invoice</span>
                            <span wire:loading> Processing...</span>
                        </button>
                        <a href="/students" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
            <div>
                <!-- Invoice Preview Section -->
                <div class="max-w-4xl p-8 mx-auto mt-10 bg-white rounded-lg shadow-md">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <img src="/images/iad new.png" alt="Logo" class="h-20">
                        </div>
                        <div class="text-right">
                            <h1 class="text-4xl font-bold">Facture</h1>
                            <p class="text-gray-500">#</p>
                        </div>
                    </div>
                
                    <div class="flex justify-between mb-8">
                        <div>
                            <h2 class="text-lg font-semibold">From:</h2>
                            <p>Comptablite, IAD</p>
                            <p>comptabilte@IAD.com</p>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">To:</h2>
                            <p>Student, IAD.</p>
                            <p>student_name@iad.com</p>
                        </div>
                    </div>
                
                    <div class="flex justify-between mb-8">
                        <div>
                            <h2 class="text-lg font-semibold">Issued</h2>
                            <p>Monday, September 18, 2023</p>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">Due</h2>
                            <p>Monday, September 25, 2023</p>
                        </div>
                    </div>
                
                    <table class="w-full mb-8">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2 text-left">Item No.</th>
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-right">Quantity</th>
                                <th class="px-4 py-2 text-right">Unit Price</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-2 border">1</td>
                                <td class="px-4 py-2 border">Frais Scola</td>
                                <td class="px-4 py-2 text-right border">1</td>
                                <td class="px-4 py-2 text-right border">DJF{{ $amount }}</td>
                                <td class="px-4 py-2 text-right border">DJF{{ $amount }}</td>
                            </tr>
                        
                        </tbody>
                    </table>
                
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-lg font-semibold">Payment Methods</h2>
                            <div class="flex space-x-2">
                                <span class="px-4 py-2 bg-gray-200 rounded">Bank Transfer</span>
                                <span class="px-4 py-2 bg-gray-200 rounded">PayPal</span>
                                <span class="px-4 py-2 bg-gray-200 rounded">Wise</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg"><span class="font-semibold">Subtotal:</span> $8700</p>
                            <p class="text-lg"><span class="font-semibold">Tax (10%):</span> $87</p>
                            <p class="mt-2 text-2xl font-bold">Total Amount: $8787</p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>


