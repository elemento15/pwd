<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
//use App\Models\Profile;

class ProfilesController extends AppController
{
    protected $mainModel = 'App\Models\Profile';
    protected $except = [];

    // params needen for index
    protected $searchFields = ['email'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = ['email'];
    protected $defaultNulls = [];
    protected $formRules = [
        'email' => 'unique:profiles,email,{{id}}|required|min:8',
    ];

    protected $allowDelete = true;
    protected $allowUpdate = true;
    protected $allowStore  = true;

    protected $useTransactions = false;
}
