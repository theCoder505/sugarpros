function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}

var activeItem = 0;
var totalSteps = $(".form-step").length;

function proceedToNext(passedThis) {
    var currentStep = $(".form-step").eq(activeItem);
    var inputs = currentStep.find("input, select"); // Get all inputs and selects
    var isValid = true;
    var firstErrorField = null;

    inputs.each(function () {
        // Only validate fields that are required
        if ($(this).prop('required') && !$(this).val()) {
            isValid = false;
            $(this).addClass('border-red-500');

            // Store the first error field to show its label in error message
            if (!firstErrorField) {
                firstErrorField = $(this);
            }
        } else {
            $(this).removeClass('border-red-500');
        }
    });

    if (!isValid) {
        // If we found a field with error, show its label in the message
        if (firstErrorField) {
            var fieldId = firstErrorField.attr('id');
            var fieldLabel = $('label[for="' + fieldId + '"]').text().trim();
            fieldLabel = fieldLabel.replace(/[*:]/g, '').trim(); // Clean up label text
            toastr.error(fieldLabel + ' is required');
        } else {
            toastr.error('Please fill in all required fields');
        }
        return;
    }

    // Proceed to next step if validation passes
    $(".active_form").removeClass('active_form');
    $(".form-step").addClass('hidden');
    activeItem++;
    $(".form-step").eq(activeItem).removeClass('hidden').addClass('active_form');
    updateButtonVisibility();
}

function cancelToBack(passedThis) {
    if (activeItem <= 0) return;

    $(".active_form").removeClass('active_form');
    $(".form-step").addClass('hidden');
    activeItem--;

    $(".form-step").eq(activeItem).removeClass('hidden').addClass('active_form');
    updateButtonVisibility();
}

function updateButtonVisibility() {
    if (activeItem > 0) {
        $(".cancel_btn").removeClass('hidden');
    } else {
        $(".cancel_btn").eq(1).addClass('hidden');
    }

    $("form").off("submit.preventDefault").on("submit.preventDefault", function (e) {
        e.preventDefault();
    });

    if (activeItem == totalSteps) {
        $("form").off("submit.preventDefault");
    } else if (activeItem == totalSteps - 1) {
        $("#nextBtn").text('Submit').attr('type', 'submit');
    } else {
        $("#nextBtn").text('Next').attr('type', 'button');
    }
}

function OnFileChange(input) {
    var file = input.files[0];
    if (file) {
        var reader = new FileReader();

        reader.onload = function (e) {
            let image = '<img id="preview-image" class="max-w-full h-full rounded-md border border-gray-300" src="' + e.target.result + '" />';
            $("#upload-box").html(image);
            $("#file-name").text(file.name).removeClass('hidden');
            $("#upload-box").addClass('border-[#FF6400]');
        };
        reader.readAsDataURL(file);
    }
}

$(document).ready(function () {
    updateButtonVisibility();

    // Only set required attribute to fields that should be required
    $("input[required], select[required]").each(function () {
        $(this).prop('required', true);
    });

    // Special handling for file upload validation
    $("form").on('submit', function (e) {
        var fileUpload = $("#file-upload");
        if (fileUpload.prop('required') && !fileUpload.val()) {
            e.preventDefault();
            toastr.error('Please upload your driver\'s license or state ID');
            fileUpload.closest('.form-step').addClass('active_form').removeClass('hidden');
            $(".form-step").not('.active_form').addClass('hidden');
            activeItem = $(".form-step.active_form").index();
            updateButtonVisibility();
        }
    });
});








function appointmentsByMonth(passedThis) {
    let csrfToken = $('.token').val();
    let selectedMonth = $(passedThis).val();
    $(".spin_items").removeClass("hidden");

    $.ajax({
        url: '/search-appointments-by-month',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            searchingMonth: selectedMonth,
        },
        success: function (response) {
            $(".spin_items").addClass("hidden");
            $("#selectedDate").html("Select Date Range");
            let appointmentList = $('#appointmentList');

            if (response.data.length === 0) {
                const currentYear = new Date().getFullYear();
                // Convert selectedMonth (e.g., "01") to month name
                const monthName = new Date(currentYear + '-' + selectedMonth + '-01').toLocaleString('default', { month: 'long' });
                appointmentList.html('<p class="text-xl text-red-500 font-semibold text-center welcome col-span-1 md:col-span-2">No Appointments Found On ' + monthName + ', ' + currentYear + '!</p>');
                return;
            }

            let html = '';
            response.data.forEach(function (item) {
                html += `
                <div class="bg-white rounded-xl border border-slate-300 p-4 shadow-sm">
                    <div class="flex flex-col sm:flex-row justify-between text-sm mb-2 gap-2">
                        <div>
                            <p class="text-slate-500">name</p>
                            <h2 class="text-[#000000] font-bold">Smith William</h2>
                        </div>

                        <span class="flex items-center gap-1 text-xs sm:text-sm">
                            <i class="fas fa-circle text-[#2889AA] text-[10px]"></i>
                            ${formatTime(item.time)} - ${formatDate(item.date)}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                        <div class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                            <i class="fas fa-envelope text-gray-400"></i>
                            <span class="truncate">username089@gmail.com</span>
                        </div>

                        <div class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                            <i class="fas fa-phone text-gray-400"></i>
                            <span>+92 3306444299</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between bg-[#DBEAFE] text-gray-800 px-3 sm:px-4 py-2 rounded-[42px] mt-3 text-xs sm:text-sm font-semibold">
                        <span class="truncate">
                            55 Water Street New York City, while 111 West 57th Street
                        </span>
                        <div class="w-[28px] h-[28px] sm:w-[33px] sm:h-[33px] bg-white rounded-full flex justify-center items-center flex-shrink-0">
                            <i class="fa-solid fa-location-dot text-[#2889AA] text-[14px] sm:text-[18px]"></i>
                        </div>
                    </div>
                </div>
                `;
            });

            appointmentList.html(html);
        },
        error: function (xhr) {
            toastr.error('Server Error! Try later!');
        }
    });
}

