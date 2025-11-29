<?php

use App\Http\Controllers\APIControllers\AppointmentBookingByPatientController;
use App\Http\Controllers\APIControllers\PatientDexcomController;
use App\Http\Controllers\APIControllers\PatientFatSecretController;
use App\Http\Controllers\APIControllers\PatientFirstSteps;
use App\Http\Controllers\APIControllers\PatientPagesController;
use App\Http\Controllers\APIControllers\ProviderClaimMDPatientController;
use App\Http\Controllers\APIControllers\ProviderWorkFlowsController;
use App\Http\Controllers\APIControllers\WebsiteBasicController;
use App\Http\Controllers\AppointmentMeetingController;
use App\Http\Controllers\PatientSubscriptionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// For Ending the meetings.
Route::middleware('api')->get('/fetch-meeting/{appointment_code}', [AppointmentMeetingController::class, 'fetchMeetingData']);

Route::middleware('api')->get('/end-meeting/{appointment_code}', [AppointmentMeetingController::class, 'endOngoingMeeting']);

Route::middleware('api')->get('/basic-website-settings', [WebsiteBasicController::class, 'settings']);

Route::middleware('api')->get('/patients', [WebsiteBasicController::class, 'allPatients']);

Route::middleware('api')->get('/providers', [WebsiteBasicController::class, 'allProviders']);





// General APIs Starts from here... 
Route::middleware('api')->post('/send-otp-to-user', [PatientFirstSteps::class, 'sendOTPToUser']);
Route::middleware('api')->post('/verify-otp', [PatientFirstSteps::class, 'verifyUsersOTP']);
Route::middleware('api')->post('/signup-new-user', [PatientFirstSteps::class, 'signupNewUser']);
Route::middleware('api')->post('/login-existing-user', [PatientFirstSteps::class, 'loginUser']);
Route::middleware('api')->get('/logout', [PatientFirstSteps::class, 'logout']);

Route::middleware('api')->post('/send-forget-request', [PatientFirstSteps::class, 'sendForgetRequest']);
Route::middleware('api')->post('/otp-verification-on-reset', [PatientFirstSteps::class, 'verifyForgetOTP']);

Route::middleware('api')->post('/check-password-validity', [PatientFirstSteps::class, 'checkPasswordValidity']);
Route::middleware('api')->post('/reset-account-password', [PatientFirstSteps::class, 'resetAccountPassword']);





//------------------------------------- API part TODO FOR PAtients -----------------------------------------
// after the signup, user also need to subscribe. stripe_status is paid, within these: ['active', 'trialing', 'paid']. stripe_status updates to the SubscriptionPlan model.
// see completeSubscription function from PatientSubscriptionsController page 
//------------------------------------- API part TODO  -----------------------------------------------------






// Check with Patients JWT Token.
Route::middleware(['api', 'jwt.patient'])->get('/basic', [PatientFirstSteps::class, 'basic']);
Route::middleware(['api', 'jwt.patient'])->post('/basic', [PatientFirstSteps::class, 'userDetailsAdding']);

Route::middleware(['api', 'jwt.patient'])->get('/privacy', [PatientFirstSteps::class, 'privacy']);
Route::middleware(['api', 'jwt.patient'])->post('/privacy', [PatientFirstSteps::class, 'fillupPrivacyForm']);

Route::middleware(['api', 'jwt.patient'])->get('/compliance', [PatientFirstSteps::class, 'compliance']);
Route::middleware(['api', 'jwt.patient'])->post('/compliance', [PatientFirstSteps::class, 'fillupComplianceForm']);

Route::middleware(['api', 'jwt.patient'])->get('/financial-responsibility-aggreement', [PatientFirstSteps::class, 'financialRespAggreement']);
Route::middleware(['api', 'jwt.patient'])->post('/financial-responsibility-aggreement', [PatientFirstSteps::class, 'fillupFinancialForm']);

