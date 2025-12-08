<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Glossary') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 상단: 등록 버튼 --}}
            <div class="flex justify-between items-center mb-6">

                {{-- 로그인한 사람 등록 버튼 --}}
                @auth
                    <button onclick="openModal('create')"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        + 단어 등록
                    </button>
                @endauth

                {{-- 로그인 안 한 사람에게 보여줄 메시지 --}}
                @guest
                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline text-sm">
                        단어를 등록하려면 로그인하세요
                    </a>
                @endguest
            </div>

            {{-- 메인 콘텐츠 박스 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                            <p class="font-bold">️ 저장하지 못했습니다.</p>
                            <ul class="list-disc list-inside text-sm mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($terms->isEmpty())
                        <p class="text-gray-500 text-center py-4">아직 등록된 단어가 없습니다.</p>
                    @else
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="p-3 w-1/4">원문</th>
                                <th class="p-3 w-1/4">번역</th>
                                <th class="p-3 w-1/3">메모</th>
                                <th class="p-3 text-center">관리</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($terms as $term)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-bold">{{ $term->term }}</td>
                                    <td class="p-3">{{ $term->target_text }}</td>
                                    <td class="p-3 text-gray-500 text-sm">{{Str::limit($term->note, 50)}}</td>
                                    <td class="p-3 text-center">
                                        {{-- 수정 버튼: 클릭 시 해당 줄의 데이터($term)를 함수에 넘김 --}}
                                        @auth
                                        <button onclick="openModal('edit', {{ $term }})"
                                                class="text-blue-500 hover:underline mr-2">
                                            수정
                                        </button>

                                        {{-- 삭제 버튼 --}}
                                        <form action="{{ route('glossary.destroy', $term->id) }}" method="POST" class="inline" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline">삭제</button>
                                        </form>
                                        @endauth
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- 단어 수정 모달 --}}
    <div id="glossaryModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- 배경 어둡게 --}}
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- 모달 창 본문 --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <form id="glossaryForm" method="POST">
                    @csrf
                    {{-- PUT 메서드용 필드가 들어갈 자리 --}}
                    <div id="methodField"></div>

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">단어 등록</h3>

                        {{-- 1. 원문 --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">원문 (Source)</label>
                            <input type="text" name="term" id="input_term" class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        {{-- 2. 번역 --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">번역 (Target)</label>
                            <input type="text" name="target_text" id="input_target_text" class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        {{-- 3. 메모 --}}
                        <div class="mb-2">
                            <label class="block text-gray-700 font-bold mb-2">메모</label>
                            <textarea name="note" id="input_note" rows="3" class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>

                    {{-- 하단 버튼 --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            저장
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            취소
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 자바스크립트 --}}
    <x-slot name="scripts">
        <script>
            function openModal(mode, data = null) {
                const modal = document.getElementById('glossaryModal');
                const form = document.getElementById('glossaryForm');
                const title = document.getElementById('modalTitle');
                const methodField = document.getElementById('methodField');

                // 입력 필드들
                const inputTerm = document.getElementById('input_term');
                const inputTarget = document.getElementById('input_target_text');
                const inputNote = document.getElementById('input_note');

                modal.classList.remove('hidden');

                if (mode === 'create') {
                    // [등록 모드]
                    title.innerText = '단어 등록';
                    form.action = "{{ route('glossary.store') }}"; // 등록 주소
                    methodField.innerHTML = ''; // POST 방식

                    // 값 비우기
                    inputTerm.value = '';
                    inputTarget.value = '';
                    inputNote.value = '';
                    inputTerm.readOnly = false; // 원문 수정 가능

                } else if (mode === 'edit' && data) {
                    // [수정 모드]
                    title.innerText = '단어 수정';
                    form.action = "/glossary/" + data.id; // 수정 주소 (/glossary/1)

                    // 라라벨은 PUT 메서드를 이렇게 처리합니다
                    methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                    // 값 채워넣기 (DB 컬럼명과 일치해야 함)
                    inputTerm.value = data.term;
                    inputTarget.value = data.target_text;
                    inputNote.value = data.note || ''; // null이면 빈 문자열
                }
            }

            function closeModal() {
                document.getElementById('glossaryModal').classList.add('hidden');
            }
        </script>
    </x-slot>
</x-app-layout>
