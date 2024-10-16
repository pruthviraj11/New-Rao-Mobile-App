<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\UserApplicationStatus;
use App\Models\ApplicationStatuses;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    protected UserService $userService;
    protected RoleService $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);



        // Permission::create(['name' => 'user-list', 'guard_name' => 'web', 'module_name' => 'Users']);
        // Permission::create(['name' => 'user-create', 'guard_name' => 'web', 'module_name' => 'Users']);
        // Permission::create(['name' => 'user-edit', 'guard_name' => 'web', 'module_name' => 'Users']);
        // Permission::create(['name' => 'user-delete', 'guard_name' => 'web', 'module_name' => 'Users']);




    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {

        $currentUser = auth()->user();
        $type = 1;

        $hierarchyData = $this->getHierarchyWiseUser($currentUser, $type);


        $reportingUserIds = $hierarchyData['reportingTo']->pluck('id')->toArray();


        $descendantUserIds = $this->getDescendants($currentUser->id)->toArray();


        $allUserIds = array_merge($reportingUserIds, $descendantUserIds);


        $filteredUserCount = User::whereIn('id', $allUserIds)->count();


        $totalUserCount = User::count();

        $data['total_user'] = $totalUserCount;
        $data['filtered_user_count'] = $filteredUserCount;
        // dd($data);
        return view('content.apps.user.list', compact('data'));
    }


    public function getDescendants($userId)
    {
        $descendants = User::where('reporting_to', $userId)->where('role_id', "!=", 2)->pluck('id');

        $allDescendants = collect($descendants);

        foreach ($descendants as $descendant) {
            $allDescendants = $allDescendants->merge($this->getDescendants($descendant));
        }

        return $allDescendants;
    }
    public function getHierarchyWiseUser($user, $type)
    {
        if ($user->role_id == 1) {
            $reportingQuery = User::whereIn('id', function ($query) {
                $query->select('id')->from('users')
                    ->where(function ($query) {
                        $query->where('role_id', '!=', 25)
                            ->where('role_id', '!=', 1);
                    });
            })->orderBy('name', 'asc')->get();

        } else if ($user->role_id == 3) {
            $reportingQuery = User::whereIn('id', function ($query) {
                $query->select('id')->from('users')
                    ->where('role_id', '!=', 25);
            })->orderBy('name', 'asc')->get();

        } else {
            $reportingQuery = User::whereIn('id', function ($query) use ($user) {
                $query->select('id')
                    ->from('users')
                    ->where('user_category', $user->user_category)
                    ->whereIn('id', $this->getDescendants($user->id))
                    ->orWhere('id', $user->id);
            })->orderBy('name', 'asc')->get();
        }

        if ($type == 1) {
            if (!empty($reportingQuery)) {
                $adviaorUsersId = collect($reportingQuery)->pluck('id')->toArray();
            } else {
                $adviaorUsersId = [];
            }
        } else {
            if (!empty($reportingQuery)) {
                $adviaorUsersId = collect($reportingQuery)->pluck('advisor_user_id')->toArray();
            } else {
                $adviaorUsersId = [];
            }
        }

        return ['reportingTo' => $reportingQuery, 'adviaorUsersId' => $adviaorUsersId];
    }
    public function getAll()
    {
        $currentUser = auth()->user();
        $type = 1;

        $hierarchyData = $this->getHierarchyWiseUser($currentUser, $type);
        $reportingUserIds = $hierarchyData['reportingTo']->pluck('id')->toArray();
        $descendantUserIds = $this->getDescendants($currentUser->id)->toArray();
        $allUserIds = array_merge($reportingUserIds, $descendantUserIds);

        $users = User::whereIn('id', $allUserIds)->get();

        return DataTables::of($users)
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->id);

                // Update Button
                $updateButton = "<a data-bs-toggle='tooltip' title='Edit' data-bs-delay='400' class='btn btn-warning' href='" . route('app-users-edit', $encryptedId) . "'><i data-feather='edit'></i></a>";

                // Delete Button
                $deleteButton = "<a data-bs-toggle='tooltip' title='Delete' data-bs-delay='400' class='btn btn-danger confirm-delete' data-idos='" . $encryptedId . "' id='confirm-color' href='" . route('app-users-destroy', $encryptedId) . "'><i data-feather='trash-2'></i></a>";

                // Start Chat Button
                $chatButton = "<a data-bs-toggle='tooltip' title='Application Journey' data-bs-delay='400' class='btn btn-info' href='" . route('users.application_journey', $encryptedId) . "'><i data-feather='send'></i></a>";

                // Block Button
                $blockButton = "<button class='BlockButton btn btn-danger btn-sm' data-id='{$encryptedId}' data-is-blocked='{$row->is_block_user}' title='Toggle Block'>
                <i class='ficon' data-feather='x'></i> " . ($row->is_block_user == 1 ? 'Unblock' : 'Block') . "
            </button>";

                $restrictedButton = "<a data-bs-toggle='tooltip' title='Restricted Screens' data-bs-delay='400' class='btn btn-primary' href='" . route('users.restricted.screen', $encryptedId) . "'> <i data-feather='smartphone'></i></a>";


                return $updateButton . " " . $deleteButton . " " . $chatButton . " " . $blockButton . " " . $restrictedButton;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
    public function bulkDelete(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        // Soft delete users
        User::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Users deleted successfully.']);
    }

    public function restrictScreens($id)
    {
        $decryptId = decrypt($id);
        $user = User::find($decryptId);

        return view('content.apps.user.user-restrictScreen', compact('user'));

    }
    public function restrictScreensStore(Request $request)
    {
        try {
            $input = $request->all();
            $user = User::findOrFail($input['user_id']);

            if (!is_null($user)) {
                $user->home_screen = isset($input['home_screen']) ? 1 : 0;
                $user->profile_screen = isset($input['profile_screen']) ? 1 : 0;
                $user->consultant_screen = isset($input['consultant_screen']) ? 1 : 0;
                $user->consulting_journy_screen = isset($input['consulting_journy_screen']) ? 1 : 0;
                $user->our_services_screen = isset($input['our_services_screen']) ? 1 : 0;
                $user->need_help_screen = isset($input['need_help_screen']) ? 1 : 0;
                $user->success_stories_screen = isset($input['success_stories_screen']) ? 1 : 0;
                $user->faq_screen = isset($input['faq_screen']) ? 1 : 0;
                $user->privacy_policy_screen = isset($input['privacy_policy_screen']) ? 1 : 0;
                $user->save();

                return redirect()->route('app-users-list')->with('success', 'Screen restrictions updated successfully!');
            }

            return redirect()->back()->with('error', 'User not found.');
        } catch (\Exception $e) {
            // Log the exception message for debugging
            dd($e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while updating the screen restrictions. Please try again.');
        }
    }


    public function blockUser(Request $request)
    {
        $decryptId = decrypt($request->blockId);

        $user = User::find($decryptId);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }


        if ($user->is_block_user == 1) {
            $user->is_block_user = 0; // Unblock the user
        } else {
            $user->is_block_user = 1; // Block the user
        }

        $user->save(); // Save the changes

        return response()->json(['message' => 'User status changed successfully.']);
    }


    public function applicationJourney($id)
    {
        $decrypt = decrypt($id);
        $user = User::find($decrypt);

        if (!$user || $user->role_id != 2) {
            return redirect()->back()->with('error', 'User not found or unauthorized.');
        }

        $status_value = ApplicationStatuses::pluck('name')->toArray();

        // Fetch application statuses based on user category
        $application_statuses = ApplicationStatuses::where('category_id', $user->user_category)
            ->orderBy("order")
            ->get();

        foreach ($application_statuses as $application_status) {
            $userApplication = UserApplicationStatus::where('user_id', $decrypt)
                ->where('application_status', $application_status->id)
                ->first();

            if ($userApplication) {
                $application_status->status_value = $userApplication->status_value;
                $application_status->status_date = $userApplication->status_date;
                $application_status->status_order = $userApplication->status_order;
            }
        }

        return view('content.apps.user.application-journey', compact('user', 'status_value', 'application_statuses'));
    }


    public function storeStatus(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'application_status_id' => 'required|exists:application_statuses,id',
            'status_value' => 'required|string',
            'status_date' => 'nullable|date',
            'status_order' => 'nullable|integer',
        ]);

        try {
            // Retrieve user
            $user = User::findOrFail($request->get('user_id'));

            // Retrieve or create application user status
            $applicationUserStatus = UserApplicationStatus::updateOrCreate(
                [
                    'application_status' => $request->get('application_status_id'),
                    'user_id' => $user->id,
                ],
                [
                    'status_value' => $request->get('status_value'),
                    'status_date' => $request->get('status_date'),
                    'status_order' => $request->get('status_order'),
                ]
            );

            // Prepare notification data if necessary
            $shouldSendNotification = $request->get('status_value') === "Done" &&
                ($applicationUserStatus->wasRecentlyCreated || $applicationUserStatus->status_value !== "Done");

            if ($shouldSendNotification) {
                // Fetch notification message from settings
                $settingMessage = DB::table('settings')->where('key', 'admin.admin_application_journey_status_message')->value('value');

                // Prepare notification data
                $notificationData = [
                    'type' => 2,
                    'title' => $applicationUserStatus->applicationStatus->name ?? '',
                    'message' => "Your status has been changed.",
                ];

                // Send push notification
                $this->sendPushNotification($user->device_token, "Application Journey", $settingMessage, $notificationData);

                // Store notification
                $this->storeNotification([
                    'title' => $notificationData['title'],
                    'message' => $settingMessage,
                    'is_sent' => 1,
                    'type' => 2,
                    'user_id' => $user->id,
                ]);
            }

            return response()->json(['status' => true, 'message' => 'Status stored successfully.']);

        } catch (\Exception $e) {
            // Log the error message for debugging
            \Log::error('Error storing status: ' . $e->getMessage());

            return response()->json(['status' => false, 'message' => 'An error occurred while storing the status. Please try again.'], 500);
        }
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $page_data['page_title'] = "User";
        $page_data['form_title'] = "Add New User";
        $user = '';
        $userslist = $this->userService->getAllUser();
        $roles = $this->roleService->getAllRoles();

        $data['reports_to'] = User::all();
        return view('.content.apps.user.create-edit', compact('page_data', 'user', 'userslist', 'roles', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        try {
            $userData['username'] = $request->get('username');
            $userData['first_name'] = $request->get('first_name');
            $userData['last_name'] = $request->get('last_name');
            $userData['email'] = $request->get('email');
            $userData['phone_no'] = $request->get('phone_no');
            $userData['password'] = Hash::make($request->get('password'));
            $userData['dob'] = $request->get('dob');
            $userData['address'] = $request->get('address');
            $userData['report_to'] = $request->get('report_to');
            $userData['status'] = $request->get('status') == 'on' ? true : false;
            $user = $this->userService->create($userData);
            $role = Role::find($request->get('role'));
            $user->assignRole($role);
            if (!empty($user)) {
                return redirect()->route("app-users-list")->with('success', 'User Added Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Adding User');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'Error while adding User');
        }
    }

    public function profile($encrypted_id)
    {
        $id = decrypt($encrypted_id);

        $data = User::find($id);
        return view('.content.pages.page-account-settings-account', compact('data'));
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $user = $this->userService->getUser($id);
            $page_data['page_title'] = "User";
            $page_data['form_title'] = "Edit User";

            $userslist = $this->userService->getAllUser();
            $roles = $this->roleService->getAllRoles();
            $user->role = $user->getRoleNames()[0];
            // dd($user);
            $data['reports_to'] = User::all();
            return view('/content/apps/user/create-edit', compact('page_data', 'user', 'data', 'roles', 'userslist'));
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'Error while editing User');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param $encrypted_id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function updateProfile(UpdateUserProfileRequest $request, $encrypted_id)
    {
        try {
            // dd($request->all());
            $id = decrypt($encrypted_id);
            // $userData['username'] = $request->get('username');
            $userData['name'] = $request->get('name');
            $userData['email'] = $request->get('email');
            $userData['phone_number'] = $request->get('phone_number');
            $user = User::where('id', $id)->first();
            $updated = $this->userService->updateUser($id, $userData);
            if (!empty($updated)) {

                return redirect()->back()->with('success', 'Profile updated successfully');
            } else {

                return redirect()->back()->with('error', 'Error while Updating User');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'Error while editing User');
        }

    }

    public function update(UpdateUserRequest $request, $encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $userData['username'] = $request->get('username');
            $userData['first_name'] = $request->get('first_name');
            $userData['last_name'] = $request->get('last_name');
            $userData['email'] = $request->get('email');
            $userData['phone_no'] = $request->get('phone_no');
            if ($request->get('password') != null && $request->get('password') != '') {
                $userData['password'] = Hash::make($request->get('password'));
            }
            $userData['dob'] = $request->get('dob');
            $userData['address'] = $request->get('address');
            $userData['report_to'] = $request->get('report_to');
            $userData['status'] = $request->get('status') == 'on' ? true : false;
            $updated = $this->userService->updateUser($id, $userData);
            $user = User::where('id', $id)->first();
            $role = Role::find($request->get('role'));
            $user->removeRole($user->getRoleNames()[0]);
            $user->assignRole($role);
            if (!empty($updated)) {
                return redirect()->route("app-users-list")->with('success', 'User Updated Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Updating User');
            }
        } catch (\Exception $error) {
            dd($error->getMessage());
            return redirect()->route("app-users-list")->with('error', 'Error while editing User');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $encrypted_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($encrypted_id)
    {
        try {
            $id = decrypt($encrypted_id);
            $deleted = $this->userService->deleteUser($id);
            if (!empty($deleted)) {
                return redirect()->route("app-users-list")->with('success', 'Users Deleted Successfully');
            } else {
                return redirect()->back()->with('error', 'Error while Deleting Users');
            }
        } catch (\Exception $error) {
            return redirect()->route("app-users-list")->with('error', 'Error while editing Users');
        }
    }
}
