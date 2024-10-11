<?php
namespace App\Repositories;

use App\Models\UserDocuments;

class UserDocumentsRepository
{
    public function find($id)
    {
        return UserDocuments::find($id);
    }

    // public function create(array $data)
    // {
    //     return UserDocuments::create($data);
    // }

    // public function update($id, array $data)
    // {
    //     return UserDocuments::where('id', $id)->update($data);
    // }

    // public function delete($id)
    // {
    //     return UserDocuments::where('id', $id)->delete();
    // }
    public function getAll()
    {
        return UserDocuments::get();
    }
}
