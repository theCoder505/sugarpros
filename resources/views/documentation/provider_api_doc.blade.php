<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <title>Provider Panel API Documentation</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --border-radius: 4px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 300px;
            background-color: var(--dark-color);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }

        .sidebar-header {
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
            color: white;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 5px;
        }

        .sidebar-nav a {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
        }

        .sidebar-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav a.active {
            background-color: var(--primary-color);
            font-weight: bold;
        }

        .sidebar-section {
            margin-bottom: 20px;
        }

        .sidebar-section-title {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--light-color);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 30px;
        }

        .section {
            margin-bottom: 40px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
        }

        .section-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .section-header h2 {
            color: var(--dark-color);
            font-size: 1.8rem;
        }

        .section-header p {
            color: #666;
            margin-top: 5px;
        }

        .endpoint {
            margin-bottom: 30px;
            border: 1px solid #eee;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .endpoint-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .method {
            display: inline-block;
            padding: 5px 10px;
            border-radius: var(--border-radius);
            font-weight: bold;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-right: 15px;
            min-width: 70px;
            text-align: center;
        }

        .method.get {
            background-color: #61affe;
            color: white;
        }

        .method.post {
            background-color: #49cc90;
            color: white;
        }

        .method.put {
            background-color: #fca130;
            color: white;
        }

        .method.delete {
            background-color: #f93e3e;
            color: white;
        }

        .endpoint-url {
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .endpoint-body {
            padding: 20px;
        }

        .endpoint-section {
            margin-bottom: 20px;
        }

        .endpoint-section h4 {
            margin-bottom: 10px;
            color: var(--dark-color);
            font-size: 1.1rem;
        }

        .code-block {
            background-color: #282c34;
            border-radius: 6px;
            padding: 16px;
            margin: 16px 0;
            overflow-x: auto;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: 'Fira Code', 'Consolas', 'Monaco', 'Andale Mono', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            color: #abb2bf;
        }

        .code-block pre {
            margin: 0;
            white-space: pre-wrap;
        }

        .code-block.json {
            position: relative;
        }

        .code-block.json::before {
            content: 'JSON';
            position: absolute;
            top: 0;
            right: 0;
            background-color: #eee;
            padding: 2px 5px;
            font-size: 0.7rem;
            border-bottom-left-radius: var(--border-radius);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table th, .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 0.7rem;
            font-weight: bold;
            border-radius: 3px;
            margin-right: 5px;
        }

        .badge.required {
            background-color: var(--accent-color);
            color: white;
        }

        .badge.optional {
            background-color: var(--warning-color);
            color: white;
        }

        .response-example {
            margin-top: 20px;
        }

        .response-example h5 {
            margin-bottom: 10px;
            font-size: 1rem;
            color: var(--dark-color);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                position: static;
                height: auto;
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .endpoint-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .method {
                margin-bottom: 10px;
                margin-right: 0;
            }
        }

        /* Toggle for mobile */
        .mobile-menu-toggle {
            display: none;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .mobile-menu-toggle {
                display: block;
            }

            .sidebar {
                display: none;
            }

            .sidebar.active {
                display: block;
            }
        }

        /* Scrollspy highlight */
        .section.highlight {
            animation: highlight 2s ease;
        }

        @keyframes highlight {
            0% { background-color: rgba(52, 152, 219, 0.2); }
            100% { background-color: white; }
        }
    </style>

    <style>
        
        .code-block pre {
            margin: 0;
            white-space: pre;
            overflow-x: auto;
        }

        .code-block::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: bold;
            color: #abb2bf;
            background-color: rgba(255, 255, 255, 0.1);
            border-bottom-left-radius: 6px;
        }

        .code-block.json::before {
            content: 'JSON';
        }

        /* Syntax Highlighting Colors */
        .code-block .token.property {
            color: #e06c75;
        }

        .code-block .token.string {
            color: #98c379;
        }

        .code-block .token.number {
            color: #d19a66;
        }

        .code-block .token.boolean {
            color: #56b6c2;
        }

        .code-block .token.null {
            color: #56b6c2;
        }

        .code-block .token.keyword {
            color: #c678dd;
        }

        .code-block .token.punctuation {
            color: #abb2bf;
        }

        /* Copy Button */
        .code-block-copy {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            color: #abb2bf;
            font-size: 0.75rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .code-block:hover .code-block-copy {
            opacity: 1;
        }

        .code-block-copy:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .code-block-copy.copied {
            color: #98c379;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1>SugarPros Provider API</h1>
                <p>Version 1.0.0</p>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Introduction</h3>
                <ul class="sidebar-nav">
                    <li><a href="#introduction">Overview</a></li>
                    <li><a href="#authentication">Authentication</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Core Features</h3>
                <ul class="sidebar-nav">
                    <li><a href="#dashboard">Dashboard</a></li>
                    <li><a href="#patient-records">Patient Records</a></li>
                    <li><a href="#appointments">Appointments</a></li>
                    <li><a href="#notes-management">Notes Management</a></li>
                    <li><a href="#notetaker">Notetaker</a></li>
                    <li><a href="#chats">Chats</a></li>
                    <li><a href="#ai-chat">AI Chat</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Account</h3>
                <ul class="sidebar-nav">
                    <li><a href="#account-management">Account Management</a></li>
                    <li><a href="#notifications">Notifications</a></li>
                </ul>
            </div>

            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Integrations</h3>
                <ul class="sidebar-nav">
                    <li><a href="#claimmd-integration">ClaimMD Integration</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <button class="mobile-menu-toggle" id="mobileMenuToggle">☰ Menu</button>

            <!-- Introduction Section -->
            <section class="section" id="introduction">
                <div class="section-header">
                    <h2>Introduction</h2>
                    <p>API documentation for the Provider Panel</p>
                </div>
                <p>The Provider Panel API allows healthcare providers to manage patient records, appointments, clinical notes, and more through a RESTful interface.</p>
                <p>All API endpoints require authentication via JWT token in the Authorization header.</p>
                <div class="code-block">
                    Authorization: Bearer [JWT_TOKEN]
                </div>
            </section>

            <!-- Authentication Section -->
            <section class="section" id="authentication">
                <div class="section-header">
                    <h2>Authentication</h2>
                    <p>Endpoints for provider authentication</p>
                </div>

                <!-- Send OTP -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/auth/otp/send</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Send OTP to provider's email for registration verification.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "email": "provider@example.com",
    "username": "Dr. Smith",
    "prefix_code": "Dr.",
    "provider_role": "doctor",
    "mobile": "1234567890",
    "password": "password123",
    "password_confirmation": "password123"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "OTP sent successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verify OTP -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/auth/otp/verify</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Verify OTP for registration.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "email": "provider@example.com",
    "otp": "123456"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "OTP verified successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Register -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/auth/register</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Register a new provider account.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "username": "Dr. Smith",
    "email": "provider@example.com",
    "prefix_code": "Dr.",
    "mobile": "1234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "provider_role": "doctor"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Registration successful",
    "data": {
        "provider": {
            "provider_id": "PR2506001",
            "name": "Dr. Smith",
            "email": "provider@example.com",
            "provider_role": "doctor",
            "pod_name": "A"
        },
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/auth/login</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Authenticate provider and receive JWT token.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "email": "provider@example.com",
    "password": "password123"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Login successful",
    "data": {
        "provider": {
            "provider_id": "PR2506001",
            "name": "Dr. Smith",
            "email": "provider@example.com"
        },
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logout -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/auth/logout</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Invalidate the current authentication token.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Logged out successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dashboard Section -->
            <section class="section" id="dashboard">
                <div class="section-header">
                    <h2>Dashboard</h2>
                    <p>Provider dashboard endpoints</p>
                </div>

                <!-- Get Dashboard Data -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/dashboard</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get dashboard data including patients, appointments, and chats.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "provider_id": "PR2506001",
        "patients": [
            {
                "patient_id": "PT2506001",
                "name": "John Doe",
                "email": "john@example.com",
                "latest_message": "Hello Doctor",
                "message_time": "2023-06-25T10:30:00Z",
                "unread_count": 2
            }
        ],
        "appointments": [
            {
                "appointment_uid": "APPT12345",
                "patient_id": "PT2506001",
                "date": "2023-06-28",
                "time": "14:00:00",
                "status": "scheduled"
            }
        ],
        "chats": [
            {
                "patient_id": "PT2506001",
                "name": "John Doe",
                "latest_message": "Hello Doctor",
                "message_time": "2023-06-25T10:30:00Z"
            }
        ],
        "total_unread": 3
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Patient Records Section -->
            <section class="section" id="patient-records">
                <div class="section-header">
                    <h2>Patient Records</h2>
                    <p>Endpoints for managing patient records</p>
                </div>

                <!-- Get All Patients -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/patients</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all patients assigned to the provider's pod.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "patients": [
            {
                "patient_id": "PT2506001",
                "name": "John Doe",
                "email": "john@example.com"
            }
        ],
        "user_details": [
            {
                "patient_id": "PT2506001",
                "age": 35,
                "gender": "male"
            }
        ]
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get Patient Records by Type -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/patients/{patient_id}/records/{type}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get patient records by type (virtual-notes, clinical-notes, quest-lab, e-prescription).</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>patient_id</td>
                                        <td>Patient ID</td>
                                    </tr>
                                    <tr>
                                        <td>type</td>
                                        <td>Record type (virtual-notes, clinical-notes, quest-lab, e-prescription)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response (Virtual Notes Example)</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "id": 1,
            "appointment_uid": "APPT12345",
            "main_note": "Patient reported improved symptoms",
            "created_at": "2023-06-20T09:15:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Appointments Section -->
            <section class="section" id="appointments">
                <div class="section-header">
                    <h2>Appointments</h2>
                    <p>Endpoints for managing appointments</p>
                </div>

                <!-- Get All Appointments -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/appointments</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all appointments for the provider.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "appointment_uid": "APPT12345",
            "patient_id": "PT2506001",
            "date": "2023-06-28",
            "time": "14:00:00",
            "status": "scheduled"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get Appointment Details -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get details for a specific appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "appointment": {
            "appointment_uid": "APPT12345",
            "patient_id": "PT2506001",
            "date": "2023-06-28",
            "time": "14:00:00",
            "status": "scheduled"
        },
        "virtual_notes": [
            {
                "id": 1,
                "main_note": "Patient reported improved symptoms"
            }
        ],
        "clinical_notes": [],
        "questlab_notes": [],
        "eprescription_notes": [],
        "meeting_url": "https://meet.example.com/room/APPT12345"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Meeting -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/meeting</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Schedule a meeting for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Meeting scheduled successfully",
    "data": {
        "appointment_uid": "APPT12345",
        "meet_link": "scheduled"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Start Meeting -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_id}/meeting/start</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Start a scheduled meeting.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_id</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Meeting can be started",
    "data": {
        "meeting_url": "https://meet.example.com/room/APPT12345"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notes Management Section -->
            <section class="section" id="notes-management">
                <div class="section-header">
                    <h2>Notes Management</h2>
                    <p>Endpoints for managing various types of notes</p>
                </div>

                <!-- Virtual Notes Section -->
                <div class="section-header">
                    <h3>Virtual Notes</h3>
                </div>

                <!-- Get All Virtual Notes -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/virtual</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all virtual notes for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "id": 1,
            "appointment_uid": "APPT12345",
            "main_note": "Patient reported improved symptoms",
            "created_at": "2023-06-20T09:15:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Virtual Note -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/virtual</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Create a new virtual note for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "main_note": "Patient reported improved symptoms"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Virtual note created successfully",
    "data": {
        "id": 2,
        "main_note": "Patient reported improved symptoms"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Virtual Note -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method put">PUT</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/virtual/{note_id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Update an existing virtual note.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                    <tr>
                                        <td>note_id</td>
                                        <td>ID of the note to update</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "main_note": "Updated note about symptoms"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Virtual note updated successfully",
    "data": {
        "id": 2,
        "main_note": "Updated note about symptoms"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Virtual Note -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/virtual/{note_id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Delete a virtual note.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                    <tr>
                                        <td>note_id</td>
                                        <td>ID of the note to delete</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Virtual note deleted successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clinical Notes Section -->
                <div class="section-header">
                    <h3>Clinical Notes</h3>
                </div>

                <!-- Get All Clinical Notes -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/clinical</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all clinical notes for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "id": 1,
            "chief_complaint": "Diabetes management",
            "history_of_present_illness": "Patient has type 2 diabetes",
            "created_at": "2023-06-20T09:15:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Clinical Note -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/clinical</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Create a new clinical note for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "chief_complaint": "Diabetes management",
    "history_of_present_illness": "Patient has type 2 diabetes",
    "past_medical_history": "Hypertension",
    "medications": "Metformin 500mg twice daily",
    "family_history": "Father with diabetes",
    "social_history": "Non-smoker, occasional alcohol",
    "physical_examination": "Normal BMI, no abnormalities",
    "assessment_plan": "Continue current regimen",
    "progress_notes": "Patient doing well",
    "provider_information": "Dr. Smith"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Clinical note created successfully",
    "data": {
        "id": 2,
        "chief_complaint": "Diabetes management"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quest Labs Section -->
                <div class="section-header">
                    <h3>Quest Labs</h3>
                </div>

                <!-- Get All Quest Labs -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/quest-labs</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all Quest Lab orders for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "id": 1,
            "test_name": "HbA1c",
            "test_code": "HBA1C",
            "category": "Diabetes",
            "created_at": "2023-06-20T09:15:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Quest Lab -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/quest-labs</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Create a new Quest Lab order for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "test_name": "HbA1c",
    "test_code": "HBA1C",
    "category": "Diabetes",
    "specimen_type": "Blood",
    "urgency": "Routine",
    "preferred_lab_location": "123 Main St",
    "date": "2023-06-28",
    "time": "10:00",
    "patient_name": "John Doe",
    "patient_id": "PT2506001",
    "clinical_notes": "Monitor diabetes control",
    "patient_phone_no": "1234567890",
    "insurance_provider": "Medicare",
    "estimated_cost": 50.00
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Quest Lab created successfully",
    "data": {
        "id": 2,
        "test_name": "HbA1c"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- E-Prescriptions Section -->
                <div class="section-header">
                    <h3>E-Prescriptions</h3>
                </div>

                <!-- Get All E-Prescriptions -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/e-prescriptions</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all e-prescriptions for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "id": 1,
            "drug_name": "Metformin",
            "strength": "500mg",
            "created_at": "2023-06-20T09:15:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create E-Prescription -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/appointments/{appointment_uid}/notes/e-prescriptions</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Create a new e-prescription for an appointment.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>Unique ID of the appointment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "patient_name": "John Doe",
    "patient_id": "PT2506001",
    "age": 35,
    "gender": "male",
    "allergies": "None",
    "drug_name": "Metformin",
    "strength": "500mg",
    "form_manufacturer": "Tablet",
    "dose_amount": "1 tablet",
    "frequency": "Twice daily",
    "time_duration": "30 days",
    "quantity": 60,
    "refills": 2,
    "start_date": "2023-06-28"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "E-Prescription created successfully",
    "data": {
        "id": 2,
        "drug_name": "Metformin"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notetaker Section -->
            <section class="section" id="notetaker">
                <div class="section-header">
                    <h2>Notetaker</h2>
                    <p>Endpoints for managing notetaker videos and notes</p>
                </div>

                <!-- Get All Notetakers -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/notetaker</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all notetaker videos and associated notes.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "notetakers": [
            {
                "note_uid": "Note123456",
                "appointment_id": "APPT12345",
                "video_url": "/storage/provider/notetaker_videos/video.mp4"
            }
        ],
        "notes": [
            {
                "note_uid": "Note123456",
                "note_text": "Important point at 2:30"
            }
        ],
        "appointments": [
            {
                "appointment_uid": "APPT12345",
                "date": "2023-06-28"
            }
        ]
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Notetaker -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/notetaker</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Upload a notetaker video.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>multipart/form-data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>String</td>
                                        <td>Appointment UID</td>
                                    </tr>
                                    <tr>
                                        <td>video_file</td>
                                        <td>File</td>
                                        <td>Video file to upload</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Notetaker video added successfully",
    "data": {
        "note_uid": "Note123456",
        "video_url": "/storage/provider/notetaker_videos/video.mp4"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Note to Notetaker -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/notetaker/{note_uid}/notes</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Add a note to a notetaker video.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>note_uid</td>
                                        <td>Unique ID of the notetaker video</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "note_text": "Important point at 2:30"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Note added successfully",
    "data": {
        "id": 1,
        "note_text": "Important point at 2:30"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Chats Section -->
            <section class="section" id="chats">
                <div class="section-header">
                    <h2>Chats</h2>
                    <p>Endpoints for managing provider-patient chats</p>
                </div>

                <!-- Get Chat Sessions -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/chats</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all chat sessions with patients.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "chats": [
            {
                "patient_id": "PT2506001",
                "name": "John Doe",
                "latest_message": "Hello Doctor",
                "message_time": "2023-06-25T10:30:00Z",
                "unread_count": 2
            }
        ],
        "total_unread": 3
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get Chat Messages -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/chats/{patient_id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all messages in a chat session with a specific patient.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>patient_id</td>
                                        <td>Patient ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "id": 1,
            "sent_by": "PT2506001",
            "message": "Hello Doctor",
            "created_at": "2023-06-25T10:30:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send Message -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/chats/{patient_id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Send a message to a patient.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>patient_id</td>
                                        <td>Patient ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "message": "How are you feeling today?"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Message sent successfully",
    "data": {
        "id": 2,
        "message": "How are you feeling today?"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send Image Message -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/chats/{patient_id}/image</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Send an image message to a patient.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>multipart/form-data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>patient_id</td>
                                        <td>Patient ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>image</td>
                                        <td>File</td>
                                        <td>Image file to send</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Image message sent successfully",
    "data": {
        "id": 3,
        "message": "/storage/chat_images/image.jpg",
        "message_type": "image"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- AI Chat Section -->
            <section class="section" id="ai-chat">
                <div class="section-header">
                    <h2>AI Chat</h2>
                    <p>Endpoints for AI-powered chat assistance</p>
                </div>

                <!-- Get AI Chat Sessions -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/ai-chat</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all AI chat sessions.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "chatuid": "Chat12345",
            "message": "Hello, how can I help you?",
            "created_at": "2023-06-25T10:30:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get AI Chat Messages -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/ai-chat/{chat_uid}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all messages in a specific AI chat session.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>chat_uid</td>
                                        <td>Unique ID of the chat session</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "message": "Hello, how can I help you?",
            "created_at": "2023-06-25T10:30:00Z"
        },
        {
            "message": "I need help with diabetes management",
            "created_at": "2023-06-25T10:31:00Z"
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send AI Message -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/ai-chat/{chat_uid}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Send a message to the AI assistant.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>chat_uid</td>
                                        <td>Unique ID of the chat session</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "message": "What's the recommended HbA1c target?"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "AI response received",
    "data": {
        "reply": "The recommended HbA1c target for most adults with diabetes is less than 7%."
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clear AI Chat Session -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="endpoint-url">/provider/ai-chat/{chat_uid}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Clear an AI chat session and start a new one.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>chat_uid</td>
                                        <td>Unique ID of the chat session to clear</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Chat session cleared",
    "data": {
        "new_chat_uid": "Chat67890"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Account Management Section -->
            <section class="section" id="account-management">
                <div class="section-header">
                    <h2>Account Management</h2>
                    <p>Endpoints for managing provider account</p>
                </div>

                <!-- Get Account Info -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/account</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get provider account information.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "provider": {
            "provider_id": "PR2506001",
            "name": "Dr. Smith",
            "email": "provider@example.com",
            "mobile": "1234567890",
            "profile_picture": "/storage/provider/profiles/photo.jpg"
        },
        "prefixcode": ["Dr.", "Mr.", "Mrs."],
        "languages": ["English", "Spanish"]
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Account Info -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method put">PUT</span>
                        <span class="endpoint-url">/provider/account</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Update provider account information.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "first_name": "John",
    "last_name": "Smith",
    "email": "newemail@example.com",
    "prefix_code": "Dr.",
    "phone_number": "9876543210",
    "about_me": "Endocrinologist specializing in diabetes",
    "language": "English"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Account updated successfully",
    "data": {
        "provider_id": "PR2506001",
        "name": "Dr. John Smith"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Profile Picture -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/account/profile-picture</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Update provider profile picture.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>multipart/form-data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>profile_picture</td>
                                        <td>File</td>
                                        <td>Image file to upload as profile picture</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Profile picture updated successfully",
    "data": {
        "profile_picture": "/storage/provider/profiles/newphoto.jpg"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Send Password OTP -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/account/password/otp</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Send OTP for password reset.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "email": "provider@example.com"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "OTP sent successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verify Password OTP -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/account/password/verify</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Verify OTP for password reset.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "email": "provider@example.com",
    "otp": "123456"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "OTP verified successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/account/password/change</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Change provider password.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "email": "provider@example.com",
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Password changed successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="endpoint-url">/provider/account</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Delete provider account.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Account deleted successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notifications Section -->
            <section class="section" id="notifications">
                <div class="section-header">
                    <h2>Notifications</h2>
                    <p>Endpoints for managing notifications</p>
                </div>

                <!-- Get Notifications -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/notifications</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get all notifications for the provider.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": [
        {
            "id": 1,
            "notification": "New appointment scheduled",
            "created_at": "2023-06-25T10:30:00Z",
            "read_status": 0
        }
    ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Notification -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="endpoint-url">/provider/notifications/{notification_id}</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Delete a specific notification.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>URL Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>notification_id</td>
                                        <td>ID of the notification to delete</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "message": "Notification deleted successfully"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ClaimMD Integration Section -->
            <section class="section" id="claimmd-integration">
                <div class="section-header">
                    <h2>ClaimMD Integration</h2>
                    <p>Endpoints for ClaimMD medical billing integration</p>
                </div>

                <!-- Get ClaimMD Credentials -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/patient-claims-biller</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get ClaimMD credentials for frontend integration.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "CLAIM_MD_CLIENT_ID": "client123",
        "CLAIM_MD_API_KEY": "api_key_123",
        "CLAIM_MD_ENV": "production"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Claim File -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="endpoint-url">/provider/claim-md/upload</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Upload EDI 837 claim file to ClaimMD.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>multipart/form-data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>claim_file</td>
                                        <td>File</td>
                                        <td>EDI 837 claim file (txt format)</td>
                                    </tr>
                                    <tr>
                                        <td>file_name</td>
                                        <td>String</td>
                                        <td>Name for the uploaded file</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "FileID": "file123",
        "Status": "Uploaded"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get Upload List -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/claim-md/uploadlist</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Get list of uploaded claim files.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "Files": [
            {
                "FileID": "file123",
                "FileName": "claim_837.txt",
                "UploadDate": "2023-06-25"
            }
        ]
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Uploaded File -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="endpoint-url">/provider/claim-md/deletefile</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Delete an uploaded claim file.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Request Body</h4>
                            <div class="code-block json">
                                <pre>{
    "file_id": "file123"
}</pre>
                            </div>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "message": "File deleted successfully"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Uploaded File -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/claim-md/viewfile</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>View content of an uploaded claim file.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Query Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>file_id</td>
                                        <td>ID of the file to view</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <div class="code-block json">
                                <pre>{
    "type": "success",
    "data": {
        "content": "ISA*00*...",
        "content_type": "text/plain"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download File -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="endpoint-url">/provider/claim-md/downloadfile</span>
                    </div>
                    <div class="endpoint-body">
                        <div class="endpoint-section">
                            <h4>Description</h4>
                            <p>Download an uploaded claim file.</p>
                        </div>
                        <div class="endpoint-section">
                            <h4>Headers</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Header</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Authorization</td>
                                        <td>Bearer [JWT_TOKEN]</td>
                                    </tr>
                                    <tr>
                                        <td>Content-Type</td>
                                        <td>application/json</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Query Parameters</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>file_id</td>
                                        <td>ID of the file to download</td>
                                    </tr>
                                    <tr>
                                        <td>filename</td>
                                        <td>Name for the downloaded file</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="endpoint-section">
                            <h4>Response</h4>
                            <p>File download with appropriate Content-Type header</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('#mobileMenuToggle').click(function() {
                $('#sidebar').toggleClass('active');
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                
                var target = this.hash;
                var $target = $(target);
                
                $('html, body').stop().animate({
                    'scrollTop': $target.offset().top - 20
                }, 900, 'swing', function() {
                    window.location.hash = target;
                    
                    // Highlight the section
                    $target.addClass('highlight');
                    setTimeout(function() {
                        $target.removeClass('highlight');
                    }, 2000);
                });
                
                // Close mobile menu if open
                if ($('#sidebar').hasClass('active')) {
                    $('#sidebar').removeClass('active');
                }
            });

            // Scrollspy to highlight current section in sidebar
            $(window).scroll(function() {
                var scrollPosition = $(document).scrollTop();
                
                $('.section').each(function() {
                    var currentSection = $(this);
                    var sectionId = currentSection.attr('id');
                    var sectionOffset = currentSection.offset().top - 100;
                    var sectionHeight = currentSection.outerHeight();
                    
                    if (scrollPosition >= sectionOffset && scrollPosition < sectionOffset + sectionHeight) {
                        $('.sidebar-nav a').removeClass('active');
                        $('.sidebar-nav a[href="#' + sectionId + '"]').addClass('active');
                    }
                });
            });

            // Initialize scrollspy on page load
            $(window).trigger('scroll');
        });



                $(document).ready(function() {
            // Add copy buttons to all code blocks
            $('.code-block').each(function() {
                const $codeBlock = $(this);
                const $copyButton = $('<button class="code-block-copy">Copy</button>');
                $codeBlock.prepend($copyButton);

                $copyButton.on('click', function() {
                    const textToCopy = $codeBlock.find('pre').text();
                    navigator.clipboard.writeText(textToCopy).then(function() {
                        $copyButton.text('Copied!').addClass('copied');
                        setTimeout(function() {
                            $copyButton.text('Copy').removeClass('copied');
                        }, 2000);
                    });
                });
            });

            // Simple syntax highlighting for JSON
            $('.code-block.json pre').each(function() {
                let code = $(this).html();

                // Highlight property names
                code = code.replace(/"([^"]+)":/g, '"<span class="token property">$1</span>":');

                // Highlight strings
                code = code.replace(/"([^"]+)"/g, '"<span class="token string">$1</span>"');

                // Highlight numbers
                code = code.replace(/: (\d+)/g, ': <span class="token number">$1</span>');

                // Highlight booleans and null
                code = code.replace(/: (true|false|null)/g, ': <span class="token boolean">$1</span>');

                // Highlight keywords
                code = code.replace(/\b(type|message|data|success)\b/g,
                    '<span class="token keyword">$1</span>');

                // Highlight punctuation
                code = code.replace(/({|}|\[|\]|,|:)/g, '<span class="token punctuation">$1</span>');

                $(this).html(code);
            });
        });
    </script>
</body>
</html>