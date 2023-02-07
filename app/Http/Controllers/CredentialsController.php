<?php

namespace App\Http\Controllers;

use App\Models\CredentialProperty;

class CredentialsController extends AppController
{
    protected $mainModel = 'App\Models\Credential';
    protected $except = [];

    // params needen for index
    protected $searchFields = [];
    protected $indexPaginate = 10;
    protected $indexJoins = [
        'profile:id,email',
        'platform:id,name,url',
    ];

    // params needer for show
    protected $showJoins = [
        'profile:id,email',
        'platform:id,name,url',
        'platform.types:id,label,platform_id,type_id',
        'platform.types.type:id,name,code',
        'properties:id,credential_id,platform_type_id,answer',
    ];

    // params needed for store/update
    protected $storeFields = ['platform_id','profile_id','comments'];
    protected $updateFields = ['comments'];
    protected $defaultNulls = ['comments'];
    protected $formRules = [];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;

    protected $useTransactions = true;


    protected function afterUpdate()
    {
        foreach($this->request->platform['types'] as $item) {
            CredentialProperty::updateOrCreate([
                'credential_id' => $this->savedRecord->id,
                'platform_type_id' => $item['id'],
            ],[
                'answer' => $item['answer'],
            ]);
        }

        return true;
    }
}
