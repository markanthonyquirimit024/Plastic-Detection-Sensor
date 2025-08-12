<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class SubAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $analysts = User::orderBy('id','asc')->paginate(10);
        return view('profile.Admin.user-management', compact('user','analysts'));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $analystaccount = User::find($id);
        $analystaccount->delete();
        return redirect()->route('admin.user-management')->with('success', 'Analyst deleted successfully.');

}
