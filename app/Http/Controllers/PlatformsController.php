<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use App\Models\PlatformType;

class PlatformsController extends AppController
{
    protected $mainModel = 'App\Models\Platform';
    protected $except = [];

    // params needen for index
    protected $searchFields = ['name'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = ['types'];

    // params needed for store/update
    protected $saveFields = ['name','url','comments'];
    protected $defaultNulls = ['url','comments'];
    protected $formRules = [
        'name' => 'unique:platforms,name,{{id}}|required|min:3',
    ];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;

    protected $useTransactions = true;


    protected function beforeStore()
    {
        return $this->beforeSave();
    }

    protected function beforeUpdate()
    {
        return $this->beforeSave();
    }

    protected function beforeSave()
    {
        $types = collect($this->request['types']);
        
        // filter types that are not marked as deleted
        $filtered = $types->reject(function ($item) {
            return isset($item['deleted']) && $item['deleted'];
        });
        
        if (empty($filtered->all())) {
            $this->msgError = 'Ingrese al menos una etiqueta';
            return false;
        }

        return true;
    }

    protected function afterStore()
    {
        foreach($this->request['types'] as $item) {
            $this->savedRecord->types()->create([
                'label' => $item['label'],
                'type_id' => $item['type_id'],
            ]);
        }

        return true;
    }

    protected function afterUpdate()
    {
        foreach($this->request['types'] as $item) {
            if (!empty($item['id'])) {
                if ($type = PlatformType::find($item['id'])) {
                    if (isset($item['deleted']) && $item['deleted']) {
                        $type->delete();
                    } else {
                        $type->label = $item['label'];
                        $type->type_id = $item['type_id'];
                        $type->save();
                    }
                }
            } else {
                $this->savedRecord->types()->create([
                    'label' => $item['label'],
                    'type_id' => $item['type_id'],
                ]);
            }
        }

        return true;
    }
}
