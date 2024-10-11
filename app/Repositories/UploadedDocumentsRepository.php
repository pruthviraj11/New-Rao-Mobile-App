<?php
namespace App\Repositories;

use App\Models\UploadedDocuments;

class UploadedDocumentsRepository
{
    public function find($id)
    {
        return UploadedDocuments::find($id);
    }

    // public function create(array $data)
    // {
    //     return UploadedDocuments::create($data);
    // }

    // public function update($id, array $data)
    // {
    //     return UploadedDocuments::where('id', $id)->update($data);
    // }

    // public function delete($id)
    // {
    //     return UploadedDocuments::where('id', $id)->delete();
    // }
    public function getAll()
    {
        return UploadedDocuments::get();
    }
}
