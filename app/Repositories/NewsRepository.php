<?php
namespace App\Repositories;

use App\Models\News;

class NewsRepository
{
    public function find($id)
    {
        return News::find($id);
    }

    public function create(array $data)
    {
        return News::create($data);
    }

    public function update($id, array $data)
    {
        return News::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return News::where('id', $id)->delete();
    }
    public function getAll()
    {
        return News::all();
    }
}
