<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'department_id' => 'required|exists:departments,id', 
            'number'=>'required|integer',
            'fix'=>'required',


        ]);

        $data['avatar'] = 'https://picsum.photos/200?x=' . rand();

        User::create($data);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $departments = Department::all();
      //  $roles = Role::all();

        return view('users.edit', compact('user', 'departments',));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'string|unique:users,name,' . $user->id,
            'email' => 'string|email|unique:users,email,' . $user->id,
            'avatar' => 'string', // Assuming avatar is a string
            'number' => 'integer', // Assuming number is an integer
            'fix' => 'string', // Assuming number is an integer
            'department_id' => 'exists:departments,id',
        ]);
    
        $data = [
            'name' => $request->name,
            'avatar' => $request->avatar, 
            'number' => $request->number,
            'fix' => $request->fix,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ];
    
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
   
        $user->update($data);
    
        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
    public function assignRole(Request $request, User $user)
    {
        if ($user->hasRole($request->role)) {
            return back()->with('message', 'Role exists.');
        }

        $user->assignRole($request->role);
        return back()->with('message', 'Role assigned.');
    }

    public function removeRole(User $user, Role $role)
    {
        if ($user->hasRole($role)) {
            $user->removeRole($role);
            return back()->with('message', 'Role removed.');
        }

        return back()->with('message', 'Role not exists.');
    }

    public function givePermission(Request $request, User $user)
    {
        if ($user->hasPermissionTo($request->permission)) {
            return back()->with('message', 'Permission exists.');
        }
        $user->givePermissionTo($request->permission);
        return back()->with('message', 'Permission added.');
    }

    public function revokePermission(User $user, Permission $permission)
    {
        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return back()->with('message', 'Permission revoked.');
        }
        return back()->with('message', 'Permission does not exists.');
    }
    public function role(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('users.role', compact('user', 'roles', 'permissions'));
    }
    public function task_user($id)
    {
       
       $user = User::find($id);
       
       return view('users.task_user', compact('user'));

   }


}
