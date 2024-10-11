<?php
namespace App\Repositories;

use App\Models\FCMTokens;

class FCMTokensRepository
{
    public function find($id)
    {
        return FCMTokens::find($id);
    }

    // public function create(array $data)
    // {
    //     return FCMTokens::create($data);
    // }

    // public function update($id, array $data)
    // {
    //     return FCMTokens::where('id', $id)->update($data);
    // }

    // public function delete($id)
    // {
    //     return FCMTokens::where('id', $id)->delete();
    // }
    public function getAll()
    {
        return FCMTokens::all();
    }
}
