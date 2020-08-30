<?php

namespace Globobalear\Products\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

use Illuminate\Contracts\Support\Renderable;

use App\Http\Notification\Facade\Toastr;

use Globobalear\Products\Models\Provider;

use Yajra\Datatables\Datatables;

use Auth;

class ProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables) : JsonResponse
    {
        return $datatables->collection(Provider::get())
            ->addColumn('action', 'products::providers.actions')
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() : Renderable
    {
        if (!Auth::user()->role->role_permission('manage_providers')) {
            abort(403, 'Unauthorized');
        }

        return view('products::providers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create() : Renderable
    {
        if (!Auth::user()->role->role_permission('manage_providers')) {
            abort(403, 'Unauthorized');
        }

        return view('products::providers.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Provider $provider
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Provider $provider, Request $request) : RedirectResponse
    {
        try {
            if (!Auth::user()->role->role_permission('manage_providers')) {
                abort(403, 'Unauthorized');
            }

            $data = $request->all();
            $this->validate(
                $request, [
                    'name' => 'string|required|max:255',
                    'email' => 'email|max:255'
                ]
            );

            $provider->create($data);

            toastr()->success('Provider created successfully!');
        } catch(Exception $e) {
            Log::error($e->getMessage());

            toastr()->error('An error has been ocurred while trying to create provider. Please, try again later.');
        }

        return redirect()->route('providers.index');

    }


    /**
     * @param Provider $provider
     * @return RedirectResponse
     */
    public function show(Provider $provider) : RedirectResponse
    {
        if (!Auth::user()->role->role_permission('manage_providers')) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * @param Provider $provider
     * @return Renderable
     */
    public function edit(Provider $provider) : Renderable
    {
        if (!Auth::user()->role->role_permission('manage_providers')) {
            abort(403, 'Unauthorized');
        }

        return view('products::providers.edit', compact('provider'));
    }


    /**
     * @param Provider $provider
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Provider $provider, Request $request) : RedirectResponse
    {
        try {
            if (!Auth::user()->role->role_permission('manage_providers')) {
                abort(403, 'Unauthorized');
            }

            $data = $request->all();
            $this->validate(
                $request, [
                    'name' => 'string|required|max:255',
                    'email' => 'email|max:255'
                ]
            );

            $provider->update($data);

            toastr()->success('Provider updated successfully!');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            toastr()->error('An error has been ocurred while trying to update provider. Please, try again later.');
        }

        return redirect()->route('providers.index');
    }

    /**
     * @param Provider $provider
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Provider $provider) : RedirectResponse
    {
        try {
            if (!Auth::user()->role->role_permission('manage_providers')) {
                abort(403, 'Unauthorized');
            }

            $provider->delete();

            toastr()->success('Provider deleted successfully!');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            toastr()->error('An error has been ocurred while trying to delete provider. Please, try again later.');
        }

        return redirect()->route('providers.index');
    }
}
