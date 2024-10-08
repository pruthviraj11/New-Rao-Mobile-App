<?php
namespace App\Services;
use App\Repositories\SliderRepository;

class SliderService
{
    protected SliderRepository $sliderRepository;

    public function __construct(SliderRepository $sliderRepository)
    {
        $this->sliderRepository = $sliderRepository;
    }
    public function create($sliderData)
    {
        $slider = $this->sliderRepository->create($sliderData);
        return $slider;
    }
    public function getAllSlider()
    {
        $slideres = $this->sliderRepository->getAll();
        return $slideres;
    }
    public function getSlider($id)
    {
        $slider = $this->sliderRepository->find($id);
        return $slider;
    }
    public function deleteSlider($id)
    {
        $deleted = $this->sliderRepository->delete($id);
        return $deleted;
    }
    public function updateSlider($id, $sliderData)
    {
        $updated = $this->sliderRepository->update($id, $sliderData);
        return $updated;
    }

}
