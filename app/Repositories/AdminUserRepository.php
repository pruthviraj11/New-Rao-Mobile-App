<?php
namespace App\Repositories;

use App\Models\AdminUser;

class AdminUserRepository
{

    public function find($id)
    {
        return AdminUser::find($id);
    }

    public function create(array $data)
    {
        return AdminUser::create($data);
    }

    public function update($id, array $data)
    {
        return AdminUser::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return AdminUser::where('id', $id)->delete();
    }
    public function getAll()
    {
        // return AdminUser::where('role_id',4)->orderBy('id','desc')->get();

        $user = auth()->user(); // Get the authenticated user

        $query = AdminUser::query(); // Initialize the query builder for AdminUser

        // Apply conditions based on role_id and user_category
        if($user->role_id == 4) {
            $query->whereIn('id', getDescendants($user->id)) // Assuming getDescendants() is a helper function
                  ->orWhere('id', $user->id)
                  ->where('user_category', $user->user_category);
        } elseif(!in_array($user->role_id, [1, 3, 4])) {
            $query->whereIn('id', getDescendants($user->id))
                  ->where('user_category', $user->user_category);
        } else {
            $query->whereNotIn('role_id', [2, 3]);
        }

        return $query->orderBy('id', 'desc')->get();
    }

}
