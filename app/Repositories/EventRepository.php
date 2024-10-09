<?php
namespace App\Repositories;

use App\Models\Events;

class EventRepository
{

    public function find($id)
    {
        return Events::find($id);
    }

    public function create(array $data)
    {
        return Events::create($data);
    }

    public function update($id, array $data)
    {
        return Events::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Events::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Events::all();
    }

}
