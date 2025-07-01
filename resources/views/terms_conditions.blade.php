@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('link')

@endsection

@section('style')
    <style>
        .terms-content h2 {
            font-size: 24px;
            font-weight: 600;
            color: #133A59;
            margin: 30px 0 15px;
        }

        .terms-content h3 {
            font-size: 20px;
            font-weight: 600;
            color: #298AAB;
            margin: 25px 0 12px;
        }

        .terms-content p,
        .terms-content li {
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .terms-content ul {
            list-style-type: disc;
            padding-left: 20px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')

    <section>
        <div class="max-w-full md:min-h-[250px] flex justify-center items-center bg-[#133A59]/10 px-4 py-8 mx-auto">
            <div class="text-center">
                <h1 class="mb-2 text-[40px] text-[#133A59]">Terms & Conditions</h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="/" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>Terms & Conditions</span>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 py-16 bg-white md:px-20">
        <div class="max-w-4xl mx-auto terms-content">
            {{-- <p class="mb-8">Last Updated: [Insert Date]</p> --}}

            <h2>1. Acceptance of Terms</h2>
            <p>By accessing or using {{$brandname}} ("the Platform"), you agree to be bound by these
                Terms & Conditions. If you do not agree with any part of these terms, you must not use our services.</p>

            <h2>2. Description of Services</h2>
            <p>The Platform provides:</p>
            <ul>
                <li>Connection to healthcare providers specializing in diabetes care (endocrinologists, dietitians, diabetes
                    educators, etc.)</li>
                <li>Appointment scheduling and management tools</li>
                <li>Educational resources about diabetes management</li>
                <li>Secure communication channels with healthcare professionals</li>
            </ul>

            <h2>3. User Accounts</h2>
            <h3>3.1 Registration</h3>
            <p>To access certain features, you must create an account providing accurate and complete information.</p>

            <h3>3.2 Account Security</h3>
            <p>You are responsible for maintaining the confidentiality of your login credentials and all activities under
                your account.</p>

            <h3>3.3 Account Termination</h3>
            <p>We reserve the right to suspend or terminate accounts that violate these terms or engage in harmful
                activities.</p>

            <h2>4. Healthcare Services</h2>
            <h3>4.1 Provider Relationships</h3>
            <p>The Platform facilitates connections with healthcare providers but does not provide medical services
                directly. Providers are independent practitioners responsible for their own services.</p>

            <h3>4.2 Not for Emergencies</h3>
            <p>Our services are not for medical emergencies. In case of emergency, contact local emergency services
                immediately.</p>

            <h3>4.3 No Guarantee of Outcomes</h3>
            <p>We do not guarantee specific health outcomes from using our services or consulting with providers.</p>

            <h2>5. Payments and Billing</h2>
            <ul>
                <li>Fees for provider services will be clearly disclosed before booking</li>
                <li>Payment processing is handled by secure third-party providers</li>
                <li>Refund policies vary by provider and will be disclosed at time of service</li>
                <li>We reserve the right to change our fee structure with prior notice</li>
            </ul>

            <h2>6. Intellectual Property</h2>
            <p>All content on the Platform (text, graphics, logos, software) is our property or licensed to us and protected
                by intellectual property laws. You may not reproduce, distribute, or create derivative works without
                permission.</p>

            <h2>7. User Responsibilities</h2>
            <p>You agree to:</p>
            <ul>
                <li>Provide accurate health information to providers</li>
                <li>Not use the Platform for illegal or unauthorized purposes</li>
                <li>Not interfere with the Platform's operation or security features</li>
                <li>Comply with all applicable laws and regulations</li>
            </ul>

            <h2>8. Privacy</h2>
            <p>Your use of the Platform is governed by our Privacy Policy, which explains how we collect, use, and protect
                your personal and health information.</p>

            <h2>9. Disclaimer of Warranties</h2>
            <p>The Platform is provided "as is" without warranties of any kind. We do not warrant that:</p>
            <ul>
                <li>The service will be uninterrupted or error-free</li>
                <li>The results obtained from using the service will be accurate or reliable</li>
                <li>The quality of any products, services, or information obtained will meet your expectations</li>
            </ul>

            <h2>10. Limitation of Liability</h2>
            <p>To the maximum extent permitted by law, we shall not be liable for any indirect, incidental, special, or
                consequential damages resulting from:</p>
            <ul>
                <li>Your use or inability to use the service</li>
                <li>Unauthorized access to or alteration of your transmissions</li>
                <li>Statements or conduct of any third party on the service</li>
                <li>Any other matter relating to the service</li>
            </ul>

            <h2>11. Modifications to Terms</h2>
            <p>We reserve the right to modify these terms at any time. Continued use after changes constitutes acceptance of
                the modified terms.</p>

            <h2>12. Governing Law</h2>
            <p>These terms shall be governed by the laws of [Your Country/State] without regard to its conflict of law
                provisions.</p>

            <h2>13. Contact Information</h2>
            <p>For questions about these Terms & Conditions, please contact us at:</p>
            <p>{{ $brandname }}<br>
                Email: {{ $contact_email }}<br>
                Phone: {{ $contact_phone }}</p>
        </div>
    </section>

@endsection

@section('script')

@endsection
