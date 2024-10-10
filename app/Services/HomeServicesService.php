<?php
namespace App\Services;
use App\Repositories\HomeServiceRepository;

class HomeServicesService
{
    protected HomeServiceRepository $homeServiceRepository;

    public function __construct(HomeServiceRepository $homeServiceRepository)
    {
        $this->homeServiceRepository = $homeServiceRepository;
    }
    public function create($HomeServiceData)
    {
        $HomeService = $this->homeServiceRepository->create($HomeServiceData);
        return $HomeService;
    }
    public function getAllHomeService()
    {
        $HomeServices = $this->homeServiceRepository->getAll();
        return $HomeServices;
    }
    public function getHomeService($id)
    {
        $HomeService = $this->homeServiceRepository->find($id);
        return $HomeService;
    }
    public function deleteHomeService($id)
    {
        $deleted = $this->homeServiceRepository->delete($id);
        return $deleted;
    }
    public function updateHomeService($id, $HomeServiceData)
    {
        $updated = $this->homeServiceRepository->update($id, $HomeServiceData);
        return $updated;
    }

}
