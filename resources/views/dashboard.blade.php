<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            
            <div>

                <div>
                <form method="POST" action='{{route("request_token")}}'>

                    @csrf
                    <div class="flex justify-center">
                        <x-label for="consumer_key" :value="__('Consumer Key')" />

                        <x-input id="consumer_key" class="block mt-1 w-full" type="text" name="consumer_key" />
                    </div>
                    <div class="flex justify-center mt-6">
                        <x-label for="consumer_secret" :value="__('Consumer Secret')" />

                        <x-input id="consumer_secret" class="block mt-1 w-full" type="text" name="consumer_secret" />
                    </div>
                    <div class="flex justify-center mt-6">
                        <x-label for="callback_url" :value="__('Callback URL')" />

                        <x-input id="callback_url" class="block mt-1 w-full" type="text" name="callback_url" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                            {{ __('Next') }}
                        </x-button>
                    </div>
                </form>

                <hr class=" mt-6">

                
                <form method="POST" action="" class="mt-6">
                    @csrf
                    <div class="flex justify-center">
                        <x-label for="consumer_verifier" :value="__('Verifier')" />

                        <x-input id="consumer_verifier" class="block mt-1 w-full" type="text" name="consumer_verifier" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                            {{ __('Submit') }}
                        </x-button>
                    </div>
                </form>

                

                </div>

            </div>
        
        </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
