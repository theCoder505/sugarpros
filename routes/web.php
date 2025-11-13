<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCredentialsController;
use App\Http\Controllers\BillerAdminController;
use App\Http\Controllers\BillerAuthController;
use App\Http\Controllers\BlogsController;
use App\Http\Controllers\CredentialsController;
use App\Http\Controllers\DexcomController;
use App\Http\Controllers\DxScriptController;
use App\Http\Controllers\EPrescriptionController;
use App\Http\Controllers\FatSecretController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientClaimsMDController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\PatientSubscriptionsController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ProviderWorks;
use App\Http\Controllers\Settings;
use App\Http\Controllers\WebPagesSetupController;
use App\Http\Middleware\AdminCredentials;
use App\Models\Provider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


//Checking if mail send properly.
Route::get('/mail-check', [CredentialsController::class, 'checkMailSending']);


// ----------------------------------- SURFACE WEBSITE ----------------------------------------------- //
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/about-us', [HomeController::class, 'about'])->name('about');
Route::get('/our-service', [HomeController::class, 'service'])->name('service');
Route::get('/reviews', [HomeController::class, 'reviews'])->name('reviews');
Route::get('/all-reviews', [HomeController::class, 'showAllReviews']); // No need of Auth here, checked inside controller.
Route::post('/add-review', [HomeController::class, 'reviewWebsite'])->middleware('patient_loggedin', 'check_if_forms_filled'); // Need Auth on submit a Review
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('/terms-and-conditions', [HomeController::class, 'TermsConditions'])->name('TermsConditions');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/our-blogs', [BlogsController::class, 'blog'])->name('blog');
Route::get('/blogs/{id}/{category}/{title}', [BlogsController::class, 'blog_details'])->name('blog_details');
Route::get('/otp', [HomeController::class, 'otp'])->name('otp'); // Not needed at all, keeping though


Route::get('/patient-panel-api-documentation', [HomeController::class, 'patientPanelDocumentation'])->name('patient_api_doc');
Route::get('/provider-panel-api-documentation', [HomeController::class, 'providerPanelDocumentation'])->name('provider_api_doc');










// ----------------------------------------------------------------------------------------------------- //
// ------------------------------------ PATIENT PORTAL STARTS ------------------------------------------ //
// ----------------------------------------------------------------------------------------------------- //
Route::get('/sign-up', [HomeController::class, 'signup'])->name('sign.up');
Route::post('/send-otp-to-user', [CredentialsController::class, 'sendOTPToUser']);
Route::post('/verify-otp', [CredentialsController::class, 'verifyUsersOTP']);
Route::post('/signup-new-user', [CredentialsController::class, 'signupNewUser']);

Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('/login-existing-user', [CredentialsController::class, 'loginUser']);
Route::get('/forgot-password', [CredentialsController::class, 'forgetPwdPage']);
Route::post('/send-forget-request', [CredentialsController::class, 'sendForgetRequest']);
Route::post('/otp-verification-on-reset', [CredentialsController::class, 'verifyForgetOTP']);
Route::post('/check-password-validity', [CredentialsController::class, 'checkPasswordValidity']);
Route::post('/reset-account-password', [CredentialsController::class, 'resetAccountPassword']);
Route::get('/logout', [CredentialsController::class, 'logout'])->name('logout');








// After Signup, pages Patient must fill up!
Route::get('/basic', [HomeController::class, 'basic'])->name('basic'); // has to like this, handles in controller
Route::post('/complete-user-details', [PatientsController::class, 'userDetailsAdding']);

Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::post('/privacy-form', [PatientsController::class, 'fillupPrivacyForm']);

Route::get('/compliance', [HomeController::class, 'compliance'])->name('compliance');
Route::post('/compliance-form', [PatientsController::class, 'fillupComplianceForm']);

Route::get('/financial-responsibility-aggreement', [HomeController::class, 'financialRespAggreement'])->name('financialRespAggreement');
Route::post('/financial-form', [PatientsController::class, 'fillupFinancialForm']);

Route::get('/agreement-for-self-payment', [HomeController::class, 'agreementSelfPayment'])->name('agreementSelfPayment');
Route::post('/self-payment-form', [PatientsController::class, 'fillupSelfPaymentForm']);











// After Patient signup and fills up all the Related Important Forms
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/hippa-consent-prefference', [CredentialsController::class, 'hippaConsentPreferrence']);
Route::post('/change-language-prefference', [CredentialsController::class, 'changeLanguagePreferrence']);

