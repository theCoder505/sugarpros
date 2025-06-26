function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
}


var activeItem = 0;
var totalSteps = $(".form-step").length;

function proceedToNext(passedThis) {
    var currentStep = $(".form-step").eq(activeItem);
    var inputs = currentStep.find("input:not([type='file']), select");
    var isValid = true;

    inputs.each(function () {
        if ($(this).prop('required') && !$(this).val()) {
            isValid = false;
            $(this).addClass('border-red-500');
        } else {
            $(this).removeClass('border-red-500');
        }
    });

    if (!isValid) {
        toastr.error('Please fillup the required fields!');
        return;
    };

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
    console.log(activeItem, totalSteps);

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
    }else if (activeItem == totalSteps - 1) {
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
    $("input, select").not("[type='file'], [type='radio']").attr('required', true);
});