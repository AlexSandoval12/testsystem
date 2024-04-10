<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDoctorScheduleRequest;
use App\Http\Requests\UpdateDoctorSchedulesRequest;
use App\Models\DentalOffice;
use App\Models\Doctor;
use App\Models\DoctorEspeciality;
use App\Models\DoctorSchedule;
use App\Models\Enterprise;
use App\Models\Especiality;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DoctorScheduleController extends Controller
{
    public function index()
    {
    	$doctor_schedules = DoctorSchedule::with('doctor', 'office')
            ->where('status', true)
            ->whereHas('doctor', function($query)
            {
                $query->where('status', true);
            });

    	$doctors = Doctor::where('status', true)->get()->pluck('fullname', 'id');
        $dental_offices = Office::where('status', true)->pluck('name', 'id');
    	if(request()->doctor_id) 
    	{
    		$doctor_schedules = $doctor_schedules->where('doctor_id', request()->doctor_id);
    	}

        if(request()->day) 
        {
            $doctor_schedules = $doctor_schedules->where('days', request()->day);
        }
    	$doctor_schedules = $doctor_schedules->orderBy('doctor_id', 'DESC')->orderby('days', 'DESC')->orderBy('id', 'DESC')->paginate(20);

    	return view('pages.doctor-schedules.index', compact('doctor_schedules', 'doctors', 'dental_offices'));
    }

    public function create()
    {
    	$doctors = Doctor::with('offices')->where('status', true)->get()->pluck('fullname', 'id');
        $dental_offices = Office::where('status',true)->pluck('name', 'id');
        $specialities = $this->GetSpecialities();
        $doctor_specialities = DoctorEspeciality::whereHas('especiality', function($query){
                $query->where('status', true);
            })
            ->get()
            ->toArray();
    	return view('pages.doctor-schedules.create', compact('doctors', 'dental_offices', 'specialities', 'doctor_specialities'));
    }

    public function store(CreateDoctorScheduleRequest $request)
    {
    	foreach($request->days as $key => $value) 
    	{
            if(isset(request()->work_start[$key]) && isset(request()->work_end[$key])) 
            {
                $schedule = DoctorSchedule::create([
                    'doctor_id' => $request->doctor_id,
                    'office_id' => $request->office_id,
                    'break_start' => $request->break_start[$key] ? $request->break_start[$key] : NULL,
                    'break_end' => $request->break_end[$key] ? $request->break_end[$key] : NULL,
                    'break_interval'   => $request->break_start[$key] ? Carbon::createFromFormat('H:i',$request->break_start[$key])->diffInMinutes(Carbon::createFromFormat('H:i',$request->break_end[$key])) : NULL,
                    'work_start' => $request->work_start[$key],
                    'work_end' => $request->work_end[$key],
                    'days' => $key
                ]);

                foreach(request()->{'specialities_ids'.$key} as $key2 => $speciality_id)
                {
                    $schedule->details()->create([
                        'speciality_id' => $speciality_id,
                        'from_time'     => request()->{'start_with_specialities'.$key}[$speciality_id],
                        'until_time'     => request()->{'end_with_specialities'.$key}[$speciality_id],
                    ]);
                }
            }
    	}

    	toastr()->success('Agregado exitosamente');

    	return response()->json(['success' => true]);
    }

    public function edit(DoctorSchedule $doctor_schedule)
    {
        $dental_offices = DentalOffice::GetAllCached()->pluck('name', 'id');
        $specialities = $this->GetSpecialities();
        $doctor_specialities = DoctorEspeciality::whereHas('especiality', function($query){
                $query->where('status', true);
            })
            ->get()
            ->toArray();

    	return view('pages.doctor-schedules.edit', compact('doctor_schedule', 'dental_offices', 'specialities', 'doctor_specialities'));
    }

    public function update(DoctorSchedule $doctor_schedule, UpdateDoctorSchedulesRequest $request)
    {
    	$doctor_schedule->update([
            'office_id'  => $request->office_id,
    		'work_start' 	    => $request->work_start,
    		'work_end'		    => $request->work_end,
    		'break_start' 	    => $request->break_start,
    		'break_end'		    => $request->break_end,
            'break_interval'   => $request->break_start && $request->break_end ? Carbon::createFromFormat('H:i',$request->break_start)->diffInMinutes(Carbon::createFromFormat('H:i',$request->break_end)) : 0,
    		'days'			    => $request->day,
    		'status' 		    => $request->status
    	]);

        $doctor_schedule->details()->delete();

        foreach($request->specialities_ids as $key2 => $speciality_id)
        {
            $doctor_schedule->details()->create([
                'speciality_id' => $speciality_id,
                'from_time'     => $request->start_with_specialities[$speciality_id],
                'until_time'     => $request->end_with_specialities[$speciality_id],
            ]);
        }

    	toastr()->success('Editado exitosamente');

    	return redirect('doctors-schedule');
    }

    private function GetSpecialities()
    { 
        return Especiality::where('status', true)->pluck('name', 'id');
    }

    public function xlsx()
    {
        $doctor_schedules = DoctorSchedule::with('doctor', 'dental_office')
            ->where('status', true)
            ->whereHas('doctor', function($query)
            {
                $query->where('status', true);
            });

    	if(request()->doctor_id) 
    	{
    		$doctor_schedules = $doctor_schedules->where('doctor_id', request()->doctor_id);
    	}

        if(request()->office_id) 
        {
            $doctor_schedules = $doctor_schedules->where('office_id', request()->office_id);
        }

        if(request()->day) 
        {
            $doctor_schedules = $doctor_schedules->where('days', request()->day);
        }

        $excelArray        = [];
        $excelArray[]      = [
            'Id',
            'Dia',
            'Doctor',
            'U. Negocio',
            'Clinica',
            'Inicio de Jornada',
            'Fin de Jornada',
            'Inicio de Receso',
            'Fin de Receso',
            'Estado'
        ];
        foreach($doctor_schedules->get() as $doctor_schedule)
        {
            $excelArray[] =[
                $doctor_schedule->id,
                config('constants.dias-semana.' . $doctor_schedule->days),
                $doctor_schedule->doctor->fullname,
                $doctor_schedule->doctor->enterprise ? $doctor_schedule->doctor->enterprise->name : '',
                $doctor_schedule->dental_office->name,
                Carbon::parse($doctor_schedule->work_start)->format('H:i'),
                Carbon::parse($doctor_schedule->work_end)->format('H:i'),
                $doctor_schedule->break_start ? Carbon::parse($doctor_schedule->break_start)->format('H:i') : NUll,
                $doctor_schedule->break_end ? Carbon::parse($doctor_schedule->break_end)->format('H:i') : NULL,
                config('constants.status.' . $doctor_schedule->status),
            ];
        }

        Excel::create('Horario-doctores', function($excel) use ($excelArray) {
                $excel->sheet('sheet1', function($sheet) use ($excelArray) {
                $sheet->fromArray($excelArray, null, 'A1', false, false);
            });
        })->export('xlsx');
    }
}
