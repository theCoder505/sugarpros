$(document).ready(function () {
    const table = $('#pa_Table').DataTable({
        paging: true,
        searching: true,
        info: false,
        lengthChange: false,
        pageLength: 10,
        ordering: false,
        language: {
            search: "",
            searchPlaceholder: "Search...",
        },
        dom: 't<"flex justify-center mt-4"p>',
    });

    $('#tableSearch').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Get the modal
    const modal = document.getElementById("patientModal");
    const modalContent = document.getElementById("modalPatientContent");

    // Get the <span> element that closes the modal
    const span = document.getElementsByClassName("close")[0];

    // Function to safely parse JSON
    function safeParseJSON(jsonString) {
        try {
            return JSON.parse(jsonString);
        } catch (e) {
            console.error("Error parsing JSON:", e);
            return null;
        }
    }

    // Function to render document sections
    const renderDocumentSection = function (title, items, fields) {
        // Ensure items is an array, if null/undefined create empty array
        const safeItems = Array.isArray(items) ? items : [];

        if (safeItems.length === 0) {
            return `<div class="document-section">
                        <h3>${title}</h3>
                        <p>No ${title.toLowerCase()} found.</p>
                    </div>`;
        }

        let content = `<div class="document-section">
                    <h3>${title}</h3>`;

        safeItems.forEach(function (item) {
            content += `<div class="document-item">`;
            fields.forEach(function (field) {
                if (item[field] !== undefined && item[field] !== null) {
                    const fieldName = field.split('_').map(function (word) {
                        return word.charAt(0).toUpperCase() + word.slice(1);
                    }).join(' ');

                    // Special handling for signature fields
                    if (field === 'license' && item[field]) {
                        content += `<img src="/${item[field]}" alt="License" class="block w-full md:w-auto h-auto md:max-h-[200px] mb-2 mt-2 rounded" />`;
                    } else if ((field === 'patients_signature' || field === 'representative_signature') && item[field]) {
                        content += `<p class="text-sm mb-1">
                            <span class="font-semibold">${fieldName}:</span>
                            <img src="/${item[field]}" alt="Signature" class="inline-block h-8 ml-2" style="vertical-align:middle;" />
                        </p>`;
                    } else {
                        content += `<p class="text-sm mb-1"><span class="font-semibold">${fieldName}:</span> ${item[field]}</p>`;
                    }
                }
            });
            content += `</div>`;
        });

        content += `</div>`;
        return content;
    };

    // When the user clicks on the button, open the modal
    $(document).on('click', '.view-details-btn', function () {
        const patientDataString = $(this).attr('data-patient-data');
        const patientName = $(this).attr('data-patient-name');

        // Safely parse the JSON data
        const patientData = safeParseJSON(patientDataString);

        if (!patientData) {
            console.error("Invalid patient data");
            return;
        }

        let content = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="col-span-1">
                ${patientData.profile_picture ? `<img src="/${patientData.profile_picture}" alt="Profile Picture" class="w-full mb-4 mx-auto block rounded-md">` : '<p class="text-gray-500">No profile picture available</p>'}
            </div>
            <div class="col-span-2">
                <div class="border shadow-md rounded-md p-4">
                <h3 class="font-semibold mb-3">Activity Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-orange-50 p-3 rounded text-center">
                <p class="text-xs text-orange-600">Upcoming Appointments</p>
                <p class="text-xl font-bold">${patientData.upcoming_appointments ? patientData.upcoming_appointments.toString().padStart(2, '0') : '00'}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded text-center">
                <p class="text-xs text-blue-600">Ongoing Appointments</p>
                <p class="text-xl font-bold">${patientData.ongoing_appointments ? patientData.ongoing_appointments.toString().padStart(2, '0') : '00'}</p>
                </div>
                <div class="bg-red-50 p-3 rounded text-center">
                <p class="text-xs text-red-600">Missed Appointments</p>
                <p class="text-xl font-bold">${patientData.missed_appointments ? patientData.missed_appointments.toString().padStart(2, '0') : '00'}</p>
                </div>
                <div class="bg-green-50 p-3 rounded text-center">
                <p class="text-xs text-green-600">Completed Appointments</p>
                <p class="text-xl font-bold">${patientData.completed_appointments ? patientData.completed_appointments.toString().padStart(2, '0') : '00'}</p>
                </div>
                <a href="/admin/view-results/${patientData.patient_id}/virtual-notes" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                <p class="text-xs text-gray-600">Total Virtual Notes</p>
                <p class="text-xl font-bold">${patientData.virtual_notes ? patientData.virtual_notes.toString().padStart(2, '0') : '00'}</p>
                </a>
                <a href="/admin/view-results/${patientData.patient_id}/clinical-notes" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                <p class="text-xs text-gray-600">Total Clinical Notes</p>
                <p class="text-xl font-bold">${patientData.clinical_notes ? patientData.clinical_notes.toString().padStart(2, '0') : '00'}</p>
                </a>
                <a href="/admin/view-results/${patientData.patient_id}/e-prescription" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                <p class="text-xs text-gray-600">Total E-Prescriptions</p>
                <p class="text-xl font-bold">${patientData.eprescriptions ? patientData.eprescriptions.toString().padStart(2, '0') : '00'}</p>
                </a>
                <a href="/admin/view-results/${patientData.patient_id}/quest-lab" class="bg-gray-50 p-3 rounded text-center hover:shadow-lg hover:bg-white">
                <p class="text-xs text-gray-600">Total QuestLabs</p>
                <p class="text-xl font-bold">${patientData.questlabs ? patientData.questlabs.toString().padStart(2, '0') : '00'}</p>
                </a>
                </div>
                </div>
            </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-1 border rounded-lg p-4 shadow-md">
                <h3 class="font-semibold mb-2">Basic Information</h3>
                <p class="text-sm mb-2"><span class="font-semibold">Full Name:</span> <span class="capitalize">${patientData.fname || ''} ${patientData.mname || ''} ${patientData.lname || ''}</span></p>
                <p class="text-sm mb-2"><span class="font-semibold">Patient ID:</span> ${patientData.patient_id || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">DOB:</span> ${patientData.dob || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Age:</span> ${patientData.age || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Gender:</span> <span class="capitalize">${patientData.gender || 'N/A'}</span></p>
                <p class="text-sm mb-2"><span class="font-semibold">Member Since:</span> ${patientData.created_at || 'N/A'}</p>
                <p class="text-sm mb-2 capitalize"><span class="font-semibold">Language:</span> ${patientData.language || 'N/A'}</p>

                <h3 class="font-semibold mb-2 border-t-2 border-gray-400 mt-4 pt-4">Contact Information</h3>
                <p class="text-sm mb-2"><span class="font-semibold">Address:</span> ${patientData.zip_code || ''}, ${patientData.street || ''}, ${patientData.city || ''}, ${patientData.state || ''}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Phone:</span> ${patientData.phone || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Email:</span> <a class="text-blue-500" href="mailto:${patientData.email || ''}">${patientData.email || 'N/A'}</a></p>
            </div>
            
            <div class="col-span-1 border rounded-lg p-4 shadow-md">
                <h3 class="font-semibold mb-2">Insurance Information</h3>
                <p class="text-sm mb-2"><span class="font-semibold">Medicare:</span> ${patientData.medicare_number || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Group:</span> ${patientData.group_number || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">SSN:</span> ${patientData.ssn || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Insurance Provider:</span> ${patientData.insurance_provider || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Insurance Plan Number:</span> ${patientData.insurance_plan_number || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Insurance Group:</span> ${patientData.insurance_group_number || 'N/A'}</p>

                <h3 class="font-semibold mb-2 border-t-2 border-gray-400 mt-4 pt-4">Emergency Contact</h3>
                <p class="text-sm mb-2"><span class="font-semibold">Name:</span> ${patientData.emmergency_name || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Relationship:</span> ${patientData.emmergency_relationship || 'N/A'}</p>
                <p class="text-sm mb-2"><span class="font-semibold">Phone:</span> ${patientData.emmergency_phone || 'N/A'}</p>
            </div>
            </div>
            
            ${renderDocumentSection('Picture of patient\'s driver license or state ID', patientData.license ? [{ license: patientData.license }] : [], ['license'])}

            ${renderDocumentSection('Privacy & Consent', patientData.privacy_forms, ['fname', 'lname', 'date', 'users_message', 'notice_of_privacy_practice', 'patients_name', 'representatives_name', 'service_taken_date', 'relation_with_patient'])}
            
            ${renderDocumentSection('Compliance Forms', patientData.compliance_forms, ['patients_name', 'dob', 'patients_signature', 'patients_dob', 'representative_signature', 'representative_dob', 'nature_with_patient'])}
            
            ${renderDocumentSection('Financial Responsibility Agreements', patientData.financial_agreements, ['user_name', 'patients_name', 'patients_signature_date'])}
            
            ${renderDocumentSection('Self-Payment', patientData.sle_payments, ['user_name', 'patients_name', 'patients_signature_date'])}
            
            <div class="mt-6">
            <p class="text-sm mb-2"><span class="font-semibold">Last Login:</span> ${patientData.last_logged_in || 'N/A'}</p>
            <p class="text-sm mb-2"><span class="font-semibold">Last Activity:</span> ${patientData.last_activity || 'N/A'}</p>
            </div>
            `;

        modalContent.innerHTML = content;
        modal.style.display = "block";
    });

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
    };

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
});