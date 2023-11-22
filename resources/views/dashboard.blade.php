<x-app-layout>
    <x-slot name="header">
        <h2>{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-center items-center">
                        <h2 class="text-lg font-bold mb-4">API Call Allowance (Daily)</h2>
                    </div>

                    @if(isset($allowance['allowance']))
                    <div class="w-full h-14 bg-gray-200 rounded-lg">
                        <div class="bg-blue-500 h-full rounded-lg" style="width: {{ $allowance['allowance']['percentage'] }}%;">
                        </div>
                        <div class="text-center text-sm text-gray-600">
                            {{ $allowance['allowance']['percentage'] }}% ({{ $allowance['allowance']['count'] }} of {{ $allowance['allowance']['limit'] }})
                        </div>
                    </div>
                    @else
                    <div class="text-center">
                        <span>An Error has occurred with the API call to get the allowance.</span>
                        <br>
                        <span>Message: </span><span class="text-red-500 font-semibold">{{ $allowance['error'] }}</span>
                    </div>

                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-center items-center">
                        <h2 class="text-lg font-bold mb-4">Meal Prep Recommendation Times</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <h3 class="text-lg font-bold mb-4">Nationally</h3>
                            <ul class="list-disc pl-4">
                                @foreach ($mealRecommendations['Nationally'] as $mealRecommendation)
                                <li class="py-2 border-b border-gray-200">
                                    {{ $mealRecommendation['message'] }}
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="col-span-1">
                            <h3 class="text-lg font-bold mb-4">Cape Town</h3>
                            <ul class="list-disc pl-4">
                                @foreach ($mealRecommendations['Cape Town'] as $mealRecommendation)
                                <li class="py-2 border-b border-gray-200">
                                    <span class="font-bold">Stage {{ $mealRecommendation['stage'] }}:</span> {{ $mealRecommendation['optimalStartTime'] }} - {{ $mealRecommendation['optimalEndTime'] }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-center items-center">
                        <h2 class="text-lg font-bold mb-4">Area Search</h2>
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-4">
                        <input type="hidden" name="area_search" value="true">
                        <label for="searchInput" class="block text-gray-700">Enter an area name:</label>
                        <div class="flex items-center">
                            <input type="text" id="searchInput" name="searchInput" placeholder="Search for an area" class="rounded-l-full px-4 py-2 border-t border-b border-l text-gray-700 border-gray-300 bg-white
                              focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 w-80">
                            <button type="submit" id="searchButton" class="rounded-r-full px-4 py-2 bg-blue-500 text-white focus:outline-none hover:bg-blue-700">
                                Search
                            </button>
                        </div>
                    </form>


                    @if(isset($areas))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($areas['areas'] as $key=>$data)
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $data['name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $data['region'] }}</p>
                            <div class="flex items-center mt-4">
                                <span class="text-gray-500">ID: {{ $data['id'] }}</span>
                                <button class="ml-2 text-blue-500 hover:text-blue-700 focus:outline-none" onclick="copyToClipboard('{{ $data['id'] }}')">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18v-2a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v4h8v-4a2 2 0 0 0-2-2zm0-16a9.31 9.31 0 0 1 5.47 1.79A1 1 0 0 0 19 6V5a1 1 0 0 0-1-1h-1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 .8-.4A7.26 7.26 0 0 0 12 4 7.26 7.26 0 0 0 4.2 9.2a1 1 0 0 0-1.6 1.2A9.31 9.31 0 0 1 12 4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif







                </div>
            </div>


            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-center items-center">
                        <h2 class="text-lg font-bold mb-4">Area Information</h2>
                    </div>

                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-4">
                        <input type="hidden" name="area_information" value="true">
                        <label for="searchId" class="text-gray-700">Enter the area ID:</label>
                        <div class="flex items-center">
                            <input type="text" id="searchId" name="searchId" placeholder="Search for area information" class="rounded-l-full px-4 py-2 border-t border-b border-l text-gray-700 border-gray-300 bg-white
                              focus:outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 w-80">
                            <button type="submit" id="searchButton" class="rounded-r-full px-4 py-2 bg-blue-500 text-white focus:outline-none hover:bg-blue-700">
                                Search
                            </button>
                        </div>
                    </form>


                    @if(isset($areaInfo))
                    <div class="container mx-auto mt-8">
                        <h2 class="text-lg font-semibold mb-4">{{ $areaInfo['info']['name'] }}</h2>
                        <p class="text-gray-600">{{ $areaInfo['info']['region'] }}</p>

                        @if(isset($areaInfo['events']) && count($areaInfo['events']) > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">Events</h3>
                            @foreach($areaInfo['events'] as $event)
                            <div class="bg-white p-4 mb-4 rounded-lg shadow-md">
                                <p class="text-sm text-gray-500">Note: {{ $event['note'] }}</p>
                                <p class="text-sm text-gray-600">Start: {{ $event['start'] }}</p>
                                <p class="text-sm text-gray-600">End: {{ $event['end'] }}</p>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($areaInfo['schedule']['days']) && count($areaInfo['schedule']['days']) > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">Schedule</h3>
                            @foreach($areaInfo['schedule']['days'] as $day)
                            <div class="mb-4">
                                <h4 class="text-md font-semibold">{{ $day['name'] }} ({{ $day['date'] }})</h4>
                                <div class="grid grid-cols-3 gap-4 mt-2">
                                    @foreach($day['stages'] as $stages)
                                    <div class="bg-white p-4 rounded-lg shadow-md">
                                        @if(count($stages) > 0)
                                        @foreach($stages as $key=>$stage)
                                        <p class="text-sm text-gray-600">{{ $stage }}</p>
                                        @endforeach
                                        @else
                                        <p class="text-sm text-gray-500">No stages for this time</p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($areaInfo['schedule']['source']))
                        <div class="mt-6">
                            <p class="text-gray-600">Data source: <a href="{{ $areaInfo['schedule']['source'] }}" target="_blank" class="text-blue-500 hover:underline">{{ $areaInfo['schedule']['source'] }}</a></p>
                        </div>
                        @endif
                    </div>
                    @endif







                </div>
            </div>



        </div>
    </div>

    <script>
        function copyToClipboard(value) {
            const el = document.createElement('textarea');
            el.value = value;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('ID copied to clipboard: ' + value);
        }
    </script>
</x-app-layout>
