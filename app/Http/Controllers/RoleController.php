<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount('users')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create([
            'name' => strtolower($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'redirect' => route('roles.index')
        ]);
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load('users');
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $role->update([
            'name' => strtolower($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'redirect' => route('roles.index')
        ]);
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deleting if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role with existing users'
            ], 400);
        }

        // Prevent deleting default roles
        if (in_array($role->name, ['admin', 'customer'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default system roles'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}