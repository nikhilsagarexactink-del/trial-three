<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Repositories\FitnessChallengeRepository;

class ChallengeAlert extends Component
{
    public $challenges;
    public $type;

    public function __construct(FitnessChallengeRepository $challengeRepository, $type = null)
    {
        // Example: always show the latest active challenge
        $this->type = $type;
        $this->challenges = $challengeRepository->loadChallengeWidget($this->type);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.challenge-alert');
    }
}