// Helper functions to format date and time like Carbon
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = date.getDate();
    const month = date.toLocaleString('default', { month: 'long' });
    const year = date.getFullYear();

    // Add ordinal suffix to day
    const suffix = getOrdinalSuffix(day);

    return `${day}${suffix} ${month} ${year}`;
}

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const period = hours >= 12 ? 'PM' : 'AM';
    const hour12 = hours % 12 || 12;

    return `${hour12}:${minutes} ${period}`;
}

function getOrdinalSuffix(day) {
    if (day > 3 && day < 21) return 'th';
    switch (day % 10) {
        case 1: return 'st';
        case 2: return 'nd';
        case 3: return 'rd';
        default: return 'th';
    }
}









function showProfilePicture(passedThis) {
    if (passedThis.files && passedThis.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#profilePicture").attr('src', e.target.result);
        };
        reader.readAsDataURL(passedThis.files[0]);
    }
}





function deleteAccount() {
    if (confirm("Are you sure you want to delete your account? This will permanently delete your account and all details.")) {
        window.location.href = '/delete-account';
    }
}


var spinner = `<div class="loader"></div>`;


// email change from settings page
function requestEmailVerification(passedThis) {
    let csrfToken = $('.token').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let curr_email = $(".curr_email").val();


    $.ajax({
        url: '/user-accout-email-verification',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            email: curr_email,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                $(".curr_email_part").addClass("hidden");
                $(".otp_part").removeClass("hidden");
                $(".email_setup").addClass("hidden");

                $(".request_otp").addClass('hidden');
                $(".request_otp_verification").removeClass('hidden');
                $(".change_email").addClass('hidden');
            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            toastr.error('Server Error! Try later!');
        }
    });
}


function requestOTPVerification(passedThis) {
    let csrfToken = $('.token').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let curr_email = $(".curr_email").val();
    let otp = $(".otp").val();


    $.ajax({
        url: '/user-accout-otp-verification',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            email: curr_email,
            otp: otp,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                $(".curr_email_part").addClass("hidden");
                $(".otp_part").addClass("hidden");
                $(".email_setup").removeClass("hidden").addClass('grid');

                $(".request_otp").addClass('hidden');
                $(".request_otp_verification").addClass('hidden');
                $(".change_email").removeClass('hidden');
            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            toastr.error('Server Error! Try later!');
        }
    });
}


function changeEmail(passedThis) {
    let csrfToken = $('.token').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let curr_email = $(".curr_email").val();
    let new_email = $(".new_email").val();
    let current_password = $(".current_password").val();


    $.ajax({
        url: '/user-accout-email-change',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            email: curr_email,
            new_email: new_email,
            current_password: current_password,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                // Reset form
                $(".curr_email").val('');
                $(".new_email").val('');
                $(".current_password").val('');
                $(".otp").val('');

                $(".curr_email_part").removeClass("hidden");
                $(".otp_part").addClass("hidden");
                $(".email_setup").addClass("hidden").removeClass('grid');

                $(".request_otp").removeClass('hidden');
                $(".request_otp_verification").addClass('hidden');
                $(".change_email").addClass('hidden');

            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            toastr.error('Server Error! Try later!');
        }
    });
}








// password change from settings page
// Password toggle functionality
$(document).on('click', '.toggleCurrentPassword, .toggleNewPassword', function () {
    const input = $(this).siblings('input');
    const type = input.attr('type') === 'password' ? 'text' : 'password';
    input.attr('type', type);
});

// New password validation
$('.new_password').on('input', function () {
    const password = $(this).val();
    validatePasswordStrength(password);
});

