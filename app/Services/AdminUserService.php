<?php
namespace App\Services;
use App\Repositories\AdminUserRepository;

class AdminUserService
{

    protected AdminUserRepository $adminUserRepository;

    public function __construct(AdminUserRepository $adminUserRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
    }
    public function create($adminUserData)
    {
        $adminUser = $this->adminUserRepository->create($adminUserData);
        return $adminUser;
    }
    public function getAllAdminUser()
    {
        $adminUser = $this->adminUserRepository->getAll();
        return $adminUser;
    }
    public function getAdminUser($id)
    {
        $adminUser = $this->adminUserRepository->find($id);
        return $adminUser;
    }
    public function deleteAdminUser($id)
    {
        $deleted = $this->adminUserRepository->delete($id);
        return $deleted;
    }
    public function updateAdminUser($id, $adminUserData)
    {
        $updated = $this->adminUserRepository->update($id,$adminUserData);
        return $updated;
    }


}
