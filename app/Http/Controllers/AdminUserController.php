<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\NewsCategory;
use App\Models\Role;
use App\Models\User;
use App\Services\AdminUserService;
use Illuminate\Http\Request;
use App\Http\Requests\AdminUser\CreateAdminUserRequest;
use App\Http\Requests\AdminUser\UpdateAdminUserRequest;
use App\Models\ClientType;
use \Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class AdminUserController extends Controller
{
    protected $adminUserService;

    public function __construct(AdminUserService $adminUserService)
    {
        $this->adminUserService = $adminUserService;
        $this->middleware('permission:admin-user-list|admin-user-create|admin-user-edit|admin-user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:admin-user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:admin-user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:admin-user-delete', ['only' => ['destroy']]);

        // Permission::create(['name' => 'admin-user-list', 'guard_name' => 'web', 'module_name' => 'Admin User']);
        // Permission::create(['name' => 'admin-user-create', 'guard_name' => 'web', 'module_name' => 'Admin User']);
        // Permission::create(['name' => 'admin-user-edit', 'guard_name' => 'web', 'module_name' => 'Admin User']);
        // Permission::create(['name' => 'admin-user-delete', 'guard_name' => 'web', 'module_name' => 'Admin User']);

    }

    public function index()
    {
        return view('content/apps/AdminUser/list');
    }

    public function create()
    {
        $adminUsers = "";
        $page_data['page_title'] = "Admin User Add";
        $page_data['form_title'] = "Add New Admin User";
        $ClientType = ClientType::where('status', '1')->get();
        $users = User::where('role_id', '!=', 2)->get();
        $roles = Role::get();
        $userCategories = Category::get();
        return view('/content/apps/AdminUser/create-edit', compact('page_data', 'adminUsers', 'ClientType', 'roles', 'users', 'userCategories'));
    }

    public function getAll()
    {
        $adminUsers = $this->adminUserService->getAllAdminUser();
        return DataTables::of($adminUsers)
            ->addColumn('client_type', function ($row) {
                return $row->clientType ? $row->clientType->displayname : 'N/A';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);
                // Update Button
                $updateButton = "<a class='btn btn-warning btn-sm ' href='" . route('app-admin-user-edit', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-edit ficon\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg> </a>";

                // Delete Button
                $deleteButton = "<a class='btn btn-danger btn-sm mx-1 confirm-delete' data-idos='$encryptedId' id='confirm-color' href='" . route('app-admin-user-delete', $encryptedId) . "'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-trash-2 ficon\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path><line x1=\"10\" y1=\"11\" x2=\"10\" y2=\"17\"></line><line x1=\"14\" y1=\"11\" x2=\"14\" y2=\"17\"></line></svg> </a>";
                $buttons = $updateButton . " " . $deleteButton;
                return "<div class='d-flex justify-content-start'>" . $buttons . "</div>";
            })
            ->rawColumns(['actions'])->make(true);
    }

    public function store(CreateAdminUserRequest $request)
    {
        try {
            $adminUserData['name'] = $request->get('name');
            $adminUserData['email'] = $request->get('email');
            $adminUserData['user_category'] = $request->get('user_category');
            $adminUserData['password'] = bcrypt($request->get('password'));
            $adminUserData['phone_number'] = $request->get('phone_number');
            $adminUserData['reporting_to'] = $request->get('reporting_to');
            $adminUserData['is_download'] = $request->get('is_download');
            $adminUserData['download_date'] = $request->get('download_date');
            $adminUserData['role_id'] = $request->get('role_id');
            $adminUserData['status'] = $request->get('status') === 'on' ? 1 : 0;

            if ($request->hasFile('avatar')) {
                $originalName = $request->file('avatar')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('avatar')->storeAs('AdminUser', $filename, 'public');
                $adminUserData['avatar'] = $imagePath;
            }

            $adminUser = $this->adminUserService->create($adminUserData);
            if (!empty($adminUser)) {
                return redirect()->route('app-admin-user-list')->with('success', 'Admin User Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding Admin User');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route('app-admin-user-list')->with('error', 'Error while editing Admin User');
        }

    }

    public function edit($encrypted_id)
    {
        try {

            $id = decrypt($encrypted_id);
            $adminUsers = $this->adminUserService->getAdminUser($id);
            $page_data['page_title'] = "Admin User Edit";
            $page_data['form_title'] = "Edit Admin User";
            $ClientType = ClientType::where('status', '1')->get();
            $users = User::where('role_id', '!=', 2)->get();
            $roles = Role::get();
            $userCategories = Category::get();

            return view('content/apps/AdminUser/create-edit', compact('page_data', 'adminUsers', 'ClientType', 'users', 'roles','userCategories'));
        } catch (\Exception $error) {
            return redirect()->route("app/AdminUser/list")->with('error', 'Error while editing Admin user');
        }
    }


    public function update(UpdateAdminUserRequest $request, $encrypted_id)
    {
        try {
            // dd($request->all());
            $id = decrypt($encrypted_id);

            $adminUsers = $this->adminUserService->getAdminUser($id);
            $adminUserData['name'] = $request->get('name');
            $adminUserData['email'] = $request->get('email');
            $adminUserData['user_category'] = $request->get('user_category');
            $adminUserData['password'] = bcrypt($request->get('password'));
            $adminUserData['phone_number'] = $request->get('phone_number');
            $adminUserData['reporting_to'] = $request->get('reporting_to');
            $adminUserData['is_download'] = $request->get('is_download');
            $adminUserData['download_date'] = $request->get('download_date');
            $adminUserData['role_id'] = $request->get('role_id');
            $adminUserData['status'] = $request->get('status') === 'on' ? 1 : 0;
            // dd($adminUserData);
            if ($request->hasFile('avatar')) {
                if ($adminUsers->avatar) {
                    Storage::disk('public')->delete($adminUsers->avatar);
                }

                $originalName = $request->file('avatar')->getClientOriginalName();
                $filename = str_replace(' ', '_', $originalName);
                $imagePath = $request->file('avatar')->storeAs('AdminUser', $filename, 'public');
                $adminUserData['avatar'] = $imagePath;
            }



            $updated = $this->adminUserService->updateAdminUser($id, $adminUserData);
            if (!empty($updated)) {
                return redirect()->route("app-admin-user-list")->with('success', 'Admin User Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating Admin User');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-admin-user-list")->with('error', 'Error while editing Admin User');
        }
    }

    public function destroy($encrypted_id = '')
    {

        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->adminUserService->deleteAdminUser($id);
            if (!empty($deleted)) {
                return redirect()->route("app-admin-user-list")->with('success', 'Admin User Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Admin User');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-admin-user-list")->with('error', 'Error while Deleting Admin User');
        }
    }

    public function destroyimage($id)
    {
        $adminUsers = User::findOrFail($id);

        if ($adminUsers->avatar) {

            // Storage::delete($adminUsers->image);
            $adminUsers->update(['avatar' => '']);
        }

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
}
