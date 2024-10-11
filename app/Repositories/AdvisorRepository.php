<?php
namespace App\Repositories;

use App\Models\Advisor;

class AdvisorRepository
{

    public function find($id)
    {
        return Advisor::find($id);
    }

    public function create(array $data)
    {
        return Advisor::create($data);
    }

    public function update($id, array $data)
    {
        return Advisor::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Advisor::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Advisor::where('role_id',4)->orderBy('id','desc')->get();
    }

}
