<?php
namespace App\Services;
use App\Repositories\InternalProgramStatusRepository;

class InternalProgramStatusService
{
    protected InternalProgramStatusRepository $internalProgramStatusRepository;

    public function __construct(InternalProgramStatusRepository $internalProgramStatusRepository)
    {
        $this->internalProgramStatusRepository = $internalProgramStatusRepository;
    }
    public function create($internalProgramStatusData)
    {
        $internalProgramStatus = $this->internalProgramStatusRepository->create($internalProgramStatusData);
        return $internalProgramStatus;
    }
    public function getAllinternalProgramStatus()
    {
        $internalProgramStatus = $this->internalProgramStatusRepository->getAll();
        return $internalProgramStatus;
    }
    public function getinternalProgramStatus($id)
    {
        $internalProgramStatus = $this->internalProgramStatusRepository->find($id);
        return $internalProgramStatus;
    }
    public function deleteinternalProgramStatus($id)
    {
        $deleted = $this->internalProgramStatusRepository->delete($id);
        return $deleted;
    }
    public function updateinternalProgramStatus($id, $internalProgramStatusData)
    {
        $updated = $this->internalProgramStatusRepository->update($id, $internalProgramStatusData);
        return $updated;
    }

}
