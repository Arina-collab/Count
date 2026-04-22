<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student_Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminIssuesController extends Controller
{
    // List all issues
    public function index()
    {
        $issues = Student_Issue::with('user')->latest()->paginate(10);
        return view('admin.issues', compact('issues'));
    }

    // Update status
    public function updateStatus(Request $request, $id)
    {
        $issue = Student_Issue::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved'
        ]);

        $issue->update(['status' => $request->status]);

        return back()->with('msg', "Issue #{$id} updated to {$request->status}!");
    }

    // Delete an issue
    public function destroy($id)
    {
        $issue = Student_Issue::findOrFail($id);

        if ($issue->screenshot_path) {
            Storage::disk('public')->delete($issue->screenshot_path);
        }

        $issue->delete();

        return back()->with('msg', 'Issue report deleted successfully.');
    }
}