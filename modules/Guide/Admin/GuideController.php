<?php
namespace Modules\Guide\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Core\Models\Attributes;
use Modules\Location\Models\Location;
use Modules\Guide\Models\Guide;
use Modules\Guide\Models\GuideTerm;
use Modules\Guide\Models\GuideTranslation;

class GuideController extends AdminController
{
    protected $guideClass;
    protected $guideTranslationClass;
    protected $guideTermClass;
    protected $attributesClass;
    protected $locationClass;
    public function __construct()
    {
        parent::__construct();
        $this->setActiveMenu('admin/module/guide');
        $this->guideClass = Guide::class;
        $this->guideTranslationClass = GuideTranslation::class;
        $this->guideTermClass = GuideTerm::class;
        $this->attributesClass = Attributes::class;
        $this->locationClass = Location::class;
    }
    public function callAction($method, $parameters)
    {
        if(!Guide::isEnable())
        {
            return redirect('/');
        }
        return parent::callAction($method, $parameters); // TODO: Change the autogenerated stub
    }

    public function index(Request $request)
    {
        $this->checkPermission('guide_view');
        $query = $this->guideClass::query() ;
        $query->orderBy('id', 'desc');
        if (!empty($guide_name = $request->input('s'))) {
            $query->where('title', 'LIKE', '%' . $guide_name . '%');
            $query->orderBy('title', 'asc');
        }

        if ($this->hasPermission('guide_manage_others')) {
            if (!empty($author = $request->input('vendor_id'))) {
                $query->where('create_user', $author);
            }
        } else {
            $query->where('create_user', Auth::id());
        }
        $data = [
            'rows'               => $query->with(['author'])->paginate(20),
            'guide_manage_others' => $this->hasPermission('guide_manage_others'),
            'breadcrumbs'        => [
                [
                    'name' => __('Guides'),
                    'url'  => 'admin/module/guide'
                ],
                [
                    'name'  => __('All'),
                    'class' => 'active'
                ],
            ],
            'page_title'=>__("Guide Management")
        ];
        return view('Guide::admin.index', $data);
    }

    public function create(Request $request)
    {
        
        $this->checkPermission('guide_create');
        $row = new $this->guideClass();
        $row->fill([
            'status' => 'publish'
        ]);
        $data = [
            'row'            => $row,
            'attributes'     => $this->attributesClass::where('service', 'guide')->get(),
            'guide_location' => $this->locationClass::where('status', 'publish')->get()->toTree(),
            'translation'    => new $this->guideTranslationClass(),
            'breadcrumbs'    => [
                [
                    'name' => __('Guides'),
                    'url'  => 'admin/module/guide'
                ],
                [
                    'name'  => __('Add Guide'),
                    'class' => 'active'
                ],
            ],
            'page_title'     => __("Add new Guide")
        ];
        return view('Guide::admin.detail', $data);
    }

    public function edit(Request $request, $id)
    {
        $this->checkPermission('guide_update');
        $row = $this->guideClass::find($id);
        if (empty($row)) {
            return redirect(route('guide.admin.index'));
        }
        $translation = $row->translateOrOrigin($request->query('lang'));
        if (!$this->hasPermission('guide_manage_others')) {
            if ($row->create_user != Auth::id()) {
                return redirect(route('guide.admin.index'));
            }
        }
        $data = [
            'row'            => $row,
            'translation'    => $translation,
            "selected_terms" => $row->terms->pluck('term_id'),
            'attributes'     => $this->attributesClass::where('service', 'guide')->get(),
            'guide_location'  => $this->locationClass::where('status', 'publish')->get()->toTree(),
            'enable_multi_lang'=>true,
            'breadcrumbs'    => [
                [
                    'name' => __('Guides'),
                    'url'  => 'admin/module/guide'
                ],
                [
                    'name'  => __('Edit Guide'),
                    'class' => 'active'
                ],
            ],
            'page_title'=>__("Edit: :name",['name'=>$row->title])
        ];
        return view('Guide::admin.detail', $data);
    }

    public function store( Request $request, $id ){

        if($id>0){
            $this->checkPermission('guide_update');
            $row = $this->guideClass::find($id);
            if (empty($row)) {
                return redirect(route('guide.admin.index'));
            }

            if($row->create_user != Auth::id() and !$this->hasPermission('guide_manage_others'))
            {
                return redirect(route('guide.admin.index'));
            }
        }else{
            $this->checkPermission('guide_create');
            $row = new $this->guideClass();
            $row->status = "publish";
        }
        $dataKeys = [
            'title',
            'content',
            'slug',
            'video',
            'image_id',
            'banner_image_id',
            'gallery',
            'is_featured',
            'policy',
            'location_id',
            'address',
            'map_lat',
            'map_lng',
            'map_zoom',
            'star_rate',
            'price',
            'sale_price',
            'check_in_time',
            'check_out_time',
            'allow_full_day',
            'status',
        ];
        if($this->hasPermission('guide_manage_others')){
            $dataKeys[] = 'create_user';
        }

        $row->fillByAttr($dataKeys,$request->input());
	    $row->ical_import_url  = $request->ical_import_url;

        $res = $row->saveOriginOrTranslation($request->input('lang'),true);

        if ($res) {
            if(!$request->input('lang') or is_default_lang($request->input('lang'))) {
                $this->saveTerms($row, $request);
            }

            if($id > 0 ){
                return back()->with('success',  __('Guide updated') );
            }else{
                return redirect(route('guide.admin.edit',$row->id))->with('success', __('Guide created') );
            }
        }
    }

    public function saveTerms($row, $request)
    {
        $this->checkPermission('guide_manage_attributes');
        if (empty($request->input('terms'))) {
            $this->guideTermClass::where('target_id', $row->id)->delete();
        } else {
            $term_ids = $request->input('terms');
            foreach ($term_ids as $term_id) {
                $this->guideTermClass::firstOrCreate([
                    'term_id' => $term_id,
                    'target_id' => $row->id
                ]);
            }
            $this->guideTermClass::where('target_id', $row->id)->whereNotIn('term_id', $term_ids)->delete();
        }
    }

    public function bulkEdit(Request $request)
    {
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('No items selected!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Please select an action!'));
        }
        switch ($action){
            case "delete":
                foreach ($ids as $id) {
                    $query = $this->guideClass::where("id", $id);
                    if (!$this->hasPermission('guide_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('guide_delete');
                    }
                    $query->first();
                    if(!empty($query)){
                        $query->delete();
                    }
                }
                return redirect()->back()->with('success', __('Deleted success!'));
                break;
            case "clone":
                $this->checkPermission('guide_create');
                foreach ($ids as $id) {
                    (new $this->guideClass())->saveCloneByID($id);
                }
                return redirect()->back()->with('success', __('Clone success!'));
                break;
            default:
                // Change status
                foreach ($ids as $id) {
                    $query = $this->guideClass::where("id", $id);
                    if (!$this->hasPermission('guide_manage_others')) {
                        $query->where("create_user", Auth::id());
                        $this->checkPermission('guide_update');
                    }
                    $query->update(['status' => $action]);
                }
                return redirect()->back()->with('success', __('Update success!'));
                break;
        }
    }
}