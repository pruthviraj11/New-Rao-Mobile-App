<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatuses;
use App\Models\ClientType;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Setting;
use Spatie\Permission\Models\Permission;
class DashboardController extends Controller
{
    public function index()
    {
        // $fullAccessRolesData = assignRoleToUsers();
        // dd($fullAccessRolesData);
        $categorys = ClientType::where('status', '1')->get();
        return view('content/apps/DashboredReport/list', compact('categorys'));
    }
    public function getUsers_data(Request $request)
    {
        $categoryId = $request->input('categoryId');

        $roles = $this->dyManager();
        $roleIds = array_column($roles, 'id');
        // dd($roleIds);
        // Fetch role names
        $roleNames = Role::whereIn('id', $roleIds)->pluck('name', 'id')->toArray();

        // Define the role array with IDs
        $roleArray = [
            1 => "admin",
            2 => "user",
            3 => "Super Admin",
            4 => "advisor",
            5 => "pro",
            6 => "Dy Manager",
            7 => "Backend",
            8 => "Head_operation_IV",
            9 => "Head_Operation_FE",
            10 => "Quality Check",
            11 => "audit",
            12 => "Application Advisor (FE)",
            13 => "Visa Advisor (FE)",
            14 => "manager",
        ];

       
        $matchedRoleIds = [];

        
        foreach ($roleNames as $id => $name) {
            /
            $roleKey = array_search($name, $roleArray);
            if ($roleKey !== false) {
                $matchedRoleIds[] = $roleKey; // Add the key to matchedRoleIds
            }
        }
        // dd($matchedRoleIds);
        // ["21","26"]
        // Check if any matched role IDs were found
        if (empty($matchedRoleIds)) {
            return response()->json(['message' => 'No valid roles found'], 404);
        }
        // Fetch users based on category and matched role IDs
        $users = User::where('user_category', $categoryId)
            ->whereIn('role_id', $matchedRoleIds)
            ->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }

        return response()->json($users);
    }


    public function getApplicationStatuses(Request $request)
    {
        $categoryId = $request->input('categoryId'); // Get category ID from request

        // Your logic to fetch application statuses goes here
        $statuses = ApplicationStatuses::where('category_id', $categoryId)->get();

        if ($statuses->isEmpty()) {
            return response()->json(['message' => 'No statuses found'], 404);
        }

        return response()->json($statuses);
    }

    public function fullAccessRoles(): array
    {
        $roles_id = json_decode(Setting::pluck('full_access')->first());

        $roleArray = array();

        foreach ($roles_id as $id) {
            $roleName = Role::where('id', $id)->pluck('name')->first();
            $roleArray[] = ['id' => $id, 'name' => $roleName]; // Store both ID and name
        }

        return $roleArray; // Returns an array of arrays with ID and name
    }

    public function dyManager()
    {
        $roles_id = json_decode(Setting::pluck('dymanager_manager')->first());

        $roleArray = array();

        foreach ($roles_id as $id) {
            $roleName = Role::where('id', $id)->pluck('name')->first();
            $roleArray[] = ['id' => $id, 'name' => $roleName]; // Store both ID and name
        }

        return $roleArray; // Returns an array of arrays with ID and name
    }


    // public function pearoRole()
    // {
    //     $roleName = Role::where('id', getSettings()->account_role)->pluck('name')->first();
    //     return $roleName;
    // }


}
