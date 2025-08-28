<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Analyst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class SubAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Query users
        $analysts = User::when($search, function ($query, $search) {
            $query->where('first_name', 'LIKE', "%{$search}%")
            ->orWhere('last_name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->orWhere('id', 'LIKE', "%{$search}%");
        })
        ->orderBy('id', 'asc')
        ->paginate(10);

        $analysts->appends(['search' => $search]);
        return view('profile.Admin.user-management', compact('user','analysts'));


    }

public function searchUser()
    {

    }   


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'first_name' =>'required','string','max:255',
        'last_name' =>'required','string','max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
        ]
    ]);

    $user = User::create([
        'first_name' => $request->first_name,
        'email' => $request->email,
        'last_name' => $request->last_name,
        'password' => bcrypt($request->password),
    ]);

    return back()->with('success', 'User created successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update (Request $request, string $id)
    {
    $analyst = User::findOrFail($id);

    $rules = [
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|string|email|max:255|unique:users,email,' . $analyst->id,
    ];

    // Only add password validation if filled
    if ($request->filled('password')) {
        $rules['password'] = [
            'nullable',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols(),
        ];
    }

    $validated = $request->validate($rules);

    $analyst->first_name = $validated['first_name'];
    $analyst->last_name  = $validated['last_name'];
    $analyst->email      = $validated['email'];

    if (!empty($validated['password'])) {
        $analyst->password = Hash::make($validated['password']);
    }

    $analyst->save();

    return redirect()->back()->with('success', 'User updated successfully!');
    }


    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $analystaccount = User::find($id);
        $analystaccount->delete();
        return redirect()->route('admin.user-management')->with('success', 'Analyst deleted successfully.');

}
}