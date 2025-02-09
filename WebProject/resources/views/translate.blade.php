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
                    <!-- 번역 submit 하신 분에게 남기는 말 -->
                    @if(session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <!-- 번역 필요한 출력 -->
                    <p><strong>번역 필요한 문장</strong></p>
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
                            <label for="answer" class="form-label">Your Answer:</label>
                            <input type="text" class="form-control" id="answer" name="answer" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
