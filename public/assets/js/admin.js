
var spinner = `<div class="loader"></div>`;


// email change from settings page
function requestEmailVerification(passedThis) {
    let csrfToken = $('.token').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let curr_email = $(".curr_email").val();


    $.ajax({
        url: '/admin/accout-email-verification',
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
        url: '/admin/accout-otp-verification',
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
        url: '/admin/accout-email-change',
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
        url: '/admin/account-password-verification',
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
        url: '/admin/account-password-otp-verification',
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
        url: '/admin/account-password-change',
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