Route::middleware(['api', 'jwt.patient'])->get('/agreement-for-self-payment', [PatientFirstSteps::class, 'agreementSelfPayment']);
Route::middleware(['api', 'jwt.patient'])->post('/agreement-for-self-payment', [PatientFirstSteps::class, 'fillupSelfPaymentForm']);

Route::middleware(['api', 'jwt.patient'])->post('/hippa-consent-prefference', [PatientFirstSteps::class, 'hippaConsentPreferrence']);
Route::middleware(['api', 'jwt.patient'])->post('/change-language-prefference', [PatientFirstSteps::class, 'changeLanguagePreferrence']);






// Patient After Logging in and filling up all the forms 
Route::middleware(['api', 'jwt.patient'])->get('/account', [PatientFirstSteps::class, 'account']);
Route::middleware(['api', 'jwt.patient'])->post('/update-profile-picture', [PatientFirstSteps::class, 'updateProfilePicture']);
Route::middleware(['api', 'jwt.patient'])->post('/update-account-details', [PatientFirstSteps::class, 'updateAccountDetails']);
Route::middleware(['api', 'jwt.patient'])->get('/delete-account', [PatientFirstSteps::class, 'DeleteUsersAccount']);

Route::middleware(['api', 'jwt.patient'])->get('/settings', [PatientFirstSteps::class, 'settings']);
Route::middleware(['api', 'jwt.patient'])->post('/user-accout-email-verification', [PatientFirstSteps::class, 'checkIfEmailExists']);
Route::middleware(['api', 'jwt.patient'])->post('/user-accout-otp-verification', [PatientFirstSteps::class, 'verifyOTPOnEmailChange']);
Route::middleware(['api', 'jwt.patient'])->post('/user-accout-email-change', [PatientFirstSteps::class, 'finalEmailCheckAndChange']);
Route::middleware(['api', 'jwt.patient'])->post('/user-account-password-verification', [PatientFirstSteps::class, 'checkIfEmailExistsForPassword']);
Route::middleware(['api', 'jwt.patient'])->post('/user-account-password-otp-verification', [PatientFirstSteps::class, 'verifyOTPOnPasswordChange']);
Route::middleware(['api', 'jwt.patient'])->post('/user-account-password-change', [PatientFirstSteps::class, 'finalPasswordCheckAndChange']);

Route::middleware(['api', 'jwt.patient'])->get('/notifications', [PatientFirstSteps::class, 'notifications']);
Route::middleware(['api', 'jwt.patient'])->delete('/notifications', [PatientFirstSteps::class, 'deleteNotification']);







// Patient Geneune Pages
Route::middleware(['api', 'jwt.patient'])->get('/dashboard', [PatientPagesController::class, 'dashboard']);




















// To fix API now
// Appointment Booking Routes
Route::middleware(['api', 'jwt.patient'])->get('/appointments/patient-details', [AppointmentBookingByPatientController::class, 'getPatientDetails']);
Route::middleware(['api', 'jwt.patient'])->post('/appointments/initiate', [AppointmentBookingByPatientController::class, 'initiateBooking']);
Route::middleware(['api', 'jwt.patient'])->post('/appointments/complete', [AppointmentBookingByPatientController::class, 'completeBooking']);
Route::middleware(['api', 'jwt.patient'])->get('/appointments/payment/success', [AppointmentBookingByPatientController::class, 'paymentSuccess']);
Route::middleware(['api', 'jwt.patient'])->get('/appointments/payment/cancel', [AppointmentBookingByPatientController::class, 'paymentCancel']);
// End fixing API





















Route::middleware(['api', 'jwt.patient'])->get('/appointments', [PatientPagesController::class, 'appointments']);
Route::middleware(['api', 'jwt.patient'])->post('/appointments', [PatientPagesController::class, 'showSpecificAppointment']);
Route::middleware(['api', 'jwt.patient'])->get('/join-meeting/{appointment_uid}', [PatientPagesController::class, 'joinMeeting']);
Route::middleware(['api', 'jwt.patient'])->post('/search-appointments-by-month', [PatientPagesController::class, 'searchAppointmentByMonth']);
Route::middleware(['api', 'jwt.patient'])->post('/fetch-specific-range-data', [PatientPagesController::class, 'fetchSpecificRangeAppointmentData']);







