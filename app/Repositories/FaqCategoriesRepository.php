<?php
namespace App\Repositories;

use App\Models\FaqCategory;

class FaqCategoriesRepository
{
    public function find($id)
    {
        return FaqCategory::find($id);
    }

    public function create(array $data)
    {
        return FaqCategory::create($data);
    }

    public function update($id, array $data)
    {
        return FaqCategory::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return FaqCategory::where('id', $id)->delete();
    }
    public function getAll()
    {
        return FaqCategory::all();
    }
}
