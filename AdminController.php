<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authorizedstaff;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function authorizeStaff(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'email' => 'required|email|regex:/^[a-zA-Z0-9.]+@aubg\.edu$/|unique:authorized_staff',
            'role'  => 'required|string', 
        ]);

        // Save to Db
        Authorizedstaff::create([
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);

        // Go back with a success msg
        return redirect()->back()->with('success', 'Staff member added successfully!');
    }

    // List only new categories
    public function newCategories()
    {
        $new = \DB::table('opportunity_categories')
            ->where('is_approved', false)
            ->get();

        return view('admin.Home', compact('new'));
    }

    // Update category
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        \DB::table('opportunity_categories')
            ->where('id', $id)
            ->update([
                'name' => trim($request->name)
            ]);

        return back()->with('success', 'Category updated.');
    }

    // Approve category
    public function approveCategory($id)
    {
        \DB::table('opportunity_categories')
            ->where('id', $id)
            ->update(['is_approved' => true]);

        return back()->with('success', 'Category approved and visible to users.');
    }

    // Delete a category
    public function deleteCategory($id)
    {
        \DB::table('opportunity_categories')->where('id', $id)->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function showImage($path)
    {
        // Only admin can see
        if (strtolower(auth()->user()->role) !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        // Storage facade to check local disk
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found on disk at: ' . $path);
        }

        // Get absolute path 
        return response()->file(Storage::disk('local')->path($path));
    }
}