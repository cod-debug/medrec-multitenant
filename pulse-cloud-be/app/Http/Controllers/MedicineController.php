<?php

namespace App\Http\Controllers;

use App\Models\GenericMedicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    //

    public function createGenericMedicineName(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:generic_medicines,name',
            ]);

            $genericMedicine = GenericMedicine::create([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Generic medicine name saved successfully', $genericMedicine);
        } catch (\Exception $e) {
            return $this->sendError('Failed to save generic medicine name', ['error' => $e->getMessage()], 500);
        }
    }

    public function listGenericMedicineNames(Request $request)
    {
        try {
            $query = GenericMedicine::query();

            // Optional search filter
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Optional pagination
            if ($request->has('paginate') && $request->paginate === 'true') {
                $perPage = $request->get('per_page', 15);
                $genericMedicines = $query->orderBy('name', 'asc')->paginate($perPage);
            } else {
                $genericMedicines = $query->orderBy('name', 'asc')->get();
            }

            return $this->sendResponse('Generic medicine names retrieved successfully', $genericMedicines);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve generic medicine names', ['error' => $e->getMessage()], 500);
        }
    }

    public function getGenericMedicineName($id)
    {
        try {
            $genericMedicine = GenericMedicine::find($id);

            if (!$genericMedicine) {
                return $this->sendError('Generic medicine name not found', [], 404);
            }

            return $this->sendResponse('Generic medicine name retrieved successfully', $genericMedicine);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve generic medicine name', ['error' => $e->getMessage()], 500);
        }
    }

    public function updateGenericMedicineName(Request $request, $id)
    {
        try {
            $genericMedicine = GenericMedicine::find($id);

            if (!$genericMedicine) {
                return $this->sendError('Generic medicine name not found', [], 404);
            }

            $request->validate([
                'name' => 'required|string|unique:generic_medicines,name,' . $id,
            ]);

            $genericMedicine->update([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Generic medicine name updated successfully', $genericMedicine);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update generic medicine name', ['error' => $e->getMessage()], 500);
        }
    }

    public function deleteGenericMedicineName($id)
    {
        try {
            $genericMedicine = GenericMedicine::find($id);

            if (!$genericMedicine) {
                return $this->sendError('Generic medicine name not found', [], 404);
            }

            $genericMedicine->delete();

            return $this->sendResponse('Generic medicine name deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete generic medicine name', ['error' => $e->getMessage()], 500);
        }
    }
}
