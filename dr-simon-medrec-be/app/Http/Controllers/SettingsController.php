<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    //
    public function getSettings()
    {
        try {
            $settings = Settings::first();
            return $this->sendResponse('Settings retrieved successfully', ['settings' => $settings]);
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve settings', ['error' => $e->getMessage()], 500);
        }
    }

    public function updateSettings(Request $request)
    {
        try {
            $settings_exist = Settings::first();
            if(!$settings_exist){
                $settings = Settings::create(['data' => $request->all()]);
                return $this->sendResponse('Settings created successfully', ['settings' => $settings]);
            } else {
                // Get the current data array, update it, then reassign
                $data = $settings_exist->data ?? [];
                foreach($request->all() as $key => $value){
                    // only update a key in data column in database if it is requested
                    $data[$key] = $value;
                }
                $settings_exist->data = $data;
                $settings_exist->save();
                return $this->sendResponse('Settings updated successfully', ['settings' => $settings_exist]);
            }
            } catch (\Exception $e) {
            return $this->sendError('Failed to update settings', ['error' => $e->getMessage()], 500);
        }
    }
}
