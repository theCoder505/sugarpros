<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SugarPros Patients API Documentation</title>
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc3f7;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --border-radius: 4px;
            --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
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
            padding: 20px 0;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-nav ul {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 5px;
        }

        .sidebar-nav a {
            display: block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent-color);
        }

        .sidebar-nav a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent-color);
            font-weight: bold;
        }

        .sidebar-nav .nav-section {
            font-weight: bold;
            padding: 15px 20px 5px;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            opacity: 0.7;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .page-header h1 {
            font-size: 2rem;
            color: var(--secondary-color);
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
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .section-header h2 {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .endpoint {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
        }

        .endpoint-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .method {
            display: inline-block;
            padding: 5px 10px;
            border-radius: var(--border-radius);
            font-weight: bold;
            margin-right: 15px;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .method.get {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .method.post {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .method.put {
            background-color: #fff8e1;
            color: #ff8f00;
            border: 1px solid #ffecb3;
        }

        .method.delete {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .url {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1.1rem;
            word-break: break-all;
        }

        .endpoint-description {
            margin-bottom: 15px;
            color: #555;
        }

        .endpoint-details {
            margin-bottom: 15px;
        }

        .detail-title {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .request-response {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .request,
        .response {
            flex: 1;
            min-width: 300px;
        }

        .code-block {
            background-color: #f5f5f5;
            border-radius: var(--border-radius);
            padding: 15px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            margin-top: 10px;
        }

        pre {
            margin: 0;
            white-space: pre-wrap;
        }

        .tab-container {
            margin-top: 20px;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab.active {
            border-bottom-color: var(--primary-color);
            font-weight: bold;
            color: var(--primary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .required {
            color: var(--danger-color);
            font-weight: bold;
        }


        /* Enhanced Code Block Styles */
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


        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                padding: 20px;
            }

            .request-response {
                flex-direction: column;
            }
        }

        /* Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Highlight JSON keys */
        .json-key {
            color: #d63384;
        }

        .json-string {
            color: #20c997;
        }

        .json-number {
            color: #fd7e14;
        }

        .json-boolean {
            color: #6610f2;
        }

        .json-null {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h1>SugarPros Patient API</h1>
                <p>Version 1.0.0</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-section">Introduction</li>
                    <li><a href="#introduction" class="active">Overview</a></li>

                    <li class="nav-section">Authentication</li>
                    <li><a href="#authentication">User Registration</a></li>
                    <li><a href="#login">User Login</a></li>
                    <li><a href="#password-recovery">Password Recovery</a></li>

                    <li class="nav-section">Forms</li>
                    <li><a href="#basic-details">Basic Details</a></li>
                    <li><a href="#privacy-form">Privacy Form</a></li>
                    <li><a href="#compliance-form">Compliance Form</a></li>
                    <li><a href="#financial-agreement">Financial Agreement</a></li>
                    <li><a href="#self-payment">Self Payment</a></li>

                    <li class="nav-section">Dashboard</li>
                    <li><a href="#dashboard">Patient Dashboard</a></li>
                    <li><a href="#hippa-consent">HIPAA Consent</a></li>
                    <li><a href="#language-preference">Language Preference</a></li>

                    <li class="nav-section">Account</li>
                    <li><a href="#account-details">Account Details</a></li>
                    <li><a href="#profile-picture">Profile Picture</a></li>
                    <li><a href="#email-change">Email Change</a></li>
                    <li><a href="#password-change">Password Change</a></li>
                    <li><a href="#delete-account">Delete Account</a></li>

                    <li class="nav-section">Notifications</li>
                    <li><a href="#notifications">Get Notifications</a></li>
                    <li><a href="#delete-notification">Delete Notification</a></li>

                    <li class="nav-section">Appointments</li>
                    <li><a href="#appointments">Get Appointments</a></li>
                    <li><a href="#specific-appointment">Specific Appointment</a></li>
                    <li><a href="#join-meeting">Join Meeting</a></li>
                    <li><a href="#appointment-booking">Appointment Booking</a></li>

                    <li class="nav-section">Chat</li>
                    <li><a href="#chat-history">Chat History</a></li>
                    <li><a href="#related-chats">Related Chats</a></li>
                    <li><a href="#send-message">Send Message</a></li>
                    <li><a href="#send-image">Send Image</a></li>

                    <li class="nav-section">AI Chat</li>
                    <li><a href="#sugarpros-ai">SugarPros AI</a></li>
                    <li><a href="#ai-response">AI Response</a></li>
                    <li><a href="#clear-chat">Clear Chat</a></li>

                    <li class="nav-section">Medical Data</li>
                    <li><a href="#clinical-notes">Clinical Notes</a></li>
                    <li><a href="#quest-lab">Quest Lab</a></li>
                    <li><a href="#e-prescriptions">E-Prescriptions</a></li>

                    <li class="nav-section">Integrations</li>
                    <li><a href="#dexcom">Dexcom Integration</a></li>
                    <li><a href="#fatsecret">FatSecret Integration</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <h1>SugarPros API Documentation</h1>
            </div>

            <!-- Introduction Section -->
            <section id="introduction" class="section">
                <div class="section-header">
                    <h2>Introduction</h2>
                </div>
                <p>Welcome to the SugarPros API documentation. This API provides all the functionality needed to
                    interact with the SugarPros diabetes management platform.</p>
                <p>The API follows RESTful principles and uses JSON for request and response payloads. All endpoints
                    require authentication via JWT tokens unless otherwise noted.</p>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/basic-website-settings</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get all basic information about the website</p>
                        <p>
                            <strong>All Patients:</strong> /api/patients </br>
                            <strong>All Providers:</strong> /api/providers
                        </p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>None required</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "data": {
        "id": 1,
        "brandname": "SugarPros",
        "brandlogo": "path/to/logo.png",
        "brandicon": "path/to/icon.ico",
        "currency": "USD",
        "contact_email": "contact@example.com",
        "contact_phone": "+1234567890",
        "streets": ["Street1", "Street2"],
        "cities": ["City1", "City2"],
        "states": ["State1", "State2"],
        "zip_codes": ["12345", "67890"],
        "prefixcode": "PA",
        "languages": ["English", "Spanish"],
        "meeting_web_root_url": "https://meet.example.com",
        "fb_url": "https://facebook.com/example",
        "twitter_url": "https://twitter.com/example",
        "instagram_url": "https://instagram.com/example",
        "linkedin_url": "https://linkedin.com/example",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Authentication Section -->
            <section id="authentication" class="section">
                <div class="section-header">
                    <h2>Authentication</h2>
                </div>

                <!-- User Registration -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/send-otp-to-user</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Send OTP to user's email for registration</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>username</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Desired username</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>User email</td>
                                    </tr>
                                    <tr>
                                        <td>prefix_code</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Phone prefix code</td>
                                    </tr>
                                    <tr>
                                        <td>mobile</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Phone number</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "message": "OTP sent to your email!"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verify OTP -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/verify-otp</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Verify OTP sent to user's email</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>username</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Same as registration</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Same as registration</td>
                                    </tr>
                                    <tr>
                                        <td>otp</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>6-digit OTP received</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "message": "OTP Verified!"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Signup -->
                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/signup-new-user</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Complete user registration after OTP verification</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>username</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Same as registration</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Same as registration</td>
                                    </tr>
                                    <tr>
                                        <td>prefix_code</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Phone prefix code</td>
                                    </tr>
                                    <tr>
                                        <td>mobile</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Phone number</td>
                                    </tr>
                                    <tr>
                                        <td>password</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Account password</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "user": {
        "id": 2,
        "patient_id": "PA25060001",
        "name": "username",
        "email": "user@example.com",
        "prefix_code": "+1",
        "mobile": "1234567890"
    },
    "message": "Login Success!"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- User Login Section -->
            <section id="login" class="section">
                <div class="section-header">
                    <h2>User Login</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/login-existing-user</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Authenticate user and return JWT token</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Registered email</td>
                                    </tr>
                                    <tr>
                                        <td>password</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Account password</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "user": {
        "id": 2,
        "patient_id": "PA25060001",
        "name": "username",
        "email": "user@example.com"
    },
    "message": "Login Success!"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/logout</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Logout user and invalidate JWT token</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "message": "Logged Out Successfully!"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Password Recovery Section -->
            <section id="password-recovery" class="section">
                <div class="section-header">
                    <h2>Password Recovery</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/send-forget-request</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Send password reset OTP to user's email</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Registered email</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "message": "A 6 Digit OTP Sent To Your Email!"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/verify-forget-otp</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Verify OTP for password reset</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Registered email</td>
                                    </tr>
                                    <tr>
                                        <td>otp</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>6-digit OTP received</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "message": "OTP matched! Set new password now."
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/check-password-validity</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Check if new password meets requirements</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Registered email</td>
                                    </tr>
                                    <tr>
                                        <td>otp</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Verified OTP</td>
                                    </tr>
                                    <tr>
                                        <td>password</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>New password</td>
                                    </tr>
                                    <tr>
                                        <td>confirm_password</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Confirm new password</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "message": "verified"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/reset-account-password</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Reset user password after verification</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Registered email</td>
                                    </tr>
                                    <tr>
                                        <td>otp</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Verified OTP</td>
                                    </tr>
                                    <tr>
                                        <td>password</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>New password</td>
                                    </tr>
                                    <tr>
                                        <td>confirm_password</td>
                                        <td>string</td>
                                        <td class="required">Yes</td>
                                        <td>Confirm new password</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
    "type": "success",
    "message": "Account Retrieved Successfully! Login Now."
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Add more sections following the same pattern for all endpoints -->


            <!-- Form Submission APIs Section -->
            <section id="basic-details" class="section">
                <div class="section-header">
                    <h2>Basic Details Form</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/basic</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get basic details form data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "fname": "John",
            "mname": "",
            "lname": "Doe",
            "dob": "1990-01-01",
            "gender": "male",
            "email": "john@example.com",
            "phone": "+1234567890",
            "street": "123 Main St",
            "city": "New York",
            "state": "NY",
            "zip_code": "10001",
            "medicare_number": "123456789",
            "group_number": "GRP123",
            "license": "path/to/license.jpg",
            "ssn": "123-45-6789",
            "notification_type": "email",
            "web_streets": ["Main St", "Broadway"],
            "web_cities": ["New York", "Los Angeles"],
            "web_states": ["NY", "CA"],
            "web_zip_codes": ["10001", "90001"]
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/basic</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Submit basic details form</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>fname</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>First name</td>
                                    </tr>
                                    <tr>
                                        <td>mname</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Middle name</td>
                                    </tr>
                                    <tr>
                                        <td>lname</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Last name</td>
                                    </tr>
                                    <tr>
                                        <td>dob</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Date of birth (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>gender</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Gender (male/female/other)</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Email address</td>
                                    </tr>
                                    <tr>
                                        <td>phone</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Phone number</td>
                                    </tr>
                                    <tr>
                                        <td>street</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Street address</td>
                                    </tr>
                                    <tr>
                                        <td>city</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>City</td>
                                    </tr>
                                    <tr>
                                        <td>state</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>State</td>
                                    </tr>
                                    <tr>
                                        <td>zip_code</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>ZIP code</td>
                                    </tr>
                                    <tr>
                                        <td>medicare_number</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Medicare number</td>
                                    </tr>
                                    <tr>
                                        <td>group_number</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Group number</td>
                                    </tr>
                                    <tr>
                                        <td>license</td>
                                        <td>file</td>
                                        <td>No</td>
                                        <td>License image</td>
                                    </tr>
                                    <tr>
                                        <td>ssn</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Social Security Number</td>
                                    </tr>
                                    <tr>
                                        <td>notification_type</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Notification preference (email/sms)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Your details have been added successfully!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Privacy Form Section -->
            <section id="privacy-form" class="section">
                <div class="section-header">
                    <h2>Privacy Form</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/privacy</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get privacy form data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": [{
            "fname": "John",
            "lname": "Doe",
            "date": "2023-01-01",
            "users_message": "Sample message",
            "notice_of_privacy_practice": "true",
            "patients_name": "John Doe",
            "representatives_name": "Jane Doe",
            "service_taken_date": "2023-01-01",
            "relation_with_patient": "spouse"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/privacy</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Submit privacy form</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>fname</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>First name</td>
                                    </tr>
                                    <tr>
                                        <td>lname</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Last name</td>
                                    </tr>
                                    <tr>
                                        <td>date</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Date (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>users_message</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>User message</td>
                                    </tr>
                                    <tr>
                                        <td>notice_of_privacy_practice</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>"true" or "false"</td>
                                    </tr>
                                    <tr>
                                        <td>patients_name</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Patient's full name</td>
                                    </tr>
                                    <tr>
                                        <td>representatives_name</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Representative's name</td>
                                    </tr>
                                    <tr>
                                        <td>service_taken_date</td>
                                        <td>date</td>
                                        <td>No</td>
                                        <td>Service date (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>relation_with_patient</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Relationship with patient</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Data taken, now fillup this page"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Compliance Form Section -->
            <section id="compliance-form" class="section">
                <div class="section-header">
                    <h2>Compliance Form</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/compliance</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get compliance form data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": [{
            "patients_name": "John Doe",
            "dob": "1990-01-01",
            "patients_signature": "path/to/signature.jpg",
            "patients_dob": "1990-01-01",
            "representative_signature": "path/to/rep_signature.jpg",
            "representative_dob": "1985-01-01",
            "nature_with_patient": "spouse"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/compliance</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Submit compliance form</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>patients_name</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Patient's full name</td>
                                    </tr>
                                    <tr>
                                        <td>dob</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Date of birth (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>patients_signature</td>
                                        <td>file</td>
                                        <td>No</td>
                                        <td>Patient's signature image</td>
                                    </tr>
                                    <tr>
                                        <td>patients_dob</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Patient's date of birth (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>representative_signature</td>
                                        <td>file</td>
                                        <td>No</td>
                                        <td>Representative's signature image</td>
                                    </tr>
                                    <tr>
                                        <td>representative_dob</td>
                                        <td>date</td>
                                        <td>No</td>
                                        <td>Representative's date of birth (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>nature_with_patient</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Relationship with patient</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Updated, now fillup this page"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Financial Agreement Section -->
            <section id="financial-agreement" class="section">
                <div class="section-header">
                    <h2>Financial Agreement Form</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/financial-responsibility-aggreement</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get financial agreement form data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": [{
            "user_name": "John Doe",
            "patients_name": "John Doe",
            "patients_signature_date": "2023-01-01",
            "relationship": "self"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/financial-responsibility-aggreement</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Submit financial agreement form</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>user_name</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>User's full name</td>
                                    </tr>
                                    <tr>
                                        <td>patients_name</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Patient's full name</td>
                                    </tr>
                                    <tr>
                                        <td>patients_signature_date</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Signature date (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>relationship</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Relationship with patient</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Updated, now fillup this page"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Self Payment Agreement Section -->
            <section id="self-payment" class="section">
                <div class="section-header">
                    <h2>Self Payment Agreement</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/agreement-for-self-payment</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get self payment agreement form data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": [{
            "user_name": "John Doe",
            "patients_name": "John Doe",
            "patients_signature_date": "2023-01-01"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/agreement-for-self-payment</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Submit self payment agreement form</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>user_name</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>User's full name</td>
                                    </tr>
                                    <tr>
                                        <td>patients_name</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Patient's full name</td>
                                    </tr>
                                    <tr>
                                        <td>patients_signature_date</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Signature date (YYYY-MM-DD)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Successfully Completed"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dashboard Section -->
            <section id="dashboard" class="section">
                <div class="section-header">
                    <h2>Patient Dashboard</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/dashboard</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get patient dashboard data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "userID": 2,
        "patient_id": "PA25060001",
        "userType": "patient",
        "pod_name": "A",
        "total_unread": 3,
        "userLang": "English",
        "Consent": true,
        "notificationMethod": "email",
        "languages": ["English", "Spanish"],
        "appointments": [{
            "id": 1,
            "appointment_uid": "SA2307-0001",
            "provider_id": "PR123",
            "provider_name": "Dr. Smith",
            "date": "2023-07-15",
            "time": "10:00:00",
            "status": "confirmed",
            "meeting_url": "https://meet.example.com/room/SA2307-0001"
        }],
        "chat_history": [{
            "id": 1,
            "sent_by": "PA25060001",
            "received_by": "PR123",
            "main_message": "Hello Doctor",
            "message_type": "text",
            "status": "seen",
            "created_at": "2023-07-10 14:30:00",
            "sender_name": "John Doe",
            "sender_image": "path/to/profile.jpg"
        }],
        "related_providers": [{
            "provider_id": "PR123",
            "name": "Dr. Smith",
            "profile_picture": "path/to/doctor.jpg",
            "latest_message": "Hello",
            "message_time": "2023-07-10 14:30:00",
            "message_type": "text",
            "is_sender": false,
            "message_status": "seen",
            "unread_count": 0
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- HIPAA Consent Section -->
            <section id="hippa-consent" class="section">
                <div class="section-header">
                    <h2>HIPAA Consent</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/hippa-consent-prefference</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Update HIPAA consent preference</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>consent</td>
                                        <td>integer</td>
                                        <td>Yes</td>
                                        <td>0 or 1 (0 = no consent, 1 = consent)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "HIPAA Consent Selection Successfully Implemented!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Language Preference Section -->
            <section id="language-preference" class="section">
                <div class="section-header">
                    <h2>Language Preference</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/change-language-prefference</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Change user's language preference</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>language</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Selected language (e.g., "English", "Spanish")</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Language Selection Successfully Implemented!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Account Details Section -->
            <section id="account-details" class="section">
                <div class="section-header">
                    <h2>Account Details</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/account</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get account details</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "accountDetails": [{
            "fname": "John",
            "mname": "",
            "lname": "Doe",
            "dob": "1990-01-01",
            "gender": "male",
            "email": "john@example.com",
            "phone": "+1234567890",
            "emmergency_name": "Jane Doe",
            "emmergency_relationship": "spouse",
            "emmergency_phone": "+1987654321",
            "street": "123 Main St",
            "city": "New York",
            "state": "NY",
            "zip_code": "10001",
            "insurance_provider": "Medicare",
            "insurance_plan_number": "PLN123",
            "insurance_group_number": "GRP123",
            "license": "path/to/license.jpg",
            "ssn": "123-45-6789",
            "notification_type": "email"
        }],
        "profile_picture": "path/to/profile.jpg",
        "streets": ["Main St", "Broadway"],
        "cities": ["New York", "Los Angeles"],
        "states": ["NY", "CA"],
        "zip_codes": ["10001", "90001"],
        "prefixcode": "PA",
        "languages": ["English", "Spanish"]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/update-account-details</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Update account details</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>fname</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>First name</td>
                                    </tr>
                                    <tr>
                                        <td>mname</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Middle name</td>
                                    </tr>
                                    <tr>
                                        <td>lname</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Last name</td>
                                    </tr>
                                    <tr>
                                        <td>dob</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Date of birth (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>gender</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Gender (male/female/other)</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Email address</td>
                                    </tr>
                                    <tr>
                                        <td>phone</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Phone number</td>
                                    </tr>
                                    <tr>
                                        <td>emmergency_name</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Emergency contact name</td>
                                    </tr>
                                    <tr>
                                        <td>emmergency_relationship</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Relationship with emergency contact</td>
                                    </tr>
                                    <tr>
                                        <td>emmergency_phone</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Emergency contact phone</td>
                                    </tr>
                                    <tr>
                                        <td>street</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Street address</td>
                                    </tr>
                                    <tr>
                                        <td>city</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>City</td>
                                    </tr>
                                    <tr>
                                        <td>state</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>State</td>
                                    </tr>
                                    <tr>
                                        <td>zip_code</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>ZIP code</td>
                                    </tr>
                                    <tr>
                                        <td>insurance_provider</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Insurance provider</td>
                                    </tr>
                                    <tr>
                                        <td>insurance_plan_number</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Insurance plan number</td>
                                    </tr>
                                    <tr>
                                        <td>insurance_group_number</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Insurance group number</td>
                                    </tr>
                                    <tr>
                                        <td>license</td>
                                        <td>file</td>
                                        <td>No</td>
                                        <td>License image</td>
                                    </tr>
                                    <tr>
                                        <td>ssn</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Social Security Number</td>
                                    </tr>
                                    <tr>
                                        <td>notification_type</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Notification preference (email/sms)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Account details updated successfully!",
        "updated_fields": {
            "fname": "John",
            "lname": "Doe"
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Profile Picture Section -->
            <section id="profile-picture" class="section">
                <div class="section-header">
                    <h2>Profile Picture</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/update-profile-picture</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Update profile picture</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>profilepicture</td>
                                        <td>file</td>
                                        <td>Yes</td>
                                        <td>Profile picture image</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Profile picture updated successfully!",
        "new_profile_picture": "path/to/new/image.jpg"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Email Change Section -->
            <section id="email-change" class="section">
                <div class="section-header">
                    <h2>Email Change</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/user-accout-email-verification</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Check email exists and send OTP for email change</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current email</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Email verified! OTP sent to your email.",
        "otp": "123456"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/user-accout-otp-verification</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Verify OTP for email change</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current email</td>
                                    </tr>
                                    <tr>
                                        <td>otp</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>6-digit OTP received</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "OTP verified!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/user-accout-email-change</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Finalize email change after OTP verification</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current email</td>
                                    </tr>
                                    <tr>
                                        <td>new_email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>New email</td>
                                    </tr>
                                    <tr>
                                        <td>current_password</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Account password</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Your Account Email Updated Successfully!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Password Change Section -->
            <section id="password-change" class="section">
                <div class="section-header">
                    <h2>Password Change</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/user-account-password-verification</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Check email exists and send OTP for password change</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current email</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Email verified! OTP sent to your email.",
        "otp": "123456"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/user-account-password-otp-verification</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Verify OTP for password change</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current email</td>
                                    </tr>
                                    <tr>
                                        <td>otp</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>6-digit OTP received</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "OTP verified!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/user-account-password-change</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Finalize password change after OTP verification</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>email</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current email</td>
                                    </tr>
                                    <tr>
                                        <td>current_password</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current password</td>
                                    </tr>
                                    <tr>
                                        <td>new_password</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>New password</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Your password has been updated successfully!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Delete Account Section -->
            <section id="delete-account" class="section">
                <div class="section-header">
                    <h2>Delete Account</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/delete-account</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Delete user account</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Account deleted successfully!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notifications Section -->
            <section id="notifications" class="section">
                <div class="section-header">
                    <h2>Notifications</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/notifications</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get user notifications</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "notifications": [{
            "id": 1,
            "user_id": "PA25060001",
            "notification": "Profile picture updated",
            "created_at": "2023-07-10 14:30:00",
            "read_status": 1
        }],
        "profile_picture": "path/to/image.jpg"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method delete">DELETE</span>
                        <span class="url">/api/notifications</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Delete notification</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>notification_id</td>
                                        <td>integer</td>
                                        <td>Yes</td>
                                        <td>ID of notification to delete</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Notification deleted successfully!"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Appointments Section -->
            <section id="appointments" class="section">
                <div class="section-header">
                    <h2>Appointments</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/appointments</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get user appointments</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "appointments": [{
            "id": 1,
            "appointment_uid": "SA2307-0001",
            "provider_id": "PR123",
            "provider_name": "Dr. Smith",
            "date": "2023-07-15",
            "time": "10:00:00",
            "status": "confirmed",
            "meeting_url": "https://meet.example.com/room/SA2307-0001"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/appointments</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get specific appointment details</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Appointment UID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "appointmentData": [{
                "id": 1,
                "appointment_uid": "SA2307-0001",
                "provider_id": "PR123",
                "provider_name": "Dr. Smith",
                "date": "2023-07-15",
                "time": "10:00:00",
                "status": "confirmed",
                "meeting_url": "https://meet.example.com/room/SA2307-0001"
            }],
            "virtual_notes": [],
            "clinical_notes": [],
            "questlab_notes": [],
            "eprescription_notes": []
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/join-meeting/{appointment_uid}</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get meeting URL for specific appointment</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">URL Parameters:</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>appointment_uid</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Appointment UID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "meeting_url": "https://meet.example.com/room/SA2307-0001"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/search-appointments-by-month</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Search appointments by month</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>searchingMonth</td>
                                        <td>integer</td>
                                        <td>Yes</td>
                                        <td>Month number (1-12)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": [{
            "id": 1,
            "appointment_uid": "SA2307-0001",
            "date": "2023-07-15",
            "time": "10:00:00",
            "status": "confirmed"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/fetch-specific-range-data</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Fetch appointments within specific date range</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>start_date</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>Start date (YYYY-MM-DD)</td>
                                    </tr>
                                    <tr>
                                        <td>end_date</td>
                                        <td>date</td>
                                        <td>Yes</td>
                                        <td>End date (YYYY-MM-DD)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": [{
            "id": 1,
            "appointment_uid": "SA2307-0001",
            "date": "2023-07-15",
            "time": "10:00:00",
            "status": "confirmed"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>









            <!-- Appointment Booking Section -->
            <section id="appointment-booking" class="section">
                    <div class="section-header">
                        <h2>Appointment Booking</h2>
                    </div>

                    <!-- GET /api/appointments/patient-details -->
                    <div class="endpoint">
                        <div class="endpoint-header">
                            <span class="method get">GET</span>
                            <span class="url">/api/appointments/patient-details</span>
                        </div>
                        <div class="endpoint-description">
                            <p>Get patient details and subscription status for appointment booking. This endpoint provides all necessary information to initialize the booking form.</p>
                        </div>
                        <div class="endpoint-details">
                            <span class="detail-title">Headers:</span>
                            <div class="code-block">
                                <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                            </div>
                        </div>
                        <div class="request-response">
                            <div class="response">
                                <span class="detail-title">Success Response (200):</span>
                                <div class="code-block">
                                    <pre>{
                "type": "success",
                "data": {
                    "patient_id": "PA25060001",
                    "fname": "John",
                    "lname": "Doe",
                    "email": "john@example.com",
                    "has_active_subscription": true,
                    "this_month_appointments": 3,
                    "prefix_codes": "PA"
                }
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (404):</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "User not found"
            }</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- POST /api/appointments/initiate -->
                    <div class="endpoint">
                        <div class="endpoint-header">
                            <span class="method post">POST</span>
                            <span class="url">/api/appointments/initiate</span>
                        </div>
                        <div class="endpoint-description">
                            <p>Initiate appointment booking process. This validates the date/time and checks for conflicts. For subscription plans, it verifies active subscription. For medicare plans, it returns payment configuration details that the frontend will use to process payment via Stripe.</p>
                        </div>
                        <div class="endpoint-details">
                            <span class="detail-title">Headers:</span>
                            <div class="code-block">
                                <pre>Authorization: Bearer [JWT_TOKEN]
            Content-Type: application/json</pre>
                            </div>
                            <span class="detail-title">Body (JSON):</span>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Required</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>date</td>
                                            <td>date</td>
                                            <td class="required">Yes</td>
                                            <td>Appointment date (YYYY-MM-DD format)</td>
                                        </tr>
                                        <tr>
                                            <td>time</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Appointment time (HH:MM format, 24-hour)</td>
                                        </tr>
                                        <tr>
                                            <td>plan</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Payment plan type: "subscription" or "medicare"</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="request-response">
                            <div class="response">
                                <span class="detail-title">Success Response (200) - Subscription Plan:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "success",
                "data": {
                    "requires_payment": false,
                    "booking_details": {
                        "date": "2025-07-15",
                        "time": "10:00",
                        "plan": "subscription"
                    }
                }
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Success Response (200) - Medicare Plan:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "success",
                "data": {
                    "requires_payment": true,
                    "stripe_key": "pk_test_51234567890",
                    "amount": 50,
                    "currency": "USD",
                    "booking_details": {
                        "date": "2025-07-15",
                        "time": "10:00",
                        "plan": "medicare"
                    }
                }
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (400) - Duplicate Booking:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "You already booked an appointment in the same date: 2025-07-15 and time: 10:00"
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (400) - No Active Subscription:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "You need to have an active subscription to book an appointment."
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (400) - Expired Subscription:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "Your subscription expired on January 15, 2025. Please renew your subscription to book appointments."
            }</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- POST /api/appointments/complete -->
                    <div class="endpoint">
                        <div class="endpoint-header">
                            <span class="method post">POST</span>
                            <span class="url">/api/appointments/complete</span>
                        </div>
                        <div class="endpoint-description">
                            <p>Complete appointment booking with full medical and insurance information. For subscription plans, no payment processing occurs on backend. For medicare plans, payment must be processed on the frontend using Stripe, and the payment details (payment_intent_id, charge_id, etc.) are sent to the backend for record-keeping only.</p>
                        </div>
                        <div class="endpoint-details">
                            <span class="detail-title">Headers:</span>
                            <div class="code-block">
                                <pre>Authorization: Bearer [JWT_TOKEN]
            Content-Type: application/json</pre>
                            </div>
                            <span class="detail-title">Body (JSON):</span>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Type</th>
                                            <th>Required</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="background-color: #f5f5f5; font-weight: bold;">Basic Information</td>
                                        </tr>
                                        <tr>
                                            <td>date</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Appointment date (YYYY-MM-DD)</td>
                                        </tr>
                                        <tr>
                                            <td>time</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Appointment time (HH:MM, 24-hour format)</td>
                                        </tr>
                                        <tr>
                                            <td>fname</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Patient first name</td>
                                        </tr>
                                        <tr>
                                            <td>lname</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Patient last name</td>
                                        </tr>
                                        <tr>
                                            <td>email</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Patient email address</td>
                                        </tr>
                                        <tr>
                                            <td>plan</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Payment plan: "subscription" or "medicare"</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="background-color: #f5f5f5; font-weight: bold;">Insurance Information</td>
                                        </tr>
                                        <tr>
                                            <td>insurance_company</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Primary insurance company name</td>
                                        </tr>
                                        <tr>
                                            <td>policyholder_name</td>
                                            <td>string</td>
                                            <td>No</td>
                                            <td>Policyholder name (if different from patient)</td>
                                        </tr>
                                        <tr>
                                            <td>policy_id</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Insurance policy/ID number</td>
                                        </tr>
                                        <tr>
                                            <td>group_number</td>
                                            <td>string</td>
                                            <td>No</td>
                                            <td>Insurance group number</td>
                                        </tr>
                                        <tr>
                                            <td>insurance_plan_type</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Plan type: "HMO", "PPO", "Medicare", "Medicaid", "Other"</td>
                                        </tr>
                                        <tr>
                                            <td>insurance_card_front</td>
                                            <td>string</td>
                                            <td>No</td>
                                            <td>Base64 encoded front image of insurance card (with data URL prefix: "data:image/jpeg;base64,...")</td>
                                        </tr>
                                        <tr>
                                            <td>insurance_card_back</td>
                                            <td>string</td>
                                            <td>No</td>
                                            <td>Base64 encoded back image of insurance card (with data URL prefix: "data:image/jpeg;base64,...")</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="background-color: #f5f5f5; font-weight: bold;">Medical Information</td>
                                        </tr>
                                        <tr>
                                            <td>chief_complaint</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Chief complaint/reason for visit</td>
                                        </tr>
                                        <tr>
                                            <td>symptom_onset</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>When symptoms started (e.g., "2 weeks", "3 months")</td>
                                        </tr>
                                        <tr>
                                            <td>prior_diagnoses</td>
                                            <td>string</td>
                                            <td>No</td>
                                            <td>Prior medical diagnoses</td>
                                        </tr>
                                        <tr>
                                            <td>current_medications</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Current medications (include dosages)</td>
                                        </tr>
                                        <tr>
                                            <td>allergies</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Known allergies (drugs, environmental, etc.)</td>
                                        </tr>
                                        <tr>
                                            <td>past_surgical_history</td>
                                            <td>string</td>
                                            <td class="required">Yes</td>
                                            <td>Past surgical history</td>
                                        </tr>
                                        <tr>
                                            <td>family_medical_history</td>
                                            <td>string</td>
                                            <td>No</td>
                                            <td>Relevant family medical history</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="background-color: #fff3cd; font-weight: bold;">Payment Information (Required for Medicare Plan Only - Processed on Frontend)</td>
                                        </tr>
                                        <tr>
                                            <td>payment_intent_id</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Stripe Payment Intent ID returned from frontend payment (e.g., "pi_3MtwBw...")</td>
                                        </tr>
                                        <tr>
                                            <td>charge_id</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Stripe Charge ID from completed payment (e.g., "ch_3MtwBw...")</td>
                                        </tr>
                                        <tr>
                                            <td>payment_status</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Payment status (e.g., "completed", "succeeded")</td>
                                        </tr>
                                        <tr>
                                            <td>amount</td>
                                            <td>number</td>
                                            <td>Conditional</td>
                                            <td>Amount paid (e.g., 50)</td>
                                        </tr>
                                        <tr>
                                            <td>currency</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Currency code (e.g., "USD")</td>
                                        </tr>
                                        <tr>
                                            <td>users_full_name</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Full name used for payment</td>
                                        </tr>
                                        <tr>
                                            <td>users_address</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Billing address used for payment</td>
                                        </tr>
                                        <tr>
                                            <td>users_email</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Email address used for payment receipt</td>
                                        </tr>
                                        <tr>
                                            <td>users_phone</td>
                                            <td>string</td>
                                            <td>Conditional</td>
                                            <td>Contact phone number</td>
                                        </tr>
                                        <tr>
                                            <td>country_code</td>
                                            <td>string</td>
                                            <td>No</td>
                                            <td>Phone country code (e.g., "+1")</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="request-response">
                            <div class="response">
                                <span class="detail-title">Success Response (201) - Subscription Plan:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "success",
                "message": "Booking Successful!",
                "data": {
                    "appointment_uid": "SA2511-0001",
                    "date": "2025-07-15",
                    "time": "10:00",
                    "plan": "subscription"
                }
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Success Response (201) - Medicare Plan:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "success",
                "message": "Payment and booking completed successfully!",
                "data": {
                    "appointment_id": 123,
                    "appointment_uid": "SA2511-0001",
                    "date": "2025-07-15",
                    "time": "10:00",
                    "plan": "medicare",
                    "payment_details": {
                        "payment_intent_id": "pi_3MtwBwLkdIwHu7ix28a3tqPa",
                        "charge_id": "ch_3MtwBwLkdIwHu7ix28a3tqPa",
                        "payment_status": "completed",
                        "amount": 50,
                        "currency": "USD"
                    }
                }
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (400) - No Active Subscription:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "Your subscription is inactive or expired."
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (400) - Duplicate Appointment:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "You already booked an appointment at this date and time"
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (422) - Validation Failed:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "Validation failed",
                "errors": {
                    "insurance_company": ["The insurance company field is required."],
                    "chief_complaint": ["The chief complaint field is required."],
                    "payment_intent_id": ["The payment intent id field is required when plan is medicare."]
                }
            }</pre>
                                </div>
                            </div>
                            <div class="response">
                                <span class="detail-title">Error Response (500) - Booking Failed:</span>
                                <div class="code-block">
                                    <pre>{
                "type": "error",
                "message": "Booking failed: [error details]"
            }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint-notes">
                    <h3>Important Notes:</h3>
                    <ul>
                        <li><strong>Payment Processing Flow:</strong> For Medicare plans, the frontend must handle all Stripe payment processing. The backend only receives and stores payment details that have already been confirmed by Stripe on the frontend.</li>
                        <li><strong>Frontend Payment Steps (Medicare):</strong>
                            <ol style="margin-top: 10px;">
                                <li>Call <code>/api/appointments/initiate</code> to get Stripe configuration (stripe_key, amount, currency)</li>
                                <li>Use Stripe.js on frontend to create Payment Intent and process payment</li>
                                <li>On successful payment, extract payment_intent_id, charge_id, and payment_status</li>
                                <li>Send all booking data along with payment details to <code>/api/appointments/complete</code></li>
                            </ol>
                        </li>
                        <li><strong>Plan Types:</strong> The system supports only "subscription" and "medicare" plans. The "cash" plan option has been removed from the API.</li>
                        <li><strong>Subscription Validation:</strong> For subscription plans, the system validates that the user has an active subscription with status "active", "trialing", or "paid" and that the subscription has not expired.</li>
                        <li><strong>Insurance Cards:</strong> Insurance card images should be sent as base64-encoded strings with the data URL prefix (e.g., "data:image/jpeg;base64,/9j/4AAQ..."). Maximum file size is 5MB per image.</li>
                        <li><strong>Appointment UID Format:</strong> Appointments are assigned a unique ID in the format "SA[YY][MM]-[XXXX]" where YY is year, MM is month, and XXXX is a sequential number padded to 4 digits.</li>
                        <li><strong>Duplicate Prevention:</strong> The system prevents booking duplicate appointments for the same date and time for the same patient by checking existing bookings before creation.</li>
                        <li><strong>Notifications:</strong> A notification is automatically created for the patient after successful booking, with different messages for subscription vs medicare plans.</li>
                        <li><strong>Conditional Fields:</strong> Payment-related fields (payment_intent_id, charge_id, payment_status, amount, currency, users_full_name, users_address, users_email, users_phone) are only required when plan is "medicare".</li>
                        <li><strong>Backend Role:</strong> The backend does NOT process any payments. It only validates booking data and stores payment information that was already successfully processed on the frontend via Stripe.</li>
                        <li><strong>Error Handling:</strong> All validation errors return status 422 with detailed error messages. System errors return status 500 with error details logged.</li>
                    </ul>
                </div>
            </section>









            <!-- Chat APIs Section -->
            <section id="chat-history" class="section">
                <div class="section-header">
                    <h2>Chat History</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/chat-history</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get chat history</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "chat_history": [{
            "id": 1,
            "sent_by": "PA25060001",
            "sender_type": "patient",
            "received_by": "PR123",
            "receiver_type": "provider",
            "main_message": "Hello Doctor",
            "message_type": "text",
            "status": "seen",
            "created_at": "2023-07-10 14:30:00",
            "sender_name": "John Doe",
            "sender_image": "path/to/profile.jpg",
            "receiver_name": "Dr. Smith",
            "receiver_image": "path/to/doctor.jpg",
            "is_me": true
        }],
        "pod_name": "A",
        "total_unread": 3
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Related Chats Section -->
            <section id="related-chats" class="section">
                <div class="section-header">
                    <h2>Related Chats</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/fetch-related-chats</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Fetch related chat messages with a specific provider</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>message_with</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Provider ID to chat with</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": [{
            "id": 1,
            "sent_by": "PA25060001",
            "received_by": "PR123",
            "main_message": "Hello Doctor",
            "message_type": "text",
            "status": "seen",
            "created_at": "2023-07-10 14:30:00"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Send Message Section -->
            <section id="send-message" class="section">
                <div class="section-header">
                    <h2>Send Message</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/add-new-message</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Send new text message</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>send_text_to</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Provider ID to send message to</td>
                                    </tr>
                                    <tr>
                                        <td>message</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Message content</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Message sent successfully"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Send Image Section -->
            <section id="send-image" class="section">
                <div class="section-header">
                    <h2>Send Image</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/send-image-message</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Send image message</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>send_text_to</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Provider ID to send message to</td>
                                    </tr>
                                    <tr>
                                        <td>image</td>
                                        <td>file</td>
                                        <td>Yes</td>
                                        <td>Image file to send</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Image sent successfully",
        "image_url": "/message_imgs/img_123456.jpg"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Update Seen Status Section -->
            <section id="update-seen-status" class="section">
                <div class="section-header">
                    <h2>Update Seen Status</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/update-message-seen</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Update message seen status</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>receiverId</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Current user ID</td>
                                    </tr>
                                    <tr>
                                        <td>senderId</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Provider ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Status updated successfully"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SugarPros AI Chat Section -->
            <section id="sugarpros-ai" class="section">
                <div class="section-header">
                    <h2>SugarPros AI Chat</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/sugarpro-ai</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get AI chat sessions and messages</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Query Parameters:</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>chatuid</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Optional chat session ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "chatSessions": [{
                "chatuid": "Chat_1234567890",
                "first_message": {
                    "id": 1,
                    "message": "Hello AI",
                    "created_at": "2023-07-10 14:30:00"
                }
            }],
            "chats": [{
                "id": 1,
                "requested_by": 2,
                "requested_to": "AI",
                "chatuid": "Chat_1234567890",
                "message_of_uid": "PA25060001",
                "message": "Hello AI",
                "created_at": "2023-07-10 14:30:00"
            }],
            "allChats": [{
                "id": 1,
                "chatuid": "Chat_1234567890",
                "message": "Hello AI",
                "created_at": "2023-07-10 14:30:00"
            }],
            "currentChatUid": "Chat_1234567890"
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- AI Response Section -->
            <section id="ai-response" class="section">
                <div class="section-header">
                    <h2>AI Response</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/chatgpt-response</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get AI response to user message</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Body (form-data):</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>message</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>User message</td>
                                    </tr>
                                    <tr>
                                        <td>chatuid</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Chat session ID</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Hello! How can I help you today?"
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Clear Chat Section -->
            <section id="clear-chat" class="section">
                <div class="section-header">
                    <h2>Clear Chat Session</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method post">POST</span>
                        <span class="url">/api/clear-chat-session</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Clear current chat session and start new one</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "newChatUid": "Chat_9876543210"
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Medical Data APIs Section -->
            <section id="clinical-notes" class="section">
                <div class="section-header">
                    <h2>Clinical Notes</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/clinical-notes</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get clinical notes</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "clinical_notes": [{
            "id": 1,
            "appointment_uid": "SA2307-0001",
            "provider_id": "PR123",
            "note_type": "diagnosis",
            "note_content": "Patient shows signs of improvement",
            "created_at": "2023-07-10 14:30:00"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quest Lab Section -->
            <section id="quest-lab" class="section">
                <div class="section-header">
                    <h2>Quest Lab Results</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/quest-lab</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get Quest lab results</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "quest_lab": [{
            "id": 1,
            "appointment_uid": "SA2307-0001",
            "provider_id": "PR123",
            "test_name": "Blood Glucose",
            "test_result": "120 mg/dL",
            "reference_range": "70-140 mg/dL",
            "created_at": "2023-07-10 14:30:00"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- E-Prescriptions Section -->
            <section id="e-prescriptions" class="section">
                <div class="section-header">
                    <h2>E-Prescriptions</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/e-prescriptions</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get e-prescriptions</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "e_prescription": [{
            "id": 1,
            "appointment_uid": "SA2307-0001",
            "provider_id": "PR123",
            "medication_name": "Metformin",
            "dosage": "500mg",
            "frequency": "Twice daily",
            "created_at": "2023-07-10 14:30:00"
        }]
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Dexcom Integration Section -->
            <section id="dexcom" class="section">
                <div class="section-header">
                    <h2>Dexcom Integration</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/dexcom</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get Dexcom dashboard data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "chart_data": {
                "labels": ["08:00", "09:00", "10:00"],
                "values": [120, 135, 110],
                "trends": ["steady", "rising", "falling"]
            },
            "latest_reading": {
                "time": "10:00",
                "value": 110,
                "trend": "falling",
                "trend_rate": -0.5,
                "timestamp": "2023-07-10T10:00:00Z"
            },
            "history": [{
                "time": "08:00",
                "value": 120,
                "trend": "steady",
                "trend_rate": 0,
                "timestamp": "2023-07-10T08:00:00Z"
            }],
            "device_info": {
                "last_sync": "July 10, 2023",
                "sync_time": "10:30 AM",
                "sensor_id": "DEX12345",
                "battery": "Full",
                "start_date": "July 5, 2023",
                "end_date": "July 15, 2023",
                "device_model": "Dexcom G6"
            }
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/connect-dexcom</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Connect to Dexcom account</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "auth_url": "https://api.dexcom.com/v2/oauth2/login?client_id=12345&redirect_uri=https://example.com/callback"
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/dexcom-callback</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Dexcom OAuth callback</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Query Parameters:</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>code</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Authorization code from Dexcom</td>
                                    </tr>
                                    <tr>
                                        <td>state</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>CSRF token</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "message": "Dexcom account connected successfully!",
        "data": {
            "access_token": "dexcom_access_token_123",
            "expires_in": 3600,
            "refresh_token": "dexcom_refresh_token_456"
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FatSecret Integration Section -->
            <section id="fatsecret" class="section">
                <div class="section-header">
                    <h2>FatSecret Integration</h2>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/nutrition-tracker</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get FatSecret dashboard data</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "foods": [{
                "id": "12345",
                "name": "Chicken Soup",
                "brand": "Homemade",
                "calories": 120,
                "protein": 10,
                "carbs": 8,
                "fat": 5,
                "serving_description": "1 cup"
            }]
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/nutrition-tracker/search</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Search foods in FatSecret database</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">Query Parameters:</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>search</td>
                                        <td>string</td>
                                        <td>No</td>
                                        <td>Search term (default: "chicken soup")</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "foods": [{
                "id": "12345",
                "name": "Chicken Soup",
                "brand": "Homemade",
                "calories": 120,
                "protein": 10,
                "carbs": 8,
                "fat": 5,
                "serving_description": "1 cup"
            }],
            "search_term": "chicken soup",
            "count": 1
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/nutrition-tracker/food/{foodId}</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get food details from FatSecret</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                        <span class="detail-title">URL Parameters:</span>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>foodId</td>
                                        <td>string</td>
                                        <td>Yes</td>
                                        <td>Food ID from search results</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>{
        "type": "success",
        "data": {
            "food": {
                "id": "12345",
                "name": "Chicken Soup",
                "brand": "Homemade",
                "calories": 120,
                "protein": 10,
                "carbs": 8,
                "fat": 5,
                "fiber": 2,
                "sugar": 3,
                "sodium": 800,
                "serving_description": "1 cup",
                "food_url": "https://platform.fatsecret.com/food/12345",
                "health_score": 75,
                "servings": [{
                    "serving_id": "1",
                    "serving_description": "1 cup",
                    "calories": 120,
                    "protein": 10,
                    "carbs": 8,
                    "fat": 5
                }]
            }
        }
    }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/nutrition-tracker/breakfast</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get breakfast food suggestions</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>Same structure as /api/nutrition-tracker/search with breakfast-related items</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/nutrition-tracker/lunch</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get lunch food suggestions</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>Same structure as /api/nutrition-tracker/search with lunch-related items</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="endpoint">
                    <div class="endpoint-header">
                        <span class="method get">GET</span>
                        <span class="url">/api/nutrition-tracker/dinner</span>
                    </div>
                    <div class="endpoint-description">
                        <p>Get dinner food suggestions</p>
                    </div>
                    <div class="endpoint-details">
                        <span class="detail-title">Headers:</span>
                        <div class="code-block">
                            <pre>Authorization: Bearer [JWT_TOKEN]</pre>
                        </div>
                    </div>
                    <div class="request-response">
                        <div class="response">
                            <span class="detail-title">Success Response (200):</span>
                            <div class="code-block">
                                <pre>Same structure as /api/nutrition-tracker/search with dinner-related items</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>












    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();

                // Remove active class from all links
                $('.sidebar-nav a').removeClass('active');

                // Add active class to clicked link
                $(this).addClass('active');

                // Get target element
                const target = $($(this).attr('href'));

                if (target.length) {
                    // Scroll to target
                    $('html, body').animate({
                        scrollTop: target.offset().top - 20
                    }, 500);
                }
            });

            // Highlight active section in sidebar
            $(window).on('scroll', function() {
                const scrollPosition = $(window).scrollTop();

                $('.section').each(function() {
                    const sectionId = $(this).attr('id');
                    const sectionTop = $(this).offset().top - 30;
                    const sectionBottom = sectionTop + $(this).outerHeight();

                    if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                        $('.sidebar-nav a').removeClass('active');
                        $('.sidebar-nav a[href="#' + sectionId + '"]').addClass('active');
                    }
                });
            });

            // JSON syntax highlighting
            function syntaxHighlight(json) {
                if (typeof json != 'string') {
                    json = JSON.stringify(json, undefined, 2);
                }
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return json.replace(
                    /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                    function(match) {
                        let cls = 'json-number';
                        if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'json-key';
                            } else {
                                cls = 'json-string';
                            }
                        } else if (/true|false/.test(match)) {
                            cls = 'json-boolean';
                        } else if (/null/.test(match)) {
                            cls = 'json-null';
                        }
                        return '<span class="' + cls + '">' + match + '</span>';
                    }
                );
            }

            // Apply syntax highlighting to all code blocks
            $('.code-block pre').each(function() {
                const code = $(this).text();
                $(this).html(syntaxHighlight(code));
            });

            // Tab functionality
            $('.tab').on('click', function() {
                const tabId = $(this).data('tab');
                const tabContainer = $(this).closest('.tab-container');

                // Remove active class from all tabs and contents
                tabContainer.find('.tab').removeClass('active');
                tabContainer.find('.tab-content').removeClass('active');

                // Add active class to clicked tab and corresponding content
                $(this).addClass('active');
                tabContainer.find('#' + tabId).addClass('active');
            });
        });
    </script>
</body>

</html>
