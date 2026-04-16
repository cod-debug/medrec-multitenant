<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Inpatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InpatientController extends Controller
{
    /**
     * Create a new inpatient record
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|in:Male,Female',
                'address' => 'nullable|string',
                'phone_number' => 'nullable|string|max:50',
                'admit_patient' => 'boolean',
                'referred_by' => 'nullable|string|max:255',
                'reason_for_admission' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation failed', $validator->errors(), 422);
            }

            if($request->patient_id && Inpatient::find($request->patient_id)) {
                $inpatient = Inpatient::find($request->patient_id);
            } else {
                $inpatient = Inpatient::firstOrCreate([
                    'full_name' => $request->full_name,
                ],[
                    'full_name' => $request->full_name,
                    'gender' => $request->gender,
                    'age' => $request->age,
                    'created_by' => auth()->id()
                ]);
            }

            $admission = null;
            if($request->admit_patient) {
                // Optionally, you can create an initial admission record here
                $admission = Admission::firstOrCreate([
                    'inpatient_id' => $inpatient->id,
                    'admission_status' => Admission::ADMISSION_STATUS_ADMITTED,
                ],[
                    'inpatient_id' => $inpatient->id,
                    'admission_date' => $request->admission_date ?? now(),
                    'referred_by' => $request->referred_by,
                    'reason_for_admission' => $request->reason_for_admission ?? '',
                    'admission_status' => Admission::ADMISSION_STATUS_ADMITTED,
                    'room_number' => $request->room_number,
                    'created_by' => auth()->id()
                ]);
            }

            $response = [
                'patient_information' => $inpatient,
                'admission' => $admission
            ];

            return $this->sendResponse('Inpatient created successfully', $response);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create inpatient', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get paginated list of inpatients
     */
    public function list(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('keyword', '');

            $query = Inpatient::withCount('admissions');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            }
            $query->addSelect(['latest_date_referred' => Admission::select('admission_date')
                ->whereColumn('inpatient_id', 'inpatients.id')
                ->orderBy('admission_date', 'desc')
                ->limit(1)
            ]);

            $inpatients = $query->orderBy('created_at', 'desc')
                                ->paginate($perPage);

            return $this->sendResponse('Inpatients retrieved successfully', $inpatients);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve inpatients', ['error' => $e->getMessage()], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search', '');

            if (!$search) {
                return $this->sendError('Search query is required', [], 400);
            }

            $inpatients = Inpatient::where('full_name', 'like', "%{$search}%")
                ->orderBy('full_name', 'asc')
                ->get();

            return $this->sendResponse('Inpatients search completed successfully', $inpatients);
        } catch (\Exception $e) {
            return $this->sendError('Failed to search inpatients', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all inpatients without pagination
     */
    public function all()
    {
        try {
            $inpatients = Inpatient::with('creator')
                                    ->orderBy('full_name', 'asc')
                                    ->get();

            return $this->sendResponse('All inpatients retrieved successfully', $inpatients);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve all inpatients', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single inpatient by ID
     */
    public function get($id)
    {
        try {
            $inpatient = Inpatient::with([
                'creator', 
                'admissions' => function($query) {
                    $query->orderBy('admission_date', 'desc');
                }, 
                'admissions.diagnoses', 
                'admissions.treatments'
            ])->find($id);

            if (!$inpatient) {
                return $this->sendError('Inpatient not found', [], 404);
            }

            return $this->sendResponse('Inpatient retrieved successfully', $inpatient);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve inpatient', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update an inpatient record
     */
    public function update(Request $request, $id)
    {
        try {
            $inpatient = Inpatient::find($id);

            if (!$inpatient) {
                return $this->sendError('Inpatient not found', [], 404);
            }

            $validator = Validator::make($request->all(), [
                'full_name' => 'sometimes|required|string|max:255',
                'age' => 'nullable|numeric',
                'gender' => 'nullable|string|in:Male,Female,Other',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation failed', $validator->errors(), 422);
            }

            $inpatient->update($request->only([
                'full_name',
                'age',
                'gender',
            ]));

            return $this->sendResponse('Inpatient updated successfully', $inpatient->load('creator'));
        } catch (\Exception $e) {
            return $this->sendError('Failed to update inpatient', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete an inpatient record
     */
    public function delete($id)
    {
        try {
            $inpatient = Inpatient::find($id);

            if (!$inpatient) {
                return $this->sendError('Inpatient not found', [], 404);
            }

            $inpatient->delete();

            return $this->sendResponse('Inpatient deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete inpatient', ['error' => $e->getMessage()], 500);
        }
    }
}

