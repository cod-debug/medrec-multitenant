<?php

namespace App\Http\Controllers;

use App\Models\MedicineBrand;
use Illuminate\Http\Request;

class MedicineBrandController extends Controller
{
    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:medicine_brands,name',
            ]);

            $medicineBrand = MedicineBrand::create([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Medicine brand saved successfully', $medicineBrand);
        } catch (\Exception $e) {
            return $this->sendError('Failed to save medicine brand', ['error' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $query = MedicineBrand::query();

            // Optional search filter
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Optional pagination
            if ($request->has('paginate') && $request->paginate === 'true') {
                $perPage = $request->get('per_page', 15);
                $medicineBrands = $query->orderBy('name', 'asc')->paginate($perPage);
            } else {
                $medicineBrands = $query->orderBy('name', 'asc')->get();
            }

            return $this->sendResponse('Medicine brands retrieved successfully', $medicineBrands);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve medicine brands', ['error' => $e->getMessage()], 500);
        }
    }

    public function get($id)
    {
        try {
            $medicineBrand = MedicineBrand::find($id);

            if (!$medicineBrand) {
                return $this->sendError('Medicine brand not found', [], 404);
            }

            return $this->sendResponse('Medicine brand retrieved successfully', $medicineBrand);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve medicine brand', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $medicineBrand = MedicineBrand::find($id);

            if (!$medicineBrand) {
                return $this->sendError('Medicine brand not found', [], 404);
            }

            $request->validate([
                'name' => 'required|string|unique:medicine_brands,name,' . $id,
            ]);

            $medicineBrand->update([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Medicine brand updated successfully', $medicineBrand);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update medicine brand', ['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $medicineBrand = MedicineBrand::find($id);

            if (!$medicineBrand) {
                return $this->sendError('Medicine brand not found', [], 404);
            }

            $medicineBrand->delete();

            return $this->sendResponse('Medicine brand deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete medicine brand', ['error' => $e->getMessage()], 500);
        }
    }
}
