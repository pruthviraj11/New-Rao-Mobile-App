<?php
namespace App\Repositories;

use App\Models\OurServices;

class OurServicesRepository
{
    public function find($id)
    {
        return OurServices::find($id);
    }

    public function create(array $data)
    {
        return OurServices::create($data);
    }

    public function update($id, array $data)
    {
        return OurServices::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return OurServices::where('id', $id)->delete();
    }
    public function getAll()
    {
        return OurServices::all();
    }
}
