<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apiResource = request()->apiNamespace.'Resources\ClientCollection';

        abort_if(!auth()->user()->tokenCan('client:index'), 403, 'You must be authorized');

        return new $apiResource(Client::with('user', 'signature')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {        
        abort_if(!auth()->user()->tokenCan('client:store'), 403, 'You must be authorized');

        DB::Transaction(function () use($request) {
            $user   = User::create([
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            ]);

            $user->client()->create([
                'name' => $request->get('name'),
                'phone' => $request->get('phone'),
            ]);

        });

        return response()->json(JsonResponse::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $apiResource = request()->apiNamespace.'Resources\ClientResource';

        abort_if(!auth()->user()->tokenCan('client:show'), 403, 'You must be authorized');
        
        return new $apiResource($client->load('user', 'signature'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        abort_if(!auth()->user()->tokenCan('client:update'), 403, 'You must be authorized');
        
        DB::transaction(function() use ($request, $client) {
            $clientName     = $request->get('name', $client->name);
            $clientPhone     = $request->get('phone', $client->phone);
            $userEmail      = $request->get('email', $client->user->email);
            $userPassword   = $request->get('password', $client->user->password);

            $client->update([
                'name'  => $clientName,
                'phone' => $clientPhone,
            ]);

            $client->user->update([
                'email' => $userEmail,
                'password'  => $userPassword
            ]);

            return response()->json(JsonResponse::HTTP_NO_CONTENT);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        abort_if(!auth()->user()->tokenCan('client:delete'), 403, 'You must be authorized');

        $client->delete();

        return response()->json(JsonResponse::HTTP_NO_CONTENT);
    }
}
