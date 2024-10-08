<?php
namespace App\Repositories;

use App\Models\NewsCategory;

class NewsRepository
{
    public function find($id)
    {
        return NewsCategory::find($id);
    }

    public function create(array $data)
    {
        return NewsCategory::create($data);
    }

    public function update($id, array $data)
    {
        return NewsCategory::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return NewsCategory::where('id', $id)->delete();
    }
    public function getAll()
    {
        return NewsCategory::all();
    }
}
