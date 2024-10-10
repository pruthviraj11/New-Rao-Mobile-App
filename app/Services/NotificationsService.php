<?php
namespace App\Services;
use App\Repositories\NotificationsRepository;

class NotificationsService
{

    protected NotificationsRepository $notificationsRepository;

    public function __construct(NotificationsRepository $notificationsRepository)
    {
        $this->notificationsRepository = $notificationsRepository;
    }
    public function create($notificationsData)
    {
        $notifications = $this->notificationsRepository->create($notificationsData);
        return $notifications;
    }
    public function getAllNotifications()
    {
        $notifications = $this->notificationsRepository->getAll();
        return $notifications;
    }
    public function getNotifications($id)
    {
        $notifications = $this->notificationsRepository->find($id);
        return $notifications;
    }
    public function deleteNotifications($id)
    {
        $deleted = $this->notificationsRepository->delete($id);
        return $deleted;
    }
    public function updateNotifications($id, $notificationsData)
    {
        $updated = $this->notificationsRepository->update($id, $notificationsData);
        return $updated;
    }


}
