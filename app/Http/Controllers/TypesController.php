<?php

namespace App\Http\Controllers;

class TypesController extends AppController
{
    protected $mainModel = 'App\Models\Type';
    protected $except = [];

    // params needen for index
    protected $searchFields = ['name'];
    protected $indexPaginate = 10;
    protected $indexJoins = [];

    // params needer for show
    protected $showJoins = [];

    // params needed for store/update
    protected $saveFields = [];
    protected $defaultNulls = [];
    protected $formRules = [];

    protected $allowDelete = false;
    protected $allowUpdate = false;
    protected $allowStore  = false;

    protected $useTransactions = false;
}
