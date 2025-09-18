<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseballPracticeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required',
            // 'game_name' => 'required',
            'p_pitches' => 'required|numeric|min:0',
            'p_strikes' => 'required|numeric|min:0',
            'p_balls' => 'required|numeric|min:0',
            'p_pitching_session' => 'required',
            'p_fastball_speed' => 'required|numeric|min:0',
            'p_changeup_speed' => 'required|numeric|min:0',
            'p_curveball_speed' => 'required|numeric|min:0',
            'p_pt_curveball' => 'nullable|numeric|min:0',
            'p_pt_fastball' => 'nullable|numeric|min:0',
            'p_pt_changeup' => 'nullable|numeric|min:0',
            'p_pt_other_pitch' => 'nullable|numeric|min:0',
            'h_number_of_swings' => 'required|numeric|min:0',
            'h_hitting_type' => 'required',
            'h_bat_speed' => 'required|numeric|min:0',
            'f_number_of_ground_balls' => 'required|numeric|min:0',
            'f_number_of_fly_balls' => 'required|numeric|min:0',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'p_pitches.required' => 'The Pitches field is required.',
            'p_strikes.required' => 'The Strikes field is required.',
            'p_balls.required' => 'The Balls field is required.',
            'p_pitching_session.required' => 'The Pitching Session field is required.',
            'p_changeup_speed.required' => 'The Changeup Speed field is required.',
            'p_curveball_speed.required' => 'The Curveball Speed field is required.',
            'p_fastball_speed.required' => 'The Fastball Speed field is required.',
            'h_number_of_swings.required' => 'The No. of Swings field is required.',
            'h_hitting_type.required' => 'The Hitting Type field is required.',
            'h_bat_speed.required' => 'The Bat Speed is required.',
            'f_number_of_ground_balls.required' => 'The No. of Ground Balls field is required.',
            'f_number_of_fly_balls.required' => 'The No. of Fly Balls field is required.',

            'p_pitches.min' => 'The Pitches field must be at least 0.',
            'p_strikes.min' => 'The Strikes field must be at least 0.',
            'p_balls.min' => 'The Balls field must be at least 0.',
            'p_pitching_session.min' => 'The Pitching Session field must be at least 0.',
            'p_changeup_speed.min' => 'The Changeup Speed field must be at least 0.',
            'p_curveball_speed.min' => 'The Curveball Speed field must be at least 0.',
            'p_fastball_speed.min' => 'The Fastball Speed field must be at least 0.',
            'h_number_of_swings.min' => 'The No. of Swings field must be at least 0.',
            'h_hitting_type.min' => 'The Hitting Type field must be at least 0.',
            'h_bat_speed.min' => 'The Bat Speed must be at least 0.',
            'f_number_of_ground_balls.min' => 'The No. of Ground Balls field must be at least 0.',
            'f_number_of_fly_balls.min' => 'The No. of Fly Balls field must be at least 0.',

            'p_pt_curveball.min' => 'The Curve Ball field must be at least 0.',
            'p_pt_fastball.min' => 'The Fast Ball must be at least 0.',
            'p_pt_changeup.min' => 'The Change up field must be at least 0.',
            'p_pt_other_pitch.min' => 'The other pitch type field must be at least 0.',

        ];
    }
}
