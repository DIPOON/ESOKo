@php use App\Enum\EnumPatch; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Download') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- 적용 방법 안내 -->
                    <div class="mt-2 text-sm">
                        각자의 Elder Scrolls Online\live\AddOns\gamedata\lang 이쪽 주소의 kr.lang 을 구글 드라이브의 kr.lang 으로 바꾸면 됩니다.
                    </div>
                    <div class="mt-2 text-sm">
                        혹시 모르니 백업하시고 다운받으신 파일로 적용해보시길 바랍니다.
                    </div>
                    <br>

                    <!-- 다운로드 목록 테이블 -->
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 px-4 py-2">버전</th>
                            <th class="border border-gray-300 px-4 py-2">다운로드 주소</th>
                            <th class="border border-gray-300 px-4 py-2">설명</th>
                            <th class="border border-gray-300 px-4 py-2">생성된 시각</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($patch_kr_link_group as $patchKrLink)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ EnumPatch::$patchName[$patchKrLink->patch] ?? '알 수 없는 버전' }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{--                                    FIXME 왜 파란색으로 안나오는지 모르겠음 --}}
                                    <a href="{{ $patchKrLink->link }}" target="_blank" rel="noopener noreferrer"
                                       class="underline">
                                        {{ $patchKrLink->link }}
                                    </a>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $patchKrLink->note }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $patchKrLink->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
