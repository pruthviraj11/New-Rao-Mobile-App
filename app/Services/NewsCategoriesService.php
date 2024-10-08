<?php
namespace App\Services;
use App\Repositories\NewsCategorieRepository;

class NewsCategoriesService
{
    protected NewsCategorieRepository $newsCategorieRepository;

    public function __construct(NewsCategorieRepository $newsCategorieRepository)
    {
        $this->newsCategorieRepository = $newsCategorieRepository;
    }
    public function create($newsCategorieData)
    {
        $newsCategorie = $this->newsCategorieRepository->create($newsCategorieData);
        return $newsCategorie;
    }
    public function getAllNewsCategorie()
    {
        $newsCategories = $this->newsCategorieRepository->getAll();
        return $newsCategories;
    }
    public function getNewsCategorie($id)
    {
        $newsCategorie = $this->newsCategorieRepository->find($id);
        return $newsCategorie;
    }
    public function deleteNewsCategorie($id)
    {
        $deleted = $this->newsCategorieRepository->delete($id);
        return $deleted;
    }
    public function updateNewsCategorie($id, $newsCategorieData)
    {
        $updated = $this->newsCategorieRepository->update($id, $newsCategorieData);
        return $updated;
    }

}
