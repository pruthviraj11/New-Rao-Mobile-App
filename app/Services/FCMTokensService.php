<?php
namespace App\Services;
use App\Repositories\FCMTokensRepository;

class FCMTokensService
{
    protected FCMTokensRepository $fcmTokensRepository;

    public function __construct(FCMTokensRepository $fcmTokensRepository)
    {
        $this->fcmTokensRepository = $fcmTokensRepository;
    }
    // public function create($applicationStatusesData)
    // {
    //     $applicationStatuses = $this->fcmTokensRepository->create($applicationStatusesData);
    //     return $applicationStatuses;
    // }
    public function getAllFCMTokens()
    {
        $fcmTokens = $this->fcmTokensRepository->getAll();
        return $fcmTokens;
    }
    public function getFCMTokens($id)
    {
        $fcmTokens = $this->fcmTokensRepository->find($id);
        return $fcmTokens;
    }
    // public function deleteApplicationStatuses($id)
    // {
    //     $deleted = $this->fcmTokensRepository->delete($id);
    //     return $deleted;
    // }
    // public function updateApplicationStatuses($id, $applicationStatusesData)
    // {
    //     $updated = $this->fcmTokensRepository->update($id, $applicationStatusesData);
    //     return $updated;
    // }

}
