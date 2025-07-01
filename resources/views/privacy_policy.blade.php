@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('link')

@endsection

@section('style')
    <style>
        .privacy-content h2 {
            font-size: 24px;
            font-weight: 600;
            color: #133A59;
            margin: 30px 0 15px;
        }

        .privacy-content h3 {
            font-size: 20px;
            font-weight: 600;
            color: #298AAB;
            margin: 25px 0 12px;
        }

        .privacy-content p,
        .privacy-content li {
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .privacy-content ul {
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
                <h1 class="mb-2 text-[40px] text-[#133A59]">Our Privacy Policy</h1>
                <div class="flex items-center justify-center text-[15px] text-gray-500">
                    <a href="/" class="hover:text-[#133A59]">Home</a>
                    <span class="mx-2">/</span>
                    <span>Privacy Policy</span>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 py-16 bg-white md:px-20">
        <div class="max-w-4xl mx-auto privacy-content">
            {{-- <p class="mb-8">Last Updated: [Insert Date]</p> --}}

            <h2>1. Introduction</h2>
            <p>Welcome to {{$brandname}} ("we," "our," or "us"). We are committed to protecting your
                personal information and your right to privacy. This Privacy Policy explains how we collect, use, disclose,
                and safeguard your information when you visit our website and use our services to connect with healthcare
                providers for diabetes care.</p>

            <h2>2. Information We Collect</h2>
            <h3>Personal Information You Provide:</h3>
            <ul>
                <li><strong>Account Information:</strong> When you register, we collect your name, email address, phone
                    number, date of birth, and other contact details.</li>
                <li><strong>Health Information:</strong> We collect medical history, diabetes-related information, treatment
                    preferences, and other health data you provide when booking appointments or using our services.</li>
                <li><strong>Payment Information:</strong> For paid services, we collect billing details and payment card
                    information (processed securely by our payment processor).</li>
                <li><strong>Provider Information:</strong> For healthcare providers, we collect professional credentials,
                    licenses, and practice information.</li>
            </ul>

            <h3>Automatically Collected Information:</h3>
            <ul>
                <li><strong>Usage Data:</strong> We collect information about how you interact with our website, including
                    IP address, browser type, pages visited, and timestamps.</li>
                <li><strong>Cookies:</strong> We use cookies to enhance your experience and analyze site usage.</li>
            </ul>

            <h2>3. How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Provide and maintain our diabetes care services</li>
                <li>Match you with appropriate healthcare providers</li>
                <li>Process appointments and payments</li>
                <li>Communicate with you about your care</li>
                <li>Improve our services and website functionality</li>
                <li>Comply with legal obligations and healthcare regulations</li>
            </ul>

            <h2>4. Sharing of Information</h2>
            <p>We may share your information in these limited circumstances:</p>
            <ul>
                <li><strong>With Healthcare Providers:</strong> To facilitate your diabetes care, we share relevant health
                    information with the providers you choose to consult.</li>
                <li><strong>Service Providers:</strong> We may share data with vendors who help operate our platform (e.g.,
                    hosting, payment processing).</li>
                <li><strong>Legal Requirements:</strong> We may disclose information if required by law or to protect rights
                    and safety.</li>
                <li><strong>Business Transfers:</strong> In case of merger or acquisition, user information may be
                    transferred.</li>
            </ul>

            <h2>5. Data Security</h2>
            <p>We implement appropriate technical and organizational measures to protect your personal and health
                information, including:</p>
            <ul>
                <li>Encryption of sensitive data</li>
                <li>Secure servers and network infrastructure</li>
                <li>Access controls and authentication protocols</li>
                <li>Regular security assessments</li>
            </ul>
            <p>However, no internet transmission is 100% secure, and we cannot guarantee absolute security.</p>

            <h2>6. Your Privacy Rights</h2>
            <p>Depending on your location, you may have rights to:</p>
            <ul>
                <li>Access and receive a copy of your personal data</li>
                <li>Request correction of inaccurate information</li>
                <li>Request deletion of your data under certain conditions</li>
                <li>Object to or restrict processing of your data</li>
                <li>Withdraw consent where applicable</li>
            </ul>
            <p>To exercise these rights, please contact us at [contact email].</p>

            <h2>7. Health Information Specifics</h2>
            <p>As a healthcare platform, we adhere to additional protections for health information:</p>
            <ul>
                <li>We maintain appropriate safeguards as required by applicable health privacy laws</li>
                <li>Healthcare providers are independently responsible for complying with professional confidentiality
                    obligations</li>
                <li>We do not sell your health information</li>
            </ul>

            <h2>8. Children's Privacy</h2>
            <p>Our services are not directed to children under 13. For minors with diabetes, accounts must be created by a
                parent or legal guardian.</p>

            <h2>9. Changes to This Policy</h2>
            <p>We may update this Privacy Policy periodically. We will notify you of significant changes by posting the new
                policy on our website with an updated effective date.</p>

            <h2>10. Contact Us</h2>
            <p>If you have questions about this Privacy Policy or our privacy practices, please contact us at:</p>
            <p>{{ $brandname }}<br>
                Email: {{ $contact_email }}<br>
                Phone: {{ $contact_phone }}</p>
        </div>
    </section>

@endsection

@section('script')

@endsection
