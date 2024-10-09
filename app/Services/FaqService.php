<?php
namespace App\Services;
use App\Repositories\FaqRepository;

class FaqService
{
    protected FaqRepository $faqsRepository;

    public function __construct(FaqRepository $faqsRepository)
    {
        $this->faqsRepository = $faqsRepository;
    }
    public function create($faqData)
    {
        $faq = $this->faqsRepository->create($faqData);
        return $faq;
    }
    public function getAllFaq()
    {
        $faqs = $this->faqsRepository->getAll();
        return $faqs;
    }
    public function getFaq($id)
    {
        $faq = $this->faqsRepository->find($id);
        return $faq;
    }
    public function deleteFaq($id)
    {
        $deleted = $this->faqsRepository->delete($id);
        return $deleted;
    }
    public function updateFaq($id, $faqData)
    {
        $updated = $this->faqsRepository->update($id, $faqData);
        return $updated;
    }

}
