<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Translate') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- 번역 필요한 출력 -->
                    <h3 style="color: blue;"><strong>번역 필요한 문장</strong></h3>
                    <p>{{ $question ?? 'None data' }}</p>

                    <!-- 답변 제출 폼 -->
                    <form method="POST" action="{{ route('translate') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="hidden" name="lang_id" value={{ $lang_id }}>
                            <input type="hidden" name="unknown" value={{ $unknown }}>
                            <input type="hidden" name="index" value={{ $index }}>
                            <input type="hidden" name="offset" value={{ $offset }}>
                            <input type="hidden" name="version" value={{ $version }}>
                            <label for="answer" class="form-label">번역 결과:</label>
                            <input type="text" class="form-control" id="answer" name="answer" required>
                        </div>
                        <button type="submit" style="background-color: blue;" class="text-white"><strong>Submit</strong></button>
                    </form>

                    <!-- 번역 로그 (위쪽) -->
                    <h3 style="margin-top: 1rem;"><strong>번역 로그</strong></h3>
                    <div id="log-division"></div>

                    <!-- 비슷한 위치의 데이터가 들어갈 영역 (아래쪽) -->
                    <h3 style="margin-top: 1rem;"><strong>번역해야하는 문장 주변</strong></h3>
                    <p>아이템인지 지역명인지 등 참조용. 번역해야하는 문장과 전혀 무관할 수 있음</p>
                    <div id="neighbor-division"></div>

                    <!-- 번역 submit 하신 분에게 남기는 말 -->
                    @if(session('message'))
                        <div class="alert alert-success" style="margin-top: 1rem;">
                            {{ '번역하신 문장은 로그 id ' . session('message') . '으로 저장되었습니다.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/translate-sub?lang_id=' + encodeURIComponent('{{ $lang_id ?? '0' }}')
            + '&unknown=' + encodeURIComponent('{{ $unknown ?? '0' }}')
            + '&index=' + encodeURIComponent('{{ $index ?? '0' }}'))
            .then(response => response.json())
            .then(data => {
                // 번역 로그 채워넣기
                const logContainer = document.getElementById('log-division');
                let historyList = data.history_list;
                historyList.forEach(sentence => {
                    let p = document.createElement('p');
                    p.textContent = sentence.text;
                    logContainer.appendChild(p);
                });

                // 비슷한 위치의 데이터 비동기로 채워넣기
                const neighborContainer = document.getElementById('neighbor-division');
                let neighborList = data.neighbor_list;
                neighborList.forEach(sentence => {
                    let p = document.createElement('p');
                    p.textContent = sentence.lang_id + '-' + sentence.unknown + '-' + sentence.index + ': ' + sentence.text;
                    neighborContainer.appendChild(p);
                });
            })
            .catch(err => {
                console.error('번역관련 정보를 가져오는데 실패했습니다.', err);
            });
    });
</script>
