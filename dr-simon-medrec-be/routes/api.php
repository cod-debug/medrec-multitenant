<?php
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\InpatientController;
use App\Http\Controllers\MedicineBrandController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedicineDoseController;
use App\Http\Controllers\MedicinePreparationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScheduleOperationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VisitController;

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->middleware('prevent.duplicate');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
    });

    Route::controller(UserController::class)
    ->middleware('auth:sanctum')
    ->prefix('user')
    ->group(function () {
        Route::post('/register', 'register');
        Route::get('/list', 'list');
        Route::post('delete/{id}', 'delete');
        Route::post('update/{id}', 'update');
        Route::post('update-info', 'updateUserInfo');
        Route::get('current-user', 'getCurrentUser');

        Route::controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout');
            Route::post('update-password', 'updatePassword');
        });
    });

    Route::controller(PatientController::class)
    ->middleware('auth:sanctum')
    ->prefix('patient')
    ->group(function () {
        Route::post('/add-and-queue', 'addPatientAndQueue');
        Route::post('/queue', 'addPatientToQueue');
        Route::post('/remove-queue', 'removeFromQueue');
        Route::get('/today', 'getPatientsToday');
        Route::get('/history/{patient_id}', 'getPatientVisitHistory');
        Route::get('/paginated', 'list');
        Route::get('/all', 'getAllPatients');
        Route::put('/update/{id}', 'updatePatient');
        Route::get('/{id}', 'view');
    });

    Route::controller(VisitController::class)
    ->prefix('visit')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/list', 'getVisitList');
        Route::get('/history/{patient_id}', 'getPatientVisitHistory');
        Route::get('/revenue', 'getDailyRevenue');
        Route::get('/revenue/daily', 'getDailyRevenueReport');
        Route::post('/save/{id}', 'saveVisitInformation');
        Route::post('/remove-info/{id}', 'removeVisitInformation');
        Route::put('/status/{id}', 'updateVisitStatus');
        Route::get('/{id}', 'viewVisitInformation');
        Route::post('/upload-laboratory-image/{id}', 'uploadLaboratoryImage');
        Route::post('/delete-laboratory-image/{id}', 'deleteLaboratoryImage');
        Route::post('/upload-findings-image/{id}', 'uploadFindingsImage');
        Route::post('/delete-findings-image/{id}', 'deleteFindingsImage');
    });

    Route::controller(ScheduleOperationController::class)
    ->prefix('operation')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/monthly-schedule', 'getSchedulesForMonth');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(MedicineController::class)
    ->prefix('medicine')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/save-generic-name', 'createGenericMedicineName');
        Route::get('/generic-names', 'listGenericMedicineNames');
        Route::get('/generic-name/{id}', 'getGenericMedicineName');
        Route::put('/generic-name/{id}', 'updateGenericMedicineName');
        Route::delete('/generic-name/{id}', 'deleteGenericMedicineName');
    });

    Route::controller(MedicineBrandController::class)
    ->prefix('medicine-brand')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/list', 'list');
        Route::get('/{id}', 'get');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(MedicineDoseController::class)
    ->prefix('medicine-dose')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/list', 'list');
        Route::get('/{id}', 'get');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(MedicinePreparationController::class)
    ->prefix('medicine-preparation')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/list', 'list');
        Route::get('/{id}', 'get');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
    });

    Route::controller(BackupController::class)
    ->prefix('backup')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/download', 'downloadBackup');
        Route::get('/info', 'getBackupInfo');
    });

    Route::controller(InpatientController::class)
    ->middleware('auth:sanctum')
    ->prefix('inpatient')
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/list', 'list');
        Route::get('/all', 'all');
        Route::get('/search', 'search');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
        Route::get('records/{id}', 'get');
    });

    Route::controller(AdmissionController::class)
    ->middleware('auth:sanctum')
    ->prefix('admission')
    ->group(function () {
        Route::post('/create', 'create');
        Route::get('/list', 'list');
        Route::get('/all', 'all');
        Route::get('/search', 'search');
        Route::get('/details/{id}', 'details');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'delete');
        Route::delete('/remove-diagnosis/{id}', 'removeDiagnosis');
        Route::delete('/remove-treatment/{id}', 'removeTreatment');
    });

    Route::controller(SettingsController::class)
    ->middleware('auth:sanctum')
    ->prefix('settings')
    ->group(function () {
        Route::get('/get', 'getSettings');
        Route::put('/update', 'updateSettings');
    });
});
