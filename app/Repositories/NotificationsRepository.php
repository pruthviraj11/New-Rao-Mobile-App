<?php
namespace App\Repositories;

use App\Models\Notifications;

class NotificationsRepository
{

    public function find($id)
    {
        return Notifications::find($id);
    }

    public function create(array $data)
    {
        return Notifications::create($data);
    }

    public function update($id, array $data)
    {
        return Notifications::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Notifications::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Notifications::select('notifications.*');
    }

}
