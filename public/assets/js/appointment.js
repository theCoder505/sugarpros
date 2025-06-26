// Initialize the date picker
const picker = new Litepicker({
    element: document.getElementById('dateRangePicker'),
    singleMode: false,
    format: 'MMM DD',
    setup: (picker) => {
        picker.on('selected', (start, end) => {
            const formattedRange = `${start.format('MMM DD')} - ${end.format('MMM DD')}`;
            document.getElementById('selectedDate').textContent = formattedRange;

            // Fetch data for the selected range
            fetchAppointmentsByDateRange(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        });
    }
});

document.getElementById('datePickerBtn').addEventListener('click', () => {
    picker.show();
});

function fetchAppointmentsByDateRange(startDate, endDate) {
    let csrfToken = $('.token').val();

    // Show loader
    $('.spin_items').removeClass('hidden');
    $('#appointmentList').addClass('hidden');

    $.ajax({
        url: '/fetch-specific-range-data',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function (response) {
            renderAppointments(response.data);
        },
        error: function (xhr) {
            toastr.error('Server Error! Try later!');
        },
        complete: function () {
            $('.spin_items').addClass('hidden');
            $('#appointmentList').removeClass('hidden');
        }
    });
}

function renderAppointments(appointments) {
    let appointmentList = $('#appointmentList');

    if (appointments.length === 0) {
        appointmentList.html(
            '<p class="text-xl text-red-500 font-semibold text-center welcome col-span-1 md:col-span-2">No Appointments Yet!</p>'
        );
        return;
    }

    let html = '';
    appointments.forEach(function (item) {
        html += `
        <div class="bg-white rounded-xl border border-slate-300 p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between text-sm mb-2 gap-2">
                <div>
                    <p class="text-slate-500">name</p>
                    <h2 class="text-[#000000] font-bold">${item.patient_name || 'Smith William'}</h2>
                </div>

                <span class="flex items-center gap-1 text-xs sm:text-sm">
                    <i class="fas fa-circle text-[#2889AA] text-[10px]"></i>
                    ${formatTime(item.time)} - ${formatDate(item.date)}
                </span>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
                <div class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                    <i class="fas fa-envelope text-gray-400"></i>
                    <span class="truncate">${item.email || 'username089@gmail.com'}</span>
                </div>

                <div class="flex p-2 items-center border border-slate-200 bg-slate-50 rounded-[42px] gap-2 text-xs sm:text-sm text-gray-600">
                    <i class="fas fa-phone text-gray-400"></i>
                    <span>${item.phone || '+92 3306444299'}</span>
                </div>
            </div>
            <div class="flex items-center justify-between bg-[#DBEAFE] text-gray-800 px-3 sm:px-4 py-2 rounded-[42px] mt-3 text-xs sm:text-sm font-semibold">
                <span class="truncate">
                    ${item.address || '55 Water Street New York City, while 111 West 57th Street'}
                </span>
                <div class="w-[28px] h-[28px] sm:w-[33px] sm:h-[33px] bg-white rounded-full flex justify-center items-center flex-shrink-0">
                    <i class="fa-solid fa-location-dot text-[#2889AA] text-[14px] sm:text-[18px]"></i>
                </div>
            </div>
        </div>
        `;
    });

    appointmentList.html(html);
}

// Helper functions (same as before)
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = date.getDate();
    const month = date.toLocaleString('default', {
        month: 'long'
    });
    const year = date.getFullYear();
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
        case 1:
            return 'st';
        case 2:
            return 'nd';
        case 3:
            return 'rd';
        default:
            return 'th';
    }
}