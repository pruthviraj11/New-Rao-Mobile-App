<?php
namespace App\Repositories;

use App\Models\DefaultDocuments;

class DefaultDocumentsRepository
{
    public function find($id)
    {
        return DefaultDocuments::find($id);
    }

    // public function create(array $data)
    // {
    //     return DefaultDocuments::create($data);
    // }

    // public function update($id, array $data)
    // {
    //     return DefaultDocuments::where('id', $id)->update($data);
    // }

    // public function delete($id)
    // {
    //     return DefaultDocuments::where('id', $id)->delete();
    // }
    public function getAll()
    {
        return DefaultDocuments::all();
    }
}
