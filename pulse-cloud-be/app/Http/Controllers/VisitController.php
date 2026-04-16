<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\VisitFee;
use App\Models\VisitFindingsImage;
use App\Models\VisitLaboratoriesImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    //
    public function getVisitList(Request $request){
        try {
            $per_page = $request->get('per_page', 10);
            $keyword = $request->get('keyword', '');
            $date_start = $request->get('date_start')
                ? Carbon::parse($request->get('date_start'))->format('Y-m-d')
                : Carbon::now()->startOfMonth()->format('Y-m-d'); // add default value start of the month

            $date_end = $request->get('date_end')
                ? Carbon::parse($request->get('date_end'))->format('Y-m-d')
                : Carbon::now()->endOfMonth()->format('Y-m-d'); // add default value end of the month

            $visits = Visit::with('patient')
                ->when($keyword, function ($query, $keyword) {
                    $query->whereHas('patient', function ($q) use ($keyword) {
                        $q->where('full_name', 'like', '%' . $keyword . '%')
                          ->orWhere('phone_number', 'like', '%' . $keyword . '%');
                    });
                })
                ->when($date_start && $date_end, function ($query) use ($date_start, $date_end) {
                     $query->whereBetween(DB::raw('DATE(visit_date)'), [$date_start, $date_end]);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($per_page);

            return $this->sendResponse('Visits retrieved successfully', $visits);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve visits', ['error' => $e->getMessage()], 500);
        }
    }

    public function viewVisitInformation($id){
        try {
            $visit = Visit::with(['patient', 'complaints', 'findings', 'prescriptions', 'referrals', 'laboratories', 'diagnoses', 'visitFees', 'laboratoriesImages', 'findingsImages'])->find($id);

            if(!$visit){
                return $this->sendError('Visit not found', [], 404);
            }

            $visit->laboratoriesImages->transform(function($image) {
                $image->image_url = asset($image->image_path);
                return $image;
            });

            $visit->findingsImages->transform(function($image) {
                $image->image_url = asset($image->image_path);
                return $image;
            });

            // get medicines generic name
            $generic_medicines = DB::table('generic_medicines')->pluck('name')->toArray();
            $medicine_brand_names = DB::table('medicine_brands')->pluck('name')->toArray();
            $doses = DB::table('medicine_doses')->pluck('name')->toArray();
            $preparations = DB::table('medicine_preparations')->pluck('name')->toArray();

            $response = [
                'visit' => $visit,
                'generic_medicines' => $generic_medicines,
                'medicine_brand_names' => $medicine_brand_names,
                'doses' => $doses,
                'preparations' => $preparations,
            ];

            return $this->sendResponse('Visit information retrieved successfully', $response);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve visit information', ['error' => $e->getMessage()], 500);
        }
    }

    public function getPatientVisitHistory(Request $request){
        try {
            $patient_id = $request->route('patient_id');
            $visits = Visit::with('patient')
                ->where('patient_id', $patient_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->sendResponse('Patient visit history retrieved successfully', $visits);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve patient visit history', ['error' => $e->getMessage()], 500);
        }
    }

    public function saveVisitInformation(Request $request, $id){
        try {
            $visit = Visit::find($id);
            $user = Auth::user();

            if(!$visit){
                return $this->sendError('Visit not found', [], 404);
            }

            $complaints = $request->complaints ?? [];
            $diagnoses = $request->diagnoses ?? [];
            $prescriptions = $request->prescriptions ?? [];
            $findings = $request->findings ?? [];
            $laboratories = $request->laboratories ?? [];
            $referrals = $request->referrals ?? [];
            $visit_fee = $request->visit_fee ?? 0;
            $is_save_as_draft = isset($request->is_save_as_draft) ? $request->is_save_as_draft : false;

            foreach($complaints as $complaint){
                if($complaint['name'] === null || $complaint['name'] === ''){
                    continue;
                }
                if(isset($complaint['id']) && !is_null($complaint['id'])){
                    $existing_complaint = $visit->complaints()->find($complaint['id']);
                    if($existing_complaint){
                        $existing_complaint->update([
                            'complaint' => $complaint['name'] ?? '',
                            'complaint_remarks' => $complaint['remarks'] ?? null,
                        ]);
                    }
                } else {
                    $visit->complaints()->create([
                        'complaint' => $complaint['name'] ?? '',
                        'complaint_remarks' => $complaint['remarks'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }
            }

            foreach($diagnoses as $diagnose){
                if($diagnose['name'] === null || $diagnose['name'] === ''){
                    continue;
                }
                if(isset($diagnose['id']) && !is_null($diagnose['id'])){
                    $existing_diagnosis = $visit->diagnoses()->find($diagnose['id']);
                    if($existing_diagnosis){
                        $existing_diagnosis->update([
                            'diagnosis' => $diagnose['name'] ?? '',
                            'diagnosis_remarks' => $diagnose['remarks'] ?? null,
                        ]);
                    }
                } else {
                    $visit->diagnoses()->create([
                        'diagnosis' => $diagnose['name'] ?? '',
                        'diagnosis_remarks' => $diagnose['remarks'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }
            }

            foreach($prescriptions as $prescription){
                if(isset($prescription['id']) && !is_null($prescription['id'])){
                    $existing_prescription = $visit->prescriptions()->find($prescription['id']);
                    if($existing_prescription){
                        $existing_prescription->update([
                            'medicine_generic' => $prescription['medicine_generic'] ?? '',
                            'medicine_brand' => $prescription['medicine_brand'] ?? null,
                            'dosage' => $prescription['dosage'] ?? null,
                            'reminder' => $prescription['reminder'] ?? null,
                            'duration' => $prescription['duration'] ?? null,
                            'quantity' => $prescription['quantity'] ?? null,
                            'remarks' => $prescription['remarks'] ?? null,
                            'is_remarks_only' => $prescription['is_remarks_only'] ?? false,
                            'created_by' => $user->id,
                        ]);
                    }
                } else {
                    $visit->prescriptions()->create([
                        'medicine_generic' => $prescription['medicine_generic'] ?? '',
                        'medicine_brand' => $prescription['medicine_brand'] ?? null,
                        'dosage' => $prescription['dosage'] ?? null,
                        'reminder' => $prescription['reminder'] ?? null,
                        'duration' => $prescription['duration'] ?? null,
                        'quantity' => $prescription['quantity'] ?? null,
                        'quantity_prefix' => $prescription['quantity_prefix'] ?? null,
                        'remarks' => $prescription['remarks'] ?? null,
                        'is_remarks_only' => $prescription['is_remarks_only'] ?? false,
                        'created_by' => $user->id,
                    ]);
                }
            }

            foreach($findings as $finding){
                if($finding['name'] === null || $finding['name'] === ''){
                    continue;
                }
                if(isset($finding['id']) && !is_null($finding['id'])){
                    $existing_finding = $visit->findings()->find($finding['id']);
                    if($existing_finding){
                        $existing_finding->update([
                            'finding' => $finding['name'] ?? '',
                            'finding_remarks' => $finding['remarks'] ?? null,
                        ]);
                    }
                } else {
                    $visit->findings()->create([
                        'finding' => $finding['name'] ?? '',
                        'finding_remarks' => $finding['remarks'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }
            }

            foreach($laboratories as $laboratory){
                if($laboratory['name'] === null || $laboratory['name'] === ''){
                    continue;
                }
                if(isset($laboratory['id']) && !is_null($laboratory['id'])){
                    $existing_laboratory = $visit->laboratories()->find($laboratory['id']);
                    if($existing_laboratory){
                        $existing_laboratory->update([
                            'laboratory_test' => $laboratory['name'] ?? '',
                            'laboratory_remarks' => $laboratory['remarks'] ?? null,
                        ]);
                    }
                } else {
                    $visit->laboratories()->create([
                        'laboratory_test' => $laboratory['name'] ?? '',
                        'laboratory_remarks' => $laboratory['remarks'] ?? null,
                        'created_by' => $user->id,
                    ]);
                }
            }

            foreach($referrals as $referral){
                if($referral['description'] === null || $referral['description'] === ''){
                    continue;
                }
                if(isset($referral['id']) && !is_null($referral['id'])){
                    $existing_referral = $visit->referrals()->find($referral['id']);
                    if($existing_referral){
                        $existing_referral->update([
                            'description' => $referral['description'] ?? '',
                        ]);
                    }
                } else {
                    $visit->referrals()->create([
                        'description' => $referral['description'] ?? '',
                        'created_by' => $user->id,
                    ]);
                }
            }
            
            $visit->visitFees()->updateOrCreate(
                ['fee_description' => VisitFee::DEFAULT_FEE_DESCRIPTION],
                [
                    'fee_amount' => $visit_fee,
                    'created_by' => $user->id,
                ]
            );

            if($visit->visit_status !== Visit::STATUS_SETTLED){
                $visit->update([
                    'visit_status' => $is_save_as_draft ? Visit::STATUS_CHECKING : Visit::STATUS_FOR_PAYMENT,
                ]);
            }

            return $this->sendResponse('Visit information updated successfully', $visit);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update visit information', ['error' => $e->getMessage()], 500);
        }
    }

    public function removeVisitInformation(Request $request, $id){
        try {
            $visit = Visit::find($request->visit_id);

            if(!$visit){
                return $this->sendError('Visit not found', [], 404);
            }

            if($request->type == 'complaint'){
                $visit->complaints()->find($id)->delete();
            }

            if($request->type == 'diagnosis'){
                $visit->diagnoses()->find($id)->delete();
            }

            if($request->type == 'treatment'){
                $visit->treatments()->find($id)->delete();
            }

            if($request->type == 'finding'){
                $visit->findings()->find($id)->delete();
            }

            if($request->type == 'laboratory'){
                $visit->laboratories()->find($id)->delete();
            }

            if($request->type == 'visitFees'){
                $visit->visitFees()->find($id)->delete();
            }

            return $this->sendResponse('Visit information removed successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to remove visit information', ['error' => $e->getMessage()], 500);
        }
    }

    public function updateVisitStatus(Request $request, $id){
        try {
            $visit = Visit::find($id);

            if(!$visit){
                return $this->sendError('Visit not found', [], 404);
            }

            $visit->update([
                'visit_status' => $request->visit_status,
            ]);

            return $this->sendResponse('Visit status updated successfully', $visit);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update visit status', ['error' => $e->getMessage()], 500);
        }
    }

    public function getDailyRevenue(){
        try {
            $visits = Visit::join('patients', 'visits.patient_id', '=', 'patients.id')
                ->addSelect('patients.full_name')
                ->where('visits.visit_status', Visit::STATUS_SETTLED)
                ->whereDate('visits.created_at', Carbon::today())
                ->withSum('visitFees as total_fee', 'fee_amount')
                ->orderBy('patients.full_name')
                ->get();

            $total_revenue = $visits->sum('total_fee');
            return $this->sendResponse('Daily revenue generated successfully', ['visits' => $visits, 'total_revenue' => $total_revenue]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate daily revenue', ['error' => $e->getMessage()], 500);
        }
    }

    public function getDailyRevenueReport(Request $request){
        try {
            $date_start = $request->get('date_start')
                ? Carbon::parse($request->get('date_start'))->format('Y-m-d')
                : null;

            $date_end = $request->get('date_end')
                ? Carbon::parse($request->get('date_end'))->format('Y-m-d')
                : null;

            $visits = Visit::join('patients', 'visits.patient_id', '=', 'patients.id')
                ->addSelect('patients.full_name')
                ->addSelect('visits.visit_date')
                ->where('visits.visit_status', Visit::STATUS_SETTLED)
                ->when($date_start && $date_end, function ($query) use ($date_start, $date_end) {
                     $query->whereBetween(DB::raw('DATE(visits.visit_date)'), [$date_start, $date_end]);
                })
                ->withSum('visitFees as total_fee', 'fee_amount')
                ->orderBy('visits.visit_date', 'asc')
                ->get();

            // Group by date and calculate totals
            $daily_reports = $visits->groupBy(function($visit) {
                return Carbon::parse($visit->visit_date)->format('Y-m-d');
            })->map(function($day_visits, $date) {
                return [
                    'date' => $date,
                    'visits' => $day_visits->sortBy('full_name')->values(),
                    'daily_total' => $day_visits->sum('total_fee')
                ];
            })
            ->values();

            $total_revenue = $visits->sum('total_fee');
            return $this->sendResponse('Daily revenue report generated successfully', ['daily_reports' => $daily_reports, 'total_revenue' => $total_revenue]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to generate daily revenue report', ['error' => $e->getMessage()], 500);
        }
    }

    public function uploadLaboratoryImage(Request $request, $id){
        try {
            $visit = Visit::find($id);

            if(!$visit){
                return $this->sendError('Visit not found', [], 404);
            }

            if(!$request->hasFile('image')){
                return $this->sendError('No image file provided', [], 400);
            }

            $file = $request->file('image');
            $file_directory = "visits/{$id}/laboratory_images";
            $file_name = $file->getClientOriginalName();
            $path = $file->storeAs($file_directory, $file_name, 'public');

            $uploaded_image = $visit->laboratoriesImages()->create([
                'image_path' => $path,
                'visit_id' => $id,
                'created_by' => Auth::id(),
            ]);

            $uploaded_image->image_url = asset($path);

            return $this->sendResponse('Laboratory image uploaded successfully', ['image_path' => $path, 'uploaded_image' => $uploaded_image]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to upload laboratory image', ['error' => $e->getMessage()], 500);
        }
    }

    public function uploadFindingsImage(Request $request, $id){
        try {
            $visit = Visit::find($id);

            if(!$visit){
                return $this->sendError('Visit not found', [], 404);
            }

            if(!$request->hasFile('image')){
                return $this->sendError('No image file provided', [], 400);
            }

            $file = $request->file('image');
            $file_directory = "visits/{$id}/findings_images";
            $file_name = $file->getClientOriginalName();
            $path = $file->storeAs($file_directory, $file_name, 'public');

            $uploaded_image = $visit->findingsImages()->create([
                'image_path' => $path,
                'visit_id' => $id,
                'created_by' => Auth::id(),
            ]);

            $uploaded_image->image_url = asset($path);

            return $this->sendResponse('Findings image uploaded successfully', ['image_path' => $path, 'uploaded_image' => $uploaded_image]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to upload findings image', ['error' => $e->getMessage()], 500);
        }
    }

    public function deleteLaboratoryImage($id){
        try {
            $image = VisitLaboratoriesImage::find($id);

            if(!$image){
                return $this->sendError('Laboratory image not found', [], 404);
            }

            // Delete the file from storage
            if (\Storage::disk('public')->exists($image->image_path)) {
                \Storage::disk('public')->delete($image->image_path);
            }

            // Delete the database record
            $image->delete();

            return $this->sendResponse('Laboratory image deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete laboratory image', ['error' => $e->getMessage()], 500);
        }
    }

    public function deleteFindingsImage($id){
        try {
            $image = VisitFindingsImage::find($id);

            if(!$image){
                return $this->sendError('Findings image not found', [], 404);
            }

            // Delete the file from storage
            if (\Storage::disk('public')->exists($image->image_path)) {
                \Storage::disk('public')->delete($image->image_path);
            }

            // Delete the database record
            $image->delete();

            return $this->sendResponse('Laboratory image deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete laboratory image', ['error' => $e->getMessage()], 500);
        }
    }
}
