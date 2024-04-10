<?php

namespace App\Http\Requests;

use App\Models\DoctorSchedule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreateDoctorScheduleRequest extends FormRequest
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
            // 'work_start.*' => 'required',
            // 'work_end.*' => 'required',
            'office_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            // 'work_start.*.required' => "El campo inicio de jornada es requerido",
            // 'work_end.*.required' => "El campo fin de jornada es requerido",
            'office_id.required' => "El campo Clinica es requerido"
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator)
        {   
            $exists_work_start = false;
            foreach(config('constants.dias-semana') as $key => $value) 
            {
                Log::info( isset(request()->work_end[$key]));
                Log::info(isset(request()->work_start[$key]) );
                if(isset(request()->work_start[$key]) && isset(request()->work_end[$key])) 
                {
                    $doctor_schedule = DoctorSchedule::where('status', true)
                        ->where('days', $key)
                        ->where('office_id', request()->office_id)
                        ->where('doctor_id', request()->doctor_id)
                        ->first();

                    if($doctor_schedule) 
                    {
                        $validator->errors()->add('doctor_id', 'Este Doctor ya tiene un horario asignado para este dia');
                    }   

                    if(request()->work_start[$key] == request()->work_end[$key]) 
                    {
                        $validator->errors()->add('work_end.*', 'El campo final de jornada no puede ser la misma que inicio de jornada');
                    }

                    if(request()->work_start[$key] > request()->work_end[$key]) 
                    {
                        $validator->errors()->add('work_end.*', 'El campo Inicio de jornada no puede ser menor al fin de jornada');
                    }

                    if(isset(request()->break_start[$key]) && isset(request()->break_end[$key]) && request()->break_start[$key] > request()->break_end[$key]) 
                    {
                        $validator->errors()->add('break_start.*', 'El campo Inicio de receso no puede ser menor al fin de receso');
                    }

                    $exists_work_start = true;

                    if(!isset(request()->{'specialities_ids'.$key})) 
                    {
                        $validator->errors()->add('specialities_ids.*', 'La especialidad es obligatoria.');
                    }

                    if(request()->{'specialities_ids'.$key}) 
                    {
                        $last_hour = request()->work_start[$key];
                        foreach(request()->{'specialities_ids'.$key} as $key2 => $speciality_id)
                        {
                            if(request()->{'start_with_specialities'.$key}[$speciality_id] < request()->work_start[$key]) 
                            {
                                $validator->errors()->add('start_with_specialities', 'El horario inicial de especialidad esta mal configurado.');
                            }

                            if(request()->{'end_with_specialities'.$key}[$speciality_id] > request()->work_end[$key]) 
                            {
                                $validator->errors()->add('end_with_specialities', 'El horario final de especialidad esta mal configurado.');
                            }

                            if(request()->{'start_with_specialities'.$key}[$speciality_id] < $last_hour) 
                            {
                                $validator->errors()->add('end_with_specialities', 'El horario inicial de especialidad no puede ser menor al inicio.');
                            }

                            if(request()->{'end_with_specialities'.$key}[$speciality_id] > request()->work_end[$key]) 
                            {
                                $validator->errors()->add('end_with_specialities', 'El horario final de especialidad no puede ser mayor al final.');
                            }

                            $last_hour = request()->{'end_with_specialities'.$key}[$speciality_id];
                        }

                        if(isset($last_hour) && $last_hour != request()->work_end[$key]) 
                        {
                            $validator->errors()->add('end_with_specialities.*', 'El horario final de especialidad no es igual al fin de jornada.');
                        }
                    }
                }
            }
            
            if(!$exists_work_start) 
            {
                $validator->errors()->add('work_start.*', 'Debe Agregar un horario de Trabajo');
            }
        });
    }
}