function validatePasswordStrength(password) {
    const userEmail = $('.curr_email_password').val().split('@')[0];
    const userName = "{{ Auth::user()->name }}";

    // Reset all requirements
    $('.password-requirements li').css('color', '#6b7280');

    // Check each requirement
    if (password.length >= 8) {
        $('.length').css('color', '#10b981');
    }

    if (/[A-Z]/.test(password)) {
        $('.uppercase').css('color', '#10b981');
    }

    if (/[a-z]/.test(password)) {
        $('.lowercase').css('color', '#10b981');
    }

    if (/[0-9]/.test(password)) {
        $('.number').css('color', '#10b981');
    }

    if (/[^A-Za-z0-9]/.test(password)) {
        $('.special').css('color', '#10b981');
    }

    // Check for common words
    const commonWords = ['password', 'qwerty', 'admin', 'welcome', 'login', 'sunshine', 'football',
        'monkey', 'dragon', 'letmein', 'password1', 'baseball', 'superman', 'mustang',
        'shadow', 'master', 'hello', 'freedom', 'whatever', 'trustno1', 'starwars',
        'pepper', 'jordan', 'michelle', 'loveme', 'hockey', 'soccer', 'george',
        'asshole', 'fuckyou', 'summer', 'winter', 'spring', 'autumn', 'iloveyou',
        'princess', 'charlie', 'thomas', 'harley', 'hunter', 'golfer'];

    let hasCommonWord = false;
    for (const word of commonWords) {
        if (password.toLowerCase().includes(word)) {
            hasCommonWord = true;
            break;
        }
    }

    if (!hasCommonWord) {
        $('.not-common').css('color', '#10b981');
    }

    // Check for personal info
    if (!password.toLowerCase().includes(userName.toLowerCase())) {
        $('.not-personal').css('color', '#10b981');
    }
    if (!password.toLowerCase().includes(userEmail.toLowerCase())) {
        $('.not-personal').css('color', '#10b981');
    }
}

function requestPasswordVerification(passedThis) {
    let csrfToken = $('.token_password').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let curr_email = $(".curr_email_password").val();

    $.ajax({
        url: '/user-account-password-verification',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            email: curr_email,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                $(".curr_email_part_password").addClass("hidden");
                $(".otp_part_password").removeClass("hidden");
                $(".password_setup").addClass("hidden");

                $(".request_otp_password").addClass('hidden');
                $(".request_otp_verification_password").removeClass('hidden');
                $(".change_password").addClass('hidden');
            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            $(passedThis).html(prevValue);
            toastr.error('Server Error! Try later!');
        }
    });
}

function verifyPasswordOTP(passedThis) {
    let csrfToken = $('.token_password').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let curr_email = $(".curr_email_password").val();
    let otp = $(".otp_password").val();

    $.ajax({
        url: '/user-account-password-otp-verification',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            email: curr_email,
            otp: otp,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                $(".curr_email_part_password").addClass("hidden");
                $(".otp_part_password").addClass("hidden");
                $(".password_setup").removeClass("hidden").addClass('grid');

                $(".request_otp_password").addClass('hidden');
                $(".request_otp_verification_password").addClass('hidden');
                $(".change_password").removeClass('hidden');
            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            $(passedThis).html(prevValue);
            toastr.error('Server Error! Try later!');
        }
    });
}

function changePassword(passedThis) {
    let csrfToken = $('.token_password').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let curr_email = $(".curr_email_password").val();
    let current_password = $(".change_pass_current_password").val();
    let new_password = $(".new_password").val();

    $.ajax({
        url: '/user-account-password-change',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            email: curr_email,
            current_password: current_password,
            new_password: new_password,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                // Reset form
                $(".curr_email_part_password").removeClass("hidden");
                $(".otp_part_password").addClass("hidden");
                $(".password_setup").addClass("hidden");
                $(".curr_email_password").val('');
                $(".otp_password").val('');
                $(".current_password").val('');
                $(".new_password").val('');

                $(".request_otp_password").removeClass('hidden');
                $(".request_otp_verification_password").addClass('hidden');
                $(".change_password").addClass('hidden');
            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            $(passedThis).html(prevValue);
            toastr.error('Server Error! Try later!');
        }
    });
}










// on other fields js
function changeLanguage(passedThis) {
    let csrfToken = $('.token').val();
    let selectedLanguage = $(passedThis).val();

    $.ajax({
        url: '/change-language-prefference',
        type: 'POST',
        data: {
            _token: csrfToken,
            language: selectedLanguage,
        },
        success: function (response) {
            if (response.type == 'success') {
                toastr.success(response.message);
            }
        },
        error: function (xhr, status, error) {
            let errorMessage = "An error occurred during validation";
            toastr.error(errorMessage);
        }
    });
}




function hippaConsent(passedThis) {
    let csrfToken = $('.token').val();
    let isChecked = $(passedThis).is(':checked');
    console.log(isChecked);


    $.ajax({
        url: '/hippa-consent-prefference',
        type: 'POST',
        data: {
            _token: csrfToken,
            consent: isChecked,
        },
        success: function (response) {
            if (response.type == 'success') {
                toastr.success(response.message);
            }
        },
        error: function (xhr, status, error) {
            let errorMessage = "An error occurred during validation";
            toastr.error(errorMessage);
        }
    });
}




function showSignature(passedThis) {
    if (passedThis.files && passedThis.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(passedThis).parent().children('label').children('img').attr('src', e.target.result);
        };
        reader.readAsDataURL(passedThis.files[0]);
    }
}






















