<?php
namespace App\Services;
use App\Repositories\ApplicationStatusesRepository;

class ApplicationStatusesService
{
    protected ApplicationStatusesRepository $applicationStatusesRepository;

    public function __construct(ApplicationStatusesRepository $applicationStatusesRepository)
    {
        $this->applicationStatusesRepository = $applicationStatusesRepository;
    }
    public function create($applicationStatusesData)
    {
        $applicationStatuses = $this->applicationStatusesRepository->create($applicationStatusesData);
        return $applicationStatuses;
    }
    public function getAllApplicationStatuses()
    {
        $applicationStatuses = $this->applicationStatusesRepository->getAll();
        return $applicationStatuses;
    }
    public function getApplicationStatuses($id)
    {
        $applicationStatuses = $this->applicationStatusesRepository->find($id);
        return $applicationStatuses;
    }
    public function deleteApplicationStatuses($id)
    {
        $deleted = $this->applicationStatusesRepository->delete($id);
        return $deleted;
    }
    public function updateApplicationStatuses($id, $applicationStatusesData)
    {
        $updated = $this->applicationStatusesRepository->update($id, $applicationStatusesData);
        return $updated;
    }

}