// Patients Chating With Providers
Route::middleware(['api', 'jwt.patient'])->get('/chats', [PatientPagesController::class, 'chats']); // no need basically
Route::middleware(['api', 'jwt.patient'])->get('/chat-history', [PatientPagesController::class, 'chatHistory']);
Route::middleware(['api', 'jwt.patient'])->post('/fetch-related-chats', [PatientPagesController::class, 'fetchRelatedChats']);
Route::middleware(['api', 'jwt.patient'])->post('/add-new-message', [PatientPagesController::class, 'addNewMessage']);
Route::middleware(['api', 'jwt.patient'])->post('/send-image-message', [PatientPagesController::class, 'sendImageMessage']);
Route::middleware(['api', 'jwt.patient'])->post('/update-message-seen', [PatientPagesController::class, 'updateSeenStatus']);

// Patient Chating With SugarPros AI (ChatGpt)
Route::middleware(['api', 'jwt.patient'])->get('/sugarpro-ai', [PatientPagesController::class, 'sugarpro_ai']);
Route::middleware(['api', 'jwt.patient'])->post('/chatgpt-response', [PatientPagesController::class, 'chatgptResponse']);
Route::middleware(['api', 'jwt.patient'])->post('/clear-chat-session', [PatientPagesController::class, 'clearChatSession']);

Route::middleware(['api', 'jwt.patient'])->get('/clinical-notes', [PatientPagesController::class, 'ClinicalNotes']);
Route::middleware(['api', 'jwt.patient'])->get('/quest-lab', [PatientPagesController::class, 'QuestLab']);
Route::middleware(['api', 'jwt.patient'])->get('/e-prescriptions', [PatientPagesController::class, 'ePrescription']);



// Result With API Works 
Route::middleware(['api', 'jwt.patient'])->get('/dexcom', [PatientDexcomController::class, 'dexcom']);
Route::middleware(['api', 'jwt.patient'])->get('/connect-dexcom', [PatientDexcomController::class, 'redirectToDexcom']);
Route::middleware(['api', 'jwt.patient'])->get('/dexcom-callback', [PatientDexcomController::class, 'handleDexcomCallback']);

// FatSecret API | Search foods
Route::middleware(['api', 'jwt.patient'])->get('/nutrition-tracker', [PatientFatSecretController::class, 'FatSecret']);
Route::middleware(['api', 'jwt.patient'])->get('/nutrition-tracker/search', [PatientFatSecretController::class, 'getFoods']);
Route::middleware(['api', 'jwt.patient'])->get('/nutrition-tracker/food/{foodId}', [PatientFatSecretController::class, 'getFoodDetails']);
Route::middleware(['api', 'jwt.patient'])->get('/nutrition-tracker/breakfast', [PatientFatSecretController::class, 'getBreakfastFoods']);
Route::middleware(['api', 'jwt.patient'])->get('/nutrition-tracker/lunch', [PatientFatSecretController::class, 'getLunchFoods']);
Route::middleware(['api', 'jwt.patient'])->get('/nutrition-tracker/dinner', [PatientFatSecretController::class, 'getDinnerFoods']);


















// ------------------------------------------------ Provider Panel API Section ---------------------------------------------------------------------
// Provider Authentication Routes
Route::middleware('api')->prefix('provider/auth')->group(function () {
    Route::post('otp/send', [ProviderWorkFlowsController::class, 'sendOTP']);
    Route::post('otp/verify', [ProviderWorkFlowsController::class, 'verifyOTP']);
    Route::post('register', [ProviderWorkFlowsController::class, 'register']);
    Route::post('login', [ProviderWorkFlowsController::class, 'login']);
    Route::post('logout', [ProviderWorkFlowsController::class, 'logout'])->middleware('jwt:provider');
});

