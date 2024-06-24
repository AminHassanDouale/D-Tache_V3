<?php
use App\Models\Student;
use App\Models\BillMethod;
use App\Models\BillMethodQuantity;
use App\Models\Status;
use App\Models\Invoice;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use Toast;

    public string $name = '';
    public string $address = '';
    public string $pin_code = '';
    public string $city = '';
    public string $state = '';
    public string $phone = '';
    public string $email = '';
    public string $join_date = '';
    public string $birth_date = '';
    public int $billmethod_id;
    public int $status_id = 1;

    public function mount(): void
    {
        $this->join_date = Carbon::now()->format('d/m/Y');
    }

    // Validation rules for inputs
    protected $rules = [
        'name' => 'required|string',
        'address' => 'required|string',
        'pin_code' => 'required|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'phone' => 'required|string',
        'email' => 'required|email',
        'join_date' => 'required|date_format:d/m/Y',
        'birth_date' => 'required|date',
        'billmethod_id' => 'required|integer|exists:bill_methods,id',
        'status_id' => 'required|integer|exists:statuses,id',
    ];

    public function save(): void
    {
        $this->validate();

        $studentId = $this->generateUniqueStudentId();

        $student = Student::create([
            'name' => $this->name,
            'address' => $this->address,
            'pin_code' => $this->pin_code,
            'city' => $this->city,
            'state' => $this->state,
            'phone' => $this->phone,
            'email' => $this->email,
            'studentId' => $studentId,
            'join_date' => Carbon::createFromFormat('d/m/Y', $this->join_date)->format('Y-m-d'),
            'birth_date' => $this->birth_date,
            'total_amount' => 350000, 
            'billmethod_id' => $this->billmethod_id,
            'status_id' => $this->status_id,
        ]);

        $billMethod = BillMethod::find($this->billmethod_id);

        for ($i = 0; $i < $billMethod->quantity; $i++) {
            $billMethodQuantity = BillMethodQuantity::create([
                'bill_method_id' => $billMethod->id,
                'student_id' => $student->id,
                'quantity' => $i + 1,
                'remaining' => 0, 
                'amount' => $billMethod->amount,
                'status_id' => 1, 

            ]);

            Invoice::create([
                'student_id' => $student->id,
                'bill_method_quantities_id' => $billMethodQuantity->id,
                'student_name' => $student->name,
                'studentId' => $student->studentId,
                'subject' => 'frais scolaire', 
                'invoiceId' => $this->generateUniqueInvoiceId(),
                'start_date' => Carbon::now()->format('Y-m-d'),
                'due_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'amount' => $billMethod->amount,
                'user_id' => Auth::id(),
                'status_id' => $this->status_id,
            ]);
        }

        $this->success('Student and invoices created successfully.', redirectTo: '/students');
    }

    private function generateUniqueStudentId()
    {
        do {
            $studentId = 'IAD' . mt_rand(1000, 9999);
        } while (Student::where('studentId', $studentId)->exists());

        return $studentId;
    }

    private function generateUniqueInvoiceId()
    {
        do {
            $invoiceId = mt_rand(1000, 9999999);
        } while (Invoice::where('invoiceId', $invoiceId)->exists());

        return $invoiceId;
    }

    public function with(): array
    {
        return [
            'billMethods' => BillMethod::all(),
            'statuses' => Status::all(),
        ];
    }
};
?>

<div>
    <x-header title="New Student" separator progress-indicator />

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <x-form wire:submit.prevent="save">
                <x-input label="Name" name="name" wire:model="name" />
                <x-input label="Address" name="address" wire:model="address" />
                @php
                $config1 = ['altFormat' => 'd/m/Y'];
                @endphp
                <x-datepicker label="Date of Birth" wire:model="birth_date" name="birth_date" icon-right="o-calendar" :config="$config1" />
                <x-input label="Pin Code" name="pin_code" wire:model="pin_code" />
                <x-input label="City" name="city" wire:model="city" />
                <x-input label="State" name="state" wire:model="state" />
                <x-input label="Phone" name="phone" wire:model="phone" />
                <x-input label="Email" name="email" wire:model="email" />
                <x-datepicker label="Join Date" wire:model="join_date" name="join_date" icon-right="o-calendar" :config="$config1" />
                <x-select label="Bill Method" name="billmethod_id" wire:model="billmethod_id" :options="$billMethods" option-label="name" option-value="id" placeholder="---" />
                <x-select label="Status" name="status_id" wire:model="status_id" :options="$statuses" option-label="name" option-value="id" placeholder="---" />
                <div class="mt-4">
                    <x-button label="Cancel" link="/students" />
                    <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
                </div>
            </x-form>
        </div>
        <div>
            <img src="/images/edit-form.png" width="300" class="mx-auto" />
        </div>
    </div>
</div>
