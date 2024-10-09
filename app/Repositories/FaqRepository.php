<?php
namespace App\Repositories;

use App\Models\Faq;

class FaqRepository
{
    public function find($id)
    {
        return Faq::find($id);
    }

    public function create(array $data)
    {
        return Faq::create($data);
    }

    public function update($id, array $data)
    {
        return Faq::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Faq::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Faq::all();
    }
}