Route::get('/account', [HomeController::class, 'account'])->name('account')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/update-profile-picture', [PatientsController::class, 'updateProfilePicture'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/update-account-details', [PatientsController::class, 'updateAccountDetails'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/delete-account', [CredentialsController::class, 'DeleteUsersAccount'])->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/settings', [HomeController::class, 'settings'])->name('settings')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/user-accout-email-verification', [CredentialsController::class, 'checkIfEmailExists']);
Route::post('/user-accout-otp-verification', [CredentialsController::class, 'verifyOTPOnEmailChange']);
Route::post('/user-accout-email-change', [CredentialsController::class, 'finalEmailCheckAndChange']);
Route::post('/user-account-password-verification', [CredentialsController::class, 'checkIfEmailExistsForPassword']);
Route::post('/user-account-password-otp-verification', [CredentialsController::class, 'verifyOTPOnPasswordChange']);
Route::post('/user-account-password-change', [CredentialsController::class, 'finalPasswordCheckAndChange']);

Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/delete-notification/{notification_id}', [PatientsController::class, 'deleteNotification'])->middleware('patient_loggedin', 'check_if_forms_filled');





Route::get('/book-appointment', [HomeController::class, 'appointment'])->name('appointment')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/book-new-appoinment', [PatientsController::class, 'bookNewAppointment'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/complete-booking', [PatientsController::class, 'completeBooking'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/payment/success', [PatientsController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel', [PatientsController::class, 'paymentCancel'])->name('payment.cancel');
// Route::get('/payment', [HomeController::class, 'payment'])->name('payment')->middleware('patient_loggedin', 'check_if_forms_filled'); // book will send to here then stripe payment, then to list




// new Patient Subscriptions Pages
Route::get('/subscriptions', [PatientSubscriptionsController::class, 'subscriptions'])->name('patient.subscriptions')->middleware('patient_loggedin');
Route::get('/subscription/{recurring_option}/{plan}', [PatientSubscriptionsController::class, 'subscriptionPlan'])->name('patient.subscriptionPlan')->middleware('patient_loggedin');
Route::post('/complete-subscription', [PatientSubscriptionsController::class, 'completeSubscription'])->name('patient.completeSubscription')->middleware('patient_loggedin');
Route::get('/subscription/success', [PatientSubscriptionsController::class, 'subscriptionSuccess'])->name('subscription.success');
Route::get('/subscription/cancel', [PatientSubscriptionsController::class, 'subscriptionCancel'])->name('subscription.cancel');
// Subscription CRON Job
Route::get('/subscription/cron-job', [PatientSubscriptionsController::class, 'subscriptionCronJob'])->name('subscription.cronJob');


Route::get('/appointments', [HomeController::class, 'appointment_list'])->name('appointment_list')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/join-meeting/{appointment_uid}', [HomeController::class, 'joinMeeting'])->name('join_meeting')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/appointments/{appointment_uid}', [HomeController::class, 'showSpecificAppointment'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/search-appointments-by-month', [PatientsController::class, 'searchByMonth'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/fetch-specific-range-data', [PatientsController::class, 'fetchSpecificRangeData'])->middleware('patient_loggedin', 'check_if_forms_filled');



// Patients Chating With Providers
Route::get('/chats', [HomeController::class, 'chats'])->name('chats')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/send-to-chats/provider/{provider_id}', [HomeController::class, 'sendToSpecificChat'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/add-new-message', [HomeController::class, 'addNewMessage'])->name('add_new_message')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/send-image-message', [HomeController::class, 'sendImageMessage'])->name('image_message')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/update-message-seen', [HomeController::class, 'updateSeenStatus'])->name('seen_status'); // for both patient & user
Route::post('/fetch-related-chats', [HomeController::class, 'fetchRelatedChats'])->name('fetch_chats')->middleware('patient_loggedin', 'check_if_forms_filled');

// Patient Chating With SugarPros AI (ChatGpt)
Route::get('/sugarpro-ai', [HomeController::class, 'sugarpro_ai'])->name('sugarpro_ai')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/chatgpt-response', [HomeController::class, 'chatgptResponse'])->name('chatgptResponse')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::post('/clear-chat-session', [HomeController::class, 'clearChatSession'])->name('clearChatSession')->middleware('patient_loggedin', 'check_if_forms_filled');




