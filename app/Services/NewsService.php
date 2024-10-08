<?php
namespace App\Services;
use App\Repositories\NewsRepository;

class NewsService
{
    protected NewsRepository $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }
    public function create($newsData)
    {
        $news = $this->newsRepository->create($newsData);
        return $news;
    }
    public function getAllNews()
    {
        $newss = $this->newsRepository->getAll();
        return $newss;
    }
    public function getNews($id)
    {
        $news = $this->newsRepository->find($id);
        return $news;
    }
    public function deleteNews($id)
    {
        $deleted = $this->newsRepository->delete($id);
        return $deleted;
    }
    public function updateNews($id, $newsData)
    {
        $updated = $this->newsRepository->update($id, $newsData);
        return $updated;
    }

}
