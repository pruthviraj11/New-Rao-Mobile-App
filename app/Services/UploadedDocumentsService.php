<?php
namespace App\Services;
use App\Repositories\UploadedDocumentsRepository;

class UploadedDocumentsService
{
    protected UploadedDocumentsRepository $uploadedDocumentsRepository;

    public function __construct(UploadedDocumentsRepository $uploadedDocumentsRepository)
    {
        $this->uploadedDocumentsRepository = $uploadedDocumentsRepository;
    }
    public function getAllUploadedDocuments()
    {
        $uploadedDocuments = $this->uploadedDocumentsRepository->getAll();
        return $uploadedDocuments;
    }
    public function getUploadedDocuments($id)
    {
        $uploadedDocuments = $this->uploadedDocumentsRepository->find($id);
        return $uploadedDocuments;
    }

}
