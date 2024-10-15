<?php

namespace App\Helpers;
use App\Models\Role;
use App\Models\ManageRoleSettings;
use App\Models\Setting;
use App\Models\User;

// use Exception;



function roleName($value)
{

    $role_id = ManageRoleSettings::where('role_config->role_name', $value)->select('role_config->role_id')->first();
    $role_name = role::where('id', $role_id)->pluck('name')->first();

    return $role_name;
}

function assignRoleToUsers()
{
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
    $users = User::get();
    foreach ($users as $user) {
        if (key_exists($user->role_id, $roleArray)) {
            $role = Role::where('name', $roleArray[$user->role_id])->first();
            $user->assignRole($role);
        } else {
            dump($user->name . '-' . $roleArray[$user->role_id]);
        }
    }
    // dd("All Roles Assigned");
    function getSettings()
    {
        return ManageRoleSettings::first();
    }
    function fullAccessRoles()
    {
        $roles_id = json_decode(ManageRoleSettings::pluck('full_access')->first());

        $roleArray = array();

        foreach ($roles_id as $id) {
            $roleName = Role::where('id', $id)->pluck('name')->first();
            array_push($roleArray, $roleName);
        }

        return $roleArray;
    }


    function accountRole()
    {
        $roleName = Role::where('id', getSettings()->account_role)->pluck('name')->first();
        return $roleName;
    }



    function getDescendants($userId)
{
    $descendants = User::where('reporting_to', $userId)->where('role_id', "!=", 2)->pluck('id');

    $allDescendants = collect($descendants);

    foreach ($descendants as $descendant) {
        $allDescendants = $allDescendants->merge(getDescendants($descendant));
    }

    return $allDescendants;
}

    function getFinancialYears()
    {
        $firstCreatedDate = \DB::table('chat_sessions')->orderBy('created_at', 'asc')->value('created_at');
        $lastCreatedDate = \DB::table('chat_sessions')->orderBy('created_at', 'desc')->value('created_at');

        $startYear = \Carbon\Carbon::parse($firstCreatedDate)->format('Y');
        $endYear = \Carbon\Carbon::parse($lastCreatedDate)->format('Y');

        $yearRanges = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearRanges[$year] = $year . '-' . ($year + 1);
        }

        return $yearRanges;
    }

}
