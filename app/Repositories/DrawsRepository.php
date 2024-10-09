<?php
namespace App\Repositories;

use App\Models\Draws;

class DrawsRepository
{

    public function find($id)
    {
        return Draws::find($id);
    }

    public function create(array $data)
    {
        return Draws::create($data);
    }

    public function update($id, array $data)
    {
        return Draws::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Draws::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Draws::all();
    }

}
