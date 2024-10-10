<?php
namespace App\Repositories;

use App\Models\OtherStakeholders;

class OtherStakeholdersRepository
{
    public function find($id)
    {
        return OtherStakeholders::find($id);
    }

    public function create(array $data)
    {
        return OtherStakeholders::create($data);
    }

    public function update($id, array $data)
    {
        return OtherStakeholders::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return OtherStakeholders::where('id', $id)->delete();
    }
    public function getAll()
    {
        return OtherStakeholders::select('other_stakeholders.*', 'users.name as user_name')
            ->leftJoin('users', 'users.id', '=', 'other_stakeholders.user_id')

            // ->leftJoin('users as advisors', function($join) {                  // Second join with `users` table as `advisors`
            //     $join->on('advisors.advisor_user_id', '=', 'other_stakeholders.attached_user_id');
            // })

            ->orderBy('created_at','desc')
            ->get();
    }
}
