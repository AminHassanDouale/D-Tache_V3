<?php

use App\Models\Student;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination;

    public Student $student;
    public $quantities;

    public function mount(Student $student)
    {
        $this->student = $student->load('billMethod', 'billMethodQuantities.invoices');
        $this->quantities = $this->student->billMethodQuantities;
    }

    public function with(): array
    {
        return [
            'student' => $this->student,
            'quantities' => $this->quantities,
        ];
    }
};
?>

<div>
    <main class="">
        <div class="p-6 mx-auto bg-white rounded-lg shadow-lg">
            <hr>
            <div class="pt-4 mb-10">
                <p class="text-lg font-bold">Detail</p>
                <div class="flex items-center justify-between">
                    <div>
                        <p>Student ID: <span class="font-semibold">{{ $student->studentId }}</span></p>
                        <p>Student Name: <span class="font-semibold">{{ $student->name }}</span></p>
                        <p>{{ $student->join_date }}</p>
                        <p>{{ $student->address }}</p>
                    </div>
                    <div>
                        <p>Pin Code: <span class="font-semibold">{{ $student->pin_code }}</span> City: <span class="font-semibold">{{ $student->city }}</span></p>
                        <p>State: <span class="font-semibold">{{ $student->state }}</span></p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="mb-4">
                <p class="text-lg font-bold">BillMethod <span class="text-green-600"> {{ $student->billMethod->name }} </span></p>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="text-sm leading-normal text-gray-600 uppercase bg-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-center">Invoice_No</th>
                                <th class="px-4 py-3 text-center">Echeance</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Discount (DJF)</th>
                                <th class="px-4 py-3 text-center">Total Amt</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-light text-gray-600">
                            @forelse ($quantities as $index => $quantity)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="px-4 py-3 text-left">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-bold text-center text-green-600 underline">
                                    <a href="">{{ $quantity->invoices->first()->invoiceId }}</a>
                                
                                </td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-center">{{ $index + 1 }}/{{ $quantities->count() }}</span></td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-center">{{ $quantity->status->name ?? 'Not_Paid' }}</span></td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-center">0</span></td>
                                <td class="px-4 py-3 text-center"><span class="font-semibold text-center">{{ $quantity->amount }} DJF</span></td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-center">
                                        @if ($quantity->invoices->isNotEmpty())
                                        <x-button label="Edit" link="/invoices/{{ $quantity->invoices->first()->id }}/edit"  icon="o-pencil" class="btn-primary" responsive />

                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-center">No quantities found for this bill method.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <button class="px-4 py-2 mt-4 text-white bg-blue-500 rounded">Add New Item</button>
            </div>
            <div class="flex items-center mt-4">
                <input type="checkbox" id="save" class="mr-2">
                <label for="save">Save to my Items list</label>
            </div>
        </div>
    </main>
</div>
