<?php

namespace App\View\Components\Games;

use Illuminate\View\Component;
use App\Models\GameMaster;


class ReactionWall extends Component
{
    public $rewardDetail;    
    public $module;
    
    public $game;

    public $moduleId;

    public $videoId;

    public $athleteId;
    public $modalId;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($rewardDetail=null, $module=null, $moduleId=null, $videoId=null, $athleteId=null , $modalId=null)
    {
        //
        $this->game = GameMaster::where('game_key', 'reaction-wall')->where('status','active')->first();
        $this->rewardDetail = $rewardDetail;
        $this->module = $module;
        $this->moduleId = $moduleId;
        $this->videoId = $videoId;
        $this->athleteId = $athleteId;
        $this->modalId = $modalId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.games.reaction-wall');
    }
}
