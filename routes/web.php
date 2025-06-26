<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCredentialsController;
use App\Http\Controllers\CredentialsController;
use App\Http\Controllers\DexcomController;
use App\Http\Controllers\FatSecretController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ProviderWorks;
use App\Http\Controllers\Settings;
use App\Models\Provider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



Route::get('/mail-check', [CredentialsController::class, 'checkMailSending']);





Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/about-us', [HomeController::class, 'about'])->name('about');

Route::get('/our-service', [HomeController::class, 'service'])->name('service');

Route::get('/reviews', [HomeController::class, 'reviews'])->name('reviews');

Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');

Route::get('/blog', [HomeController::class, 'blog'])->name('blog');

Route::get('/blog-details', [HomeController::class, 'blog_details'])->name('blog_details');

Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

Route::get('/otp', [HomeController::class, 'otp'])->name('otp');

Route::get('/sign-up', [HomeController::class, 'signup'])->name('sign.up');

Route::get('/login', [HomeController::class, 'login'])->name('login');

Route::get('/logout', [CredentialsController::class, 'logout'])->name('logout');




// patients portal 
Route::get('/basic', [HomeController::class, 'basic'])->name('basic'); //checks in the page


Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy')->middleware('patient_loggedin');

Route::get('/compliance', [HomeController::class, 'compliance'])->name('compliance')->middleware('patient_loggedin');

Route::get('/financial-responsibility-aggreement', [HomeController::class, 'financialRespAggreement'])->name('financialRespAggreement')->middleware('patient_loggedin');

Route::get('/agreement-for-self-payment', [HomeController::class, 'agreementSelfPayment'])->name('agreementSelfPayment')->middleware('patient_loggedin');








Route::get('/book-appointment', [HomeController::class, 'appointment'])->name('appointment')->middleware('patient_loggedin', 'check_if_forms_filled');

// Route::get('/payment', [HomeController::class, 'payment'])->name('payment')->middleware('patient_loggedin', 'check_if_forms_filled'); // book will send to here then stripe payment, then to list

Route::get('/appointments', [HomeController::class, 'appointment_list'])->name('appointment_list')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/join-meeting/{appointment_uid}', [HomeController::class, 'joinMeeting'])->name('join_meeting')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/patient/show-appointment/{appointment_uid}', [HomeController::class, 'showSpecificAppointment'])->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/account', [HomeController::class, 'account'])->name('account')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/send-to-chats/provider/{provider_id}', [HomeController::class, 'sendToSpecificChat'])->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/chats', [HomeController::class, 'chats'])->name('chats')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::post('/add-new-message', [HomeController::class, 'addNewMessage'])->name('add_new_message')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::post('/send-image-message', [HomeController::class, 'sendImageMessage'])->name('image_message')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::post('/update-message-seen', [HomeController::class, 'updateSeenStatus'])->name('seen_status'); // for both patient & user

Route::post('/fetch-related-chats', [HomeController::class, 'fetchRelatedChats'])->name('fetch_chats')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/settings', [HomeController::class, 'settings'])->name('settings')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications')->middleware('patient_loggedin', 'check_if_forms_filled');




// Doing by understanding with docs and help of AI 
Route::get('/dexcom', [DexcomController::class, 'dexcom'])->name('dexcom')->middleware('patient_loggedin', 'check_if_forms_filled', 'check_dexcom');

Route::get('/connect-dexcom', [DexcomController::class, 'redirectToDexcom'])->name('connect.dexcom');

Route::get('/dexcom-callback', [DexcomController::class, 'handleDexcomCallback']);




Route::get('/clinical-notes', [HomeController::class, 'ClinicalNotes'])->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/quest-lab', [HomeController::class, 'QuestLab'])->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/e-prescriptions', [HomeController::class, 'ePrescription'])->middleware('patient_loggedin', 'check_if_forms_filled');





// Probably unnecessary
// Route::get('/join-meeting', [HomeController::class, 'join_meeting'])->name('join_meeting')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/meeting-room', [HomeController::class, 'meeting_room'])->name('meeting_room')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/end-meeting/{message}', [HomeController::class, 'endMeeting'])->name('endMeeting');









// All AI works
Route::get('/fat-secret', [FatSecretController::class, 'FatSecret'])
    ->name('fatsecret')
    ->middleware('patient_loggedin', 'check_if_forms_filled');

// Search foods
// Route::get('/fat-secret/search', [FatSecretController::class, 'getFoods'])->name('fatsecret.search');

Route::get('/fat-secret/search', [FatSecretController::class, 'getFoods'])->name('fatsecret.search');

// Get specific food details
Route::get('/fat-secret/food/{foodId}', [FatSecretController::class, 'getFoodDetails'])->name('fatsecret.food.details');

