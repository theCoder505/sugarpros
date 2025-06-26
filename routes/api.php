<?php

use App\Http\Controllers\AppointmentMeetingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('api')->get('/fetch-meeting/{appointment_code}', [AppointmentMeetingController::class, 'fetchMeetingData'])->name('fetch-meeting-data');

Route::middleware('api')->get('/end-meeting/{appointment_code}', [AppointmentMeetingController::class, 'endOngoingMeeting'])->name('end-meeting');

