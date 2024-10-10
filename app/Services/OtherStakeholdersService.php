<?php
namespace App\Services;
use App\Repositories\OtherStakeholdersRepository;

class OtherStakeholdersService
{
    protected OtherStakeholdersRepository $otherStakeholdersRepository;

    public function __construct(OtherStakeholdersRepository $otherStakeholdersRepository)
    {
        $this->otherStakeholdersRepository = $otherStakeholdersRepository;
    }
    public function create($otherStakeholdersData)
    {
        $otherStakeholders = $this->otherStakeholdersRepository->create($otherStakeholdersData);
        return $otherStakeholders;
    }
    public function getAllOtherStakeholders()
    {
        $otherStakeholders = $this->otherStakeholdersRepository->getAll();
        return $otherStakeholders;
    }
    public function getOtherStakeholders($id)
    {
        $otherStakeholders = $this->otherStakeholdersRepository->find($id);
        return $otherStakeholders;
    }
    public function deleteOtherStakeholders($id)
    {
        $deleted = $this->otherStakeholdersRepository->delete($id);
        return $deleted;
    }
    public function updateOtherStakeholders($id, $otherStakeholdersData)
    {
        $updated = $this->otherStakeholdersRepository->update($id, $otherStakeholdersData);
        return $updated;
    }

}
