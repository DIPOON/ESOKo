<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     * index 메서드 제외 인증 미들 웨어 적용
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * 목록 보기
     * @return View|Factory
     */
    public function index()
    {
        // 최신순으로 가져오기
        $terms = Term::latest()->get();
        return view('glossary.index', compact('terms'));
    }

    /**
     * 저장 처리 (POST)
     * @throws Exception
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // 정규화
        $cleanTerm = self::normalizeTerm($request->input('term'));
        $request->merge(['term' => $cleanTerm]);

        // 데이터 주입
        $validated = $request->validate([
            'term' => 'required|unique:terms,term|max:255',
            'target_text' => 'required|max:255',
            'note' => 'nullable|string',
        ]);

        // 데이터 주입
        $term = new Term();
        $term->term = $validated['term'];
        $term->target_text = $validated['target_text'];
        $term->note = $validated['note'] ?? '';

        // 4. 저장
        $term->save();

        return redirect()->route('glossary.index');
    }

    /**
     * 수정 처리 (PUT)
     * @param Request $request
     * @param Term $term
     * @return RedirectResponse
     */
    public function update(Request $request, Term $term)
    {
        // 정규화
        $cleanTerm = self::normalizeTerm($request->input('term'));
        $request->merge(['term' => $cleanTerm]);

        // 데이터 주입
        $validated = $request->validate([
            'term' => 'required|max:255|unique:terms,term,' . $term->id, // 수정할 때는 자기 자신(id)의 중복은 예외 처리해야 함
            'target_text' => 'required|max:255',
            'note' => 'nullable|string',
        ]);
        $validated['note'] = $validated['note'] ?? '';

        $term->update($validated);

        return redirect()->route('glossary.index');
    }

    /**
     * 삭제 처리 (DELETE)
     * @param Term $term
     * @return RedirectResponse
     */
    public function destroy(Term $term)
    {
        $term->delete();
        return redirect()->route('glossary.index');
    }

    /**
     * 정규화
     * @param string $term
     * @return string
     */
    private function normalizeTerm(string $term): string
    {
        // 1. 소문자로 변환
        $normalized = strtolower($term);

        // 2. 앞뒤 공백 제거
        $normalized = trim($normalized);

        // 3. stop word 제거 로직
        return preg_replace('/^(a|an|the)\s+/i', '', $normalized);
    }
}
