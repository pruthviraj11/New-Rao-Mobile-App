<?php
namespace App\Services;
use App\Repositories\OurServicesRepository;

class OurServicesService
{
    protected OurServicesRepository $ourServicesRepository;

    public function __construct(OurServicesRepository $ourServicesRepository)
    {
        $this->ourServicesRepository = $ourServicesRepository;
    }
    public function create($ourServicesData)
    {
        $ourServices = $this->ourServicesRepository->create($ourServicesData);
        return $ourServices;
    }
    public function getAllOurServices()
    {
        $ourServices = $this->ourServicesRepository->getAll();
        return $ourServices;
    }
    public function getOurServices($id)
    {
        $ourServices = $this->ourServicesRepository->find($id);
        return $ourServices;
    }
    public function deleteOurServices($id)
    {
        $deleted = $this->ourServicesRepository->delete($id);
        return $deleted;
    }
    public function updateOurServices($id, $ourServicesData)
    {
        $updated = $this->ourServicesRepository->update($id, $ourServicesData);
        return $updated;
    }

}
