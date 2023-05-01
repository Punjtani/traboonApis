<?php
namespace Modules\Virtual\Controllers;

use App\Http\Controllers\Controller;
use Modules\Virtual\Models\Virtual;
use Illuminate\Http\Request;

class VirtualController extends Controller
{
    public $virtual;
    public function __construct(Virtual $virtual)
    {
        $this->virtual = $virtual;
    }

    public function index(Request $request)
    {

    }

    public function detail(Request $request, $slug)
    {
        $row = $this->virtual::where('slug', $slug)->where("status", "publish")->first();;
        if (empty($row)) {
            return redirect('/');
        }

        //Auth::user()->can('viewAny', Tour::class);


        $translation = $row->translateOrOrigin(app()->getLocale());
        $data = [
            'row' => $row,
            'translation' => $translation,
            'seo_meta' => $row->getSeoMetaWithTranslation(app()->getLocale(), $translation),
        ];
        $this->setActiveMenu($row);
        return view('Virtual::frontend.detail', $data);
    }

    public function searchForSelect2( Request $request ){
        $search = $request->query('search');
        $query = Virtual::select('bravo_virtuals.*', 'bravo_virtuals.name as title')->where("bravo_virtuals.status","publish");
        if ($search) {
            $query->where('bravo_virtuals.name', 'like', '%' . $search . '%');

            if( setting_item('site_enable_multi_lang') && setting_item('site_locale') != app_get_locale() ){
                $query->leftJoin('bravo_virtual_translations', function ($join) use ($search) {
                    $join->on('bravo_virtuals.id', '=', 'bravo_virtual_translations.origin_id');
                });
                $query->orWhere(function($query) use ($search) {
                    $query->where('bravo_virtual_translations.name', 'LIKE', '%' . $search . '%');
                });
            }

        }
        $res = $query->orderBy('id', 'desc')->limit(20)->get()->toTree();
        if(!empty($res) and count($res)){
            $list_json = [];
            $traverse = function ($virtuals, $prefix = '') use (&$traverse, &$list_json) {
                foreach ($virtuals as $virtual) {
                    $translate = $virtual->translateOrOrigin(app()->getLocale());
                    $list_json[] = [
                        'id' => $virtual->id,
                        'title' => $prefix . ' ' . $translate->name,
                    ];
                    $traverse($virtual->children, $prefix . '-');
                }
            };
            $traverse($res);
            $this->sendSuccess(['data'=>$list_json]);
        }
        return $this->sendError(__("Virtual not found"));
    }
}