// Meal-specific searches
Route::get('/fat-secret/breakfast', [FatSecretController::class, 'getBreakfastFoods'])->name('fatsecret.breakfast');

Route::get('/fat-secret/lunch', [FatSecretController::class, 'getLunchFoods'])->name('fatsecret.lunch');

Route::get('/fat-secret/dinner', [FatSecretController::class, 'getDinnerFoods'])->name('fatsecret.dinner');




Route::get('/sugarpro-ai', [HomeController::class, 'sugarpro_ai'])->name('sugarpro_ai')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::post('/chatgpt-response', [HomeController::class, 'chatgptResponse'])->name('chatgptResponse')->middleware('patient_loggedin', 'check_if_forms_filled');

Route::post('/clear-chat-session', [HomeController::class, 'clearChatSession'])->name('clearChatSession')->middleware('patient_loggedin', 'check_if_forms_filled');




















// Post Routes 
Route::post('/send-otp-to-user', [CredentialsController::class, 'sendOTPToUser']);

Route::post('/verify-otp', [CredentialsController::class, 'verifyUsersOTP']);

Route::post('/signup-new-user', [CredentialsController::class, 'signupNewUser']);

Route::post('/login-existing-user', [CredentialsController::class, 'loginUser']);

Route::get('/forgot-password', [CredentialsController::class, 'forgetPwdPage']);

Route::post('/send-forget-request', [CredentialsController::class, 'sendForgetRequest']);

Route::post('/otp-verification-on-reset', [CredentialsController::class, 'verifyForgetOTP']);

Route::post('/check-password-validity', [CredentialsController::class, 'checkPasswordValidity']);

Route::post('/reset-account-password', [CredentialsController::class, 'resetAccountPassword']);





// patient arena
Route::post('/complete-user-details', [PatientsController::class, 'userDetailsAdding']);

Route::post('/book-new-appoinment', [PatientsController::class, 'bookNewAppointment']);


Route::post('/complete-booking', [PatientsController::class, 'completeBooking'])->middleware('patient_loggedin', 'check_if_forms_filled');

Route::get('/payment/success', [PatientsController::class, 'paymentSuccess'])->name('payment.success');

Route::get('/payment/cancel', [PatientsController::class, 'paymentCancel'])->name('payment.cancel');





Route::post('/search-appointments-by-month', [PatientsController::class, 'searchByMonth']);

Route::post('/fetch-specific-range-data', [PatientsController::class, 'fetchSpecificRangeData']);

Route::post('/update-profile-picture', [PatientsController::class, 'updateProfilePicture']);

Route::post('/update-account-details', [PatientsController::class, 'updateAccountDetails']);

Route::get('/delete-account', [CredentialsController::class, 'DeleteUsersAccount']);

Route::get('/delete-notification/{notification_id}', [PatientsController::class, 'deleteNotification']);




Route::post('/user-accout-email-verification', [CredentialsController::class, 'checkIfEmailExists']);

Route::post('/user-accout-otp-verification', [CredentialsController::class, 'verifyOTPOnEmailChange']);

Route::post('/user-accout-email-change', [CredentialsController::class, 'finalEmailCheckAndChange']);



Route::post('/user-account-password-verification', [CredentialsController::class, 'checkIfEmailExistsForPassword']);

Route::post('/user-account-password-otp-verification', [CredentialsController::class, 'verifyOTPOnPasswordChange']);

Route::post('/user-account-password-change', [CredentialsController::class, 'finalPasswordCheckAndChange']);

Route::post('/hippa-consent-prefference', [CredentialsController::class, 'hippaConsentPreferrence']);

Route::post('/change-language-prefference', [CredentialsController::class, 'changeLanguagePreferrence']);




// forms after signup 
Route::post('/privacy-form', [PatientsController::class, 'fillupPrivacyForm']);

Route::post('/compliance-form', [PatientsController::class, 'fillupComplianceForm']);

Route::post('/financial-form', [PatientsController::class, 'fillupFinancialForm']);

Route::post('/self-payment-form', [PatientsController::class, 'fillupSelfPaymentForm']);


















// Provider Portal starts 
// signup procedure 
Route::get('/provider/sign-up', [ProviderController::class, 'signup'])->name('provider.signup');

Route::post('/provider/check-and-send-otp', [ProviderController::class, 'checkAndSendOTP']);

Route::post('/provider/verify-otp-and-signup', [ProviderController::class, 'verifyOTPAndSignup']);

Route::post('/add-new-provider', [ProviderController::class, 'addNewProvider']);

Route::get('/provider/otp', [ProviderController::class, 'otp'])->name('provider.otp');


// login procedure 
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





Route::get('/provider/e-prescription/{appointment_uid}', [ProviderController::class, 'e_prescription'])->name('provider.e_prescription')->middleware('check_if_provider');

