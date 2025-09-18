<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseballGameRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => 'required',
            // 'game_name' => 'required',
            'p_pitches' => 'required|numeric|min:0',
            'p_strikes' => 'required|numeric|min:0',
            'p_balls' => 'required|numeric|min:0',
            'p_innings' => 'required|numeric|min:0',
            'p_hits' => 'required|numeric|min:0',
            'p_runs' => 'required|numeric|min:0',
            'p_walks' => 'required|numeric|min:0',
            'p_hbp' => 'required|numeric|min:0',
            'h_rbi' => 'required|numeric|min:0',
            'h_plate_attempts' => 'required|numeric|min:0',
            'h_hits' => 'required|numeric|min:0',
            'h_walks' => 'required|numeric|min:0',
            'f_number_of_attempts' => 'required|numeric|min:0',
            'f_errors' => 'required|numeric|min:0',
            'f_outs_made' => 'required|numeric|min:0',
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
            'p_innings.required' => 'The Innings field is required.',
            'p_hits.required' => 'The Hits field is required.',
            'p_runs.required' => 'The Runs field is required.',
            'p_walks.required' => 'The Walks field is required.',
            'p_hbp.required' => 'The HBP field is required.',

            'h_plate_attempts.required' => 'The Plate Attempts field is required.',
            'h_hits.required' => 'The Hits field is required.',
            'h_walks.required' => 'The Walks field is required.',
            'h_rbi.required' => 'The RBI field is required.',

            'f_number_of_attempts.required' => 'The No. of Attempts field is required.',
            'f_errors.required' => 'The Errors field is required.',
            'f_outs_made.required' => 'The Outs Made field is required.',

            'p_pitches.min' => 'The Pitches field must be at least 0.',
            'p_strikes.min' => 'The Strikes field must be at least 0.',
            'p_balls.min' => 'The Balls field must be at least 0.',
            'p_innings.min' => 'The Innings field must be at least 0.',
            'p_hits.min' => 'The Hits field must be at least 0.',
            'p_runs.min' => 'The Runs field must be at least 0.',
            'p_walks.min' => 'The Walks field must be at least 0.',
            'p_hbp.min' => 'The HBP field must be at least 0.',

            'h_plate_attempts.min' => 'The Plate Attempts field must be at least 0.',
            'h_hits.min' => 'The Hits field must be at least 0.',
            'h_walks.min' => 'The Walks field must be at least 0.',
            'h_rbi.min' => 'The RBI field must be at least 0.',

            'f_number_of_attempts.min' => 'The No. of Attempts field must be at least 0.',
            'f_errors.min' => 'The Errors must be at least 0.',
            'f_outs_made.min' => 'The Outs Made must be at least 0.',

        ];
    }
}
