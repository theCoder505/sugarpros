@extends('layouts.provider')

@section('title', 'SugarPros Notetaker')

@section('link')

@endsection

@section('style')

@endsection


@section('content')
    @include('layouts.provider_header')


    <div class="min-h-screen p-4 bg-gray-100 md:p-6">
        <div class="grid p-4 mx-auto bg-white max-w-7xl rounded-xl ">

            <h2 class="text-[24px]  text-black font-semibold mb-4">SugarPros Notetaker</h2>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @forelse ($all_appointments as $item)
                    <div
                        class="item group bg-white border-2 cursor-pointer border-gray-200 rounded-xl shadow-lg p-6 transition-all duration-300 hover:border-purple-500 hover:shadow-2xl focus-within:ring-2 focus-within:ring-purple-400">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-calendar-alt text-purple-500 text-2xl mr-3"></i>
                            <div>
                                <div class="font-semibold text-lg text-gray-800">ID:
                                    {{ $item->appointment_uid ?? 'Appointment' }}</div>
                                <div class="text-sm text-gray-500">{{ $item->date ?? '' }}</div>
                            </div>
                        </div>
                        @php $hasNotetaker = false; @endphp
                        @foreach ($notetakers as $notetaker)
                            @if ($notetaker->appointment_id == $item->appointment_uid)
                                @php $hasNotetaker = true; @endphp
                                {{-- Add A icon that indicates video already added here, icon like the Select A Video File we have in the form --}}
                                <div
                                    class="flex flex-col items-center px-4 py-6 bg-gray-50 text-purple-500 rounded-lg shadow-inner tracking-wide uppercase border border-purple-200 cursor-pointer hover:bg-purple-100 transition mb-6">
                                    <i class="fas fa-video text-3xl text-purple-500"></i>
                                    <span class="text-sm text-purple-500">Video already added</span>
                                </div>
                                <button
                                    class="view_note flex items-center justify-center gap-2 px-4 py-2 mt-2 border-2 border-purple-500 text-purple-500 rounded-lg shadow hover:bg-purple-600 hover:text-white transition focus:outline-none focus:ring-2  w-full"
                                    data-id="{{ $item->appointment_uid }}" onclick="activateNote(this)">
                                    <i class="fas fa-play"></i>
                                    View Note
                                </button>
                                @break
                            @endif
                        @endforeach
                        @if (!$hasNotetaker)
                            <form action="/provider/add-notetaker" enctype="multipart/form-data" method="post"
                                class="flex flex-col gap-3 notetaker_form">
                                @csrf
                                <input type="hidden" name="appointment_uid" value="{{ $item->appointment_uid }}">
                                <label
                                    class="flex flex-col items-center px-4 py-6 bg-gray-50 text-purple-500 rounded-lg shadow-inner tracking-wide uppercase border border-purple-200 cursor-pointer hover:bg-purple-100 transition group-focus-within:ring-2 group-focus-within:ring-purple-400">
                                    <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                                    <span class="text-sm leading-tight">Select a video file</span>
                                    <input type="file" name="video_file" class="hidden" required accept="video/*"
                                        onchange="addVideo(this)">
                                </label>
                                <div data-appid="{{ $item->appointment_uid }}"
                                    class="hidden items-center gap-2 p-2 bg-gray-100 rounded">
                                    <i class="fas fa-video text-purple-500"></i>
                                    <span class="text-sm truncate"></span>
                                </div>
                                <button type="submit"
                                    class="flex items-center justify-center gap-2 px-4 py-2 mt-2 bg-purple-500 text-white rounded-lg shadow hover:bg-purple-600 transition focus:outline-none focus:ring-2 focus:ring-purple-400">
                                    <i class="fas fa-upload"></i>
                                    <span>Upload File</span>
                                    <div class="spinner hidden ml-2">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500 py-8">
                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                        <div>No appointments found.</div>
                    </div>
                @endforelse
            </div>



            <div class="video_section hidden mt-12">
                <h2 class="text-[24px]  text-black font-semibold mb-4">Note Over Appointment ID: <span
                        class="appintmentID"></span> </h2>
                <input type="hidden" class="token" value="{{ csrf_token() }}">

                <div class="flex flex-col gap-6 lg:flex-row">

                    <div class="flex-1">
                        <div class="relative overflow-hidden rounded-lg shadow-lg aspect-w-16 aspect-h-9 group">
                            <video id="videoPlayer" class="w-full h-full rounded-lg" preload="metadata" controls
                                controlsList="nodownload">
                                <source src="{{ asset('assets/image/dummy.mp4') }}" type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        <a href="/"
                            class="removing_link flex items-center justify-center gap-2 w-full px-4 py-3 mt-6 rounded-lg bg-gradient-to-r from-red-500 to-pink-500 text-white font-semibold shadow-lg hover:from-red-600 hover:to-pink-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-300">
                            <i class="fa fa-trash text-lg"></i>
                            <span>Remove Entire Record</span>
                        </a>
                    </div>





                    <!-- Transcript Panel -->
                    <div class="w-full p-4 lg:pt-0 rounded-lg shadow lg:w-1/3 bg-gray-50"
                        style="backdrop-filter: blur(111.51000213623047px);">
                        <h3 class="text-[20px] font-semibold mb-2 text-center">Your Notes</h3>

                        <div class="relative flex items-center justify-center w-full px-4 my-4 md:mt-0 md:w-auto">
                            <i
                                class="absolute text-xl text-black transform -translate-y-1/2 fas fa-search left-6 top-1/2"></i>
                            <input type="text" placeholder="Search something..." onkeyup="searchNote(this)"
                                class="w-full md:w-[350px] pl-10 pr-3 py-3 text-sm text-slate-900 placeholder:text-black rounded-md bg-white focus:outline-none shadow">
                        </div>



                        <div id="transcript" class="space-y-4 text-sm h-[320px] overflow-y-auto"></div>


                        <form action="/provider/add-notes-on-notetaker" method="POST" class="mt-6 flex flex-col gap-3">
                            @csrf
                            <input type="hidden" name="notetaker_id" class="notetaker_id" value="">
                            <div
                                class="flex items-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-400 focus:outline-none overflow-hidden">
                                <input type="text" name="note"
                                    class="flex-1 px-4 py-3 text-sm bg-white shadow-sm placeholder-gray-400 focus:border-none focus:outline-none"
                                    placeholder="Type your note here..." required>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-purple-500 to-indigo-500 text-white font-semibold rounded-r-lg shadow hover:from-purple-600 hover:to-indigo-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-400 text-sm lg:text-md">
                                    <i class="fas fa-plus mr-2 hidden lg:block"></i>
                                    Add Note
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>



        </div>
    </div>



