<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- 검색할 문장 영역 -->
                    <form method="GET" action="{{ route('search.text') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="curious_text" class="form-label">조회할 내용</label>
                            <textarea name="curious_text" style="width:100%; overflow:hidden;" rows="1" required></textarea>
                        </div>
                        <button type="submit"><strong>Submit</strong></button>
                    </form>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- 검색한 내용 -->
                    @if (session('curious_text'))
                        <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                            검색한 문장 : {{ session('curious_text') }}
                        </div>
                    @endif

                    <!-- 검색 결과 테이블 영역 -->
                    <table class="min-w-full border-collapse border border-gray-300" style="margin-top: 1rem;">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">키</th>
                            <th class="border border-gray-300 px-4 py-2">점수</th>
                            <th class="border border-gray-300 px-4 py-2">검색된 문장</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (session('result_group'))
                            @foreach(session('result_group') as $result)
                                <tr class="result-row cursor-pointer" data-id="{{ $result['_id'] ?? 'None' }}">
                                    <td class="border border-gray-300 px-4 py-2">{{ $result['_id'] ?? 'None' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $result['_score'] ?? 'None' }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $result['_source']['content'] ?? 'None' }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // textarea 입력할 때 자동으로 높이 조절하기
    document.querySelectorAll("textarea").forEach(function (textarea) {
        textarea.style.height = textarea.scrollHeight + "px";
        textarea.style.overflowY = "hidden";
        textarea.addEventListener("input", function () {
            this.style.height = "auto";
            this.style.height = this.scrollHeight + "px";
        });
    });

    // 결과 행 클릭 이벤트
    document.querySelectorAll(".result-row").forEach(function (row) {
        row.addEventListener("click", function () {
            let resultId = encodeURIComponent(this.dataset.id);
            window.location.href = '/search-detail?result_id=' + resultId;
        });
    });
</script>