// Patients MY RESULTS PAGES ---------------------
Route::get('/send-to-dexcom', [DexcomController::class, 'sendToDexcom'])->name('send_dexcom')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/dexcom', [DexcomController::class, 'dexcom'])->name('dexcom')->middleware('patient_loggedin', 'check_if_forms_filled', 'check_dexcom');
Route::get('/connect-dexcom', [DexcomController::class, 'redirectToDexcom'])->name('connect.dexcom');
Route::get('/dexcom-callback', [DexcomController::class, 'handleDexcomCallback']);

// FatSecret API | Search foods
Route::get('/nutrition-tracker', [FatSecretController::class, 'FatSecret'])->name('fatsecret')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/nutrition-tracker/search', [FatSecretController::class, 'getFoods'])->name('fatsecret.search');
Route::get('/nutrition-tracker/food/{foodId}', [FatSecretController::class, 'getFoodDetails'])->name('fatsecret.food.details'); // Get specific food details
Route::get('/nutrition-tracker/breakfast', [FatSecretController::class, 'getBreakfastFoods'])->name('fatsecret.breakfast'); // Meal-specific searches
Route::get('/nutrition-tracker/lunch', [FatSecretController::class, 'getLunchFoods'])->name('fatsecret.lunch');
Route::get('/nutrition-tracker/dinner', [FatSecretController::class, 'getDinnerFoods'])->name('fatsecret.dinner');
Route::get('/nutrition-tracker/snacks', [FatSecretController::class, 'getSnacksFoods'])->name('fatsecret.snacks');

