$(".claims").addClass('text-black');

// Diagnosis Functions
function addNewDiagnosis(passedThis) {
    const parentContainer = $(passedThis).closest('.flex.gap-2.items-center.justify-between');
    const serviceContainer = $(passedThis).closest('.service');
    const serviceIndex = serviceContainer.data('index');
    const diagnoses_code = (parentContainer.find('.diagnoses_code').val() || '').trim();
    const diagnoses_text = (parentContainer.find('.diagnoses_text').val() || '').trim();

    if (diagnoses_code && diagnoses_text) {
        const diagnosisText = `${diagnoses_code} - ${diagnoses_text}`;
        const newHtml = `<div class="inline-block">
                            <div class="flex items-center justify-between px-3 py-2 gap-4 bg-gray-100 rounded-full">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium">${diagnosisText}</span>
                                </div>
                                <div class="w-6 h-6 flex justify-center items-center bg-[#2d92b3] rounded-full text-white cursor-pointer"
                                    onclick="removediagnosis(this)">
                                    <i class="fa fa-times"></i>
                                </div>
                            </div>
                        </div>`;
        parentContainer.siblings('.all_diagnoses').append(newHtml);
        parentContainer.find('.diagnoses_code, .diagnoses_text').val('');

        if (!services[serviceIndex]) {
            services[serviceIndex] = getServiceData(serviceContainer);
        }
        if (!services[serviceIndex].diagnoses) {
            services[serviceIndex].diagnoses = [];
        }
        services[serviceIndex].diagnoses.push(diagnosisText);
    } else {
        toastr.error('Fill up both fields to add new diagnosis!');
    }
}

function removediagnosis(passedThis) {
    const serviceContainer = $(passedThis).closest('.service');
    const serviceIndex = serviceContainer.data('index');
    const diagnosisText = $(passedThis).siblings().find('span').text();

    $(passedThis).parent().parent().remove();

    if (services[serviceIndex] && services[serviceIndex].diagnoses) {
        services[serviceIndex].diagnoses = services[serviceIndex].diagnoses.filter(d => d !== diagnosisText);
    }
}

if (typeof services === 'undefined') {
    var services = [];
}

function updateServiceNumbers() {
    $('.service').each(function (index) {
        $(this).find('h3').text(`Service ${index + 1}`);
        $(this).attr('data-index', index);
    });
}

function removeService(passedThis) {
    const serviceIndex = $(passedThis).closest('.service').data('index');
    $(passedThis).closest('.service').remove();
    updateServiceNumbers();

    services.splice(serviceIndex, 1);
    services = $('.service').map(function () {
        return getServiceData($(this));
    }).get();
}

function toggleService(passedThis) {
    $(passedThis).children('.fa').toggleClass('fa-chevron-down');
    $(passedThis).parent().children('.control_line').toggleClass('hidden');
    $(passedThis).parent().parent().parent().parent().children('.service_details').toggleClass('hidden');
}

function clearService(passedThis) {
    const serviceElement = $(passedThis).closest('.service');
    const serviceIndex = serviceElement.data('index');

    serviceElement.find('input, textarea').val('');
    serviceElement.find('.all_diagnoses').empty();

    if (services[serviceIndex]) {
        services[serviceIndex].diagnoses = [];
    }
}

function duplicateService(passedThis) {
    const serviceElement = $(passedThis).closest('.service');
    const serviceIndex = serviceElement.data('index');
    const clone = serviceElement.clone();

    clone.find('input, textarea').val('');
    clone.find('.all_diagnoses').empty();

    serviceElement.after(clone);
    updateServiceNumbers();

    const newServiceIndex = clone.data('index');
    services[newServiceIndex] = getServiceData(clone);

    if (services[serviceIndex] && services[serviceIndex].diagnoses) {
        services[newServiceIndex].diagnoses = [...services[serviceIndex].diagnoses];
        const allDiagnosesContainer = clone.find('.all_diagnoses');
        services[newServiceIndex].diagnoses.forEach(diagnosis => {
            const newHtml = `<div class="inline-block">
                                <div class="flex items-center justify-between px-3 py-2 gap-4 bg-gray-100 rounded-full">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">${diagnosis}</span>
                                    </div>
                                    <div class="w-6 h-6 flex justify-center items-center bg-[#2d92b3] rounded-full text-white cursor-pointer"
                                        onclick="removediagnosis(this)">
                                        <i class="fa fa-times"></i>
                                    </div>
                                </div>
                            </div>`;
            allDiagnosesContainer.append(newHtml);
        });
    }
}

