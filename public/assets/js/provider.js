var spinner = `<div class="loader"></div>`;

function checkSignUp(passedThis) {
    let username = $("input[name='username']").val().trim();
    let email = $("input[name='email']").val().trim();
    let prefix_code = $("select[name='prefix_code']").val().trim();
    let mobile = $("input[name='mobile']").val().trim();
    let provider_role = $("select[name='provider_role']").val().trim();
    let password = $("input[name='password']").val().trim();
    let confirm_password = $("input[name='confirm_password']").val().trim();
    let agreement = $("#terms").is(":checked");
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);


    if (!username || !email || !prefix_code || !mobile || !provider_role || !password || !confirm_password) {
        toastr.error('Fill in all details');
        $(passedThis).html(prevValue);
        return;
    }


    let errors = [];


    if (password.length < 8) {
        errors.push("Password must be at least 8 characters long");
    }


    if (password.toLowerCase().includes(username.toLowerCase()) ||
        password.toLowerCase().includes(email.split('@')[0].toLowerCase())) {
        errors.push("Password cannot contain your username or email");
    }


    const commonWords = [
        'password', 'qwerty', 'admin', 'welcome', 'login', 'sunshine', 'football',
        'monkey', 'dragon', 'letmein', 'password1', 'baseball', 'superman',
        'mustang', 'shadow', 'master', 'hello', 'freedom', 'whatever', 'trustno1',
        'starwars', 'pepper', 'jordan', 'michelle', 'loveme', 'hockey', 'soccer',
        'george', 'asshole', 'fuckyou', 'summer', 'winter', 'spring', 'autumn',
        'iloveyou', 'princess', 'charlie', 'thomas', 'harley', 'hunter', 'golfer'
    ];


    const letterSequences = password.toLowerCase().match(/[a-z]{3,}/g) || [];


    const containsCommonWord = letterSequences.some(seq =>
        commonWords.includes(seq)
    );

    if (containsCommonWord) {
        errors.push("Password cannot contain common words or phrases");
    }


    let hasUpper = /[A-Z]/.test(password);
    let hasLower = /[a-z]/.test(password);
    let hasNumber = /[0-9]/.test(password);
    let hasSpecial = /[^A-Za-z0-9]/.test(password);

    if (!hasUpper || !hasLower || !hasNumber || !hasSpecial) {
        errors.push("Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character");
    }

    if (errors.length > 0) {
        errors.forEach(error => toastr.error(error));
        $(passedThis).html(prevValue);
        return;
    }

    if (password !== confirm_password) {
        toastr.error('Passwords do not match');
        $(passedThis).html(prevValue);
        return;
    }

    if (!agreement) {
        toastr.error('Please accept the agreement');
        $(passedThis).html(prevValue);
        return;
    }

    let csrfToken = $('.token').val();

    $.ajax({
        url: '/provider/check-and-send-otp',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            username: username,
            email: email,
            prefix_code: prefix_code,
            mobile: mobile,
            provider_role: provider_role,
            password: password,
            confirm_password: confirm_password
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                $(".send_otp").addClass('hidden');
                $(".final_signup").removeClass('hidden');
                $(".signup_form").addClass('hidden');
                $(".otp_form").removeClass('hidden');

                $('html, body').animate({
                    scrollTop: $("form").offset().top
                }, 500);
                toastr.success(response.message);
            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            toastr.error('Server Error! Try later!');
            $(passedThis).html(prevValue);
        }
    });
}







function verifyAndSignup(passedThis) {
    let email = $("input[name='email']").val().trim();
    let otp = $("input[name='user_otp']").val().trim();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);

    if (!otp || otp.length !== 6) {
        toastr.error('Please enter a valid 6-digit OTP');
        $(passedThis).html(prevValue);
        return;
    }

    let csrfToken = $('.token').val();

    $.ajax({
        url: '/provider/verify-otp-and-signup',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            email: email,
            otp: otp,
        },
        success: function(response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                $("#providerSignUpForm").off('submit').submit();
            } else {
                toastr.error(response.message || 'Invalid OTP. Please try again.');
            }
        },
        error: function(xhr) {
            toastr.error('Server Error! Try later!');
            $(passedThis).html(prevValue);
        }
    });
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
        window.location.href = '/provider/delete-account';
    }
}














function validatePasswordStrength(password) {
    const userEmail = $('.curr_email_password').val().split('@')[0];
    const userName = "{{ Auth::guard('provider')->user()->name }}";

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
        url: '/provider/user-account-password-verification',
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
        url: '/provider/user-account-password-otp-verification',
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
        url: '/provider/user-account-password-change',
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
