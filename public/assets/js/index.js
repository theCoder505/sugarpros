document.querySelectorAll('.toggle-faq').forEach(btn => {
    btn.addEventListener('click', () => {
        const content = btn.nextElementSibling;
        const isOpen = !content.classList.contains('hidden');

        document.querySelectorAll('.faq-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.toggle-faq .icon').forEach(i => i.textContent = '+');

        if (!isOpen) {
            content.classList.remove('hidden');
            btn.querySelector('.icon').textContent = '−';
        }
    });
});


const menuBtn = document.getElementById('mobileMenuBtn');
const mobileMenu = document.getElementById('mobileMenu');
const iconMenu = document.getElementById('iconMenu');
const iconClose = document.getElementById('iconClose');

menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    iconMenu.classList.toggle('hidden');
    iconClose.classList.toggle('hidden');
});






var spinner = `<div class="loader"></div>`;


function sendOTP(passedThis) {
    let username = ($(".username").val()).trim();
    let email = ($(".email").val()).trim();
    let prefix_code = ($(".prefix_code").val()).trim();
    let mobile = ($(".mobile").val()).trim();
    let password = ($(".password").val()).trim();
    let confirm_password = ($(".confirm_password").val()).trim();
    let aggrement = $(".aggrement").is(":checked");
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);

    if (!username || !email || !prefix_code || !mobile || !password || !confirm_password) {
        toastr.error('Fill in all details');
        $(passedThis).html(prevValue);
        return;
    }

    // Password validation
    let errors = [];

    // At least 8 characters
    if (password.length < 8) {
        errors.push("Password must be at least 8 characters long");
    }

    // Doesn't contain username or email
    if (password.toLowerCase().includes(username.toLowerCase()) ||
        password.toLowerCase().includes(email.split('@')[0].toLowerCase())) {
        $(".uemail_err").removeClass('hidden');
    } else {
        $(".uemail_err").addClass('hidden');
    }

    // Check for complete meaningful words (3+ letters)
    const commonWords = [
        'password', 'qwerty', 'admin', 'welcome', 'login', 'sunshine', 'football',
        'monkey', 'dragon', 'letmein', 'password1', 'baseball', 'superman',
        'mustang', 'shadow', 'master', 'hello', 'freedom', 'whatever', 'trustno1',
        'starwars', 'pepper', 'jordan', 'michelle', 'loveme', 'hockey', 'soccer',
        'george', 'asshole', 'fuckyou', 'summer', 'winter', 'spring', 'autumn',
        'iloveyou', 'princess', 'charlie', 'thomas', 'harley', 'hunter', 'golfer'
    ];

    // Extract all letter sequences of 3+ characters
    const letterSequences = password.toLowerCase().match(/[a-z]{3,}/g) || [];

    // Check if any of these sequences are in our common words list
    const containsCommonWord = letterSequences.some(seq =>
        commonWords.includes(seq)
    );

    if (containsCommonWord) {
        errors.push("Password cannot contain common words or phrases");
    }

    // Check character categories
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

    if (!aggrement) {
        toastr.error('Please accept the agreement');
        $(passedThis).html(prevValue);
        return;
    }

    let csrfToken = $('.token').val();

    $.ajax({
        url: '/send-otp-to-user',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            username: username,
            email: email,
            prefix_code: prefix_code,
            mobile: mobile,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                $(".send_otp").addClass('hidden');
                $(".final_signup").removeClass('hidden');
                $(".signup_form").addClass('hidden');
                $(".otp_form").removeClass('hidden');
                // ScrollTop to $("form")
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
        }
    });
}



function verifyOTP(passedThis) {
    let csrfToken = $('.token').val();
    let username = ($(".username").val()).trim();
    let email = ($(".email").val()).trim();
    let one_time_password = ($(".one_time_password").val()).trim();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);


    $.ajax({
        url: '/verify-otp',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            username: username,
            email: email,
            otp: one_time_password,
        },
        success: function (response) {
            $(passedThis).html(prevValue);
            if (response.type == 'success') {
                toastr.success(response.message);
                $(passedThis).parent().submit();
            } else if (response.type == 'no_mail_error') {
                toastr.error(response.message);
                $(".send_otp").removeClass('hidden');
                $(".signup_form").removeClass('hidden');
                $(".final_signup").addClass('hidden');
                $(".otp_form").addClass('hidden');
            } else {
                toastr.error(response.message || 'Server Error! Try later!');
            }
        },
        error: function (xhr) {
            toastr.error('Server Error! Try later!');
        }
    });
}











