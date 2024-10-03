<?php

namespace App\Http\Controllers;

use App\Device;
use Carbon\Carbon;
use App\Http\Controllers\VoyagerBaseController;

use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;
use TCG\Voyager\Database\Schema\SchemaManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Events\BreadDataAdded;
use App\DataType;

class DeviceDatas extends VoyagerBaseController
{

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) [
            'value' => $request->get('s'),
            'key' => $request->get('key'),
            'filter' => $request->get('filter'),
            'device_id' => $request->get('device_id'),
            'data_id' => $request->get('data_id'),
            'start' => $request->get('start'),
            'end' => $request->get('end')
        ];
        //print_r($search);die;
        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $row = $dataRow->where('field', $value)->first();
                if ($row) {
                    $displayName = $row->getTranslatedAttribute('display_name');
                    $searchNames[$value] = $displayName ?: ucwords(str_replace('_', ' ', $value));
                }
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', null);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*');
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            $devices =   Device::where('company_id', Auth::user()->company_id)->get();
            $deviceIds = $devices->pluck('id')->toArray();
            $search->device_id = in_array($search->device_id, $deviceIds) ? $search->device_id : $deviceIds[0];
            $search->data_id = isset($search->data_id) ? $search->data_id : 0;
            $search->start = isset($search->start) ? $search->start : str_replace(' ', 'T', Carbon::now()->subDays(1)->format('Y-m-d H:i'));
            $search->end = isset($search->end) ? $search->end :  str_replace(' ', 'T', Carbon::now()->addHours(1)->format('Y-m-d H:i'));
            $query->where('device_id', $search->device_id);
            $query->where('data_id', $search->data_id);
            $query->where('created_at', ">=", $search->start);
            $query->where('created_at', "<=", $search->end);

            if ($search->value != '' && $search->key && $search->filter) {
                if (is_array($search->value)) {
                    foreach ($search->value as $vKey => $value) {
                        $key = $search->key[$vKey];
                        $filter = $search->filter[$vKey];
                        $search_filter = ($filter == 'equals') ? '=' : 'LIKE';
                        $search_value = ($filter == 'equals') ? $value : '%' . $value . '%';
                        $query->where($key, $search_filter, $search_value);
                    }
                } else {
                    $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                    $search_value = ($search->filter == 'equals') ? $search->value : '%' . $search->value . '%';
                    $query->where($search->key, $search_filter, $search_value);
                }
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, 'desc']];
            if (!$sortOrder && isset($dataType->order_direction)) {
                $sortOrder = $dataType->order_direction;
                $orderColumn = [[$index, $dataType->order_direction]];
            } else {
                $orderColumn = [[$index, 'desc']];
            }
        }

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'devices',
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn'
        ));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = DataType::where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        $dataType->addRows->push((object)[
            "data_type_id" => 34,
            "field" => "hourly",
            "type" => "timestamp",
            "display_name" => "Hourly",
            "edit" => 1,
            "add" => 1,
            "details" => "{}"
        ]);

        $request->merge(['hourly' => Carbon::parse($request->created_at)->format('Y-m-d H:00:00')]);

        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());


        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }
}
