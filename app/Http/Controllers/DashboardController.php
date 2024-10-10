<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApplicationStatuses;
use App\Models\ClientType;
use App\Models\User;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission;
class DashboardController extends Controller
{
    public function index()
    {

        $categorys = ClientType::where('status', '1')->get();
        return view('content/apps/DashboredReport/list', compact('categorys'));
    }
    public function getApplicationStatuses($categoryId)
    {
        $applicationStatuses = ApplicationStatuses::where('category_id', $categoryId)->get();

        // Check if no statuses found
        if ($applicationStatuses->isEmpty()) {
            return response()->json(['message' => 'No statuses found'], 404);
        }

        return response()->json($applicationStatuses);
    }

    public function getUsers_data($categoryId)
    {
        $roleId = 21;
        $users = User::where('user_category', $categoryId)->where('role_id', $roleId)->get();

        // Check if no users found
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }

        return response()->json($users);
    }

}
