<?php
namespace App\Services;
use App\Repositories\EventRepository;

class EventService
{

    protected EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }
    public function create($eventData)
    {
        $events = $this->eventRepository->create($eventData);
        return $events;
    }
    public function getAllEvents()
    {
        $events = $this->eventRepository->getAll();
        return $events;
    }
    public function getEvents($id)
    {
        $events = $this->eventRepository->find($id);
        return $events;
    }
    public function deleteEvents($id)
    {
        $deleted = $this->eventRepository->delete($id);
        return $deleted;
    }
    public function updateEvents($id, $eventsData)
    {
        $updated = $this->eventRepository->update($id, $eventsData);
        return $updated;
    }


}
