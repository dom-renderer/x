<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $title = 'Roles';
    protected $view = 'roles.';

    public function __construct()
    {
        $this->middleware('permission:roles.index')->only(['index', 'ajax']);
        $this->middleware('permission:roles.create')->only(['create']);
        $this->middleware('permission:roles.store')->only(['store']);
        $this->middleware('permission:roles.edit')->only(['edit']);
        $this->middleware('permission:roles.update')->only(['update']);
        $this->middleware('permission:roles.show')->only(['show']);
        $this->middleware('permission:roles.destroy')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->ajax();
        }

        $title = $this->title;
        $subTitle = 'Manage roles here';

        return view($this->view . 'index', compact('title', 'subTitle'));
    }

    /**
     * return the json resource.
     */
    public function ajax()
    {
        $query = Role::where('type', 'user');

        return datatables()
        ->eloquent($query)
        ->addColumn('action', function ($row) {
            $html = '';

            if (auth()->user()->can('roles.edit')) {
                if ($row->name != 'admin') {
                    $html .= '<ul>
                        <li><a href="' . route('roles.edit', encrypt($row->id)) . '"> Edit </a></li>
                    </ul>';
                }
            }

            if ($row->is_sytem_role == 0 && auth()->user()->can('roles.destroy')) {
                $html .= '<button type="button" class="btn btn-sm btn-danger" id="deleteRow" data-row-route="' . route('roles.destroy', $row->id) . '"> <i class="fa fa-trash"> </i> </button>&nbsp;';
            }

            if (auth()->user()->can('roles.show')) {
                    $html .= '<ul>
                        <li><a href="' . route('roles.show', encrypt($row->id)) . '"> Show </a></li>
                    </ul>';
            }

            return $html;
        })
        ->rawColumns(['phone_number', 'status', 'roles', 'action'])
        ->addIndexColumn()
        ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = $this->title;
        $subTitle = 'Add New Role';
        $permissions = self::groupPermissionsByPrefix();

        return view($this->view . 'create', compact('title', 'subTitle', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:roles,title'
        ]);

        $role = Role::create([
            'title' => $request->title,
            'name' => Str::slug($request->title),
        ]);

        if ($request->has('permissions') && is_array($request->permissions)) {
            foreach ($request->permissions as $permissionId) {
                $role->permissions()->attach($permissionId);
            }
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::findOrFail(decrypt($id));
        $title = $this->title;
        $subTitle = 'View Role';
        $permissions = self::groupPermissionsByPrefix();
        $existingPermissions = $role->permissions->pluck('id')->toArray();

        return view($this->view . 'view', compact('title', 'subTitle', 'role', 'permissions', 'existingPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail(decrypt($id));
        $title = $this->title;
        $subTitle = 'Edit Role';
        $permissions = self::groupPermissionsByPrefix();
        $existingPermissions = $role->permissions->pluck('id')->toArray();

        return view($this->view . 'edit', compact('title', 'subTitle', 'role', 'permissions', 'existingPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail(decrypt($id));
        $request->validate([
            'title' => 'required|string|max:255|unique:roles,title,' . $role->id
        ]);

        $role->update([
            'title' => $request->title,
            'name' => Str::slug($request->title),
        ]);

        $role->permissions()->detach();        
        if ($request->has('permissions') && is_array($request->permissions)) {
            foreach ($request->permissions as $permissionId) {
                $role->permissions()->attach($permissionId);
            }
        }        

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->is_sytem_role) {
            return response()->json(['error' => 'System roles cannot be deleted.'], 403);
        }
        $role->delete();
        return response()->json(['success' => 'Role deleted successfully.']);
    }

    /**
     * Make group of permissions
     * **/
    public static function groupPermissionsByPrefix()
    {
        $permissions = Permission::all();
        
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $prefix = explode('.', $permission->name)[0];

            if (!isset($groupedPermissions[$prefix])) {
                $groupedPermissions[$prefix] = [];
            }

            $groupedPermissions[$prefix][] = $permission;
        }

        return $groupedPermissions;
    }

}