// Provider Routes (protected)
Route::middleware(['api', 'jwt.provider'])->group(function () {
    // Dashboard
    Route::get('/provider/dashboard', [ProviderWorkFlowsController::class, 'dashboard']);

    // Patient Records
    Route::get('/provider/patients', [ProviderWorkFlowsController::class, 'getPatients']);
    Route::get('/provider/patients/{patient_id}/records/{type}', [ProviderWorkFlowsController::class, 'getPatientRecords']);

    // Appointments
    Route::get('/provider/appointments', [ProviderWorkFlowsController::class, 'getAppointments']);
    Route::get('/provider/appointments/{appointment_uid}', [ProviderWorkFlowsController::class, 'getAppointmentDetails']);
    Route::post('/provider/appointments/{appointment_uid}/meeting', [ProviderWorkFlowsController::class, 'scheduleMeeting']);
    Route::get('/provider/appointments/{appointment_id}/meeting/start', [ProviderWorkFlowsController::class, 'startMeeting']);

    // Notes
    Route::prefix('provider/appointments/{appointment_uid}/notes')->group(function () {
        // Virtual Notes
        Route::get('virtual', [ProviderWorkFlowsController::class, 'getVirtualNotes']);
        Route::get('virtual/{note_id}', [ProviderWorkFlowsController::class, 'getVirtualNote']);
        Route::post('virtual', [ProviderWorkFlowsController::class, 'createVirtualNote']);
        Route::put('virtual/{note_id}', [ProviderWorkFlowsController::class, 'updateVirtualNote']);
        Route::delete('virtual/{note_id}', [ProviderWorkFlowsController::class, 'deleteVirtualNote']);

        // Clinical Notes
        Route::get('clinical', [ProviderWorkFlowsController::class, 'getClinicalNotes']);
        Route::get('clinical/{note_id}', [ProviderWorkFlowsController::class, 'getClinicalNote']);
        Route::post('clinical', [ProviderWorkFlowsController::class, 'createClinicalNote']);
        Route::put('clinical/{note_id}', [ProviderWorkFlowsController::class, 'updateClinicalNote']);
        Route::delete('clinical/{note_id}', [ProviderWorkFlowsController::class, 'deleteClinicalNote']);

        // Quest Labs
        Route::get('quest-labs', [ProviderWorkFlowsController::class, 'getQuestLabs']);
        Route::get('quest-labs/{quest_id}', [ProviderWorkFlowsController::class, 'getQuestLab']);
        Route::post('quest-labs', [ProviderWorkFlowsController::class, 'createQuestLab']);
        Route::put('quest-labs/{quest_id}', [ProviderWorkFlowsController::class, 'updateQuestLab']);
        Route::delete('quest-labs/{quest_id}', [ProviderWorkFlowsController::class, 'deleteQuestLab']);

        // E-Prescriptions
        Route::get('e-prescriptions', [ProviderWorkFlowsController::class, 'getEPrescriptions']);
        Route::get('e-prescriptions/{prescription_id}', [ProviderWorkFlowsController::class, 'getEPrescription']);
        Route::post('e-prescriptions', [ProviderWorkFlowsController::class, 'createEPrescription']);
        Route::put('e-prescriptions/{prescription_id}', [ProviderWorkFlowsController::class, 'updateEPrescription']);
        Route::delete('e-prescriptions/{prescription_id}', [ProviderWorkFlowsController::class, 'deleteEPrescription']);
    });

    // Notetaker
    Route::prefix('provider/notetaker')->group(function () {
        Route::get('/', [ProviderWorkFlowsController::class, 'getNotetakers']);
        Route::post('/', [ProviderWorkFlowsController::class, 'createNotetaker']);
        Route::get('{appointment_uid}', [ProviderWorkFlowsController::class, 'getNotetakerData']);
        Route::post('{note_uid}/notes', [ProviderWorkFlowsController::class, 'addNotetakerNote']);
        Route::delete('{appointment_uid}', [ProviderWorkFlowsController::class, 'deleteNotetaker']);
    });

    // Chats
    Route::prefix('provider/chats')->group(function () {
        Route::get('/', [ProviderWorkFlowsController::class, 'getChatSessions']);
        Route::get('{patient_id}', [ProviderWorkFlowsController::class, 'getChatMessages']);
        Route::post('{patient_id}', [ProviderWorkFlowsController::class, 'sendMessage']);
        Route::post('{patient_id}/image', [ProviderWorkFlowsController::class, 'sendImageMessage']);
    });

    // AI Chat
    Route::prefix('provider/ai-chat')->group(function () {
        Route::get('/', [ProviderWorkFlowsController::class, 'getAIChatSessions']);
        Route::get('{chat_uid}', [ProviderWorkFlowsController::class, 'getAIChatMessages']);
        Route::post('{chat_uid}', [ProviderWorkFlowsController::class, 'sendAIMessage']);
        Route::delete('{chat_uid}', [ProviderWorkFlowsController::class, 'clearAIChatSession']);
    });

    // Account Management
    Route::prefix('provider/account')->group(function () {
        Route::get('/', [ProviderWorkFlowsController::class, 'getAccountInfo']);
        Route::put('/', [ProviderWorkFlowsController::class, 'updateAccountInfo']);
        Route::post('profile-picture', [ProviderWorkFlowsController::class, 'updateProfilePicture']);

        // Password Management
        Route::post('password/otp', [ProviderWorkFlowsController::class, 'sendPasswordOTP']);
        Route::post('password/verify', [ProviderWorkFlowsController::class, 'verifyPasswordOTP']);
        Route::post('password/change', [ProviderWorkFlowsController::class, 'changePassword']);

        Route::delete('/', [ProviderWorkFlowsController::class, 'deleteAccount']);
    });

    // Notifications
    Route::get('/provider/notifications', [ProviderWorkFlowsController::class, 'getNotifications']);
    Route::delete('/provider/notifications/{notification_id}', [ProviderWorkFlowsController::class, 'deleteNotification']);
});






