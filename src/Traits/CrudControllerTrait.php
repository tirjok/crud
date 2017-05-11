<?php

namespace Tirjok\CrudGenerator\Traits;


trait CrudControllerTrait
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $$this->crudName = $this->service->all();

        return view($this->viewPath . $this->viewName . '.index', compact($$this->crudName));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->viewPath . $this->viewName . '.create');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $$this->crudNameSingular = $this->service->find($id);

        if (!$$this->crudNameSingular) {
            Session::flash('flash_error', $this->modelName . ' not found!');

            return redirect("{$this->routeGroup}{$this->viewName}");
        }

        return view($this->viewPath.$this->viewName.'.show', compact($$this->crudNameSingular));
    }
}
