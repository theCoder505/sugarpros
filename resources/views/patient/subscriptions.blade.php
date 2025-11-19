@extends('layouts.patient_portal')

@section('title', 'Your Subscriptions')

@section('link')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('style')
    <style>
        .active-plan {
            border: 2px solid #2889AA;
            position: relative;
        }

        .active-plan-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #2889AA;
            color: white;
            padding: 2px 10px;
            font-size: 12px;
            border-bottom-left-radius: 5px;
        }

        .plan-card {
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .common_option {
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 300;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .active_option {
            background: #2889AA;
            color: white;
            font-weight: 500;
        }

        .toggle-faq .icon {
            transition: transform 0.3s ease;
        }

        .toggle-faq.active .icon {
            transform: rotate(45deg);
        }

        .highlight-box {
            border-left: 4px solid #2889AA;
            background: rgba(40, 138, 170, 0.05);
        }

        @media (max-width: 768px) {
            .plan-cards-container {
                flex-direction: column;
            }

            .plan-card-wrapper {
                max-width: 100%;
                width: 100%;
                margin-bottom: 1.5rem;
            }
        }
    </style>
@endsection

@section('content')
    @include('layouts.patient_header')

    <section class="px-6 py-16 bg-[#F3F4F6] md:px-20">

        <div class="container mx-auto mb-4 text-3xl text-black font-semibold">
            Pricing Plan
        </div>

        <div class="container mx-auto bg-white p-4 md:p-12 rounded-xl">
            <div class="mx-auto mb-8">
                <h2 class="text-[28px] md:text-[35px] font-semibold mt-8 text-center">
                    We believe world-class diabetes care should be <br class="hidden md:block"> accessible and affordable.
                </h2>
                <p class="mt-3 text-gray-600 text-center text-sm md:text-base">
                    No hidden fees. No surprise bills. Just straightforward pricing that puts your health first.
                </p>
            </div>

            <!-- Current Plan Banner -->
            @if ($current_plan)
                <div class="flex justify-center">
                    <div class="mb-8 inline-block w-full md:w-auto">
                        <div class="highlight-box p-4 rounded-lg flex items-center">
                            <div class="mr-4">
                                <i class="fas fa-check-circle text-3xl text-[#2889AA]"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-[#133A59]">Your Current Plan</h3>
                                <p class="text-gray-700 text-sm">
                                    You're subscribed to the <span
                                        class="font-semibold text-font_color capitalize">{{ $current_recurring_option }}</span>
                                    plan.
                                    @if ($current_recurring_option == 'monthly')
                                        Your next billing date is {{ now()->addMonth()->format('M j, Y') }}.
                                    @else
                                        Your next billing date is {{ now()->addYear()->format('M j, Y') }}.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div
                class="gap-6 mx-auto flex flex-col md:flex-row max-w-6xl justify-center items-stretch plan-cards-container">
                <!-- Medicare Plan Card -->
                <div
                    class="border-gray-300 border rounded-xl p-6 flex flex-col justify-between w-full md:flex-1 md:max-w-[400px] bg-white plan-card plan-card-wrapper">
                    <div>
                        <h3 class="mb-2 text-xl md:text-2xl font-semibold text-[#133A59]">For Medicare Patients</h3>
                        <p class="mb-4 text-xs md:text-sm font-semibold text-gray-800">$0 OUT-OF-POCKET FOR CORE SERVICES
                        </p>
                        <p class="text-[15px] md:text-[16px] text-gray-700 mb-4">Your complete diabetes care covered by
                            Medicare:</p>

                        <ul class="text-[15px] md:text-[16px] pb-6 text-gray-600 space-y-3">
                            <li class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                <span>Monthly virtual visits with endocrinologists</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                <span>Continuous glucose monitors (CGMs)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                <span>Full access to SugarPros AI</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                <span>Annual comprehensive diabetes evaluation</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-5 border-t border-t-gray-300 pt-4">
                        <p class="mb-4 text-[15px] md:text-[16px] text-gray-700">Optional wellness add-ons available at
                            $50/month</p>
                        <a href="#"
                            class="block px-6 py-3 text-white rounded-lg bg-[#2889AA] hover:opacity-90 text-center text-[15px] md:text-[16px] font-semibold transition-all">
                            Continue with plan
                        </a>
                    </div>
                </div>

                <!-- Premium Care Plan Card -->
                <div
                    class="border-gray-300 border rounded-xl overflow-hidden w-full md:flex-1 md:max-w-[400px] bg-white plan-card plan-card-wrapper">
                    <div class="p-6">
                        <h3 class="text-xl md:text-2xl font-semibold text-[#133A59] mb-4">Premium Care Plan</h3>

                        <div class="flex justify-start mb-6">
                            <div class="p-1 bg-[#F3F4F6] rounded-full">
                                <div class="bg-white rounded-full flex">
                                    <button class="common_option active_option" onclick="subscriptionPlan(this)"
                                        data-plan="monthly">
                                        Monthly
                                    </button>
                                    <button class="common_option" onclick="subscriptionPlan(this)" data-plan="annually">
                                        Annually
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Pricing -->
                        <div class="monthlyPlans">
                            <div class="mb-6">
                                <div class="text-4xl md:text-5xl font-bold text-[#133A59]">
                                    ${{ number_format($monthly_premium_amount) }}
                                    <span class="text-base md:text-lg font-normal text-gray-600">/month</span>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h4 class="font-semibold text-[#133A59] mb-3 text-base md:text-lg">What's included:</h4>
                                <ul class="text-[15px] md:text-[16px] text-gray-600 space-y-3">
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>1 visit/month</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Medication management</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Basic lab review</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>CGM data analysis</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Weight loss support</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Urgent care messaging</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Unlimited provider messaging</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Dedicated care coordinator</span>
                                    </li>
                                </ul>
                            </div>

                            <button onclick="continueMonthlyPlan('premium')"
                                class="w-full px-6 py-3 text-white rounded-lg bg-[#2889AA] hover:opacity-90 text-center text-[15px] md:text-[16px] font-semibold transition-all">
                                Continue to pay
                            </button>
                        </div>

                        <!-- Annual Pricing -->
                        <div class="annuallyPlans hidden">
                            <div class="mb-6">
                                <div class="text-4xl md:text-5xl font-bold text-[#133A59]">
                                    ${{ number_format($annual_premium_amount) }}
                                    <span class="text-base md:text-lg font-normal text-gray-600">/year</span>
                                </div>
                                <p class="text-[#2889AA] font-medium mt-2 text-sm md:text-base">
                                    Save ${{ number_format(($monthly_premium_amount * 12) - $annual_premium_amount) }} per year
                                </p>
                            </div>

                            <div class="mb-6">
                                <h4 class="font-semibold text-[#133A59] mb-3 text-base md:text-lg">What's included:</h4>
                                <ul class="text-[15px] md:text-[16px] text-gray-600 space-y-3">
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>1 visit/month</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Medication management</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Basic lab review</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>CGM data analysis</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Weight loss support</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Urgent care messaging</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Unlimited provider messaging</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-[#6A7282] rounded-sm"></div>
                                        <span>Dedicated care coordinator</span>
                                    </li>
                                </ul>
                            </div>

                            <button onclick="continueAnnuallyPlan('premium')"
                                class="w-full px-6 py-3 text-white rounded-lg bg-[#2889AA] hover:opacity-90 text-center text-[15px] md:text-[16px] font-semibold transition-all">
                                Continue to pay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script>
        function subscriptionPlan(passedThis) {
            var plan = passedThis.getAttribute('data-plan');
            $(passedThis).addClass('active_option').siblings().removeClass('active_option');

            if (plan == 'monthly') {
                $('.monthlyPlans').removeClass('hidden');
                $('.annuallyPlans').addClass('hidden');
            } else if (plan == 'annually') {
                $('.annuallyPlans').removeClass('hidden');
                $('.monthlyPlans').addClass('hidden');
            }
        }

        function continueMonthlyPlan(plan) {
            var route = '/subscription/monthly/' + plan;
            window.location.href = route;
        }

        function continueAnnuallyPlan(plan) {
            var route = '/subscription/annually/' + plan;
            window.location.href = route;
        }
    </script>
@endsection
