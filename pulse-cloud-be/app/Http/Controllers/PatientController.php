<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    //

    public function view($id){
        try {
            $patient = Patient::find($id);

            if(!$patient){
                return $this->sendError('Patient not found', [], 404);
            }

            return $this->sendResponse('Patient information retrieved successfully', $patient);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve patient information', ['error' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        //
        try {
            $per_page = $request->get('per_page', 10);
            $keyword = $request->get('keyword', '');

            $patients = Patient::when($keyword, function ($query, $keyword) {
                $query->where('full_name', 'like', '%' . $keyword . '%')
                      ->orWhere('phone_number', 'like', '%' . $keyword . '%');
            })
            ->withCount('visits')
            ->orderBy('full_name', 'asc')
            ->paginate($per_page);

            return $this->sendResponse('Patients retrieved successfully', $patients);
        }
        catch (\Exception $e) {
            return $this->sendError('Failed to retrieve patients', ['error' => $e->getMessage()], 500);
        }
    }

    public function getAllPatients(Request $request): JsonResponse{
        try {
            $keyword = $request->get('keyword', '');

            $patients = Patient::where('full_name', 'like', '%' . $keyword . '%')
            ->orderBy('full_name', 'asc')
            ->get();

            return $this->sendResponse('Patients retrieved successfully', $patients);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve patients', ['error' => $e->getMessage()], 500);
        }
    }

    public function addPatientAndQueue(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'address' => 'required|string|max:500',
                'phone_number' => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $data = [
                'full_name' => $request->full_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'created_by' => Auth::id(),
            ];
            
            $new_patient = Patient::create($data);

            if($new_patient){
                // Create a new visit and add to queue
                $visit = Visit::create([
                    'patient_id' => $new_patient->id,
                    'status' => Visit::STATUS_QUEUED,
                    'created_by' => Auth::id(),
                    'visit_date' => now(),
                    'visit_status' => Visit::STATUS_QUEUED
                ]);

                return $this->sendResponse('Patient added and queued successfully', ['patient' => $new_patient, 'visit' => $visit]);
            } else {
                return $this->sendError('Failed to add patient', [], 400);
            }
        } catch (\Exception $e) {
            return $this->sendError('Failed to create patient', ['error' => $e->getMessage()], 500);
        }
    }

    public function updatePatient(Request $request, $id){
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'sometimes|required|string|max:255',
                'date_of_birth' => 'sometimes|required|date',
                'address' => 'sometimes|required|string|max:500',
                'phone_number' => 'sometimes|required|string|max:20',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $patient = Patient::withCount('visits')->find($id);

            if(!$patient){
                return $this->sendError('Patient not found', [], 404);
            }

            $patient->update($request->only(['full_name', 'date_of_birth', 'gender', 'address', 'phone_number']));

            return $this->sendResponse('Patient updated successfully', ['patient' => $patient]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update patient information', ['error' => $e->getMessage()], 500);
        }
    }

    public function getPatientsToday(Request $request){
        try {
            $status = $request->get('status');
            $per_page = $request->get('per_page', 10);
            $keyword = $request->get('keyword', '');

            $queued_visits = Visit::select('visits.id as visit_id','visits.visit_status', 'patients.*')
                ->join('patients', 'visits.patient_id', '=', 'patients.id')
                ->when($status, function ($query, $status) {
                    $query->where('visits.visit_status', $status);
                })
                ->when($keyword, function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('patients.full_name', 'like', "%{$keyword}%")
                          ->orWhere('patients.phone_number', 'like', "%{$keyword}%");
                    });
                })
                ->whereDate('visits.visit_date', now()->toDateString())
                ->orderByRaw("CASE visits.visit_status
                    WHEN 'checking' THEN 1
                    WHEN 'queued' THEN 2
                    WHEN 'for-payment' THEN 3
                    WHEN 'settled' THEN 4
                    WHEN 'cancelled' THEN 5
                    ELSE 6
                END")
                ->orderBy('visits.created_at', 'asc')
                ->paginate($per_page);

            return $this->sendResponse('Patients retrieved successfully', $queued_visits);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve patients', ['error' => $e->getMessage()], 500);
        }
    }

    public function addPatientToQueue(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $visit = Visit::create([
                'patient_id' => $request->patient_id,
                'created_by' => Auth::id(),
                'visit_date' => now(),
                'visit_status' => Visit::STATUS_QUEUED
            ]);

            return $this->sendResponse('Patient added to queue successfully', ['visit' => $visit]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to add patient to queue', ['error' => $e->getMessage()], 500);
        }
    }

    public function removeFromQueue(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'visit_id' => 'required|exists:visits,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $visit = Visit::find($request->visit_id);
            $visit->delete();

            return $this->sendResponse('Patient removed from queue successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to remove patient from queue', ['error' => $e->getMessage()], 500);
        }
    }

    public function getPatientVisitHistory(Request $request, $patient_id){
        try {
            $visits = Visit::with(['patient', 'complaints', 'findings', 'referrals', 'prescriptions', 'laboratories', 'diagnoses', 'visitFees', 'laboratoriesImages', 'findingsImages'])
                ->where('patient_id', $patient_id)
                ->where('visit_status', '!=', Visit::STATUS_QUEUED)
                ->orderBy('created_at', 'desc');

                if(!is_null($request->get('visit_id'))){
                    $visits->whereNot('id', $request->visit_id);
                }

                $visits = $visits->get();

                $visits->each(function($visit) {
                    $visit->laboratoriesImages->transform(function($image) {
                        $image->image_url = asset( $image->image_path);
                        return $image;
                    });
                    return $visit;
                });
                
                $visits->each(function($visit) {
                    $visit->findingsImages->transform(function($image) {
                        $image->image_url = asset($image->image_path);
                        return $image;
                    });
                    return $visit;
                });

            return $this->sendResponse('Patient visit history retrieved successfully', $visits);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve patient visit history', ['error' => $e->getMessage()], 500);
        }
    }
}
