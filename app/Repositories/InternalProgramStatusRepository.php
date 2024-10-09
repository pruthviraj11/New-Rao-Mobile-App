<?php
namespace App\Repositories;

use App\Models\InternalProgramStatus;

class InternalProgramStatusRepository
{
    public function find($id)
    {
        return InternalProgramStatus::find($id);
    }

    public function create(array $data)
    {
        return InternalProgramStatus::create($data);
    }

    public function update($id, array $data)
    {
        return InternalProgramStatus::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return InternalProgramStatus::where('id', $id)->delete();
    }
    public function getAll()
    {
        return InternalProgramStatus::all();
    }
}
