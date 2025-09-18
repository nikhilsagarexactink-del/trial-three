<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\FoodTrackerRepository;

class UserMealRequest extends FormRequest
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
     
         return $rules;
     }

}
