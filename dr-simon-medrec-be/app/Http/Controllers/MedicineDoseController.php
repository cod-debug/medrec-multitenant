<?php

namespace App\Http\Controllers;

use App\Models\MedicineDose;
use Illuminate\Http\Request;

class MedicineDoseController extends Controller
{
    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:medicine_doses,name',
            ]);

            $medicineDose = MedicineDose::create([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Medicine dose saved successfully', $medicineDose);
        } catch (\Exception $e) {
            return $this->sendError('Failed to save medicine dose', ['error' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $query = MedicineDose::query();

            // Optional search filter
            if ($request->has('search') && $request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Optional pagination
            if ($request->has('paginate') && $request->paginate === 'true') {
                $perPage = $request->get('per_page', 15);
                $medicineDoses = $query->orderBy('name', 'asc')->paginate($perPage);
            } else {
                $medicineDoses = $query->orderBy('name', 'asc')->get();
            }

            return $this->sendResponse('Medicine doses retrieved successfully', $medicineDoses);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve medicine doses', ['error' => $e->getMessage()], 500);
        }
    }

    public function get($id)
    {
        try {
            $medicineDose = MedicineDose::find($id);

            if (!$medicineDose) {
                return $this->sendError('Medicine dose not found', [], 404);
            }

            return $this->sendResponse('Medicine dose retrieved successfully', $medicineDose);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve medicine dose', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $medicineDose = MedicineDose::find($id);

            if (!$medicineDose) {
                return $this->sendError('Medicine dose not found', [], 404);
            }

            $request->validate([
                'name' => 'required|string|unique:medicine_doses,name,' . $id,
            ]);

            $medicineDose->update([
                'name' => $request->name,
            ]);

            return $this->sendResponse('Medicine dose updated successfully', $medicineDose);
        } catch (\Exception $e) {
            return $this->sendError('Failed to update medicine dose', ['error' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $medicineDose = MedicineDose::find($id);

            if (!$medicineDose) {
                return $this->sendError('Medicine dose not found', [], 404);
            }

            $medicineDose->delete();

            return $this->sendResponse('Medicine dose deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete medicine dose', ['error' => $e->getMessage()], 500);
        }
    }
}
