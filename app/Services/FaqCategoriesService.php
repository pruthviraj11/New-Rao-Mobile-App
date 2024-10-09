<?php
namespace App\Services;
use App\Repositories\FaqCategoriesRepository;

class FaqCategoriesService
{
    protected FaqCategoriesRepository $faqCategoriesRepository;

    public function __construct(FaqCategoriesRepository $faqCategoriesRepository)
    {
        $this->faqCategoriesRepository = $faqCategoriesRepository;
    }
    public function create($faqCategorieData)
    {
        $faqCategorie = $this->faqCategoriesRepository->create($faqCategorieData);
        return $faqCategorie;
    }
    public function getAllFaqCategorie()
    {
        $faqCategories = $this->faqCategoriesRepository->getAll();
        return $faqCategories;
    }
    public function getFaqCategorie($id)
    {
        $faqCategorie = $this->faqCategoriesRepository->find($id);
        return $faqCategorie;
    }
    public function deleteFaqCategorie($id)
    {
        $deleted = $this->faqCategoriesRepository->delete($id);
        return $deleted;
    }
    public function updateFaqCategorie($id, $faqCategorieData)
    {
        $updated = $this->faqCategoriesRepository->update($id, $faqCategorieData);
        return $updated;
    }

}
