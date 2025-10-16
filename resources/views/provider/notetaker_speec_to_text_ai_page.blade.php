@extends('layouts.provider')

@section('title', 'SugarPros Notetaker - Speech AI')

@section('link')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
@endsection

@section('style')
    <style>
        .audio-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .record-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .record-btn.recording {
            background: #28a745;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .result-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>
@endsection

@section('content')
    @include('layouts.provider_header')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">SugarPros Notetaker - Speech to SOAP Notes</h4>
                    </div>
                    <div class="card-body">
                        <!-- Appointment Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="appointment_select" class="form-label">Select Appointment (Optional)</label>
                                <select class="form-select" id="appointment_select">
                                    <option value="">-- No Appointment Selected --</option>
                                    @foreach ($all_appointments as $appointment)
                                        <option value="{{ $appointment->id }}">
                                            {{ $appointment->date }} - {{ $appointment->patient_name ?? 'Unknown Patient' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Audio Input Section -->
                        <div class="audio-container">
                            <h5>Record or Upload Audio</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex flex-column align-items-center">
                                        <button id="recordBtn" class="record-btn mb-3">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                        <div id="timer">00:00</div>
                                        <small class="text-muted mt-2">Click to start/stop recording</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex flex-column">
                                        <label for="audioUpload" class="form-label">Or Upload Audio File</label>
                                        <input type="file" id="audioUpload" class="form-control" accept="audio/*">
                                        <small class="text-muted">Supported formats: MP3, WAV, M4A, OGG (Max 10MB)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <audio id="audioPreview" controls style="width: 100%; display: none;"></audio>
                            </div>
                        </div>

                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Processing...</span>
                            </div>
                            <p class="mt-2">Processing audio... This may take a few moments.</p>
                        </div>

                        <!-- Results Section -->
                        <div id="resultsSection" class="result-section" style="display: none;">
                            <h5>Results</h5>

                            <!-- Transcript -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Transcript</label>
                                <div id="transcriptResult" class="p-3 bg-white rounded border" style="min-height: 100px;">
                                    <!-- Transcript will appear here -->
                                </div>
                            </div>

                            <!-- SOAP Notes -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">SOAP Notes</label>
                                <div id="soapResult" class="p-3 bg-white rounded border" style="min-height: 150px;">
                                    <!-- SOAP notes will appear here -->
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button id="saveNoteBtn" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save Notes
                                </button>
                                <button id="newRecordingBtn" class="btn btn-primary">
                                    <i class="fas fa-redo"></i> New Recording
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let mediaRecorder;
        let audioChunks = [];
        let isRecording = false;
        let recordingTimer;

        // Elements
        const recordBtn = document.getElementById('recordBtn');
        const timer = document.getElementById('timer');
        const audioPreview = document.getElementById('audioPreview');
        const audioUpload = document.getElementById('audioUpload');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const resultsSection = document.getElementById('resultsSection');
        const transcriptResult = document.getElementById('transcriptResult');
        const soapResult = document.getElementById('soapResult');
        const saveNoteBtn = document.getElementById('saveNoteBtn');
        const newRecordingBtn = document.getElementById('newRecordingBtn');

        // Timer function
        function updateTimer() {
            let seconds = 0;
            recordingTimer = setInterval(() => {
                seconds++;
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                timer.textContent =
                    `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        // Start recording
        async function startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, {
                        type: 'audio/wav'
                    });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioPreview.src = audioUrl;
                    audioPreview.style.display = 'block';

                    // Process the audio
                    processAudioFile(audioBlob);
                };

                mediaRecorder.start();
                isRecording = true;
                recordBtn.classList.add('recording');
                recordBtn.innerHTML = '<i class="fas fa-stop"></i>';
                updateTimer();
            } catch (error) {
                console.error('Error starting recording:', error);
                alert('Error accessing microphone. Please check permissions.');
            }
        }

        // Stop recording
        function stopRecording() {
            if (mediaRecorder && isRecording) {
                mediaRecorder.stop();
                isRecording = false;
                recordBtn.classList.remove('recording');
                recordBtn.innerHTML = '<i class="fas fa-microphone"></i>';
                clearInterval(recordingTimer);

                // Stop all tracks
                mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
        }

        // Process audio file (both recorded and uploaded)
        async function processAudioFile(audioBlob) {
            const formData = new FormData();
            formData.append('audio', audioBlob, 'recording.wav');

            const appointmentId = document.getElementById('appointment_select').value;
            if (appointmentId) {
                formData.append('appointment_id', appointmentId);
            }

            // Show loading, hide results
            loadingSpinner.style.display = 'block';
            resultsSection.style.display = 'none';

            try {
                const response = await fetch('{{ route('process.audio') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Display results
                    transcriptResult.textContent = result.transcript;
                    soapResult.textContent = result.soap_notes;
                    resultsSection.style.display = 'block';

                    // Store note ID for saving
                    saveNoteBtn.dataset.noteId = result.note_id;
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error processing audio. Please try again.');
            } finally {
                loadingSpinner.style.display = 'none';
            }
        }

        // Event Listeners
        recordBtn.addEventListener('click', () => {
            if (!isRecording) {
                startRecording();
            } else {
                stopRecording();
            }
        });

        audioUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    return;
                }

                audioPreview.src = URL.createObjectURL(file);
                audioPreview.style.display = 'block';
                processAudioFile(file);
            }
        });

        saveNoteBtn.addEventListener('click', () => {
            alert('Notes saved successfully!');
            // You can add additional save functionality here
        });

        newRecordingBtn.addEventListener('click', () => {
            // Reset interface
            audioPreview.style.display = 'none';
            resultsSection.style.display = 'none';
            audioUpload.value = '';
            transcriptResult.textContent = '';
            soapResult.textContent = '';
            timer.textContent = '00:00';
        });
    </script>
@endsection
