<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


class RewardManagementRequest extends FormRequest
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
        $rules = [
            'feature_key' => 'required',
        ];
        if ($this->input('is_gamification') === 'on') {
            $rules['game_type'] = 'required|in:specific,random';
            $rules['min_points'] = 'required|numeric';
            $rules['max_points'] = 'required|numeric';
            $rules['score'] = 'required';
            $rules['duration'] = 'required';
            // game_key is only required if game_type is 'specific'
            if ($this->input('game_type') === 'specific') {
                $rules['game_key'] = 'required';
            }
        }else{
            $rules['point'] = 'required';
        }
        return $rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('is_gamification') === 'on') {
                $min = $this->input('min_points');
                $max = $this->input('max_points');

                if (is_numeric($min) && is_numeric($max) && $max <= $min) {
                    $validator->errors()->add('max_points', 'Maximum points must be greater than minimum points.');
                }
            }
        });
    }


    public function messages()
    {
        return [
            'feature_key.required' => 'The feature field is required.',
            'point.required' => 'The point field is required.',
            'game_type.required' => 'The game type is required when gamification is enabled.',
            'min_points.required' => 'Minimum points are required when gamification is enabled.',
            'max_points.required' => 'Maximum points are required when gamification is enabled.',
            'score.required' => 'Maximum score is required when gamification is enabled.',
            'duration.required' => 'Duration is required when gamification is enabled.',
            'game_key.required' => 'The game field is required when game type is specific.',
        ];
    }
}
