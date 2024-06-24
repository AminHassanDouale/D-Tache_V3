<?php

use App\Models\BillMethod;
use App\Models\Status;
use App\Models\Student;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public Student $student;
    public $name;
    public $address;
    public $pin_code;
    public $city;
    public $state;
    public $phone;
    public $email;
    public $join_date;
    public $birth_date;
    public $billmethod_id;
    public $status_id;

    public function mount(Student $student): void
    {
        $this->student = $student;
        $this->name = $student->name;
        $this->address = $student->address;
        $this->pin_code = $student->pin_code;
        $this->city = $student->city;
        $this->state = $student->state;
        $this->phone = $student->phone;
        $this->email = $student->email;
        $this->join_date = $student->join_date;
        $this->birth_date = $student->birth_date;
        $this->billmethod_id = $student->billmethod_id;
        $this->status_id = $student->status_id;
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'pin_code' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'join_date' => 'required|date',
            'birth_date' => 'required|date',
            'billmethod_id' => 'required|integer|exists:bill_methods,id',
            'status_id' => 'required|integer|exists:statuses,id',
        ]);

        $this->student->update([
            'name' => $this->name,
            'address' => $this->address,
            'pin_code' => $this->pin_code,
            'city' => $this->city,
            'state' => $this->state,
            'phone' => $this->phone,
            'email' => $this->email,
            'join_date' => $this->join_date,
            'birth_date' => $this->birth_date,
            'billmethod_id' => $this->billmethod_id,
            'status_id' => $this->status_id,
        ]);

        $this->success('Student updated successfully.', redirectTo: '/students');
    }

    public function delete(): void
    {
        $this->student->delete();
        $this->success('Student deleted successfully.', redirectTo: '/students');
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
    <x-header :title="$student->name" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Delete" icon="o-trash" wire:click="delete" class="btn-error" wire:confirm="Are you sure?" spinner responsive />
        </x-slot:actions>
    </x-header>

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <x-form wire:submit.prevent="update">
                <x-input label="Name" name="name" wire:model.defer="name" />
                <x-input label="Address" name="address" wire:model.defer="address" />
                @php
                    $config1 = ['altFormat' => 'd/m/Y'];
                @endphp
                <x-datepicker label="Date of Birth" wire:model.defer="birth_date" name="birth_date" icon-right="o-calendar" :config="$config1" />
                <x-input label="Pin Code" name="pin_code" wire:model.defer="pin_code" />
                <x-input label="City" name="city" wire:model.defer="city" />
                <x-input label="State" name="state" wire:model.defer="state" />
                <x-input label="Phone" name="phone" wire:model.defer="phone" />
                <x-input label="Email" name="email" wire:model.defer="email" />
                <x-datepicker label="Join Date" wire:model.defer="join_date" name="join_date" icon-right="o-calendar" :config="$config1" />
                <x-select label="Bill Method" name="billmethod_id" wire:model.defer="billmethod_id" :options="$billMethods" option-label="name" option-value="id" placeholder="---" />
                <x-select label="Status" name="status_id" wire:model.defer="status_id" :options="$statuses" option-label="name" option-value="id" placeholder="---" />
                <div class="mt-4">
                    <x-button label="Cancel" link="/students" />
                    <x-button label="Save Changes" spinner="update" type="submit" icon="o-paper-airplane" class="btn-primary" />
                </div>
            </x-form>
        </div>
        <div>
            <img src="/images/edit-form.png" width="300" class="mx-auto" />
        </div>
    </div>
</div>
