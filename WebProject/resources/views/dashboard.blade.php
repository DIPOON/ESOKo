<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- 최근 번역 보여주는 부분 -->
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                        최근 번역
                    </div>
                    <table class="min-w-full border-collapse border border-gray-300" style="margin-top: 1rem;">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">키</th>
                            <th class="border border-gray-300 px-4 py-2">번역 필요한 영어 문장</th>
                            <th class="border border-gray-300 px-4 py-2">최신 한패에서의 문장</th>
                            <th class="border border-gray-300 px-4 py-2">적용 시도한 로그 문장</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($total_log_group as $totalLog)
                            <tr class="result-row cursor-pointer" data-id="{{ $totalLog['search_id'] ?? 'None' }}">
                                <td class="border border-gray-300 px-4 py-2">{{ $totalLog['search_id'] ?? 'None' }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $totalLog['en_text'] ?? 'None' }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $totalLog['kr_text'] ?? 'None' }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $totalLog['log_text'] ?? 'None' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $total_log_page->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // 결과 행 클릭 이벤트
    document.querySelectorAll(".result-row").forEach(function (row) {
        row.addEventListener("click", function () {
            let resultId = encodeURIComponent(this.dataset.id);
            window.location.href = '/search-detail?result_id=' + resultId;
        });
    });
</script>