function sendForgetOTP(passedThis) {
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let csrfToken = $('.token').val();
    let email = ($(".email").val()).trim();

    if (!email) {
        toastr.error("Enter Your Email First!");
        $(passedThis).html(prevValue);
    } else {
        $.ajax({
            url: '/send-forget-request',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                email: email,
            },
            success: function (response) {
                $(passedThis).html(prevValue);
                if (response.type == 'success') {
                    toastr.success(response.message);
                    // $(passedThis).parent().submit();
                    $('.email_part').addClass('hidden');
                    $('.otp_part').removeClass('hidden');
                    $('.set_new_password').addClass('hidden');

                    $('.send_otp').addClass('hidden');
                    $('.verify_otp').removeClass('hidden');
                    $('.reset_password').addClass('hidden');
                } else {
                    toastr.error(response.message || 'Server Error! Try later!');
                }
            },
            error: function (xhr) {
                toastr.error('Server Error! Try later!');
            }
        });
    }
}





function submitOTP(passedThis) {
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);
    let csrfToken = $('.token').val();
    let email = ($(".email").val()).trim();
    let otp = ($(".otp").val()).trim();

    if (!otp) {
        toastr.error("Enter the valid OTP!");
        $(passedThis).html(prevValue);
    } else {
        $.ajax({
            url: '/otp-verification-on-reset',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                email: email,
                otp: otp,
            },
            success: function (response) {
                $(passedThis).html(prevValue);
                if (response.type == 'success') {
                    toastr.success(response.message);
                    // $(passedThis).parent().submit();
                    $('.email_part').addClass('hidden');
                    $('.otp_part').addClass('hidden');
                    $('.set_new_password').removeClass('hidden');

                    $('.send_otp').addClass('hidden');
                    $('.verify_otp').addClass('hidden');
                    $('.reset_password').removeClass('hidden');
                } else {
                    toastr.error(response.message || 'Server Error! Try later!');
                }
            },
            error: function (xhr) {
                toastr.error('Server Error! Try later!');
            }
        });
    }
}




function finalForgetSubmit(passedThis) {
    let csrfToken = $('.token').val();
    let prevValue = $(passedThis).html();
    $(passedThis).html(spinner);

    let password = ($(".password").val()).trim();
    let confirm_password = ($(".confirm_password").val()).trim();
    let email = $(".email").val(); // Assuming you have an email field
    let otp = $(".otp").val(); // Assuming you have an OTP field

    // Basic check for empty fields
    if (!password || !confirm_password) {
        toastr.error("Please fill in all fields");
        $(passedThis).html(prevValue);
        return;
    }

    // Send AJAX request to backend for validation
    $.ajax({
        url: '/check-password-validity',
        type: 'POST',
        data: {
            _token: csrfToken,
            email: email,
            otp: otp,
            password: password,
            confirm_password: confirm_password
        },
        success: function (response) {
            if (response.type === 'success' && response.message === 'verified') {
                // Password is valid, submit the form
                $(passedThis).parent().submit();
            }
            else if (response.type === 'error') {
                // Handle error responses
                if (Array.isArray(response.messages)) {
                    // Show all error messages
                    response.messages.forEach(message => {
                        toastr.error(message);
                    });
                }
                else if (response.message) {
                    // Show single error message
                    toastr.error(response.message);
                }
                else {
                    // Fallback error message
                    toastr.error("An error occurred during validation");
                }
                $(passedThis).html(prevValue);
            }
            else {
                // Handle unexpected response format
                toastr.error("Unexpected response from server");
                $(passedThis).html(prevValue);
            }
        },
        error: function (xhr, status, error) {
            // Handle AJAX errors
            let errorMessage = "An error occurred during validation";

            // Try to parse the response if it contains JSON error details
            try {
                let response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                } else if (response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                console.error("Couldn't parse error response", e);
            }

            toastr.error(errorMessage);
            $(passedThis).html(prevValue);
            console.error(error);
        }
    });
}





