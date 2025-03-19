<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('meno', 'like', '%' . $search . '%')
                    ->orWhere('vek', 'like', '%' . $search . '%')
                    ->orWhere('pohlavie', 'like', '%' . $search . '%');
            });
        }

        $limit = $request->input('limit', 10);
        $paginated = $query->paginate($limit);

        return response()->json([
            'people' => $paginated->items(),
            'total' => $paginated->total(),
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'meno' => 'required|string|max:32',
                'vek' => 'required|integer',
                'pohlavie' => 'required|in:muž,žena',
            ]);

            $person = Person::create($validated);
            DB::commit();

            return response()->json([
                'message' => 'Osoba vytvorená úspešne',
                'data' => $person,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Nepodarilo sa vytvoriť osobu.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $person = Person::find($id);

            if (!$person) {
                return response()->json(['error' => 'Osoba nebola nájdená'], 404);
            }

            $validated = $request->validate([
                'meno' => 'required|string|max:32',
                'vek' => 'required|integer',
                'pohlavie' => 'required|in:muž,žena',
            ]);

            $person->update($validated);
            DB::commit();

            return response()->json([
                'message' => 'Osoba upravená úspešne',
                'data' => $person,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Nepodarilo sa upraviť osobu.'], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $person = Person::find($id);

            if (!$person) {
                return response()->json(['error' => 'Osoba nebola nájdená'], 404);
            }

            $person->delete();
            DB::commit();

            return response()->json(['message' => 'Osoba vymazaná úspešne'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Nepodarilo sa vymazať osobu.'], 500);
        }
    }
}