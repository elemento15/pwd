<?php 

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppController extends Controller {

    protected $savedRecord;
    protected $request;
    protected $record;
    protected $msgError = false;

    // attributes overwritten in child classes
    protected $except;
    protected $mainModel;
    protected $defaultNulls;
    protected $searchFields;
    protected $showJoins;
    protected $indexJoins;
    protected $indexSelect;
    protected $indexPaginate;
    protected $formRules;
    protected $saveFields;
    protected $storeFields;
    protected $updateFields;
    protected $allowStore;
    protected $allowUpdate;
    protected $allowDelete;
    protected $useTransactions;


    public function __construct()
    {
        $this->middleware('auth', $this->except);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $filters = $request->filters;
        $order_field = $request->order['field'] ?? false;
        $order_type = $request->order['type'] ?? 'asc';
        $model = new $this->mainModel;

        // set relationships
        if (isset($this->indexJoins) && count($this->indexJoins)) {
            $model = $model->with($this->indexJoins);
        }

        if ($filters) {
            $model = $model->where(function ($query) use ($filters) {
                foreach ($filters as $item) {
                    if (isset($item['isNull'])) {
                        $query = $query->where($item['field'], NULL);
                    } else {
                        $query = $query->where($item['field'], $item['value']);
                    }
                }
            });
        }
        
        if ($search) {
            $model = $model->where(function ($query) use ($search) {
                foreach ($this->searchFields as $key => $field) {
                    if (strpos($field, '.') == false) {
                        if ($key == 0) {
                            $query = $query->where($field, 'like', '%'.$search.'%');
                        } else {
                            $query = $query->orWhere($field, 'like', '%'.$search.'%');
                        }
                    } else {
                        $txt = explode('.', $field);
                        if ($key == 0) {
                            $query = $query->whereRelation($txt[0], $txt[1], 'like', '%'.$search.'%');
                        } else {
                            $query = $query->orWhereRelation($txt[0], $txt[1], 'like', '%'.$search.'%');
                        }
                    }

                }
            });
        }

        if ($order_field) {
            $model = $model->orderBy($order_field, $order_type);
        }

        if ($request->page) {
            if (isset($this->indexSelect)) {
                $model = $model->select($this->indexSelect)->paginate($this->indexPaginate);
            } else {
                $model = $model->paginate($this->indexPaginate);
            }
        } else {
            if (isset($this->indexSelect)) {
                $model = $model->select($this->indexSelect)->get();
            } else {
                $model = $model->get();
            }
        }

        return $model;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->request = $request;
        
        if (! $this->allowStore) {
            return Response::json(array('msg' => 'Modelo no permite insertar'), 500);
        }

        $mainModel = $this->mainModel;

        if (method_exists($this, 'beforeStore')) {
            if (! $this->beforeStore()) {
                return Response::json(array('msg' => $this->msgError), 500);
            }
        }

        foreach ($this->defaultNulls as $item) {
            $this->request[$item] = ($this->request[$item] == '' || $this->request[$item] == 'null') ? 
                null : 
                $this->request[$item];
        }

        $rules = $this->parseFormRules(0);
        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(array('errors' => $validator->errors()->all()), 422);
        } else {
            $data = $this->getSavingFields($this->request->all(), 'store');

            try {
                if ($this->useTransactions) {
                    DB::beginTransaction();
                }

                $this->savedRecord = $mainModel::create($data);

                if (method_exists($this, 'afterStore')) {
                    if (! $this->afterStore()) {
                        return Response::json(array('msg' => $this->msgError), 500);
                    }
                }

                if ($this->useTransactions) {
                    DB::commit();
                }

                return $this->savedRecord;
            } catch (Exception $e) {
                if ($this->useTransactions) {
                    DB::rollBack();
                }
                
                return Response::json(array('msg' => $e->getMessage() ?? 'Error al guardar'), 500);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $model = $this->mainModel;
        $model = $model::find($id);

        if (! $model) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        // relationships
        if (isset($this->showJoins) && count($this->showJoins)) {
            $model = $model->load($this->showJoins);
        }

        return $model;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        $this->request = $request;

        if (! $this->allowUpdate) {
            return Response::json(array('msg' => 'Modelo no permite actualizar'), 500);
        }

        $mainModel = $this->mainModel;

        if (method_exists($this, 'beforeUpdate')) {
            if (! $this->beforeUpdate()) {
                return Response::json(array('msg' => $this->msgError), 500);
            }
        }
        
        foreach ($this->defaultNulls as $item) {
            $this->request[$item] = ($this->request[$item] == '') ? null : $this->request[$item];
        }

        $rules = $this->parseFormRules($id);
        $validator = Validator::make($this->request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(array('errors' => $validator->errors()->all()), 422);
        } else {
            $record = $mainModel::find($id);

            if (! $record) {
                return Response::json(array('msg' => 'Registro no encontrado'), 500);
            }

            $data = $this->getSavingFields($this->request->all(), 'update');

            try {
                if ($this->useTransactions) {
                    DB::beginTransaction();
                }

                $record->fill($data)->save();
                $this->savedRecord = $record;
                
                if (method_exists($this, 'afterUpdate')) {
                    if (! $this->afterUpdate()) {
                        return Response::json(array('msg' => $this->msgError), 500);
                    }
                }

                if ($this->useTransactions) {
                    DB::commit();
                }

                return $this->savedRecord;
            } catch (Exception $e) {
                if ($this->useTransactions) {
                    DB::rollBack();
                }

                return Response::json(array('msg' => $e->getMessage()), 500);
            }
        
            return $record;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (! $this->allowDelete) {
            return Response::json(array('msg' => 'Modelo no permite eliminar'), 500);
        }

        $mainModel = $this->mainModel;

        if (! $this->record = $mainModel::find($id)) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        if (method_exists($this, 'beforeDestroy')) {
            if (! $this->beforeDestroy()) {
                return Response::json(array('msg' => $this->msgError), 500);
            }
        }

        if (! $this->record->delete()) {
            return Response::json(array('msg' => 'Error al eliminar'), 500);
        }
    }

    public function activate($id)
    {
        $mainModel = $this->mainModel;

        $record = $mainModel::find($id);

        if (! $record) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        $record->active = 1;
        
        if ($record->save()) {
            return Response::json($record);
        } else {
            return Response::json(array('msg' => 'Error al activar'), 500);
        }
    }

    public function deactivate($id)
    {
        $mainModel = $this->mainModel;

        $record = $mainModel::find($id);

        if (! $record) {
            return Response::json(array('msg' => 'Registro no encontrado'), 500);
        }

        $record->active = 0;
        
        if ($record->save()) {
            return Response::json($record);
        } else {
            return Response::json(array('msg' => 'Error al desactivar'), 500);
        }
    }


    protected function getSavingFields($request, $type)
    {
        $fields = false;
        
        if (isset($this->saveFields)) {
            $fields = $this->saveFields;
        } else {
            if ($type == 'store' && isset($this->storeFields)) {
                $fields = $this->storeFields;
            }

            if ($type == 'update' && isset($this->updateFields)) {
                $fields = $this->updateFields;
            }
        }

        return ($fields) ? $this->mapFields($request, $fields) : $request;
    }

    protected function mapFields($request, $fields)
    {
        $data = [];
        
        foreach ($fields as $field) {
            $data[$field] = $request[$field];
        }

        return $data;
    }

    protected function parseFormRules($id)
    {
        $rules = [];
        
        foreach ($this->formRules as $key => $rule) {
            $rules[$key] = str_replace('{{id}}', $id, $rule);
        }

        return $rules;
    }

}