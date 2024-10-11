<?php
namespace App\Services;
use App\Repositories\UserDocumentsRepository;

class UserDocumentsService
{
    protected UserDocumentsRepository $userDocumentsRepository;

    public function __construct(UserDocumentsRepository $userDocumentsRepository)
    {
        $this->userDocumentsRepository = $userDocumentsRepository;
    }
    public function getAllUserDocuments()
    {
        $userDocuments = $this->userDocumentsRepository->getAll();
        return $userDocuments;
    }
    public function getUserDocuments($id)
    {
        $userDocuments = $this->userDocumentsRepository->find($id);
        return $userDocuments;
    }

}
