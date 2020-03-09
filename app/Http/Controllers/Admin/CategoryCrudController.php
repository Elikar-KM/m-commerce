<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CategoryRequest as StoreRequest;
use App\Http\Requests\CategoryUpdateRequest as UpdateRequest;

class CategoryCrudController extends CrudController
{

    public function setUp()
    {

        /*
		|--------------------------------------------------------------------------
		| BASIC CRUD INFORMATION
		|--------------------------------------------------------------------------
		*/
        $this->crud->setModel("App\Models\Category");
        $this->crud->setRoute("admin/categories");
        $this->crud->setEntityNameStrings('category', 'categories');

        /*
        |--------------------------------------------------------------------------
        | BUTTONS
        |--------------------------------------------------------------------------
        */
        $this->crud->enableReorder('name', 0);


         /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */
        // daterange filter
        $this->crud->addFilter([
          'type'  => 'date_range',
          'name'  => 'from_to',
          'label' => 'Date range'
        ],
          false,
          function ($value) { // if the filter is active, apply these constraints
              $dates = json_decode($value);
              $this->crud->addClause('where', 'created_at', '>=', $dates->from);
              $this->crud->addClause('where', 'created_at', '<=', $dates->to . ' 23:59:59');
          });
        
        /*
        |--------------------------------------------------------------------------
        | COLUMNS
        |--------------------------------------------------------------------------
        */
        $this->crud->addColumns([
            [
                'type'      => "select",
                'label'     => trans('category.parent'),
                'name'      => 'parent_id',
                'entity'    => 'parent',
                'attribute' => "name",
                'model'     => "App\Models\Category",
            ],
            [
                'name'  => 'name',
                'label' => trans('category.name'),
            ],
            [
                'name'  => 'slug',
                'label' => trans('category.slug'),
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |-------------------------------------------------------------------------
        */
        $this->setPermissions();

        /*
        |--------------------------------------------------------------------------
        | FIELDS
        |--------------------------------------------------------------------------
        */
        $this->setFields();

        /*
        |--------------------------------------------------------------------------
        | AJAX TABLE VIEW
        |--------------------------------------------------------------------------
        */
        $this->crud->enableAjaxTable();

    }

    public function setPermissions()
    {
        // Get authenticated user
        $user = auth()->user();

        // Deny all accesses
        $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // Allow list access
        if ($user->can('list_categories')) {
            $this->crud->allowAccess('list');
        }

        // Allow create access
        if ($user->can('create_category')) {
            $this->crud->allowAccess('create');
        }

        // Allow update access
        if ($user->can('update_category')) {
            $this->crud->allowAccess('update');
        }

        // Allow reorder access
        if ($user->can('reorder_categories')) {
            $this->crud->allowAccess('reorder');
        }

        // Allow delete access
        if ($user->can('delete_category')) {
            $this->crud->allowAccess('delete');
        }
    }

    public function setFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('category.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'slug',
                'label' => trans('category.slug'),
                'type'  => 'text',
            ],
            [
                'name'  => 'forfooter',
                'label' => 'forfooter',
                'type'  => 'checkbox',
            ]
        ]);
    }

	public function store(StoreRequest $request)
	{
        $redirect_location = parent::storeCrud();

        return $redirect_location;
	}

	public function update(UpdateRequest $request)
	{
        $redirect_location = parent::updateCrud();

        return $redirect_location;
	}
}
