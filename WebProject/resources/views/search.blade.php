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
                    <form method="POST" action="{{ route('search') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="answer" class="form-label">조회할 내용</label>
                            <textarea name="answer" style="width:100%; overflow:hidden;" rows="1" required></textarea>
                        </div>
                        <button type="submit"><strong>Submit</strong></button>
                    </form>

                    <!-- 검색 결과 테이블 영역 -->
                    <table class="min-w-full border-collapse border border-gray-300" style="margin-top: 1rem;">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">키</th>
                            <th class="border border-gray-300 px-4 py-2">검색된 문장</th>
                            <th class="border border-gray-300 px-4 py-2">유사도</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($result_group as $result)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $result->key }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $result->note }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $result->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <!-- 검색 레코드 확인하는 모달창 -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // textarea 입력할 때 자동으로 높이 조절하기
    document.querySelectorAll("textarea").forEach(function(textarea) {
        textarea.style.height = textarea.scrollHeight + "px";
        textarea.style.overflowY = "hidden";
        textarea.addEventListener("input", function() {
            this.style.height = "auto";
            this.style.height = this.scrollHeight + "px";
        });
    });
</script>
