<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $term
 * @property string $target_text
 * @property string $note
 * @property int $user_id
 * @method static Builder|Term latest($column = null)
 */
class TermLog extends Model
{
    // terms_log state 정의
    CONST STATE_INSERT = 1;
    CONST STATE_UPDATE = 2;
    CONST STATE_DELETE = 3;

    protected $fillable = ['term_id', 'state', 'term', 'target_text', 'note', 'user_id'];
}
