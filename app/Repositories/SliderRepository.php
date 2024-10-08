<?php
namespace App\Repositories;

use App\Models\Slide;

class SliderRepository
{
    public function find($id)
    {
        return Slide::find($id);
    }

    public function create(array $data)
    {
        return Slide::create($data);
    }

    public function update($id, array $data)
    {
        return Slide::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Slide::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Slide::all();
    }
}
