<?php

namespace App\Http\Controllers;

use App\Models\PermissionRoutes;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserRolePermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;

class UserRoleController extends Controller
{
    public function viewAllUserRoles(Request $request)
    {
        if ($request->ajax()) {

            $data = UserRole::orderBy('id', 'desc')->get();
            return Datatables::of($data)
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i:s a", strtotime($data->created_at));
                })
                ->editColumn('updated_at', function ($data) {
                    if ($data->updated_at) {
                        return date("Y-m-d h:i:s a", strtotime($data->updated_at));
                    }
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('edit/user/role') . '/' . $data->id . '" class="mb-1 btn-sm btn-warning rounded d-inline-block"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Delete" class="btn-sm btn-danger rounded d-inline-block deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.role_permission.user_roles_view');
    }

    public function newUserRole()
    {
        return view('backend.role_permission.user_role_create');
    }

    public function saveUserRole(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:user_roles'],
        ]);

        $roleId = UserRole::insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'created_at' => Carbon::now()
        ]);

        foreach ($request->permission_id as $permissionId) {
            $routeInfo = PermissionRoutes::where('id', $permissionId)->first();
            RolePermission::insert([
                'role_id' => $roleId,
                'role_name' => $request->name,
                'permission_id' => $permissionId,
                'route' => $routeInfo->route,
                'route_name' => $routeInfo->name,
                'created_at' => Carbon::now()
            ]);
        }

        Toastr::success('New Role Created', 'Success');
        return redirect('view/user/roles');
    }

    public function deleteUserRole($id)
    {
        $userRoleInfo = UserRole::where('id', $id)->first();
        RolePermission::where('role_id', $userRoleInfo->id)->delete();
        UserRolePermission::where('role_id', $userRoleInfo->id)->delete();
        $userRoleInfo->delete();
        return response()->json(['success' => 'Made SuperAdmin Successfully']);
    }

    public function EditUserRole($id)
    {
        $userRoleInfo = UserRole::where('id', $id)->first();
        return view('backend.role_permission.user_role_edit', compact('userRoleInfo'));
    }

    public function UpdateUserRole(Request $request)
    {

        // try {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        UserRole::where('id', $request->role_id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'updated_at' => Carbon::now()
        ]);

        // Check if the permission is already assigned to the user
        $users = UserRolePermission::where('role_id', $request->role_id)
            ->select('user_id')
            ->distinct()
            ->get();

        // If any users are assigned to this role
        if ($users->count() > 0) {
            RolePermission::where('role_id', $request->role_id)->delete();
            UserRolePermission::where('role_id', $request->role_id)->delete();

            if (isset($request->permission_id) && count($request->permission_id) > 0) {
                foreach ($users as $user) {
                    foreach ($request->permission_id as $permissionId) {
                        $routeInfo = PermissionRoutes::where('id', $permissionId)->first();

                        RolePermission::insert([
                            'role_id' => $request->role_id,
                            'role_name' => $request->name,
                            'permission_id' => $permissionId,
                            'route' => $routeInfo->route,
                            'route_name' => $routeInfo->name,
                            'created_at' => Carbon::now()
                        ]);

                        UserRolePermission::insert([
                            'user_id' => $user->user_id,
                            'role_id' => $request->role_id,
                            'role_name' => $request->name,
                            'permission_id' => $permissionId,
                            'route' => $routeInfo->route,
                            'route_name' => $routeInfo->name,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()

                        ]);
                    }
                }
            }
        } else {
            RolePermission::where('role_id', $request->role_id)->delete();

            if (isset($request->permission_id) && count($request->permission_id) > 0) {

                foreach ($request->permission_id as $permissionId) {

                    $routeInfo = PermissionRoutes::where('id', $permissionId)->first();

                    RolePermission::insert([
                        'role_id' => $request->role_id,
                        'role_name' => $request->name,
                        'permission_id' => $permissionId,
                        'route' => $routeInfo->route,
                        'route_name' => $routeInfo->name,
                        'created_at' => Carbon::now()
                    ]);
                }
            }
        }

        Toastr::success('User Role Updated', 'Success');
        return redirect('view/user/roles');
        // } catch (\Exception $e) {
        //     \Log::error($e->getMessage());
        // }
    }

    public function viewUserRolePermission(Request $request)
    {
        if ($request->ajax()) {
            $data = User::where('user_type', 2)->orderBy('id', 'desc')->get();
            return Datatables::of($data)
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i:s a", strtotime($data->created_at));
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = ' <a href="' . url('/assign/role/permission') . "/" . $data->id . '" class="btn-sm btn-warning rounded"><i class="fas fa-edit"></i> Assign</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'user_type'])
                ->make(true);
        }
        return view('backend.role_permission.user_role_permission');
    }

    public function assignRolePermission($id)
    {
        $userId = $id;
        return view('backend.role_permission.user_role_permission_assign', compact('userId'));
    }

    public function SaveAssignedRolePermission(Request $request)
    {
        UserRolePermission::where('user_id', $request->user_id)->delete();

        if (isset($request->role_id) && count($request->role_id) > 0) {
            foreach ($request->role_id as $roleId) {
                $rolePermissions = RolePermission::where('role_id', $roleId)->get();
                foreach ($rolePermissions as $rolePermission) {
                    UserRolePermission::insert([
                        'user_id' => $request->user_id,
                        'role_id' => $rolePermission->role_id,
                        'role_name' => $rolePermission->role_name,
                        'permission_id' => $rolePermission->permission_id,
                        'route' => $rolePermission->route,
                        'route_name' => $rolePermission->route_name,
                        'created_at' => Carbon::now()
                    ]);
                }
            }
        }

        if (isset($request->permission_id) && count($request->permission_id) > 0) {
            foreach ($request->permission_id as $permissionId) {
                $routeInfo = PermissionRoutes::where('id', $permissionId)->first();

                // Check if the permission is already assigned to the user
                $existingPermission = UserRolePermission::where('user_id', request()->user_id)
                    ->where('permission_id', $permissionId)
                    ->first();

                if ($existingPermission) {
                    $existingPermission->update([
                        'updated_at' => Carbon::now()
                    ]);
                } else {
                    UserRolePermission::insert([
                        'user_id' => $request->user_id,
                        'role_id' => null,
                        'role_name' => null,
                        'permission_id' => $permissionId,
                        'route' => $routeInfo->route,
                        'route_name' => $routeInfo->name,
                        'created_at' => Carbon::now()
                    ]);
                }
            }
        }

        Toastr::success('User Role Updated', 'Success');
        return redirect('view/user/role/permission');
    }
}
