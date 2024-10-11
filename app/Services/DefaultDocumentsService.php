<?php
namespace App\Services;
use App\Repositories\DefaultDocumentsRepository;

class DefaultDocumentsService
{
    protected DefaultDocumentsRepository $defaultDocumentsRepository;

    public function __construct(DefaultDocumentsRepository $defaultDocumentsRepository)
    {
        $this->defaultDocumentsRepository = $defaultDocumentsRepository;
    }
    public function getAllDefaultDocuments()
    {
        $defaultDocuments = $this->defaultDocumentsRepository->getAll();
        return $defaultDocuments;
    }
    public function getDefaultDocuments($id)
    {
        $defaultDocuments = $this->defaultDocumentsRepository->find($id);
        return $defaultDocuments;
    }

}
