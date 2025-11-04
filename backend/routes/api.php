<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\AdminMiddleware; // Import the Admin Middleware

// 📌 Authentification Routes (Public)
Route::prefix('auth')->group(function () {
    // Inscription (User Registration)
    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'in:admin,agent', // Only allow valid roles
        ]);

        $role = $request->role ?? 'agent';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    });

    // Connexion (User Login)
    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Les identifiants sont incorrects.',
            ], 401);
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => [
                'role' => $user->role, // ✅ Doit être présent pour éviter les erreurs
            ]
        ]);
        
    });

    // Déconnexion (User Logout)
    Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    });

    // Récupérer l'utilisateur authentifié
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return response()->json([
            'message' => 'Utilisateur Authentifié',
            'user' => $request->user(),
        ]);
    });
});

// 📌 Visitor Management (CRUD) Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/visitors', function (Request $request) {
        $request->validate([
            'name' => 'required|string',
            'cin' => 'required|string|unique:visitors,cin',
            'phone' => 'required|string',
            'reason' => 'required|string',
        ]);

        $visitor = Visitor::create([
            'name' => $request->name,
            'cin' => $request->cin,
            'phone' => $request->phone,
            'reason' => $request->reason,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Visiteur ajouté avec succès', 'visitor' => $visitor], 201);
    });

    Route::get('/visitors', function () {
        return response()->json(Visitor::all());
    });

    Route::get('/visitors/{id}', function ($id) {
        $visitor = Visitor::find($id);
        if (!$visitor) {
            return response()->json(['message' => 'Visiteur non trouvé'], 404);
        }
        return response()->json($visitor);
    });

    Route::put('/visitors/{id}', function (Request $request, $id) {
        $visitor = Visitor::find($id);
        if (!$visitor) {
            return response()->json(['message' => 'Visiteur non trouvé'], 404);
        }

        $request->validate([
            'name' => 'string',
            'cin' => 'string|unique:visitors,cin,' . $visitor->id,
            'phone' => 'string',
            'reason' => 'string',
        ]);

        $visitor->update($request->only(['name', 'cin', 'phone', 'reason']));
        return response()->json(['message' => 'Visiteur mis à jour avec succès', 'visitor' => $visitor]);
    });
});

// 📌 Visitor Stats (Accessible to both Admins and Agents)
Route::middleware(['auth:sanctum'])->get('/visitor-stats', function () {
    return response()->json([
        'total_visitors' => \App\Models\Visitor::count(),
        'visits_today' => \App\Models\Visitor::whereDate('created_at', now())->count(),
        'visits_this_month' => \App\Models\Visitor::whereMonth('created_at', now()->month)->count(),
        'most_frequent_visitors' => \App\Models\Visitor::select('name', 'cin', \DB::raw('COUNT(*) as visit_count'))
            ->groupBy('name', 'cin')
            ->orderByDesc('visit_count')
            ->limit(5)
            ->get(),
        'visits_per_agent' => \App\Models\User::where('role', 'agent')
            ->withCount('visitors')
            ->get(['name', 'visitors_count']),
        'visits_per_month' => \App\Models\Visitor::selectRaw('MONTHNAME(created_at) as month, COUNT(*) as visits')
            ->groupBy('month')
            ->orderByRaw('MONTH(created_at)')
            ->get()
    ]);
});
Route::middleware(['auth:sanctum', 'admin'])->delete('/visitors/{id}', function ($id) {
    $visitor = Visitor::find($id);

    if (!$visitor) {
        return response()->json(['message' => 'Visiteur non trouvé'], 404);
    }

    $visitor->delete();
    return response()->json(['message' => 'Visiteur supprimé avec succès']);
});

Route::middleware(['auth:sanctum'])->put('/visitors/{id}/status', function (Request $request, $id) {
    $visitor = Visitor::find($id);
    if (!$visitor) {
        return response()->json(['message' => 'Visiteur non trouvé'], 404);
    }

    $request->validate([
        'status' => 'required|string|in:En attente,Entré,Sorti',
    ]);

    $visitor->status = $request->status;
    $visitor->save();

    return response()->json(['message' => 'Statut mis à jour avec succès', 'visitor' => $visitor]);
});
Route::middleware('auth:sanctum')->get('/visitor-history', function (Request $request) {
    $query = \App\Models\Visitor::query();

    // Filtrer par statut si sélectionné
    if ($request->has('status') && $request->status !== '') {
        $query->where('status', $request->status);
    }

    // Filtrer par période (start_date & end_date)
    if ($request->has('start_date') && $request->has('end_date')) {
        $query->whereBetween('updated_at', [$request->start_date, $request->end_date]);
    }

    // Filtrer par nom ou CIN
    if ($request->has('search') && $request->search !== '') {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'LIKE', "%{$request->search}%")
              ->orWhere('cin', 'LIKE', "%{$request->search}%");
        });
    }

    return response()->json($query->orderBy('updated_at', 'desc')->get());
});