function getServiceData(serviceElement) {
    const serviceIndex = serviceElement.data('index');
    const currentDiagnoses = serviceElement.find('.all_diagnoses .inline-block').map(function () {
        return $(this).find('span').text();
    }).get();

    return {
        modifiers: serviceElement.find('[name="modifiers"]').val(),
        billing_code: serviceElement.find('[name="billing_code"]').val(),
        billing_text: serviceElement.find('[name="billing_text"]').val(),
        diagnoses: currentDiagnoses,
        start_date: serviceElement.find('[name="start_date"]').val(),
        end_date: serviceElement.find('[name="end_date"]').val(),
        units: serviceElement.find('[name="units"]').val(),
        quantity: serviceElement.find('[name="quantity"]').val(),
        billed_charge: serviceElement.find('[name="billed_charge"]').val()
    };
}

function addNewService(passedThis) {
    const serviceCount = $('.service').length;
    const serviceHTML = `
        <div class="service bg-white rounded-xl border-2 border-gray-200" data-index="${serviceCount}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="lg:flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Service ${serviceCount + 1}</h3>
                    <div class="flex items-center space-x-4 controls">
                        <div class="text-[#2d92b3] hover:text-teal-700 text-sm cursor-pointer control_line"
                            onclick="clearService(this)">Clear all</div>
                        <div class="text-gray-600 hover:text-black text-sm cursor-pointer control_line"
                            onclick="duplicateService(this)">Duplicate</div>
                        <div class="text-red-600 hover:text-red-500 text-sm cursor-pointer control_line"
                            onclick="removeService(this)">Delete</div>
                        <div class="text-gray-400 hover:text-gray-600 px-4 cursor-pointer"
                            onclick="toggleService(this)">
                            <i class="fa fa-chevron-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-6 space-y-6 service_details">
                <!-- Modifiers -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Modifiers</label>
                    <div class="relative">
                        <input type="text" placeholder="Search here" name="modifiers[]"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md form-input">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Billing Code -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">BILLING CODE*</label>
                    <div class="flex items-center rounded-md border-2 overflow-hidden">
                        <input type="text" name="billing_code[]" required
                            class="px-3 py-2 border-r border-gray-300 max-w-[75px] outline-none"
                            placeholder="99214">
                        <input type="text" name="billing_text[]" required
                            class="px-3 py-2 border-l border-gray-300 w-full outline-none"
                            placeholder="Office or other outpatient visit for the evaluation...">
                    </div>
                </div>

                <!-- ICD-10 Diagnoses -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ICD-10 DIAGNOSES</label>
                    <div class="flex gap-2 items-center justify-between mt-2">
                        <div class="flex items-center rounded-md border-2 overflow-hidden w-full diagnosis_input_group">
                            <input type="text"
                                class="px-3 py-2 border-r border-gray-300 max-w-[75px] outline-none diagnoses_code"
                                placeholder="E11.9">
                            <input type="text"
                                class="px-3 py-2 border-l border-gray-300 w-full outline-none diagnoses_text"
                                placeholder="Type description...">
                        </div>
                        <div class="w-10 h-10 text-white bg-[#2d92b3] rounded cursor-pointer flex items-center justify-center text-lg"
                            onclick="addNewDiagnosis(this)">
                            <i class="fa fa-plus"></i>
                        </div>
                    </div>
                    <div class="all_diagnoses flex justify-start mt-4 items-center flex-wrap gap-2"></div>
                </div>

                <!-- Date Fields -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">START DATE*</label>
                        <input type="date" placeholder="MM/DD/YYYY" name="start_date[]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md form-input uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">END DATE*</label>
                        <input type="date" placeholder="MM/DD/YYYY" name="end_date[]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md form-input uppercase">
                    </div>
                </div>

                <!-- Units and Quantity -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">UNITS</label>
                        <input type="text" placeholder="Add Units here" name="units[]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">QTY</label>
                        <input type="number" value="1" name="quantity[]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                    </div>
                </div>

                <!-- Billed Charge -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">BILLED CHARGE</label>
                    <input type="text" value="$0.00" name="billed_charge[]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md form-input">
                </div>
            </div>
        </div>`;

    $(".services").append(serviceHTML);
    services[serviceCount] = getServiceData($('.service').last());
}


function prepareFormData() {
    $('[name^="diagnoses["]').remove();
    $('.service').each(function () {
        const serviceIndex = $(this).data('index');
        services[serviceIndex] = getServiceData($(this));
        if (services[serviceIndex].diagnoses && services[serviceIndex].diagnoses.length > 0) {
            services[serviceIndex].diagnoses.forEach((diagnosis) => {
                $('.pcbForm').append(
                    `<input type="hidden" name="diagnoses[${serviceIndex}][]" value="${diagnosis}">`
                );
            });
        } else {
            $('.pcbForm').append(
                `<input type="hidden" name="diagnoses[${serviceIndex}]" value="">`
            );
        }
    });
}

function saveForm(passedThis) {
    $(".action").val('save');
    prepareFormData();
    $('.pcbForm').submit();
}

function sendToBiller(passedThis) {
    $(".action").val('pcb');
    prepareFormData();
    $('.pcbForm').submit();
}