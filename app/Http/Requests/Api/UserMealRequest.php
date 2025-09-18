<?php

namespace App\Http\Requests\Api;

use App\Repositories\FoodTrackerRepository;

class UserMealRequest extends ApiRequest
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
         $userId = getUser()->id;
         $mealContentStatus = FoodTrackerRepository::findMealContentStatus(['user_id' => $userId,'status' => 'active']); 
         $rules = [];

         if ($mealContentStatus) {
             if ($mealContentStatus->calories_status == 'enabled') {
                 $rules['calories'] = 'nullable|integer|min:1';
             }
     
             if ($mealContentStatus->proteins_status == 'enabled') {
                 $rules['proteins'] = 'nullable|integer|min:1';
             }
     
             if ($mealContentStatus->carbohydrates_status == 'enabled') {
                 $rules['carbohydrates'] = 'nullable|integer|min:1';
             }
         }
         $rules['meal_id'] = 'required';
     
         return $rules;
     }

     public function messages()
    {
        return [
            'meal_id.required' => 'Please select a meal.',
        ];
    }

}
