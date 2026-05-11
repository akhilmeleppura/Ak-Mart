<?php

namespace App\Http\Controllers\entities;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Account\AccountBaseController;

/**
 * Class UserManagement
 *
 * Controller for managing users, roles, and user-related operations.
 *
 * @package App\Http\Controllers\laravel_example
 */
class UserManagement extends AccountBaseController
{
    /**
     * Permissions required for this controller.
     *
     * @var array
     */
    protected $permissions = ['view_users', 'edit_users'];

    /**
     * Show the User Management dashboard view with stats and form configuration.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function UserManagement(): View
    {
        $users = User::all();
        $userCount = $users->count();
        $verified = User::whereNotNull('email_verified_at')->count();
        $notVerified = User::whereNull('email_verified_at')->count();
        $usersUnique = $users->unique(['email']);
        $userDuplicates = $users->diff($usersUnique)->count();

        $countries = ['India', 'USA', 'Canada'];
        $roles = Role::all();

        $formConfig = [
            'fields' => [
                'name' => 'text',
                'email' => 'email',
                'userContact' => 'text',
                'company' => 'text',
                'country' => 'select2',
                'role' => 'select'
            ],
            'labels' => [
                'name' => 'Full Name',
                'email' => 'Email',
                'userContact' => 'Contact',
                'company' => 'Company',
                'country' => 'Country',
                'role' => 'User Role'
            ]
        ];

        return view('content.entities.user-management', [
            'totalUser' => $userCount,
            'verified' => $verified,
            'notVerified' => $notVerified,
            'userDuplicates' => $userDuplicates,
            'standardDataTableConfig' => [
                'table' => [
                    'id' => ['type' => 'text', 'dbColumn' => 'id', 'headerName' => 'ID'],
                    'name' => ['type' => 'nameWithAvatar', 'responsivePriority' => 4, 'dbColumn' => 'name', 'headerName' => 'name'],
                    'email' => ['type' => 'email', 'dbColumn' => 'email', 'headerName' => 'email'],
                    'email_verified_at' => ['type' => 'verification', 'className' => 'text-center', 'dbColumn' => 'email_verified_at', 'headerName' => 'email_verified_at'],
                ],
                'otherConfig' => [
                    'ajaxUrl' => 'user-list',
                ]
            ],
            'countries' => $countries,
            'roles' => $roles,
            'formConfig' => $formConfig
        ]);
    }

    /**
     * Show the standard datatable example view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function standardDatatable(): View
    {
        $users = User::all();
        $userCount = $users->count();
        $verified = User::whereNotNull('email_verified_at')->count();
        $notVerified = User::whereNull('email_verified_at')->count();
        $usersUnique = $users->unique(['email']);
        $userDuplicates = $users->diff($usersUnique)->count();

        return view('content.HS.standard-datatable', [
            'totalUser' => $userCount,
            'verified' => $verified,
            'notVerified' => $notVerified,
            'userDuplicates' => $userDuplicates,
        ]);
    }

    /**
     * Return paginated, searchable list of users for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $columns = [
            1 => 'id',
            2 => 'name',
            3 => 'email',
            4 => 'email_verified_at',
        ];

        $totalData = User::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';

        $query = User::query();

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $users = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        $ids = $start;

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->id,
                'fake_id' => ++$ids,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created user or update an existing user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $userID = $request->id;

        $data = $request->only(['name', 'email', 'userContact', 'company', 'country', 'role']);
        $data['role_id'] = $data['role'];
        unset($data['role']);

        if (auth()->user()?->is_supreme_admin) {
            $data['is_super_admin'] = (int) $request->input('is_super_admin', 0);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userID,
            'role' => 'required|exists:roles,id',
            'password' => $userID ? 'nullable|string|min:6|confirmed' : '',
        ]);

        $role = Role::find($request->role);

        if ($userID) {
            $user = User::findOrFail($userID);
            $user->fill($data);

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();
            $user->syncRoles([$role->name]);

            return response()->json('Updated');
        } else {
            if (!User::where('email', $request->email)->exists()) {
                $password = $request->filled('password') ? $request->password : Str::random(10);
                $data['password'] = bcrypt($password);

                $user = User::create($data);
                $user->assignRole($role->name);

                return response()->json('Created');
            } else {
                return response()->json(['message' => "User already exists"], 422);
            }
        }
    }

    /**
     * Fetch user data for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id): JsonResponse
    {
        $user = User::select('id', 'name', 'email', 'role_id', 'is_super_admin')
            ->findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     * (Currently Empty Placeholder)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // To be implemented if needed.
    }

    /**
     * Delete a user by ID.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
    }
}
