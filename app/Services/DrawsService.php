<?php
namespace App\Services;
use App\Repositories\DrawsRepository;

class DrawsService
{

    protected DrawsRepository $drawsRepository;

    public function __construct(DrawsRepository $drawsRepository)
    {
        $this->drawsRepository = $drawsRepository;
    }
    public function create($drawsData)
    {
        $draws = $this->drawsRepository->create($drawsData);
        return $draws;
    }
    public function getAllDraws()
    {
        $draws = $this->drawsRepository->getAll();
        return $draws;
    }
    public function getDraws($id)
    {
        $draws = $this->drawsRepository->find($id);
        return $draws;
    }
    public function deleteDraws($id)
    {
        $deleted = $this->drawsRepository->delete($id);
        return $deleted;
    }
    public function updateDraws($id, $drawsData)
    {
        $updated = $this->drawsRepository->update($id, $drawsData);
        return $updated;
    }


}
