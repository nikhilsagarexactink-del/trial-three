<?php

namespace App\View\Components;

use Illuminate\View\Component;

use App\Models\HealthMeasurement;
use App\Models\HealthMarker;
use App\Models\UserVideoProgress;
use App\Models\TrainingVideoRating;
use Illuminate\Support\Facades\DB;

class GameModal extends Component
{
    public $rewardDetail;
    public $module;
    public $moduleId;
    public $videoId;

    public $athleteId;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($rewardDetail = null, $module = '', $moduleId = '', $videoId = '', $athleteId = '')
    {
        DB::reconnect(); // Add this
        $this->rewardDetail = $rewardDetail;
        $this->module = $module;
        $this->moduleId = $moduleId;
        $this->videoId = $videoId;     
        $this->athleteId = $athleteId;     
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.game-modal');
    }
}
