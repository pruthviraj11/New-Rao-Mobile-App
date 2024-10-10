<?php
namespace App\Repositories;

use App\Models\HomeService;

class HomeServiceRepository
{
    public function find($id)
    {
        return HomeService::find($id);
    }

    public function create(array $data)
    {
        return HomeService::create($data);
    }

    public function update($id, array $data)
    {
        return HomeService::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return HomeService::where('id', $id)->delete();
    }
    public function getAll()
    {
        return HomeService::all();
    }
}
