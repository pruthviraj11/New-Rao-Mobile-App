<?php
namespace App\Repositories;

use App\Models\ApplicationStatuses;

class ApplicationStatusesRepository
{
    public function find($id)
    {
        return ApplicationStatuses::find($id);
    }

    public function create(array $data)
    {
        return ApplicationStatuses::create($data);
    }

    public function update($id, array $data)
    {
        return ApplicationStatuses::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return ApplicationStatuses::where('id', $id)->delete();
    }
    public function getAll()
    {
        return ApplicationStatuses::all();
    }
}
