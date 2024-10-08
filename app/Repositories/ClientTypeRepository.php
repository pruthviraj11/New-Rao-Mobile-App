<?php
namespace App\Repositories;

use App\Models\ClientType;

class ClientTypeRepository
{
    public function find($id)
    {
        return ClientType::find($id);
    }

    public function create(array $data)
    {
        return ClientType::create($data);
    }

    public function update($id, array $data)
    {
        return ClientType::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return ClientType::where('id', $id)->delete();
    }
    public function getAll()
    {
        return ClientType::all();
    }
}