Route::get('/clinical-notes', [HomeController::class, 'ClinicalNotes'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/quest-lab', [HomeController::class, 'QuestLab'])->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/e-prescriptions', [HomeController::class, 'ePrescription'])->middleware('patient_loggedin', 'check_if_forms_filled');



// ------------------------- Probably unnecessary ---------------------
// Route::get('/join-meeting', [HomeController::class, 'join_meeting'])->name('join_meeting')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/meeting-room', [HomeController::class, 'meeting_room'])->name('meeting_room')->middleware('patient_loggedin', 'check_if_forms_filled');
Route::get('/end-meeting/{message}', [HomeController::class, 'endMeeting'])->name('endMeeting');







// E-Prescription Routes (protected by patient auth middleware)
Route::get('/eprescriptions', [EPrescriptionController::class, 'eprescriptionIndex'])->name('patient.eprescription');

Route::post('/eprescription/authenticate', [EPrescriptionController::class, 'authenticate'])->name('eprescription.authenticate');

Route::post('/webhook/dxscript/hl7', [EPrescriptionController::class, 'receiveHL7Message'])->name('dxscript.webhook');



















// ----------------------------------------------------------------------------------------------------- //
// ------------------------------------ PROVIDER PORTAL STARTS ----------------------------------------- //
// ----------------------------------------------------------------------------------------------------- //
Route::get('/provider/sign-up', [ProviderController::class, 'signup'])->name('provider.signup');
Route::post('/provider/check-and-send-otp', [ProviderController::class, 'checkAndSendOTP']);
// Route::get('/provider/otp', [ProviderController::class, 'otp'])->name('provider.otp');
Route::post('/provider/verify-otp-and-signup', [ProviderController::class, 'verifyOTPAndSignup']);
Route::post('/add-new-provider', [ProviderController::class, 'addNewProvider']);

Route::get('/provider/login', [ProviderController::class, 'login'])->name('provider.login');
Route::post('/provider/sign-in', [ProviderController::class, 'loginProvider']);
Route::get('/provider/logout', [ProviderController::class, 'logoutProvider'])->middleware('check_if_provider');






// provider panel
Route::get('/provider/dashboard', [ProviderController::class, 'dashboard'])->name('provider.dashboard')->middleware('check_if_provider');

Route::get('/provider/patient-records', [ProviderController::class, 'patientRecords'])->middleware('check_if_provider');

Route::get('/provider/notifications', [ProviderController::class, 'providerNotification'])->middleware('check_if_provider');

Route::get('/provider/delete-notification/{notification_id}', [ProviderController::class, 'deleteNotification'])->middleware('check_if_provider');

Route::get('/provider/appointments', [ProviderController::class, 'appointment'])->name('provider.appointment')->middleware('check_if_provider');



Route::get('/provider/account', [ProviderController::class, 'account'])->name('provider.account')->middleware('check_if_provider');

Route::get('/provider/settings', [ProviderController::class, 'providerSettings'])->middleware('check_if_provider');

Route::post('/provider/update-profile-picture', [ProviderController::class, 'updateProfilePicture'])->middleware('check_if_provider');

Route::post('/provider/update-provider-account', [ProviderController::class, 'updateProviderInformation'])->middleware('check_if_provider');

Route::get('/provider/delete-account', [ProviderController::class, 'deleteProviderAccount'])->middleware('check_if_provider');

Route::post('/provider/user-account-password-verification', [ProviderController::class, 'checkIfEmailExistsForPassword'])->middleware('check_if_provider');

Route::post('/provider/user-account-password-otp-verification', [ProviderController::class, 'verifyOTPOnPasswordChange'])->middleware('check_if_provider');

Route::post('/provider/user-account-password-change', [ProviderController::class, 'finalPasswordCheckAndChange'])->middleware('check_if_provider');







Route::get('/provider/virtual-notes/{appointment_uid}', [ProviderController::class, 'virtual_notes'])->name('provider.virtual_notes')->middleware('check_if_provider');

Route::get('/provider/virtual-notes/{appointment_uid}/{note_id}', [ProviderController::class, 'spec_virtual_notes'])->middleware('check_if_provider');

Route::post('/provider/add-virtual-notes', [ProviderController::class, 'addvirtualNotes'])->middleware('check_if_provider');

Route::post('/provider/update-virtual-notes', [ProviderController::class, 'updatevirtualNotes'])->middleware('check_if_provider');

Route::post('/provider/delete-virtual-notes/{note_id}', [ProviderController::class, 'deletevirtualNotes'])->middleware('check_if_provider');




Route::get('/provider/clinical-notes/{appointment_uid}', [ProviderController::class, 'clinical_notes'])->name('provider.clinical_notes')->middleware('check_if_provider');

Route::get('/provider/clinical-notes/{appointment_uid}/{note_id}', [ProviderController::class, 'spec_clinical_notes'])->middleware('check_if_provider');

Route::post('/provider/add-clinical-notes', [ProviderController::class, 'addClinicalNotes'])->middleware('check_if_provider');

Route::post('/provider/update-clinical-notes', [ProviderController::class, 'updateClinicalNotes'])->middleware('check_if_provider');

Route::post('/provider/delete-clinical-notes/{prescription_id}', [ProviderController::class, 'deleteClinicalNotes'])->middleware('check_if_provider');





Route::get('/provider/quest-lab/{appointment_uid}', [ProviderController::class, 'quest_lab'])->name('provider.quest_lab')->middleware('check_if_provider');

Route::get('/provider/quest-lab/{appointment_uid}/{questid}', [ProviderController::class, 'spec_quest_lab'])->middleware('check_if_provider');

Route::post('/provider/add-quest-lab', [ProviderController::class, 'addQuestLabs'])->middleware('check_if_provider');

Route::post('/provider/update-quest-lab', [ProviderController::class, 'updateQuestLabs'])->middleware('check_if_provider');

Route::post('/provider/delete-quest-lab/{prescription_id}', [ProviderController::class, 'deleteQuestLab'])->middleware('check_if_provider');






Route::get('/provider/e-prescription/{appointment_uid}/{prescription_id}', [ProviderController::class, 'spec_eprescription'])->middleware('check_if_provider');




// DxScript Integration
Route::get('/provider/e-prescription/{appointment_uid}', [ProviderController::class, 'e_prescription'])->name('provider.e_prescription')->middleware('check_if_provider');

Route::post('/provider/add-e-prescription', [ProviderController::class, 'addEPrescriptionsNotes'])->middleware('check_if_provider');

Route::get('/provider/prescriptions/{appointment_uid}', [ProviderController::class, 'getAllPrescriptions'])->middleware('check_if_provider');

Route::get('/provider/send-to-dxscript/{prescription_id}', [ProviderController::class, 'sendToDxScript'])->middleware('check_if_provider');

Route::post('/provider/dxscript/get-token', [DxScriptController::class, 'getToken'])->middleware('check_if_provider');
Route::post('/provider/dxscript/prescription-status', [DxScriptController::class, 'updatePrescriptionStatus'])->middleware('check_if_provider');

// Webhook (no auth - DxScript will call this)
Route::post('/webhooks/dxscript/prescription', [DxScriptController::class, 'handlePrescriptionWebhook']);
// Show E-Prescription page
// Route::get('/provider/e-prescription/{appointment_uid}', [ProviderController::class, 'showEPrescriptionPage'])->middleware('check_if_provider');











Route::post('/provider/update-e-prescription', [ProviderController::class, 'updateEPrescriptionsNotes'])->middleware('check_if_provider');

Route::post('/provider/delete-eprescription/{prescription_id}', [ProviderController::class, 'deleteEPrescription'])->middleware('check_if_provider');



// Provider Will see patient past records 
Route::get('/provider/view-appointment/{appointment_uid}', [ProviderWorks::class, 'viewAppointment'])->middleware('check_if_provider');

Route::post('/provider/set-meeting-link', [ProviderController::class, 'setMeetingLink'])->middleware('check_if_provider');

Route::get('/provider/start-meeting/{appointment_id}', [ProviderController::class, 'startMeeting'])->middleware('check_if_provider');

Route::get('/patient/{patient_id}/results/{pageType}', [ProviderWorks::class, 'relatedInformation'])->middleware('check_if_provider');








Route::get('/provider/passio-ai', [ProviderController::class, 'passio'])->middleware('check_if_provider');

Route::get('/provider/chats', [ProviderController::class, 'providerChats'])->name('provider.allChats')->middleware('check_if_provider');

Route::get('/send-to-chats/patient/{patient_id}', [ProviderController::class, 'sendToSpecificChat'])->middleware('check_if_provider');

Route::post('/provider/fetch-users-subscription', [ProviderController::class, 'fetchSubscription'])->middleware('check_if_provider');

Route::post('/provider/fetch-related-chats', [ProviderController::class, 'fetchRelatedChats'])->name('provider.relatedChats')->middleware('check_if_provider');

Route::post('/provider/send-message', [ProviderController::class, 'sendMessage'])->name('provider.sendMessage')->middleware('check_if_provider');

Route::post('/provider/send-image-message', [ProviderController::class, 'sendImageMessage'])->name('provider.sendImageMessage')->middleware('check_if_provider');

Route::get('/provider/ai-chat', [ProviderController::class, 'ai_chat'])->name('provider.ai')->middleware('check_if_provider');

Route::get('/provider/dexcom', [ProviderController::class, 'dexcom'])->name('provider.dexcom')->middleware('check_if_provider');


// new works 
Route::get('/provider/notetaker', [ProviderController::class, 'noteTakerPage'])->middleware('check_if_provider');
Route::post('/provider/process-audio', [ProviderController::class, 'processAudio'])->name('process.audio');




Route::get('/provider/notetaker-speech-ai', [ProviderController::class, 'noteTakerSpeechAI'])->middleware('check_if_provider');

Route::post('/provider/add-notetaker', [ProviderController::class, 'addNoteTaker'])->middleware('check_if_provider');

Route::post('/provider/get-notetaker-data', [ProviderController::class, 'notetakerData'])->middleware('check_if_provider');

Route::post('/provider/add-notes-on-notetaker', [ProviderController::class, 'addNotesOnNotetaker'])->middleware('check_if_provider');

Route::get('/provider/remove-note/{appointment_uid}', [ProviderController::class, 'removeNoteData'])->middleware('check_if_provider');



Route::get('/provider/management', [ProviderController::class, 'management'])->name('provider.management')->middleware('check_if_provider');

Route::get('/provider/sugarpros-ai', [ProviderController::class, 'providerSugarProsAI'])->name('providerSugarProsAI')->middleware('check_if_provider');

Route::post('/provider/chatgpt-response', [ProviderController::class, 'providerChatgptResponse'])->name('providerChatgptResponse')->middleware('check_if_provider');

Route::post('/provider/clear-chat-session', [ProviderController::class, 'providerClearChatSession'])->name('providerClearChatSession')->middleware('check_if_provider');













// Patient Claims Biller For Provider 
Route::options('/provider/claim-md/{any}', function () {
    return response('', 204)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

// ClaimMD Routes
Route::middleware(['check_if_provider'])->group(function () {
    // Main interface
    Route::get('/provider/patient-claims-biller', [PatientClaimsMDController::class, 'patientClaimsBiller'])->name('provider.biller');

    // SDK proxy
    Route::match(['get', 'post'], '/provider/claim-md/proxy', [PatientClaimsMDController::class, 'claimMdProxy'])
        ->withoutMiddleware(['verify.csrf']);

    // API proxy
    Route::post('/provider/claim-md/api/{endpoint}', [PatientClaimsMDController::class, 'claimMdApi'])
        ->where('endpoint', '.*');

    // File operations
    Route::post('/provider/claim-md/upload', [PatientClaimsMDController::class, 'uploadClaimFile']);
    Route::post('/provider/claim-md/uploadlist', [PatientClaimsMDController::class, 'getUploadList']);
    Route::post('/provider/claim-md/deletefile', [PatientClaimsMDController::class, 'deleteUploadedFile']);
    Route::post('/provider/claim-md/viewfile', [PatientClaimsMDController::class, 'viewUploadedFile']);
    Route::get('/provider/claim-md/downloadfile', [PatientClaimsMDController::class, 'downloadFile'])->name('claim-md-provider.download');



    // new PCB
    Route::get('/provider/patient-claims-biller/{appointment_uid}', [PatientClaimsMDController::class, 'specificPatientClaimsBiller']);
    Route::post('/provider/add-new-patient-claims-md', [PatientClaimsMDController::class, 'addNewPatientClaimsMD']);

    Route::get('/provider/claim-md/get-claims', [PatientClaimsMDController::class, 'getClaims']);
    Route::get('/provider/claim-md/get-claim/{id}', [PatientClaimsMDController::class, 'getClaim']);
    Route::delete('/provider/claim-md/delete-claim/{id}', [PatientClaimsMDController::class, 'deleteClaim']);
    Route::get('/provider/mark-appointment-proceed/{appointment_uid}', [PatientClaimsMDController::class, 'markAppointmentProceed']);
    // Done New PCB
});










































// ----------------------------------------------------------------------------------------------------- //
// -------------------------------------- ADMIN PORTAL STARTS ------------------------------------------ //
// ----------------------------------------------------------------------------------------------------- //
Route::get('/admin/login', [AdminCredentialsController::class, 'showLoginForm'])->name('adminlogin');

Route::post('/admin/verify-admin-credentials', [AdminCredentialsController::class, 'verifyAdminCredentials']);

Route::get('/admin/logout', [AdminCredentialsController::class, 'logoutAdmin'])->name('admin.logout');


Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('super')->middleware('admin_loggedin');

Route::get('/admin/get-user-chart-data', [AdminController::class, 'getUserChartData'])->name('admin.getUserChartData')->middleware('admin_loggedin');

Route::get('/admin/providers', [AdminController::class, 'allProviders'])->middleware('admin_loggedin');

Route::post('/admin/change-pod', [AdminController::class, 'changeProvidersPOD'])->middleware('admin_loggedin');

Route::get('/admin/new-provider', [AdminController::class, 'newProvider'])->middleware('admin_loggedin');

Route::post('/admin/add-new-provider', [AdminController::class, 'addNewProvider'])->middleware('admin_loggedin');

Route::get('/admin/patients', [AdminController::class, 'allPatientsRecord'])->middleware('admin_loggedin');

Route::get('/admin/appointments', [AdminController::class, 'allAppointments'])->middleware('admin_loggedin');

Route::get('/admin/view-appointment/{appointment_uid}', [AdminController::class, 'viewAppointment'])->middleware('admin_loggedin');

Route::get('/admin/clinical-notes/{appointment_uid}/{note_id}', [AdminController::class, 'spec_clinical_notes'])->middleware('admin_loggedin');

Route::get('/admin/quest-lab/{appointment_uid}/{questid}', [AdminController::class, 'spec_quest_lab'])->middleware('admin_loggedin');

Route::get('/admin/e-prescription/{appointment_uid}/{prescription_id}', [AdminController::class, 'spec_eprescription'])->middleware('admin_loggedin');

Route::get('/admin/view-results/{patient_id}/{pageType}', [AdminController::class, 'relatedInformation'])->middleware('admin_loggedin');

Route::get('/admin/adress-page', [AdminController::class, 'setupAddressPage'])->middleware('admin_loggedin');

Route::post('/admin/add-street', [AdminController::class, 'addStreet'])->middleware('admin_loggedin');

Route::post('/admin/add-city', [AdminController::class, 'addCity'])->middleware('admin_loggedin');

Route::post('/admin/add-state', [AdminController::class, 'addState'])->middleware('admin_loggedin');

Route::post('/admin/add-zip-code', [AdminController::class, 'addZipCode'])->middleware('admin_loggedin');

Route::post('/admin/add-country-code', [AdminController::class, 'addCountryCode'])->middleware('admin_loggedin');

Route::post('/admin/add-language', [AdminController::class, 'addLanguage'])->middleware('admin_loggedin');






Route::get('/admin/account', [AdminCredentialsController::class, 'adminAccount'])->middleware('admin_loggedin');

Route::post('/admin/accout-email-verification', [AdminCredentialsController::class, 'checkIfEmailExists']);

Route::post('/admin/accout-otp-verification', [AdminCredentialsController::class, 'verifyOTPOnEmailChange']);

Route::post('/admin/accout-email-change', [AdminCredentialsController::class, 'finalEmailCheckAndChange']);



Route::post('/admin/account-password-verification', [AdminCredentialsController::class, 'checkIfEmailExistsForPassword']);

Route::post('/admin/account-password-otp-verification', [AdminCredentialsController::class, 'verifyOTPOnPasswordChange']);

Route::post('/admin/account-password-change', [AdminCredentialsController::class, 'finalPasswordCheckAndChange']);








Route::get('/admin/remove-street/{address}', [AdminController::class, 'removeStreet'])->middleware('admin_loggedin');

Route::get('/admin/remove-city/{address}', [AdminController::class, 'removeCity'])->middleware('admin_loggedin');

Route::get('/admin/remove-state/{address}', [AdminController::class, 'removeState'])->middleware('admin_loggedin');

Route::get('/admin/remove-zip-code/{address}', [AdminController::class, 'removeZipCode'])->middleware('admin_loggedin');

Route::get('/admin/remove-country-code/{address}', [AdminController::class, 'removeCountryCode'])->middleware('admin_loggedin');

Route::get('/admin/remove-language/{lang}', [AdminController::class, 'removeLanguage'])->middleware('admin_loggedin');



Route::get('/admin/sugarpros-ai', [AdminController::class, 'adminSugarProsAI'])->name('adminSugarProsAI')->middleware('admin_loggedin');

Route::post('/admin/chatgpt-response', [AdminController::class, 'adminChatgptResponse'])->name('adminChatgptResponse')->middleware('admin_loggedin');

Route::post('/admin/clear-chat-session', [AdminController::class, 'adminClearChatSession'])->name('adminClearChatSession')->middleware('admin_loggedin');


// admin manage pages
Route::get('/admin/settings', [Settings::class, 'settingsPage'])->middleware('admin_loggedin');

Route::put('/admin/update-settings', [Settings::class, 'updateSettingsPage'])->middleware('admin_loggedin');










// CORS Preflight
Route::options('/admin/claim-md/{any}', function () {
    return response('', 204)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

// ClaimMD Routes
Route::middleware(['admin_loggedin'])->group(function () {
    // Main interface
    Route::get('/admin/patient-claims-biller', [PatientClaimsMDController::class, 'patientClaimsBillerAdmin']);

    // new pcb
    Route::get('/admin/patient-claims-biller/{appointment_uid}', [PatientClaimsMDController::class, 'specificPatientClaimsBiller']);
    Route::post('/admin/add-new-patient-claims-md', [PatientClaimsMDController::class, 'addNewPatientClaimsMD']);


    Route::get('/admin/claim-md/get-claims', [PatientClaimsMDController::class, 'getClaims']);
    Route::get('/admin/claim-md/get-claim/{id}', [PatientClaimsMDController::class, 'getClaim']);
    Route::delete('/admin/claim-md/delete-claim/{id}', [PatientClaimsMDController::class, 'deleteClaim']);
    Route::get('/admin/mark-appointment-proceed/{appointment_uid}', [PatientClaimsMDController::class, 'markAppointmentProceed']);
    //new pcb 


    // SDK proxy
    Route::match(['get', 'post'], '/admin/claim-md/proxy', [PatientClaimsMDController::class, 'claimMdProxy'])
        ->withoutMiddleware(['verify.csrf']);

    // API proxy
    Route::post('/admin/claim-md/api/{endpoint}', [PatientClaimsMDController::class, 'claimMdApi'])
        ->where('endpoint', '.*');

    // File operations
    Route::post('/admin/claim-md/upload', [PatientClaimsMDController::class, 'uploadClaimFile']);
    Route::post('/admin/claim-md/uploadlist', [PatientClaimsMDController::class, 'getUploadList']);
    Route::post('/admin/claim-md/deletefile', [PatientClaimsMDController::class, 'deleteUploadedFile']);
    Route::post('/admin/claim-md/viewfile', [PatientClaimsMDController::class, 'viewUploadedFile']);
    Route::get('/admin/claim-md/downloadfile', [PatientClaimsMDController::class, 'downloadFile'])->name('claim-md.download');
});













Route::get('/admin/faqs', [WebPagesSetupController::class, 'faqManagement'])->middleware('admin_loggedin');

Route::post('/admin/add-new-faq', [WebPagesSetupController::class, 'addNewFaq'])->middleware('admin_loggedin');

Route::post('/admin/update-faq', [WebPagesSetupController::class, 'updaeFaq'])->middleware('admin_loggedin');

Route::get('/admin/delete-faq/{faqID}', [WebPagesSetupController::class, 'deleteFaq'])->middleware('admin_loggedin');


Route::get('/admin/reviews', [WebPagesSetupController::class, 'reviewsManagement'])->middleware('admin_loggedin');

Route::get('/admin/update-review/{reviewID}/{status}', [WebPagesSetupController::class, 'updateReviewStatus'])->middleware('admin_loggedin');

Route::get('/admin/delete-review/{reviewID}', [WebPagesSetupController::class, 'removeReview'])->middleware('admin_loggedin');


Route::get('/admin/services', [WebPagesSetupController::class, 'servicesManagement'])->middleware('admin_loggedin');

Route::post('/admin/add-new-service', [WebPagesSetupController::class, 'addNewService'])->middleware('admin_loggedin');

Route::post('/admin/update-service', [WebPagesSetupController::class, 'updateService'])->middleware('admin_loggedin');

Route::get('/admin/delete-service/{serviceID}', [WebPagesSetupController::class, 'deleteService'])->middleware('admin_loggedin');





Route::get('/admin/categories', [BlogsController::class, 'categories'])->middleware('admin_loggedin');

Route::post('/admin/add-new-category', [BlogsController::class, 'category_store'])->name('category.store')->middleware('admin_loggedin');

Route::post('/admin/update-category', [BlogsController::class, 'updateCategory'])->name('category.update')->middleware('admin_loggedin');

Route::get('/admin/delete-category/{cat_id}', [BlogsController::class, 'removeCategory'])->name('category.remove')->middleware('admin_loggedin');




// Blogs section 
Route::get('/admin/blogs', [BlogsController::class, 'allBlogs'])->middleware('admin_loggedin');

Route::get('/admin/add-new-blog', [BlogsController::class, 'addBlogPage'])->middleware('admin_loggedin');

Route::get('/admin/get-blog/{id}', [BlogsController::class, 'getBlog'])->middleware('admin_loggedin');

Route::post('/admin/add-new-blog', [BlogsController::class, 'addNewBlog'])->middleware('admin_loggedin');

Route::post('/admin/update-blog', [BlogsController::class, 'updateBlog'])->middleware('admin_loggedin');

Route::post('/admin/delete-blog/{blog_id}', [BlogsController::class, 'deleteBlog'])->middleware('admin_loggedin');


// Biller Admins
Route::get('/admin/biller-admins', [BillerAdminController::class, 'billerAdmins'])->middleware('admin_loggedin');
Route::post('/admin/add-new-biller-admin', [BillerAdminController::class, 'addNewBillerAdmin'])->middleware('admin_loggedin');
Route::post('/admin/biller-admin/edit', [BillerAdminController::class, 'updateBillerData'])->middleware('admin_loggedin');
Route::get('/admin/biller-admin/remove/{biller_admin_id}', [BillerAdminController::class, 'removeBiller'])->middleware('admin_loggedin');




// Biller Admin Auth Routes
Route::prefix('biller-admin')->group(function () {
    Route::get('/login', [BillerAuthController::class, 'showLoginForm'])->name('biller-admin.login.form');
    Route::post('/login', [BillerAuthController::class, 'login'])->name('biller-admin.login');
    Route::post('/logout', [BillerAuthController::class, 'logout'])->name('biller-admin.logout');

    Route::get('/appointments', [BillerAuthController::class, 'appointments'])->name('biller-admin.appointments')->middleware('biller-admin');
    Route::get('/dashboard', [PatientClaimsMDController::class, 'patientClaimsBillerAdminBiller'])->name('biller-admin.dashboard')->middleware('biller-admin');

    Route::get('/patient-claims-biller/{appointment_uid}', [PatientClaimsMDController::class, 'specificPatientClaimsBiller'])->middleware('biller-admin');
    Route::post('/add-new-patient-claims-md', [PatientClaimsMDController::class, 'addNewPatientClaimsMD'])->middleware('biller-admin');

    Route::get('/claim-md/get-claims', [PatientClaimsMDController::class, 'getClaims'])->middleware('biller-admin');
    Route::get('/claim-md/get-claim/{id}', [PatientClaimsMDController::class, 'getClaim'])->middleware('biller-admin');
    Route::delete('/claim-md/delete-claim/{id}', [PatientClaimsMDController::class, 'deleteClaim'])->middleware('biller-admin');
    Route::get('/mark-appointment-proceed/{appointment_uid}', [PatientClaimsMDController::class, 'markAppointmentProceed'])->middleware('biller-admin');
});






// Clearing & Optimizing Routes, Views & Website
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('storage:link');
    // Artisan::call('optimize');
    return "Cleared!";
});
