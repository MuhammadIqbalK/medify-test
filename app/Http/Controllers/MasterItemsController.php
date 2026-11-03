<?php

namespace App\Http\Controllers;

use App\Exports\MasterItemsExcel;
use App\Http\Requests\SaveMasterItemRequest;
use App\Http\Requests\SearchMasterItemsRequest;
use App\Models\Category;
use App\Models\MasterItem;
use App\Queries\MasterItemQuery;
use App\Services\MasterItemService;

class MasterItemsController extends Controller
{
    public function __construct(private MasterItemService $items)
    {
    }

    public function index()
    {
        return view('master_items.index.index');
    }

    public function downloadExcel(MasterItemsExcel $export)
    {
        return $export->download();
    }

    public function search(SearchMasterItemsRequest $request, MasterItemQuery $query)
    {
        return json_encode([
            'status' => 200,
            'data' => $query->search($request->only(['kode', 'nama', 'hargamin', 'hargamax']), $request->filled('hargamin'), $request->filled('hargamax')),
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = new MasterItem;
        } else {
            $item = MasterItem::with('categories')->findOrFail($id);
        }
        $data['item'] = $item;
        $data['method'] = $method;
        $data['categories'] = Category::orderBy('name')->get();

        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::with('categories')->where('kode', $kode)->firstOrFail();

        return view('master_items.single.index', $data);
    }

    public function formSubmit(SaveMasterItemRequest $request, $method, $id = 0)
    {
        $this->items->save($request->validated(), $method, $id, $request->file('image'));

        return redirect('master-items');
    }

    public function delete($id)
    {
        MasterItem::with('categories')->findOrFail($id)->delete();

        return redirect('master-items');
    }

    public function updateRandomData()
    {
        $this->items->updateRandomData();
    }
}
