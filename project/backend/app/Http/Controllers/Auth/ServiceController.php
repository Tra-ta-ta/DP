<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getServices()
    {
        $query = Service::query();
        $services = $query->get();
        return response()->json(['services' => $services], 200);
    }
    public function index(Request $request)
    {
        $name = $request->query('name', null);
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $query = Service::query();
        if (!is_null($name)) {
            $query->where('name', $name);
        }
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                $services = $query->paginate($limit, ['*'], 'page', $page)->withQueryString();

                return response()->json(['services' => $services], 200);
            }
            if (Auth::user()->isUser()) {
                return response()->json(Auth::user()->getServices(), 200);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->isAdmin()) {
            return view('admin.serviceCreate');
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $request->validate([
                'name' => 'required|unique:services,name',
                'discriprion' => 'required'
            ]);
            $service = Service::create([
                'name' => $request->name,
                'discriprion' => $request->discriprion
            ]);
            $service->save();
            return response()->json(['status' => 'Услуга успешно добавлена', 'service' => $service], 200);
        }
        return response()->json(['status' => 'Вы не авторизированы'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $service = Service::find($id);
            return response()->json(['service' => $service], 200);
        }
        if (Auth::user()->isUser()) {
            $service = Service::find($id);
            return response()->json(['service' => $service], 200);
        }
        return response()->json(['error' => 'Вы не авторизированы'], 401);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $service = Service::find($id);
            return response()->json(['service' => $service], 200);
        }
        return response()->json(['status' => 'Вы не авторизированы'], 401);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->isAdmin()) {
            $service = Service::find($id);
            $request->validate([
                'name' => 'required|unique:services,name',
                'discriprion' => 'required'
            ]);
            $service->update([
                'name' => $request->name,
                'discriprion' => $request->discriprion
            ]);
            return response()->json(['status' => 'Услуга с ID ' . $service->id . ' успешно изменена'], 200);
        }
        return response()->json(['status' => 'Вы не авторизированы'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->isAdmin()) {
            $service = Service::find($id);
            $service->delete();
            return response()->json(['status' => 'Услуга удалена'], 200);
        }
        return response()->json(['status' => 'Вы не авторизированы'], 401);
    }
}
