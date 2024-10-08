<?php
namespace App\Services;
use App\Repositories\ClientTypeRepository;

class ClientTypeService
{
    protected ClientTypeRepository $clientTypeRepository;

    public function __construct(ClientTypeRepository $clientTypeRepository)
    {
        $this->clientTypeRepository = $clientTypeRepository;
    }
    public function create($client_typeData)
    {
        $client_type = $this->clientTypeRepository->create($client_typeData);
        return $client_type;
    }
    public function getAllClientType()
    {
        $client_types = $this->clientTypeRepository->getAll();
        return $client_types;
    }
    public function getClientType($id)
    {
        $client_type = $this->clientTypeRepository->find($id);
        return $client_type;
    }
    public function deleteClientType($id)
    {
        $deleted = $this->clientTypeRepository->delete($id);
        return $deleted;
    }
    public function updateClientType($id, $client_typeData)
    {
        $updated = $this->clientTypeRepository->update($id, $client_typeData);
        return $updated;
    }

}
