<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('환영합니다!') }}
        </h2>
    </x-slot>

    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
        @guest
            <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>
            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
        @endguest
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1> 엘더 스크롤 온라인 한국어/한글 번역 페이지</h1>
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        어서오세요. 제니맥스 사에서 만든 엘더 스크롤 온라인 게임은 영어로 되어 있습니다. Translate 페이지로 가시면 번역을 하실 수 있습니다.
                    </div>
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        이 웹사이트는 비영리적인 목적으로 유저 한글 패치를 만들기 위해 운영되고 있습니다.
                    </div>
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        번역하실 때 번역하신 것을 보호하기 위해 가입/로그인을 권장드립니다.
                    </div>
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        아래 구글 독스 링크에서 번역을 어떻게 할지 논의했던 것이 있습니다. 참고가 되길 바랍니다.
                    </div>
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        https://docs.google.com/spreadsheets/d/1WIi01XHTRRPbfSivALgHNdGYuo8MyB3tdJwQ7j-Wk-0/edit?usp=sharing
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
