<?php

namespace App\Http\Controllers;

use App\Models\MedicinePreparation;
use Illuminate\Http\Request;

class MedicinePreparationController extends Controller
{
    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:medicine_preparations,name',
            ]);

            $medicinePreparation = MedicinePreparation::create([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Medicine preparation saved successfully', $medicinePreparation);
        } catch (\Exception $e) {
            return $this->sendError('Failed to save medicine preparation', ['error' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $query = MedicinePreparation::query();

            // Optional search filter
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Optional pagination
            if ($request->has('paginate') && $request->paginate === 'true') {
                $perPage = $request->get('per_page', 15);
                $medicinePreparations = $query->orderBy('name', 'asc')->paginate($perPage);
            } else {
                $medicinePreparations = $query->orderBy('name', 'asc')->get();
            }

            return $this->sendResponse('Medicine preparations retrieved successfully', $medicinePreparations);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve medicine preparations', ['error' => $e->getMessage()], 500);
        }
    }

    public function get($id)
    {
        try {
            $medicinePreparation = MedicinePreparation::find($id);

            if (!$medicinePreparation) {
                return $this->sendError('Medicine preparation not found', [], 404);
            }

            return $this->sendResponse('Medicine preparation retrieved successfully', $medicinePreparation);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve medicine preparation', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $medicinePreparation = MedicinePreparation::find($id);

            if (!$medicinePreparation) {
                return $this->sendError('Medicine preparation not found', [], 404);
            }

            $request->validate([
                'name' => 'required|string|unique:medicine_preparations,name,' . $id,
            ]);

            $medicinePreparation->update([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Medicine preparation updated successfully', $medicinePreparation);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update medicine preparation', ['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $medicinePreparation = MedicinePreparation::find($id);

            if (!$medicinePreparation) {
                return $this->sendError('Medicine preparation not found', [], 404);
            }

            $medicinePreparation->delete();

            return $this->sendResponse('Medicine preparation deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete medicine preparation', ['error' => $e->getMessage()], 500);
        }
    }
}
