<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DownloadController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function get(): View|Factory|Application
    {
        $patchKrLinkGroup = DB::table('patch_kr_links')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        return view('download', array('patch_kr_link_group' => $patchKrLinkGroup));
    }
}
