<?php

namespace App\Http\Controllers;

use App\Models\OperationSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleOperationController extends Controller
{
    //
    public function create(Request $request){
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'operation_type' => 'required|string|max:255',
                'operating_room' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
                'scheduled_at' => 'required|date',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            // Create a new operation schedule
            $operationSchedule = OperationSchedule::create([
                'patient_id' => $request['patient_id'],
                'operation_type' => $request['operation_type'],
                'operating_room' => $request['operating_room'] ?? null,
                'diagnosis' => $request['diagnosis'] ?? null,
                'remarks' => $request['remarks'] ?? null,
                'scheduled_at' => $request['scheduled_at'],
                'created_by' => auth()->id(),
            ]);

            $operationSchedule->load('patient');

            return $this->sendResponse('Operation schedule created successfully', ['operation_schedule' => $operationSchedule]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to create operation schedule', ['error' => $e->getMessage()], 500);
        }
    }

    public function getSchedulesForMonth(Request $request){
        try {
            $month = $request->get('active_month');
            $start_date = Carbon::parse($month)->startOfMonth()->toDateString();
            $end_date = Carbon::parse($month)->endOfMonth()->toDateString();
            
            $schedules = OperationSchedule::whereBetween('scheduled_at', [$start_date, $end_date])
                ->with('patient')
                ->get();

            return $this->sendResponse('Operation schedules retrieved successfully', ['schedules' => $schedules]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve operation schedules', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id){
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'operation_type' => 'required|string|max:255',
                'operating_room' => 'nullable|string|max:255',
                'diagnosis' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
                'scheduled_at' => 'required|date',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error', $validator->errors(), 422);
            }

            $operationSchedule = OperationSchedule::findOrFail($id);
            $operationSchedule->update([
                'patient_id' => $request['patient_id'],
                'operation_type' => $request['operation_type'],
                'operating_room' => $request['operating_room'] ?? null,
                'diagnosis' => $request['diagnosis'] ?? null,
                'remarks' => $request['remarks'] ?? null,
                'scheduled_at' => $request['scheduled_at'],
            ]);

            $operationSchedule->load('patient');

            return $this->sendResponse('Operation schedule updated successfully', ['operation_schedule' => $operationSchedule]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update operation schedule', ['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id){
        try {
            $operationSchedule = OperationSchedule::findOrFail($id);
            $operationSchedule->delete();

            return $this->sendResponse('Operation schedule deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete operation schedule', ['error' => $e->getMessage()], 500);
        }
    }

}
