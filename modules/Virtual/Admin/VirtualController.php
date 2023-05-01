<?php
namespace Modules\Virtual\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Virtual\Models\Virtual;
use Modules\Virtual\Models\VirtualTranslation;

class VirtualController extends AdminController
{
    public function __construct()
    {
        $this->setActiveMenu('admin/module/virtual');
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->checkPermission('virtual_view');
        $listVirtual = Virtual::query() ;
        if (!empty($search = $request->query('s'))) {
            $listVirtual->where('name', 'LIKE', '%' . $search . '%');
        }
        $listVirtual->orderBy('created_at', 'asc');
        $data = [
            'rows'        => $listVirtual->get()->toTree(),
            'row'         => new Virtual(),
            'translation' => new VirtualTranslation(),
            'breadcrumbs' => [
                [
                    'name' => __('Virtual'),
                    'url'  => 'admin/module/virtual'
                ],
                [
                    'name'  => __('All'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Virtual::admin.index', $data);
    }

    public function edit(Request $request, $id)
    {
        $this->checkPermission('virtual_update');
        $row = Virtual::find($id);
        $translation = $row->translateOrOrigin($request->query('lang'));
        if (empty($row)) {
            return redirect('admin/module/virtual');
        }
        $data = [
            'translation' => $translation,
            'enable_multi_lang'=>true,
            'row'         => $row,
            'breadcrumbs' => [
                [
                    'name' => __('Virtual'),
                    'url'  => 'admin/module/virtual'
                ],
                [
                    'name'  => __('Edit'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Virtual::admin.detail', $data);
    }

    public function store( Request $request, $id ){
        $this->checkPermission('virtual_update');

        if($id>0){
            $row = Virtual::find($id);
            if (empty($row)) {
                return redirect(route('virtual.admin.index'));
            }
        }else{
            $row = new Virtual();
            $row->status = "publish";
        }

        $row->fill($request->input());
        $res = $row->saveOriginOrTranslation($request->input('lang'),true);

        if ($res) {
            if($id > 0 ){
                return back()->with('success',  __('Virtual updated') );
            }else{
                return redirect(route('virtual.admin.edit',$row->id))->with('success', __('Virtual created') );
            }
        }
    }

    public function getForSelect2(Request $request)
    {
        $pre_selected = $request->query('pre_selected');
        $selected = $request->query('selected');

        if($pre_selected && $selected){
            if(is_array($selected))
            {
                $items = Virtual::select('id', 'name as text')->whereIn('id',$selected)->take(50)->get();
                return response()->json([
                    'items'=>$items
                ]);
            }else{
                $item = Virtual::find($selected);
            }
            if(empty($item)){
                return response()->json([
                    'text'=>''
                ]);
            }else{
                return response()->json([
                    'text'=>$item->name
                ]);
            }
        }

        $q = $request->query('q');
        $query = Virtual::select('id', 'name as text')->where("status","publish");
        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }
        $res = $query->orderBy('id', 'desc')->limit(20)->get();
        return response()->json([
            'results' => $res
        ]);
    }

    public function editBulk(Request $request)
    {
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __("Select at least 1 item!"));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Select an Action!'));
        }
        if ($action == "delete") {
            foreach ($ids as $id) {
                $query = Virtual::where("id", $id);
                if (!$this->hasPermission('virtual_manage_others')) {
                    $query->where("create_user", Auth::id());
                    $this->checkPermission('virtual_delete');
                }
                $query->first();
                if(!empty($query)){
                    
                    //Del parent virtual
                    $query->delete();
                }
            }
        } else {
            foreach ($ids as $id) {
                $query = Virtual::where("id", $id);
                if (!$this->hasPermission('virtual_manage_others')) {
                    $query->where("create_user", Auth::id());
                    $this->checkPermission('virtual_update');
                }
                $query->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated success!'));
    }
}
