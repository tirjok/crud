<?php

namespace Tirjok\CrudGenerator\Traits;

use Illuminate\Support\Facades\Session;

trait CrudControllerTrait
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        ${$this->crudName} = $this->service->all();

        return view($this->viewPath . $this->viewName . '.index', compact("{$this->crudName}"));
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
        ${$this->crudNameSingular} = $this->service->find($id);

        if (!${$this->crudNameSingular}) {
            Session::flash('flash_error', $this->modelName . ' not found!');

            return redirect("{$this->routeGroup}{$this->viewName}");
        }

        return view($this->viewPath.$this->viewName . '.show', compact("{$this->crudNameSingular}"));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        ${$this->crudNameSingular} = $this->service->find($id);

        if (!${$this->crudNameSingular}) {
        Session::flash('flash_error', $this->modelName.' not found!');

            return redirect("{$this->routeGroup}{$this->viewName}");
    }

        return view($this->viewPath.$this->viewName . '.edit', compact("{$this->crudNameSingular}"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        try {
            $is_deleted = $this->service->delete($id);

            if ($is_deleted) {
                Session::flash('flash_message', '{{modelName}} deleted!');
            }

        } catch (\Exception $e) {
            Session::flash('flash_error', 'There is no data regarding this id');
        }

        return redirect("{$this->routeGroup}{$this->viewName}");
    }
}
