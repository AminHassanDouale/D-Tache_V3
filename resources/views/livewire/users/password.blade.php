<?php

use App\Actions\DeleteCustomerAction;
use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


new class extends Component {
    use Toast;

public $currentPassword;
public $newPassword;
public $newPasswordConfirmation;

public function update(): void
{
    // Validate the current password
    if (!Hash::check($this->currentPassword, $this->model->password)) {
        $this->error('The current password is incorrect.');
        return;
    }

    // Validate the new password
    $this->validate([
        'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // Update the user's password
    $this->model->update([
        'password' => Hash::make($this->newPassword),
    ]);

    // Log out the user
    Auth::logout();
}
};?>
<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                @if (session('message'))
                    <h5 class="mb-2 alert alert-success">{{ session('message') }}</h5>
                @endif

                @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                    <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </ul>
                @endif

                <div class="shadow card">
                    <div class="card-header bg-primary">
                        <h4 class="mb-0 text-white">Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('change-password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" />
                            </div>
                            <div class="mb-3 text-end">
                                <hr>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




