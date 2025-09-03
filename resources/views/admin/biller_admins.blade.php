@extends('layouts.admin_app')

@section('title', 'Biller Admin Management')

@section('link')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
@endsection

@section('content')
    <main class="mx-auto my-12 md:max-w-6xl">
        <div class="space-y-6">
            <!-- Add New Biller Admin Form -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Biller Admin Management</h2>
                <form action="/admin/add-new-biller-admin" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="biller_name" class="block text-sm font-medium text-gray-700 mb-1">Biller
                                Name</label>
                            <input type="text" id="biller_name" name="biller_name" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label for="biller_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="biller_email" name="biller_email" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="text" id="password" name="password" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                            Add New Biller Admin
                        </button>
                    </div>
                </form>
            </div>

            <!-- Biller Admins Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">All Biller Admins</h2>
                    <div class="mt-2 md:mt-0">
                        <label class="text-sm text-gray-600 mr-2">Show entries:</label>
                        <select class="border border-gray-300 rounded px-3 py-1 text-sm">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table id="billerAdminsTable" class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Password</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Login</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($all_biller_admins as $biller)
                                <tr class="hover:bg-gray-50 transition">
                                    <form action="/admin/biller-admin/edit" method="post">
                                        @csrf
                                        <input type="hidden" name="biller_admin_id" value="{{ $biller->biller_admin_id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">
                                            {{ $biller->biller_admin_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="text" name="biller_name" value="{{ $biller->biller_name }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                                data-id="{{ $biller->biller_admin_id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="email" name="biller_email" value="{{ $biller->biller_email }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                                data-id="{{ $biller->biller_admin_id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="text" name="password" value="{{ $biller->password }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                                data-id="{{ $biller->biller_admin_id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                            {{ $biller->last_login_time ? \Carbon\Carbon::parse($biller->last_login_time)->format('M j, Y g:i A') : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                            {{ $biller->last_activity ? \Carbon\Carbon::parse($biller->last_activity)->format('M j, Y g:i A') : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-3">
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 transition"
                                                    title="Save Changes">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <a href="/admin/biller-admin/remove/{{ $biller->biller_admin_id }}"
                                                    class="text-red-600 hover:text-red-800 transition"
                                                    onclick="return confirm('Are you sure you want to delete this biller admin?')"
                                                    title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex flex-col md:flex-row items-center justify-between">
                    <div class="text-sm text-gray-600 mb-2 md:mb-0">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">1</span> of <span
                            class="font-medium">1</span> entries
                    </div>
                    <div class="flex space-x-2">
                        <button
                            class="px-3 py-1 border border-gray-300 rounded text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                            disabled>
                            Previous
                        </button>
                        <button
                            class="px-3 py-1 border border-gray-300 rounded text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        $(".biller_admins").addClass("font-semibold");

        $(document).ready(function() {
            $('#billerAdminsTable').DataTable({
                responsive: true,
                dom: '<"flex flex-col md:flex-row md:items-center md:justify-between"<"mb-4"l><"mb-4 md:mb-0"f>>rt<"flex flex-col md:flex-row items-center justify-between"<"mb-4"i><"mb-4 md:mb-0"p>>',
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                },
                initComplete: function() {
                    $('.dataTables_filter input').addClass(
                        'border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    );
                    $('.dataTables_length select').addClass(
                        'border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500'
                    );
                }
            });
        });
    </script>
@endsection
