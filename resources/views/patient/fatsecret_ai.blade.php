@extends('layouts.patient_portal')

@section('title', 'Nutrition Tracker')

@section('link')

@endsection

@section('style')
    <style>
        .loading {
            display: none;
        }

        .loading.show {
            display: block;
        }

        .food-item {
            transition: all 0.3s ease;
        }

        .food-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Modal styles */
        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            width: 90%;
            max-width: 800px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #e5e7eb;
            padding: 1.5rem;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            border-radius: 12px 12px 0 0;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .nutrition-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .nutrition-badge.protein {
            background-color: rgba(234, 179, 8, 0.1);
            color: #b45309;
        }

        .nutrition-badge.carbs {
            background-color: rgba(0, 53, 102, 0.07);
            color: #1e40af;
        }

        .nutrition-badge.fat {
            background-color: rgba(248, 113, 113, 0.09);
            color: #b91c1c;
        }

        .serving-item {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .serving-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 0.75rem;
        }

        .nutrition-item {
            background-color: #f9fafb;
            padding: 0.75rem;
            border-radius: 8px;
        }

        .nutrition-value {
            font-weight: 600;
            color: #111827;
            margin-top: 0.25rem;
        }

        /* Modal loader */
        .modal-loader {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 20;
            border-radius: 12px;
        }

        .modal-loader.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection

