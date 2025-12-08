<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('환영합니다!') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- 제목 --}}
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">
                        엘더 스크롤 온라인 한국어/한글 번역 프로젝트
                    </h1>

                    {{-- 본문 --}}
                    <div class="prose max-w-none text-gray-600 dark:text-gray-400 text-sm space-y-3">
                        <p>
                            어서오세요. 제니맥스 사의 <b>엘더 스크롤 온라인</b>을 한국어와 한글로 즐기기 위한 비영리 유저 한글 패치 프로젝트입니다.
                        </p>
                        <p>
                            여러분의 참여로 번역이 완성됩니다. Translate 페이지로 가시면 번역을 하실 수 있습니다. 번역 기여 내역을 보호하고 관리하기 위해
                            <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-bold">회원가입/로그인</a>을 권장드립니다.
                        </p>

                        <hr class="my-6 border-gray-200">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-bold text-gray-800 mb-2">📚 번역 가이드라인</h3>
                                <p class="mb-2">번역을 어떻게 해야 할지 논의된 문서입니다.</p>
                                <a href="https://docs.google.com/spreadsheets/d/1WIi01XHTRRPbfSivALgHNdGYuo8MyB3tdJwQ7j-Wk-0/edit?usp=sharing" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                                    구글 독스 바로가기
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>

                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <h3 class="font-bold text-gray-800 mb-2">💬 오픈 카카오톡</h3>
                                <p class="mb-2">문의 사항이나 한글 패치 관련 논의가 필요하시면 아래에서 문의주세요.</p>
                                <a href="https://open.kakao.com/o/gnnZTLF" target="_blank" class="text-yellow-700 hover:text-yellow-900 font-bold flex items-center">
                                    채팅방 참여하기
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
