<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
        if($this->method() == 'POST'){
            
            if(str_contains($this->url(), 'admin/user/add')){
                $password_rule = 'required|min:5|confirmed';   
                $email_rule =  'required|email|unique:users';
            } else{  //edit case
                $password_rule = 'nullable|min:5|confirmed';    
                $email_rule =  'required|email|unique:users,email,'.$this->route('user_id');
            } 

            return [
                'first_name' => 'required|max:100',
                'last_name' => 'required|max:100',
                'last_name_access' => 'required',
                'email' => $email_rule,
                'email_access' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'city_id_access' => 'required',
                'occupation' => 'required',
                'occupation_access' => 'required',
                'work_website' => 'required|max:255',
                'work_website_access' => 'required',
                'education' => 'required|max:255',
                'education_access' => 'required',
                'high_school' => 'required|max:255',
                'high_school_access' => 'required',
                'college' => 'required|max:255',
                'college_access' => 'required',
                'alma_matter' => 'required|max:255',
                'alma_matter_access' => 'required',
                'likes' => 'required|max:255',
                'likes_access' => 'required',
                'military_id' => 'required',
                'military_id_access' => 'required',
                'political_id' => 'required',
                'political_id_access' => 'required',
                'religion_id' => 'required',
                'religion_id_access' => 'required',
                'relationship_id' => 'required',
                'relationship_id_access' => 'required',
                'capture_time_period' => 'nullable',
                // 'capture_distance' => 'required',
                'status' => 'required',
                'password' => $password_rule,
                //here we take 3 img only because there can be max 3 images only
                'image.0' => 'nullable|mimes:jpg,jpeg,png', 
                'image.1' => 'nullable|mimes:jpg,jpeg,png',
                'image.2' => 'nullable|mimes:jpg,jpeg,png'
            ];
        } else{
            return [];
        }
    }

    public function messages(){
        return [
            'state_id.required' => 'State is required',
            'city_id.required' => 'City is required',
            'military_id.required' => 'Military is required',
            'political_id.required' => 'Political is required',
            'religion_id.required' => 'Religion is required',
            'relationship_id.required' => 'Relationship is required',
            'image.0.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
            'image.1.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
            'image.2.mimes' => 'The image must be a file of type: jpg, jpeg, png.'
        ];
    }
}