@section('content')
    @include('layouts.patient_header')

    <div class="bg-gray-100 min-h-screen p-6">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <h1 class="text-xl font-semibold text-[#000000]">
                    Nutrition Tracker
                </h1>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative flex-1">
                        <input type="text" id="foodSearch" placeholder="Search for foods..."
                            class="md:w-[350px] pl-10 text-white bg-[#133A59] pr-4 py-4 rounded-xl text-sm focus:outline-none placeholder:text-white" />
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
                    </div>

                    <select id="mealTypeSelect"
                        class="w-full sm:w-auto px-4 py-2 text-sm border bg-white border-gray-300 rounded focus:outline-none text-black">
                        <option value="">All Foods</option>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>
            </div>

            <!-- Loading indicator -->
            <div id="loadingIndicator" class="loading text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#133A59]"></div>
                <p class="mt-2 text-gray-600">Searching foods...</p>
            </div>

            <!-- Error message -->
            <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <span id="errorText"></span>
            </div>

            <!-- Food results -->
            <div id="foodResults"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 space-y-5 md:space-y-0 bg-white px-4 py-12 rounded-md">
                @if (isset($foods) && count($foods) > 0)
                    @foreach ($foods as $food)
                        <div class="food-item bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4 cursor-pointer"
                            data-food-id="{{ $food['id'] ?? '' }}">
                            <div class="flex-1 px-2 w-full">
                                <h3 class="text-[18px] font-semibold">{{ $food['name'] ?? 'Unknown Food' }}</h3>
                                @if (isset($food['brand']) && $food['brand'])
                                    <p class="text-xs text-gray-500">{{ $food['brand'] }}</p>
                                @endif

                                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                                    <!-- Protein -->
                                    <div
                                        class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                        <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt="Protein">
                                        {{ number_format($food['protein'] ?? 0, 1) }}g
                                    </div>
                                    <!-- Carbs -->
                                    <div
                                        class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                        <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt="Carbs">
                                        {{ number_format($food['carbs'] ?? 0, 1) }}g
                                    </div>
                                    <!-- Fat -->
                                    <div
                                        class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                        <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt="Fat">
                                        {{ number_format($food['fat'] ?? 0, 1) }}g
                                    </div>
                                </div>

                                <p class="text-xs text-[#1C1917] mt-2">
                                    {{ number_format($food['calories'] ?? 0) }} <span
                                        class="text-[12px] text-[#737373]">kcal</span>
                                </p>

                                @if (isset($food['serving_description']))
                                    <p class="text-xs text-gray-500 mt-1">{{ $food['serving_description'] }}</p>
                                @endif

                                @if (!empty($food['food_url']))
                                    <a href="{{ $food['food_url'] }}" target="_blank"
                                       class="inline-block mt-3 px-3 py-1 bg-[#133A59] text-white rounded-full shadow hover:bg-[#1d4e7a] transition font-normal text-xs">
                                        See Details
                                    </a>
                                @endif
                            </div>

                            <div class="relative w-10 h-10">
                                <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                                    <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="text-green-500" stroke="currentColor" stroke-width="4"
                                        stroke-dasharray="{{ $food['health_score'] ?? 80 }}, 100" fill="none"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div
                                    class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                                    {{ $food['health_score'] ?? 80 }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-600">No foods found. Try searching for something!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Food Details Modal -->
    <div id="foodModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4" onclick="closeModal(event)">
        <div class="modal-content bg-white" onclick="event.stopPropagation()">
            <div class="modal-header">
                <div class="flex justify-between items-center">
                    <h2 id="modalFoodName" class="text-xl font-bold text-gray-800"></h2>
                    <button id="closeModal" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="modalBrand" class="text-sm text-gray-500 mt-1"></div>
            </div>
            
            <div class="modal-body relative">
                <div id="modalLoader" class="modal-loader">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#133A59]"></div>
                        <p class="mt-2 text-gray-600">Loading food details...</p>
                    </div>
                </div>
                
                <div id="modalFoodDetails">
                    <!-- Food details will be populated here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".fatsecret_ai").addClass("active_nav_tab");

        $(document).ready(function() {
            let searchTimeout;

            // Search functionality
            $('#foodSearch').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();

                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        searchFoods(query);
                    }, 500);
                } else if (query.length === 0) {
                    // Load default foods when search is cleared
                    searchFoods('chicken soup');
                }
            });

            // Meal type filter
            $('#mealTypeSelect').on('change', function() {
                const mealType = $(this).val();
                if (mealType) {
                    loadMealTypeFoods(mealType);
                } else {
                    const searchQuery = $('#foodSearch').val().trim();
                    searchFoods(searchQuery || 'chicken soup');
                }
            });

            // Food item click for details
            $(document).on('click', '.food-item', function() {
                const foodId = $(this).data('food-id');
                if (foodId) {
                    showFoodDetails(foodId);
                }
            });

            // Close modal when clicking the close button
            $('#closeModal').on('click', function(e) {
                e.stopPropagation();
                closeModal();
            });

            function searchFoods(query) {
                showLoading(true);
                hideError();

                $.ajax({
                    url: '{{ route('fatsecret.search') }}',
                    method: 'GET',
                    data: {
                        search: query
                    },
                    success: function(response) {
                        showLoading(false);
                        if (response.success && response.foods) {
                            displayFoods(response.foods);
                        } else {
                            showError('No foods found for your search.');
                        }
                    },
                    error: function(xhr) {
                        showLoading(false);
                        showError('Error searching for foods. Please try again.');
                        console.error('Search error:', xhr);
                    }
                });
            }

            function loadMealTypeFoods(mealType) {
                showLoading(true);
                hideError();

                const urls = {
                    'breakfast': '{{ route('fatsecret.breakfast') }}',
                    'lunch': '{{ route('fatsecret.lunch') }}',
                    'dinner': '{{ route('fatsecret.dinner') }}'
                };

                $.ajax({
                    url: urls[mealType],
                    method: 'GET',
                    success: function(response) {
                        showLoading(false);
                        if (response.success && response.foods) {
                            displayFoods(response.foods);
                        } else {
                            showError('No foods found for ' + mealType + '.');
                        }
                    },
                    error: function(xhr) {
                        showLoading(false);
                        showError('Error loading ' + mealType + ' foods. Please try again.');
                        console.error('Meal type error:', xhr);
                    }
                });
            }

            function displayFoods(foods) {
                const container = $('#foodResults');
                container.empty();

                if (foods.length === 0) {
                    container.html(
                        '<div class="col-span-full text-center py-8"><p class="text-gray-600">No foods found.</p></div>'
                        );
                    return;
                }

                foods.forEach(food => {
                    const foodHtml = `
                        <div class="food-item bg-gray-100 rounded-xl shadow-sm p-4 gap-4 cursor-pointer" 
                             data-food-id="${food.id}">
                            <div class="flex flex-col sm:flex-row items-center justify-between">
                                <div class="flex-1 px-2 w-full">
                                    <h3 class="text-[18px] font-semibold">${food.name}</h3>
                                    ${food.brand ? `<p class="text-xs text-gray-500">${food.brand}</p>` : ''}

                                    <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                                        <div class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                            <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt="Protein"> 
                                            ${parseFloat(food.protein || 0).toFixed(1)}g
                                        </div>
                                        <div class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                            <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt="Carbs"> 
                                            ${parseFloat(food.carbs || 0).toFixed(1)}g
                                        </div>
                                        <div class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                            <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt="Fat"> 
                                            ${parseFloat(food.fat || 0).toFixed(1)}g
                                        </div>
                                    </div>

                                    <p class="text-xs text-[#1C1917] mt-2">
                                        ${Math.round(food.calories || 0)} <span class="text-[12px] text-[#737373]">kcal</span>
                                    </p>
                                    
                                    ${food.serving_description ? `<p class="text-xs text-gray-500 mt-1">${food.serving_description}</p>` : ''}
                                </div>

                                <div class="relative w-10 h-10">
                                    <svg class="w-full h-full md:w-[40px] md:h-[40px]" viewBox="0 0 36 36">
                                        <path class="text-gray-200" stroke="currentColor" stroke-width="4" fill="none"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="text-green-500" stroke="currentColor" stroke-width="4" 
                                            stroke-dasharray="${food.health_score || 80}, 100" fill="none"
                                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
                                        ${food.health_score || 80}%
                                    </div>
                                </div>
                            </div>

                            <div class="w-full mt-2">
                                <a href="${food.food_url}" target="_blank"
                                    class="inline-block px-3 py-1 bg-[#133A59] text-white rounded-full shadow hover:bg-[#1d4e7a] transition font-normal text-xs">
                                    See Details
                                </a>
                            </div>
                        </div>
                    `;
                    container.append(foodHtml);
                });
            }

            function showFoodDetails(foodId) {
                // Show modal with loader
                $('#foodModal').removeClass('hidden');
                $('#modalLoader').addClass('active');
                $('#modalFoodDetails').hide();
                
                // Clear previous content
                $('#modalFoodName').text('');
                $('#modalBrand').text('');
                $('#modalFoodDetails').html('');
                
                $.ajax({
                    url: `{{ route('fatsecret.food.details', ':foodId') }}`.replace(':foodId', foodId),
                    method: 'GET',
                    success: function(response) {
                        $('#modalLoader').removeClass('active');
                        $('#modalFoodDetails').show();
                        
                        if (response.success && response.food) {
                            displayFoodModal(response.food);
                        } else {
                            showError('Could not load food details.');
                            $('#modalFoodDetails').html(
                                '<div class="text-center py-8 text-gray-500">Failed to load food details. Please try again.</div>'
                            );
                        }
                    },
                    error: function(xhr) {
                        $('#modalLoader').removeClass('active');
                        $('#modalFoodDetails').show().html(
                            '<div class="text-center py-8 text-gray-500">Error loading food details. Please try again.</div>'
                        );
                        console.error('Food details error:', xhr);
                    }
                });
            }

            function displayFoodModal(food) {
                $('#modalFoodName').text(food.food_name || 'Unknown Food');
                
                if (food.brand_name) {
                    $('#modalBrand').text(food.brand_name);
                } else {
                    $('#modalBrand').text('');
                }

                const servings = Array.isArray(food.servings.serving) ? food.servings.serving : [food.servings.serving];
                let detailsHtml = '';

                // Add health score at the top
                if (food.health_score) {
                    detailsHtml += `
                        <div class="mb-6">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-700">Health Score</h3>
                                <span class="font-bold ${food.health_score >= 70 ? 'text-green-600' : food.health_score >= 50 ? 'text-yellow-600' : 'text-red-600'}">
                                    ${food.health_score}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                <div class="h-2.5 rounded-full ${food.health_score >= 70 ? 'bg-green-500' : food.health_score >= 50 ? 'bg-yellow-500' : 'bg-red-500'}" 
                                     style="width: ${food.health_score}%"></div>
                            </div>
                        </div>
                    `;
                }

                // Add nutrition summary
                detailsHtml += `
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="nutrition-badge protein">
                            <img src="{{ asset('assets/image/ao1.png') }}" class="w-4 mr-1" alt="Protein">
                            Protein: ${food.protein || 0}g
                        </span>
                        <span class="nutrition-badge carbs">
                            <img src="{{ asset('assets/image/ao2.png') }}" class="w-4 mr-1" alt="Carbs">
                            Carbs: ${food.carbohydrate || 0}g
                        </span>
                        <span class="nutrition-badge fat">
                            <img src="{{ asset('assets/image/ao3.png') }}" class="w-4 mr-1" alt="Fat">
                            Fat: ${food.fat || 0}g
                        </span>
                    </div>
                `;

                servings.forEach((serving, index) => {
                    detailsHtml += `
                        <div class="serving-item">
                            <h4 class="font-semibold text-gray-800">${serving.serving_description || 'Serving'}</h4>
                            <div class="nutrition-grid">
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Calories</div>
                                    <div class="nutrition-value">${serving.calories || 0} kcal</div>
                                </div>
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Protein</div>
                                    <div class="nutrition-value">${serving.protein || 0}g</div>
                                </div>
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Carbohydrate</div>
                                    <div class="nutrition-value">${serving.carbohydrate || 0}g</div>
                                </div>
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Fat</div>
                                    <div class="nutrition-value">${serving.fat || 0}g</div>
                                </div>
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Fiber</div>
                                    <div class="nutrition-value">${serving.fiber || 0}g</div>
                                </div>
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Sugar</div>
                                    <div class="nutrition-value">${serving.sugar || 0}g</div>
                                </div>
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Sodium</div>
                                    <div class="nutrition-value">${serving.sodium || 0}mg</div>
                                </div>
                                <div class="nutrition-item">
                                    <div class="text-sm text-gray-500">Serving Size</div>
                                    <div class="nutrition-value">${serving.metric_serving_amount || 0} ${serving.metric_serving_unit || 'g'}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#modalFoodDetails').html(detailsHtml);
            }

            function showLoading(show) {
                if (show) {
                    $('#loadingIndicator').addClass('show');
                    $('#foodResults').hide();
                } else {
                    $('#loadingIndicator').removeClass('show');
                    $('#foodResults').show();
                }
            }

            function showError(message) {
                $('#errorText').text(message);
                $('#errorMessage').removeClass('hidden');
                setTimeout(() => {
                    $('#errorMessage').addClass('hidden');
                }, 5000);
            }

            function hideError() {
                $('#errorMessage').addClass('hidden');
            }

            // Enter key search
            $('#foodSearch').on('keypress', function(e) {
                if (e.which === 13) {
                    const query = $(this).val().trim();
                    if (query.length >= 2) {
                        searchFoods(query);
                    }
                }
            });
        });

        function closeModal(event) {
            if (event) {
                event.stopPropagation();
            }
            $('#foodModal').addClass('hidden');
        }
    </script>
@endsection