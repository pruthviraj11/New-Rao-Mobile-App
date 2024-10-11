<?php
namespace App\Services;
use App\Repositories\AdvisorRepository;

class AdvisorService
{

    protected AdvisorRepository $advisorRepository;

    public function __construct(AdvisorRepository $advisorRepository)
    {
        $this->advisorRepository = $advisorRepository;
    }
    public function create($advisorData)
    {
        $advisors = $this->advisorRepository->create($advisorData);
        return $advisors;
    }
    public function getAllAdvisor()
    {
        $advisors = $this->advisorRepository->getAll();
        return $advisors;
    }
    public function getAdvisor($id)
    {
        $advisors = $this->advisorRepository->find($id);
        return $advisors;
    }
    public function deleteAdvisor($id)
    {
        $deleted = $this->advisorRepository->delete($id);
        return $deleted;
    }
    public function updateAdvisor($id, $advisorData)
    {
        $updated = $this->advisorRepository->update($id,$advisorData);
        return $updated;
    }


}