// Claim MD
// CORS Preflight handler for all claim-md routes
Route::middleware(['api', 'jwt.provider'])->options('/provider/claim-md/{any}', function () {
    return response('', 204)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

// ClaimMD API Routes
Route::middleware(['api', 'jwt.provider'])->prefix('provider')->group(function () {
    // Main interface - Get credentials
    Route::get('/patient-claims-biller', [ProviderClaimMDPatientController::class, 'patientClaimsBiller'])->name('provider.biller');

    // Group claim-md specific routes
    Route::prefix('claim-md')->group(function () {
        // SDK proxy
        Route::match(['get', 'post'], '/proxy', [ProviderClaimMDPatientController::class, 'claimMdProxy'])
            ->withoutMiddleware(['verify.csrf']);

        // API proxy
        Route::post('/api/{endpoint}', [ProviderClaimMDPatientController::class, 'claimMdApi'])
            ->where('endpoint', '.*');

        // Claims management
        Route::get('/get-claims', [ProviderClaimMDPatientController::class, 'getClaims']);
        Route::get('/get-claim/{id}', [ProviderClaimMDPatientController::class, 'getClaim']);
        Route::delete('/delete-claim/{id}', [ProviderClaimMDPatientController::class, 'deleteClaim']);
        
        // Mark appointment as proceed
        Route::get('/mark-appointment-proceed/{appointment_uid}', [ProviderClaimMDPatientController::class, 'markAppointmentProceed']);

        // File operations
        Route::post('/upload', [ProviderClaimMDPatientController::class, 'uploadClaimFile']);
        Route::get('/uploadlist', [ProviderClaimMDPatientController::class, 'getUploadList']);
        Route::delete('/deletefile', [ProviderClaimMDPatientController::class, 'deleteUploadedFile']);
        Route::get('/viewfile', [ProviderClaimMDPatientController::class, 'viewUploadedFile']);
        Route::get('/downloadfile', [ProviderClaimMDPatientController::class, 'downloadFile'])
            ->name('claim-md-provider.download');
    });
});
