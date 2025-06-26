@extends('layouts.patient_portal')

@section('title', 'Account')

@section('link')

@endsection

@section('style')
    <style>
        .notification {
            font-weight: bold;
            color: #2889AA !important;
        }

        .notification_nav {
            background: #c6edfa !important;
        }
    </style>

@endsection


@section('content')

    @include('layouts.patient_header')

    <div class="bg-gray-100 ">
        <div class=" md:flex md:max-w-7xl py-[2rem] mx-auto rounded-lg">
            <!-- Sidebar -->
            <aside class="md:w-64 bg-white rounded-l-lg">
                <h3 class="border-b text-[18px] p-4 font-bold border[#0000001A]/10">Notification</h3>
                <nav class="space-y-4 p-4 text-[14px]">
                    <a href="/account" class="flex items-center  text-[#000000] space-x-2">
                        <i class="fas fa-user"></i>
                        <span>Account</span>
                    </a>
                    <a href="/settings" class="flex items-center text-[#000000] hover:text-[#2889AA] space-x-2">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="/notifications"
                        class="flex items-center text-[#000000] notification hover:text-[#2889AA] space-x-2">
                        <i class="fas fa-bell"></i>
                        <span>Notification</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 bg-white border-l rounded-r-lg border-[#00000A]/10 p-4">
                <h3 class="text-[18px] font-bold ">Your All Notifications</h3>

                <ul class="divide-y divide-gray-200 mt-2">
                    <!-- Unread Notifications -->
                    @forelse ($notifications as $notification)
                        @if ($notification->read_status == 0)
                            <li class="flex items-start py-4 bg-blue-50 rounded mb-2 px-4">
                                <div class="ml-3 flex-1">
                                    <p class="font-semibold text-gray-900">{!!$notification->notification !!}</p>
                                    <span class="text-xs text-gray-500">
                                        {{ $notification->created_at->format('g:i A') }}, {{ $notification->created_at->format('jS F Y') }}
                                    </span>
                                </div>
                                <a href="/delete-notification/{{ $notification->id }}" class="ml-4 text-gray-400 hover:text-red-500 mt-2" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <span class="ml-2 inline-block w-2 h-2 bg-blue-500 rounded-full mt-2"></span>
                            </li>
                        @else
                            <li class="flex items-start py-4 px-4">
                                <div class="ml-3 flex-1">
                                    <p class="text-gray-700">{!!$notification->notification !!}</p>
                                    <span class="text-xs text-gray-500">
                                        {{ $notification->created_at->format('g:i A') }}, {{ $notification->created_at->format('jS F Y') }}
                                    </span>
                                </div>
                                <a href="/delete-notification/{{ $notification->id }}" class="ml-4 text-gray-400 hover:text-red-500 mt-2" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </li>
                        @endif
                    @empty
                        <li class="mt-10">
                            <h3 class="text-center font-medium text-xl text-gray-400">No notifications to show!</h3>
                        </li>
                    @endforelse
                </ul>

            </div>
        </div>
    </div>






@endsection

@section('script')
@endsection