Route::get('/provider/e-prescription/{appointment_uid}/{prescription_id}', [ProviderController::class, 'spec_eprescription'])->middleware('check_if_provider');

Route::post('/provider/add-e-prescription', [ProviderController::class, 'addEPrescriptionsNotes'])->middleware('check_if_provider');

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

Route::post('/provider/fetch-related-chats', [ProviderController::class, 'fetchRelatedChats'])->name('provider.relatedChats')->middleware('check_if_provider');

Route::post('/provider/send-message', [ProviderController::class, 'sendMessage'])->name('provider.sendMessage')->middleware('check_if_provider');

Route::post('/provider/send-image-message', [ProviderController::class, 'sendImageMessage'])->name('provider.sendImageMessage')->middleware('check_if_provider');

Route::get('/provider/ai-chat', [ProviderController::class, 'ai_chat'])->name('provider.ai')->middleware('check_if_provider');

Route::get('/provider/dexcom', [ProviderController::class, 'dexcom'])->name('provider.dexcom')->middleware('check_if_provider');

Route::get('/provider/patient-claims-biller', [ProviderController::class, 'patientClaimsBiller'])->name('provider.biller')->middleware('check_if_provider');


// new works 
Route::get('/provider/notetaker', [ProviderController::class, 'noteTakerPage'])->middleware('check_if_provider');

Route::post('/provider/add-notetaker', [ProviderController::class, 'addNoteTaker'])->middleware('check_if_provider');

Route::post('/provider/get-notetaker-data', [ProviderController::class, 'notetakerData'])->middleware('check_if_provider');

Route::post('/provider/add-notes-on-notetaker', [ProviderController::class, 'addNotesOnNotetaker'])->middleware('check_if_provider');

Route::get('/provider/remove-note/{appointment_uid}', [ProviderController::class, 'removeNoteData'])->middleware('check_if_provider');



Route::get('/provider/management', [ProviderController::class, 'management'])->name('provider.management')->middleware('check_if_provider');

Route::get('/provider/sugarpros-ai', [ProviderController::class, 'providerSugarProsAI'])->name('providerSugarProsAI')->middleware('check_if_provider');

Route::post('/provider/chatgpt-response', [ProviderController::class, 'providerChatgptResponse'])->name('providerChatgptResponse')->middleware('check_if_provider');

Route::post('/provider/clear-chat-session', [ProviderController::class, 'providerClearChatSession'])->name('providerClearChatSession')->middleware('check_if_provider');

















// super dashboard
Route::get('/admin/login', [AdminCredentialsController::class, 'showLoginForm'])->name('adminlogin');

Route::post('/admin/verify-admin-credentials', [AdminCredentialsController::class, 'verifyAdminCredentials'])->name('admin.login');

Route::get('/admin/logout', [AdminCredentialsController::class, 'logoutAdmin'])->name('admin.logout');


Route::get('/admin/dashboard', [AdminController::class, 'super'])->name('super')->middleware('admin_loggedin');

Route::get('/admin/providers', [AdminController::class, 'allProviders'])->middleware('admin_loggedin');

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


Route::get('/admin/remove-street/{address}', [AdminController::class, 'removeStreet'])->middleware('admin_loggedin');

Route::get('/admin/remove-city/{address}', [AdminController::class, 'removeCity'])->middleware('admin_loggedin');

Route::get('/admin/remove-state/{address}', [AdminController::class, 'removeState'])->middleware('admin_loggedin');

Route::get('/admin/remove-zip-code/{address}', [AdminController::class, 'removeZipCode'])->middleware('admin_loggedin');

Route::get('/admin/remove-country-code/{address}', [AdminController::class, 'removeCountryCode'])->middleware('admin_loggedin');

Route::get('/admin/remove-language/{lang}', [AdminController::class, 'removeLanguage'])->middleware('admin_loggedin');



Route::get('/admin/sugarpros-ai', [AdminController::class, 'adminSugarProsAI'])->name('adminSugarProsAI')->middleware('admin_loggedin');

Route::post('/admin/chatgpt-response', [AdminController::class, 'adminChatgptResponse'])->name('adminChatgptResponse')->middleware('admin_loggedin');

Route::post('/admin/clear-chat-session', [AdminController::class, 'adminClearChatSession'])->name('adminClearChatSession')->middleware('admin_loggedin');


Route::get('/admin/settings', [Settings::class, 'settingsPage'])->middleware('admin_loggedin');

Route::put('/admin/update-settings', [Settings::class, 'updateSettingsPage'])->middleware('admin_loggedin');

Route::get('/admin/patient-claims-biller', [AdminController::class, 'patientClaimsBiller'])->middleware('admin_loggedin');








Route::get('/404', [ProviderController::class, 'error'])->name('provider.error');






Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('storage:link');
    Artisan::call('optimize');
    return "Cleared!";
});



