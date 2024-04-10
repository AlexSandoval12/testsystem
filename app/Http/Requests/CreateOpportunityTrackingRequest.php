<?php

namespace App\Http\Requests;

use App\Models\SalesOpportunity;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateOpportunityTrackingRequest extends FormRequest
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
        $rules = [
        ];
            
        return $rules;
    }

    public function messages()
    {
        return [
            'attended.required'            => 'El campo contacto es obligatorio.',
            'contact_form.required_if'     => 'El campo medio es obligatorio.',
            'not_attended.required_if'     => 'El campo motivo es obligatorio.',
            'observation.required_if'      => 'El campo observación es obligatorio.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {


            if((request()->radio==3 || request()->reject) && !request()->rejected_motive_id)
            {
                $validator->errors()->add('document_number', 'El campo motivo de rechazo es obligatorio.');
            }

            if(request()->call_again)
            {
                if(check_date(request()->call_again)) 
                {
                    $call_again = Carbon::createFromFormat('d/m/Y', request()->call_again)->format('Y-m-d');
                    if ($this->route('opportunity')->enterprise_id == 3 || $this->route('opportunity')->enterprise_id == 6)
                    {
                        $until_date = Carbon::now()->addDays(3)->format('Y-m-d');
                        $day = 3;
                    }
                    else
                    {
                        $until_date = Carbon::now()->addDays(7)->format('Y-m-d');
                        $day = 7;
                    }
                    if ($call_again > $until_date)
                    {
                        $validator->errors()->add('call_again', 'La fecha de volver a llamar no debe ser mayor a '.$day.' dias.');
                    }
                }
            }

        });
    }
}
