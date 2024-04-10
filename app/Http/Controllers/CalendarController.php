<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCalendarEventRequest;
use App\Http\Requests\CreatePurchaseImageRequest;
use App\Http\Requests\CreateWishPurchaseRequest;
use App\Models\Branch;
use App\Models\Calendar;
use App\Models\Doctor;
use App\Models\Presentation;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseBudget;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\WishPurchase;
use App\Models\WishPurchaseDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    public function index()
    {
        $chosen_doctor = Doctor::orderBy('first_name')->orderBy('last_name')->first();
        $doctors = Doctor::where('status',1)->pluck('first_name','id');

        return view('pages.calendar.index', compact('doctors', 'chosen_doctor'));
    }

    public function create()
    {
        $doctors = Doctor::where('status',1)->pluck('first_name','id');
        $doctors_dental_office = Doctor::with('offices')->where('status', true)->whereHas('offices')->get();
        return view('pages.calendar.create', compact('doctors', 'doctors_dental_office'));
    }

    public function store(CreateCalendarEventRequest $request)
    {
        if(request()->ajax())
        {
            $calendar_event =  Calendar::create([
                'client_id'             => $request->client_id,
                'start'                 => $request->start,
                'end'                   => $request->end,
                'doctor_id'             => $request->doctor_id,
                'office_id'             => $request->office_id,
                'first_consultation'    => $request->first_consultation,
                'observation'           => $request->observation,
                'observation_doctor'    => $request->observation_doctor,
                'user_id'               => auth()->user()->id,
                'status'                => true

            ]);

            // ServiceLevelJob::dispatch($calendar_event->id, 'CalendarEvent')->onQueue('totalizador');

            toastr()->success('Agregado exitosamente');

            return response()->json(['success' => true]);
        }
        abort(404);
    }

    public function ajax_calendar()
    {
        if(request()->ajax())
        {
            $in_office = false;

            $events = Calendar::with(['client', 'doctor', 'user'])
                ->where('status', 1);

            if(request()->day_events)
            {
                $events = $events->where('status', '<', 23);
            }
            else
            {
                $events = $events->where('status', '<', 25);
            }
            if(request()->client_id)
            {
                $events = $events->where('client_id', request()->client_id);
            }
            if(request()->status)
            {
                $events = $events->where('status', request()->status);
            }
            if(request()->next_visit_not_null)
            {
                $events = $events->whereNotNull('next_visit');
            }
            if(request()->start && request()->end)
            {
                $events = $events->whereBetween('start', [request()->start, request()->end]);
            }
            if(request()->doctor_id)
            {
                $status = [10,15,5,1,20,3];
                $events = $events->where('doctor_id', request()->doctor_id)->orderByRaw('FIELD (status, ' . implode(', ', $status) . ') ASC');
            }

            if (request()->get_last_event)
            {
                $events = $events->orderByDesc('start')->limit(1)->get();
            }
            else
            {
                $events = $events->orderBy('start')->get();
            }

            $results = [];
            foreach ($events as $key => $event)
            {
                $results['events'][$key]['id'] = (int) $event->id;
                $results['events'][$key]['title'] = $event->client->fullname;
                $results['events'][$key]['doctor_fullname'] = $event->doctor->fullname;
                $results['events'][$key]['client_document_number'] = $event->client->document_number;
                $results['events'][$key]['client_id'] = $event->client_id;
                $results['events'][$key]['dental_office'] = $event->office->name;
                $phones_text = '';
                $results['events'][$key]['client_phones'] = $phones_text;
                $contracts_text = '';
                // $today = today()->format('Y-m-d 00:00:00');
                if($event->status == 15 && $event->start > today()->format('Y-m-d 00:00:00'))
                {
                    $in_office = true;
                }

                $results['events'][$key]['status'] = $event->status;
                $results['events'][$key]['status_name'] = config('constants.calendar-events-status.' . $event->status);
                $results['events'][$key]['status_label'] = config('constants.calendar-events-status-label.' . $event->status);
                $results['events'][$key]['first_consultation'] = $event->first_consultation;
                $results['events'][$key]['start'] = $event->start->format('Y-m-d H:i:s');
                $results['events'][$key]['end'] = $event->end->format('Y-m-d H:i:s');
                $results['events'][$key]['color'] = '#' . config('constants.css-color-labels.' . config('constants.calendar-events-status-label.' . $event->status));
                $results['events'][$key]['user_name'] = $event->user->fullname;
                $results['events'][$key]['observation'] = $event->observation;
                $results['events'][$key]['observation_doctor'] = $event->observation_doctor;
                $results['events'][$key]['created_at'] = $event->created_at->format('d/m/Y H:i:s');
                $results['events'][$key]['in_office'] = $in_office;
                $results['events'][$key]['dayOfWeek'] = config('constants.day-week.'.Carbon::parse($event->start)->dayOfWeek);
            }
            $results['total_events_in_clinic'] = $events->where('status', 10)->count();
            return $results;
        }

        abort(404);
    }
    

}
