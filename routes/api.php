<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

////////////////// Auth API's /////////////////////
Route::post('login', [\App\Http\Controllers\authController::class, 'login']);
Route::post('register/patient', [\App\Http\Controllers\authController::class, 'register_patient']);
Route::post('register/doctor', [\App\Http\Controllers\Super_adminController::class, 'registerDoctor'])->middleware('auth:sanctum');
Route::post('register/secertary', [\App\Http\Controllers\Super_adminController::class, 'registerSecertary'])->middleware('auth:sanctum');
Route::post('register/admin', [\App\Http\Controllers\Super_adminController::class, 'registeradmin']);
Route::post('edit/employee/block', [\App\Http\Controllers\Super_adminController::class, 'makeuserbloke'])->middleware('auth:sanctum');
Route::post('edit/employee/active', [\App\Http\Controllers\Super_adminController::class, 'makeuseractive'])->middleware('auth:sanctum');
Route::post('edit/my/profile', [\App\Http\Controllers\Super_adminController::class, 'editpatientprofile'])->middleware('auth:sanctum');
//////////////////secertry/////////////////////
Route::get('get/secertaries', [\App\Http\Controllers\secertaryController::class, 'getallsecertarys'])->middleware('auth:sanctum');
Route::post('edit/seceratry', [\App\Http\Controllers\secertaryController::class, 'editsecertary'])->middleware('auth:sanctum');
Route::post('edit/pathint/block', [\App\Http\Controllers\secertaryController::class, 'makeuserblock'])->middleware('auth:sanctum');
Route::post('edit/pathint/active', [\App\Http\Controllers\secertaryController::class, 'makeuseractive'])->middleware('auth:sanctum');
//////////////////specialization/////////////////////
Route::post('edit/specialization', [\App\Http\Controllers\specializationController::class, 'editspecialization'])->middleware('auth:sanctum');
Route::post('add/specialization', [\App\Http\Controllers\specializationController::class, 'addspecialization'])->middleware('auth:sanctum');
Route::get('get/allspecializations', [\App\Http\Controllers\specializationController::class, 'getallspecializations'])->middleware('auth:sanctum');
//////////////////Search/////////////////////
Route::post('search/specialization/doctors', [\App\Http\Controllers\searchController::class, 'getdoctorsbyspecialization'])->middleware('auth:sanctum');
Route::post('search/name/doctors', [\App\Http\Controllers\searchController::class, 'getdoctorsbyname'])->middleware('auth:sanctum');
Route::post('search/name/secertaries', [\App\Http\Controllers\searchController::class, 'getsecertarybyname'])->middleware('auth:sanctum');
Route::post('search/name/patients', [\App\Http\Controllers\searchController::class, 'getpatientsbyname'])->middleware('auth:sanctum');
Route::post('search/name/medications', [\App\Http\Controllers\searchController::class, 'getmedicationbyname'])->middleware('auth:sanctum');
Route::post('search/num/clinics', [\App\Http\Controllers\searchController::class, 'getclinicbynum'])->middleware('auth:sanctum');
Route::post('search/secertary/schedual', [\App\Http\Controllers\searchController::class, 'getsecertary_schedual'])->middleware('auth:sanctum');
Route::post('search/appointments/state', [\App\Http\Controllers\searchController::class, 'getallappointments'])->middleware('auth:sanctum');
Route::post('search/name/users', [\App\Http\Controllers\searchController::class, 'getusersbyname'])->middleware('auth:sanctum');
//////////////////medication/////////////////////
Route::post('add/medication', [\App\Http\Controllers\medicationController::class, 'addmedication'])->middleware('auth:sanctum');
Route::post('edit/medication', [\App\Http\Controllers\medicationController::class, 'editmedication'])->middleware('auth:sanctum');
Route::post('get/medication', [\App\Http\Controllers\medicationController::class, 'getmedicationbyid'])->middleware('auth:sanctum');
Route::get('get/allmedications', [\App\Http\Controllers\medicationController::class, 'getallmedications'])->middleware('auth:sanctum');
/////////////////schedual//////////////////////
Route::post('add/schedual', [\App\Http\Controllers\schedualController::class, 'addschedual'])->middleware('auth:sanctum');
Route::post('edit/schedual', [\App\Http\Controllers\schedualController::class, 'editschedual'])->middleware('auth:sanctum');
Route::post('get/schedual', [\App\Http\Controllers\schedualController::class, 'getschedualbyid'])->middleware('auth:sanctum');
Route::get('get/allscheduals', [\App\Http\Controllers\schedualController::class, 'getallscheduals'])->middleware('auth:sanctum');
/////////////////clinic//////////////////////
Route::post('add/clinic', [\App\Http\Controllers\clinicController::class, 'addclinic'])->middleware('auth:sanctum');
Route::post('edit/clinic', [\App\Http\Controllers\clinicController::class, 'editclinic'])->middleware('auth:sanctum');
Route::post('get/clinic', [\App\Http\Controllers\clinicController::class, 'getclinicbyid'])->middleware('auth:sanctum');
Route::get('get/allclinics', [\App\Http\Controllers\clinicController::class, 'getallclinics'])->middleware('auth:sanctum');
/////////////////secertary_schedual//////////////////////
Route::post('add/secertary/schedual', [\App\Http\Controllers\secertary_schedualController::class, 'addsecertary_schedual'])->middleware('auth:sanctum');
Route::post('edit/secertary/schedual', [\App\Http\Controllers\secertary_schedualController::class, 'editsecertary_schedual'])->middleware('auth:sanctum');
//Route::post('get/schedual', [\App\Http\Controllers\secertary_schedualController::class, 'getsecertary_schedualbyid'])->middleware('auth:sanctum');
Route::get('get/secertary/allscheduals', [\App\Http\Controllers\secertary_schedualController::class, 'getallsecertary_scheduals'])->middleware('auth:sanctum');
Route::post('get/secertary/scheduals', [\App\Http\Controllers\secertary_schedualController::class, 'getsecertary_scheduals'])->middleware('auth:sanctum');
/////////////////appointment//////////////////////
Route::post('add/appointment', [\App\Http\Controllers\appointmentController::class, 'addappointment'])->middleware('auth:sanctum')->middleware('auth:sanctum');
Route::post('edit/appointment/description', [\App\Http\Controllers\appointmentController::class, 'edit_description_appointment'])->middleware('auth:sanctum');
Route::post('edit/appointment/date', [\App\Http\Controllers\appointmentController::class, 'editappointment_date'])->middleware('auth:sanctum');
Route::post('edit/appointment/stete_Done', [\App\Http\Controllers\appointmentController::class, 'editstateappointmenttodone'])->middleware('auth:sanctum');
Route::post('edit/appointment/stete_Undone', [\App\Http\Controllers\appointmentController::class, 'editstateappointmenttoundone'])->middleware('auth:sanctum');
Route::post('delete/appointment', [\App\Http\Controllers\appointmentController::class, 'deleteappointment'])->middleware('auth:sanctum');
Route::post('get/secertary/appointments', [\App\Http\Controllers\appointmentController::class, 'get_all_active_appointment_to_secertary'])->middleware('auth:sanctum');
Route::post('get/doctor/appointments', [\App\Http\Controllers\appointmentController::class, 'get_all_active_appointment_to_dotor'])->middleware('auth:sanctum');
Route::post('get/patient/appointments/done', [\App\Http\Controllers\appointmentController::class, 'get_all_done_appointment_to_patient'])->middleware('auth:sanctum');
Route::post('get/patient/appointments/active', [\App\Http\Controllers\appointmentController::class, 'get_all_active_appointment_to_patient'])->middleware('auth:sanctum');
Route::post('get/patient/personal/profile', [\App\Http\Controllers\appointmentController::class, 'get_personal_profile'])->middleware('auth:sanctum');
Route::post('get/patient/medical/profile', [\App\Http\Controllers\appointmentController::class, 'get_medical_profile'])->middleware('auth:sanctum');
/////////////////doctortimetoappointmentController/////////////////
Route::post('get/doctor/time/to/do/appointment', [\App\Http\Controllers\doctortimetoappointmentController::class, 'get_doctor_time_to_do_appointment'])->middleware('auth:sanctum');
/////////////////doctor_schedual_clinic//////////////////////
Route::post('add/doctor/schedual/clinic', [\App\Http\Controllers\docotr_schedual_clinicController::class, 'adddoctor_schedual_clinic'])->middleware('auth:sanctum');
Route::post('edit/doctor/schedual/clinic', [\App\Http\Controllers\docotr_schedual_clinicController::class, 'editDoctor_schedual_clinic'])->middleware('auth:sanctum');
Route::post('edit/doctor/schedual/clinic/state/to/enabled', [\App\Http\Controllers\docotr_schedual_clinicController::class, 'editDoctor_schedual_clinicbyid_to_enabled'])->middleware('auth:sanctum');
Route::post('edit/doctor/schedual/clinic/state/to/disabled', [\App\Http\Controllers\docotr_schedual_clinicController::class, 'editDoctor_schedual_clinicbyid_to_disabled'])->middleware('auth:sanctum');
Route::post('get/doctor/schedual/clinic', [\App\Http\Controllers\docotr_schedual_clinicController::class, 'getDoctor_schedual_clinicbyid'])->middleware('auth:sanctum');
Route::get('get/alldoctor/scheduals/clinic', [\App\Http\Controllers\docotr_schedual_clinicController::class, 'getallDoctor_schedual_clinics'])->middleware('auth:sanctum');
/////////////////appointment_medication//////////////////////
Route::post('add/appointment/medication', [\App\Http\Controllers\appointment_medicationsController::class, 'addappointment_medication'])->middleware('auth:sanctum');
Route::post('edit/appointment/medication', [\App\Http\Controllers\appointment_medicationsController::class, 'editappointment_medication'])->middleware('auth:sanctum');
Route::post('get/appointment/medication', [\App\Http\Controllers\appointment_medicationsController::class, 'getappointment_medicationbyid'])->middleware('auth:sanctum');
Route::get('get/allappointment/medications', [\App\Http\Controllers\appointment_medicationsController::class, 'getallappointment_medications'])->middleware('auth:sanctum');
/////////////////Message//////////////////////
Route::post('delete/message', [\App\Http\Controllers\messageController::class, 'deleteMessage'])->middleware('auth:sanctum');
/////////////////User//////////////////////
Route::post('edit/user/profile', [\App\Http\Controllers\userController::class, 'edituser'])->middleware('auth:sanctum');
Route::post('edit/employee/profile', [\App\Http\Controllers\userController::class, 'editemployee'])->middleware('auth:sanctum');
Route::post('change/password', [\App\Http\Controllers\userController::class, 'changepassword'])->middleware('auth:sanctum');
