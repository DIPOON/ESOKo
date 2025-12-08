<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $term
 * @property string $target_text
 * @property string $note
 * @property int $user_id
 * @method static Builder|Term latest($column = null)
 */
class Term extends Model
{
    protected $fillable = [
        'term',
        'target_text',
//        'user_id', 요청자 user_id 로 입력
        'note',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // 생성 직전 이벤트
        static::creating(function ($term) {
            if (!auth()->check()) {
                throw new Exception('Unauthenticated: 단어를 등록하려면 로그인이 필요합니다.');
            }
            $term->user_id = Auth::id();
        });

        // 생성 직후 이벤트
        static::created(function ($term) {
            TermLog::create([
                'state' => TermLog::STATE_INSERT,
                'term_id' => $term->id,
                'term' => $term->term,
                'target_text' => $term->target_text,
                'note' => $term->note,
                'user_id' => Auth::id() ?? 0, // 로그인 안된 경우(시스템) 대비
            ]);
        });

        // 수정 직전 이벤트
        static::updating(function ($term) {
            if (!auth()->check()) {
                throw new Exception('Unauthenticated: 단어를 등록하려면 로그인이 필요합니다.');
            }
            $term->user_id = Auth::id();
        });

        // 수정 직후 이벤트
        static::updated(function ($term) {
            // 변경사항이 있을 때만 로그 남기기 (선택사항)
             if ($term->wasChanged()) {
                 TermLog::create([
                     'state' => TermLog::STATE_UPDATE,
                     'term_id' => $term->id,
                     'term' => $term->term,
                     'target_text' => $term->target_text,
                     'note' => $term->note,
                     'user_id' => Auth::id() ?? 0,
                 ]);
             }
        });

        static::deleted(function ($term) {
            TermLog::create([
                'state' => TermLog::STATE_DELETE,
                'term_id' => $term->id,
                'term' => $term->term,
                'target_text' => $term->target_text,
                'note' => $term->note,
                'user_id' => Auth::id() ?? 0,
            ]);
        });
    }
}
