<?php
namespace App\Services;
use App\Repositories\SuccessStoriesRepository;

class SuccessStoriesService
{
    protected SuccessStoriesRepository $successStoriesRepository;

    public function __construct(SuccessStoriesRepository $successStoriesRepository)
    {
        $this->successStoriesRepository = $successStoriesRepository;
    }
    public function create($successStoriesData)
    {
        $successStories = $this->successStoriesRepository->create($successStoriesData);
        return $successStories;
    }
    public function getAllSuccessStory()
    {
        $successStories = $this->successStoriesRepository->getAll();
        return $successStories;
    }
    public function getSuccessStory($id)
    {
        $successStories = $this->successStoriesRepository->find($id);
        return $successStories;
    }
    public function deleteSuccessStory($id)
    {
        $deleted = $this->successStoriesRepository->delete($id);
        return $deleted;
    }
    public function updateSuccessStory($id, $successStoriesData)
    {
        $updated = $this->successStoriesRepository->update($id, $successStoriesData);
        return $updated;
    }

}
