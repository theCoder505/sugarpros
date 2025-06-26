@extends('layouts.patient_portal')

@section('title', 'FatSecret AI')

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
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
    @include('layouts.patient_header')

    <div class="bg-gray-100 min-h-screen p-6">
        <div class="min-h-screen bg-[#f4f6f8] p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <h1 class="text-xl font-semibold text-[#000000]">
                    FatSecret AI
                </h1>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative flex-1">
                        <input type="text" id="foodSearch" placeholder="Search for foods..."
                            class="md:w-[350px] pl-10 text-white bg-[#133A59] pr-4 py-4 rounded-xl text-sm focus:outline-none placeholder:text-white" />
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 transform -translate-y-1/2 text-white"></i>
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
            <div id="foodResults" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 space-y-5 md:space-y-0 bg-white px-4 py-12 rounded-md">
                @if(isset($foods) && count($foods) > 0)
                    @foreach($foods as $food)
                        <div class="food-item bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4" 
                             data-food-id="{{ $food['id'] ?? '' }}">
                            <img src="{{ asset('assets/image/food-placeholder.jpg') }}" alt="Food"
                                class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

                            <div class="flex-1 px-2 w-full">
                                <h3 class="text-[18px] font-semibold">{{ $food['name'] ?? 'Unknown Food' }}</h3>
                                @if(isset($food['brand']) && $food['brand'])
                                    <p class="text-xs text-gray-500">{{ $food['brand'] }}</p>
                                @endif

                                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 text-xs mt-2">
                                    <!-- Protein -->
                                    <div class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#EAB3081A]/10 rounded-full">
                                        <img src="{{ asset('assets/image/ao1.png') }}" class="w-[15px]" alt="Protein"> 
                                        {{ number_format($food['protein'] ?? 0, 1) }}g
                                    </div>
                                    <!-- Carbs -->
                                    <div class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#00356612] rounded-full">
                                        <img src="{{ asset('assets/image/ao2.png') }}" class="w-[13px]" alt="Carbs"> 
                                        {{ number_format($food['carbs'] ?? 0, 1) }}g
                                    </div>
                                    <!-- Fat -->
                                    <div class="flex items-center justify-center gap-1 text-black w-16 h-6 bg-[#F8717117] rounded-full">
                                        <img src="{{ asset('assets/image/ao3.png') }}" class="w-[11px]" alt="Fat"> 
                                        {{ number_format($food['fat'] ?? 0, 1) }}g
                                    </div>
                                </div>

                                <p class="text-xs text-[#1C1917] mt-2">
                                    {{ number_format($food['calories'] ?? 0) }} <span class="text-[12px] text-[#737373]">kcal</span>
                                </p>
                                
                                @if(isset($food['serving_description']))
                                    <p class="text-xs text-gray-500 mt-1">{{ $food['serving_description'] }}</p>
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
                                <div class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-green-600">
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
    <div id="foodModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" onclick="closeModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 id="modalFoodName" class="text-2xl font-bold"></h2>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700" onclick="closeModal()">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="modalFoodDetails" class="space-y-4">
                        <!-- Food details will be populated here -->
                    </div>
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

            // Modal close
            $('#closeModal, #foodModal').on('click', function(e) {
                if (e.target === this) {
                    $('#foodModal').addClass('hidden');
                }
            });

            function closeModal() {
                $('#foodModal').addClass('hidden');
            }

            function searchFoods(query) {
                showLoading(true);
                hideError();

                $.ajax({
                    url: '{{ route("fatsecret.search") }}',
                    method: 'GET',
                    data: { search: query },
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
                    'breakfast': '{{ route("fatsecret.breakfast") }}',
                    'lunch': '{{ route("fatsecret.lunch") }}',
                    'dinner': '{{ route("fatsecret.dinner") }}'
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
                    container.html('<div class="col-span-full text-center py-8"><p class="text-gray-600">No foods found.</p></div>');
                    return;
                }

                foods.forEach(food => {
                    const foodHtml = `
                        <div class="food-item bg-gray-100 rounded-xl shadow-sm flex flex-col sm:flex-row items-center justify-between p-4 gap-4 cursor-pointer" 
                             data-food-id="${food.id}">
                            <img src="{{ asset('assets/image/food-placeholder.jpg') }}" alt="Food"
                                class="rounded-lg w-[92px] h-[90px] object-cover shrink-0" />

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
                    `;
                    container.append(foodHtml);
                });
            }

            function showFoodDetails(foodId) {
                $.ajax({
                    url: `{{ route("fatsecret.food.details", ":foodId") }}`.replace(':foodId', foodId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.food) {
                            displayFoodModal(response.food);
                        } else {
                            showError('Could not load food details.');
                        }
                    },
                    error: function(xhr) {
                        showError('Error loading food details.');
                        console.error('Food details error:', xhr);
                    }
                });
            }

            function displayFoodModal(food) {
                $('#modalFoodName').text(food.food_name);
                
                const servings = Array.isArray(food.servings.serving) ? food.servings.serving : [food.servings.serving];
                let detailsHtml = '';
                
                servings.forEach((serving, index) => {
                    detailsHtml += `
                        <div class="border-b pb-4 ${index < servings.length - 1 ? 'mb-4' : ''}">
                            <h4 class="font-semibold mb-2">${serving.serving_description}</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div><strong>Calories:</strong> ${serving.calories}</div>
                                <div><strong>Protein:</strong> ${serving.protein}g</div>
                                <div><strong>Carbs:</strong> ${serving.carbohydrate}g</div>
                                <div><strong>Fat:</strong> ${serving.fat}g</div>
                                <div><strong>Fiber:</strong> ${serving.fiber || 0}g</div>
                                <div><strong>Sugar:</strong> ${serving.sugar || 0}g</div>
                                <div><strong>Sodium:</strong> ${serving.sodium || 0}mg</div>
                            </div>
                        </div>
                    `;
                });
                
                $('#modalFoodDetails').html(detailsHtml);
                $('#foodModal').removeClass('hidden');
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
    </script>

@endsection