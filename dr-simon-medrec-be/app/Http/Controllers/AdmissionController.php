<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AdmissionDiagnosis;
use App\Models\AdmissionTreatment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdmissionController extends Controller
{
    /**
     * Get paginated list of admissions with filters
     */
    public function list(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $status = $request->input('status', '');
            $keyword = $request->input('keyword', '');
            $date_start = $request->get('date_start')
                ? Carbon::parse($request->get('date_start'))->format('Y-m-d')
                : null; // add default value start of the month
            $date_end = $request->get('date_end')
                ? Carbon::parse($request->get('date_end'))->format('Y-m-d')
                : null; // add default value end of the month

            $query = Admission::select('admissions.*')
            ->join('inpatients', 'admissions.inpatient_id', '=', 'inpatients.id')
            ->addSelect('inpatients.full_name')
            ->with(['patient', 'patient.creator', 'creator']);

            // Filter by status
            if ($status) {
                $query->where('admissions.admission_status', $status);
            }

            // Filter by keyword (search in patient's name, phone, or reason for admission)
            if ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->whereHas('patient', function($inpatientQuery) use ($keyword) {
                        $inpatientQuery->where('inpatients.full_name', 'like', "%{$keyword}%")
                                     ->orWhere('inpatients.phone_number', 'like', "%{$keyword}%");
                    })
                    ->orWhere('admissions.reason_for_admission', 'like', "%{$keyword}%")
                    ->orWhere('admissions.referred_by', 'like', "%{$keyword}%");
                });
            }

            if($date_start && $date_end) {
                // Ensure date_end is greater than or equal to date_start
                if ($date_end < $date_start) {
                    return $this->sendError('Invalid date range: end date must be greater than or equal to start date', [], 400);
                }
                
                $query->whereBetween(DB::raw('DATE(admissions.admission_date)'), [$date_start, $date_end]);
            }
            $admissions = $query->orderBy('inpatients.full_name')
                                ->paginate($perPage);

            return $this->sendResponse('Admissions retrieved successfully', $admissions);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve admissions', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all admissions
     */
    public function all()
    {
        try {
            $admissions = Admission::with(['patient', 'diagnoses', 'treatments'])->get();
            return $this->sendResponse('Admissions retrieved successfully', $admissions);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve admissions', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Search admissions
     */
    public function search(Request $request)
    {
        try {
            $keyword = $request->input('keyword', '');
            $query = Admission::with(['patient', 'diagnoses', 'treatments']);

            if ($keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->whereHas('patient', function($patientQuery) use ($keyword) {
                        $patientQuery->where('full_name', 'like', "%{$keyword}%")
                                     ->orWhere('phone_number', 'like', "%{$keyword}%");
                    })
                    ->orWhere('reason_for_admission', 'like', "%{$keyword}%")
                    ->orWhere('referred_by', 'like', "%{$keyword}%");
                });
            }

            $admissions = $query->orderBy('admission_date', 'desc')->get();
            return $this->sendResponse('Admissions searched successfully', $admissions);
        } catch (\Exception $e) {
            return $this->sendError('Failed to search admissions', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a specific admission
     */
    public function get($id)
    {
        try {
            $admission = Admission::with(['patient', 'diagnoses', 'treatments'])->find($id);

            if (!$admission) {
                return $this->sendError('Admission not found', [], 404);
            }

            return $this->sendResponse('Admission retrieved successfully', $admission);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve admission', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new admission
     */
    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'inpatient_id' => 'required|exists:inpatients,id',
                'admission_date' => 'required|date',
                'referred_by' => 'nullable|string',
                'reason_for_admission' => 'required|string',
                'diagnoses' => 'nullable|array',
                'diagnoses.*.diagnosis' => 'required|string',
                'diagnoses.*.remarks' => 'nullable|string',
                'treatments' => 'nullable|array',
                'treatments.*.treatment' => 'required|string',
                'treatments.*.remarks' => 'nullable|string',
            ]);

            $admission = Admission::create([
                'inpatient_id' => $validatedData['inpatient_id'],
                'admission_date' => $validatedData['admission_date'],
                'referred_by' => $validatedData['referred_by'],
                'reason_for_admission' => $validatedData['reason_for_admission'],
                'admission_status' => Admission::ADMISSION_STATUS_ADMITTED,
                'created_by' => auth()->id()
            ]);

            // Create diagnoses if provided
            if (isset($validatedData['diagnoses'])) {
                foreach ($validatedData['diagnoses'] as $diagnosisData) {
                    AdmissionDiagnosis::create([
                        'admission_id' => $admission->id,
                        'diagnosis' => $diagnosisData['diagnosis'],
                        'remarks' => $diagnosisData['remarks'] ?? null,
                        'created_by' => auth()->id()
                    ]);
                }
            }

            // Create treatments if provided
            if (isset($validatedData['treatments'])) {
                foreach ($validatedData['treatments'] as $treatmentData) {
                    AdmissionTreatment::create([
                        'admission_id' => $admission->id,
                        'treatment' => $treatmentData['treatment'],
                        'remarks' => $treatmentData['remarks'] ?? null,
                        'created_by' => auth()->id()
                    ]);
                }
            }

            $admission->load(['patient', 'diagnoses', 'treatments']);
            return $this->sendResponse('Admission created successfully', $admission);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create admission', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update admission details
     */
    public function update(Request $request, $id)
    {
        try {
            $admission = Admission::find($id);

            if (!$admission) {
                return $this->sendError('Admission not found', [], 404);
            }

            $validatedData = $request->validate([
                'referred_by' => 'nullable|string',
                'reason_for_admission' => 'nullable|string',
                'discharge_date' => 'nullable|date',
                'admission_status' => 'nullable|string|in:admitted,discharged',
                'diagnoses' => 'nullable|sometimes',
                'diagnoses.*.id' => 'sometimes|nullable|exists:admission_diagnoses,id',
                'diagnoses.*.diagnosis' => 'sometimes|nullable|string',
                'diagnoses.*.remarks' => 'sometimes|nullable|string',
                'treatments' => 'nullable|sometimes',
                'treatments.*.id' => 'sometimes|nullable|exists:admission_treatments,id',
                'treatments.*.treatment' => 'sometimes|nullable|string',
                'treatments.*.remarks' => 'sometimes|nullable|string',
                'admission_fee' => 'nullable|numeric',
                'referred_by_hospital' => 'nullable|string',
                'room_number' => 'nullable|string',
                'admission_date' => 'required|date'
            ]);

            // Update admission basic info
            $admission->update([
                'referred_by' => $validatedData['referred_by'] ?? $admission->referred_by,
                'reason_for_admission' => $validatedData['reason_for_admission'] ?? $admission->reason_for_admission,
                'admission_status' => $validatedData['admission_status'] ?? $admission->admission_status,
                'admission_fee' => $validatedData['admission_fee'] ?? $admission->admission_fee,
                'room_number' => $validatedData['room_number'] ?? $admission->room_number,
                'admission_date' => $validatedData['admission_date'] ?? $admission->admission_date,
                'referred_by_hospital' => $validatedData['referred_by_hospital'] ?? $admission->referred_by_hospital,
            ]);

            if(isset($validatedData['admission_status']) && $validatedData['admission_status'] == Admission::ADMISSION_STATUS_DISCHARGED && isset($validatedData['discharge_date'])) {
                $admission->update([
                    'discharge_date' => Carbon::now(),
                ]);
            }

            // Update diagnoses
            if (isset($validatedData['diagnoses'])) {
                foreach ($validatedData['diagnoses'] as $diagnosisData) {
                    if(empty($diagnosisData['diagnosis'])) {
                        continue; // Skip creating diagnosis if diagnosis name is empty
                    }
                    if (isset($diagnosisData['id']) && $diagnosisData['id']) {
                        // Update existing diagnosis
                        $diagnosis = AdmissionDiagnosis::find($diagnosisData['id']);
                        if ($diagnosis) {
                            $diagnosis->update([
                                'diagnosis' => $diagnosisData['diagnosis'],
                                'remarks' => $diagnosisData['remarks'] ?? null,
                            ]);
                        }
                    } else {
                        // Create new diagnosis
                        AdmissionDiagnosis::create([
                            'admission_id' => $admission->id,
                            'diagnosis' => $diagnosisData['diagnosis'],
                            'remarks' => $diagnosisData['remarks'] ?? null,
                            'created_by' => auth()->id()
                        ]);
                    }
                }
            }

            // Update treatments
            if (isset($validatedData['treatments'])) {
                foreach ($validatedData['treatments'] as $treatmentData) {
                    if(empty($treatmentData['treatment'])) {
                        continue; // Skip creating treatment if treatment name is empty
                    }
                    if (isset($treatmentData['id']) && $treatmentData['id']) {
                        // Update existing treatment
                        $treatment = AdmissionTreatment::find($treatmentData['id']);
                        if ($treatment) {
                            $treatment->update([
                                'treatment' => $treatmentData['treatment'],
                                'remarks' => $treatmentData['remarks'] ?? null,
                            ]);
                        }
                    } else {
                        // Create new treatment
                        AdmissionTreatment::create([
                            'admission_id' => $admission->id,
                            'treatment' => $treatmentData['treatment'],
                            'remarks' => $treatmentData['remarks'] ?? null,
                            'created_by' => auth()->id()
                        ]);
                    }
                }
            }

            $admission->load(['patient', 'diagnoses', 'treatments']);
            return $this->sendResponse('Admission updated successfully', $admission);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update admission', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete an admission record
     */
    public function delete($id)
    {
        try {
            $admission = Admission::find($id);

            if (!$admission) {
                return $this->sendError('Admission not found', [], 404);
            }

            $admission->delete();

            return $this->sendResponse('Admission deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete admission', ['error' => $e->getMessage()], 500);
        }
    }

    public function details($id){
        try {
            $admission = Admission::with(['patient', 'diagnoses', 'treatments'])->find($id);

            if (!$admission) {
                return $this->sendError('Admission not found', [], 404);
            }

            return $this->sendResponse('Admission details retrieved successfully', $admission);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve admission details', ['error' => $e->getMessage()], 500);
        }
    }

    public function removeDiagnosis($id){
        try {
            $diagnosis = AdmissionDiagnosis::find($id);

            if (!$diagnosis) {
                return $this->sendError('Diagnosis not found', [], 404);
            }

            $diagnosis->delete();

            return $this->sendResponse('Diagnosis removed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to remove diagnosis', ['error' => $e->getMessage()], 500);
        }
    }

    public function removeTreatment($id){
        try {
            $treatment = AdmissionTreatment::find($id);

            if (!$treatment) {
                return $this->sendError('Treatment not found', [], 404);
            }

            $treatment->delete();

            return $this->sendResponse('Treatment removed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to remove treatment', ['error' => $e->getMessage()], 500);
        }
    }
}
