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
                    <h3 style="color: blue;"><strong>번역 필요한 영어 문장</strong></h3>
                    <p>{{ $en_text ?? '서버에서 데이터를 안내려주는데? (웹 개발자가 씀)' }}</p>
                    <h3 style="color: blue;"><strong>최신 한패에서의 문장</strong></h3>
                    <p>{{ $kr_text ?? '근데 한패에서는 뭐라고 적혀있는지 모르겠어요. (웹 개발자가 씀)' }}</p>

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
                            <textarea name="answer" style="width:100%; overflow:hidden;" rows="1" required></textarea>
                        </div>
                        <button type="submit" style="background-color: blue;" class="text-white"><strong>Submit</strong></button>
                    </form>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- 번역 로그 (위쪽) -->
                    <h3><strong>번역 로그</strong></h3>
                    <div id="log-division"></div>

                    <!-- 비슷한 위치의 데이터가 들어갈 영역 (아래쪽) -->
                    <h3 style="margin-top: 1rem;"><strong>번역해야하는 문장 주변</strong></h3>
                    <p>아이템인지 지역명인지 등 참조용. 번역해야하는 문장과 전혀 무관할 수 있음</p>
                    <div id="neighbor-division"></div>
                </div>
                @if(session('message'))
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- 번역 submit 하신 분에게 남기는 말 -->
                        <div class="alert alert-success" style="margin-top: 1rem;">
                            {{ '번역하신 문장은 로그 id ' . session('message') . '으로 저장되었습니다.' }}
                        </div>
                    </div>
                @endif
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

    // 페이지 로드되면 비동기적으로 번역에 부가적인 정보 불러오기
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
