<?php
namespace App\Repositories;

use App\Models\SuccessStories;

class SuccessStoriesRepository
{
    public function find($id)
    {
        return SuccessStories::find($id);
    }

    public function create(array $data)
    {
        return SuccessStories::create($data);
    }

    public function update($id, array $data)
    {
        return SuccessStories::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return SuccessStories::where('id', $id)->delete();
    }
    public function getAll()
    {
        return SuccessStories::all();
    }
}