@endsection

@section('script')
    <script>
        function activateNote(passedThis) {
            let selectedAppointmentID = $(passedThis).attr('data-id');
            $(".appintmentID").html(selectedAppointmentID);
            $(".item").removeClass("border-purple-500").addClass('border-gray-200');
            $(passedThis).closest(".item").addClass("border-purple-500").removeClass('border-gray-200');
            $(".removing_link").attr('href', '/provider/remove-note/' + selectedAppointmentID);
            $("#transcript").html('');

            $.ajax({
                url: '/provider/get-notetaker-data',
                type: 'POST',
                data: {
                    appointment_uid: selectedAppointmentID,
                    _token: $('.token').val()
                },
                success: function(response) {
                    // Example: set video source and notetaker_id
                    if (response.video_url) {
                        $("#videoPlayer source").attr("src", '/' + response.video_url);
                        $("#videoPlayer")[0].load();
                    }
                    if (response.notetaker_id) {
                        $(".notetaker_id").val(response.notetaker_id);
                    }

                    // Inside the success callback of the activateNote function
                    if (response.notes && response.notes.length > 0) {
                        let notesHtml = '';
                        response.notes.forEach(function(note) {
                            // Format created_at as 11:39 PM, 26/06/25
                            const date = new Date(note.created_at);
                            const options = {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            };
                            const time = date.toLocaleTimeString('en-US', options);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = String(date.getFullYear()).slice(-2);
                            const formattedDate = `${time}, ${day}/${month}/${year}`;

                            notesHtml += `<div class="w-full text-[16px] space-y-4 transcript-note" data-note="${note.note_text.toLowerCase()}">
                                            <div class="p-2 hover:bg-blue-100 rounded transcript-block">
                                                <span class="float-right text-xs text-gray-500">${formattedDate}</span>
                                                <p>${note.note_text}</p>
                                            </div>
                                        </div>`;
                        });
                        $("#transcript").html(notesHtml);
                    }

                    $(".video_section").removeClass('hidden');
                    $('html, body').animate({
                        scrollTop: $(".video_section").offset().top - 40
                    }, 600);
                },
                error: function(xhr) {
                    toastr.error('Failed to load note data.');
                }
            });
        }





        function searchNote(input) {
            const searchTerm = $(input).val().toLowerCase().trim();
            const $notes = $("#transcript .transcript-note");

            if (searchTerm === '') {
                // If search is empty, show all notes
                $notes.show();
                return;
            }

            $notes.each(function() {
                const noteText = $(this).data('note');
                if (noteText.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }




        function addVideo(input) {
            const file = input.files[0];
            if (file) {
                const $parentDiv = $(input).closest('.item');
                const appointmentId = $parentDiv.find('input[name="appointment_uid"]').val();
                const $fileInfoDiv = $(`[data-appid="${appointmentId}"]`);
                const $fileNameSpan = $fileInfoDiv.find('span');

                // Show file info
                $fileInfoDiv.removeClass('hidden').addClass('flex');
                $fileNameSpan.text(file.name);

                // Show video icon
                const $videoIcon = $fileInfoDiv.find('i');
                $videoIcon.removeClass().addClass(file.type.includes('video') ?
                    'fas fa-video text-purple-500' :
                    'fas fa-file-video text-purple-500');
            }
        }

        // Add form submission handler to show loading spinner
        $('.notetaker_form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const $uploadIcon = $button.find('.fa-upload');
            const $textSpan = $button.find('span');

            // Remove spinner if exists, add progress bar if not present
            let $progress = $form.find('.upload-progress');
            if ($progress.length === 0) {
                $progress = $(
                    '<div class="upload-progress w-full bg-gray-200 rounded mt-2 overflow-hidden"><div class="progress-bar bg-blue-500 text-white text-xs text-center transition-all duration-200" style="width:0%">0%</div></div>'
                );
                $button.after($progress);
            }
            $progress.show();
            const $bar = $progress.find('.progress-bar');
            $bar.css('width', '0%').text('0%');

            $uploadIcon.addClass('hidden');
            $textSpan.text('Uploading...');
            $button.prop('disabled', true);

            // Prepare form data
            const formData = new FormData(this);

            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = $.ajaxSettings.xhr();
                    if (xhr.upload) {
                        xhr.upload.addEventListener('progress', function(evt) {
                            if (evt.lengthComputable) {
                                const percent = Math.round((evt.loaded / evt.total) * 100);
                                $bar.css('width', percent + '%').text(percent + '%');
                            }
                        }, false);
                    }
                    return xhr;
                },
                success: function(response) {
                    $bar.css('width', '100%').text('100%');
                    $textSpan.text('Uploaded!');
                    setTimeout(function() {
                        $progress.fadeOut();
                        $button.prop('disabled', false);
                        $uploadIcon.removeClass('hidden');
                        $textSpan.text('Upload File');
                        // Optionally reload or update UI
                        location.reload();
                    }, 800);
                },
                error: function() {
                    $bar.css('width', '0%').text('0%');
                    $textSpan.text('Upload Failed');
                    $button.prop('disabled', false);
                    $uploadIcon.removeClass('hidden');
                }
            });
        });
    </script>
@endsection
