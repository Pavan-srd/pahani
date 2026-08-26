<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pahani;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PahaniManagementController extends Controller
{
    /**
     * Display a list of all uploaded PDFs (admin view)
     * Table format: User | Mandal | Village | Document | File | Uploaded | Actions
     */
    public function index(Request $request)
    {
        $query = Pahani::with(['pahaniDocument', 'user', 'mandal', 'village'])
            ->where('physical_document', 'yes')
            ->whereNotNull('file_path')
            ->whereNull('deleted_at');

        if ($request->filled('search_user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_user . '%');
            });
        }

        if ($request->filled('mandal_id')) {
            $query->where('mandal_id', $request->mandal_id);
        }

        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        // NEW: filter by selected document
        if ($request->filled('pahani_document_id')) {
            $query->where('pahani_document_id', $request->pahani_document_id);
        }

        $pahanis = $query->orderBy('created_at', 'desc')->paginate(25);

        $mandals = \App\Models\Mandal::where('is_active', true)->orderBy('name')->get();
        // village must carry mandal_id so the JS can filter it client-side
        $villages = \App\Models\Village::where('is_active', true)->orderBy('name')->get(['id', 'name', 'mandal_id']);
        // NEW: full master list of the 68 document types (same set for every village)
        $documents = \App\Models\PahaniDocument::orderBy('label')->get(['id', 'label']);

        return view('admin.pahani-management', compact('pahanis', 'mandals', 'villages', 'documents'));
    }

    /**
     * Display details for a single PDF
     */
    public function show(Pahani $pahani)
    {
        $pahani->load(['pahaniDocument', 'user', 'mandal', 'village']);
        return view('admin.pahani-detail', compact('pahani'));
    }

    /**
     * Delete a PDF record and file
     */
    public function destroy(Pahani $pahani)
    {
        try {
            // Delete file from R2
            if ($pahani->file_path) {
                Storage::disk('r2')->delete($pahani->file_path);
            }

            // Soft delete or hard delete
            $pahani->delete();

            return response()->json([
                'success' => true,
                'message' => 'PDF deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete PDFs
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No PDFs selected.',
            ], 422);
        }

        try {
            $pahanis = Pahani::whereIn('id', $ids)->get();

            foreach ($pahanis as $pahani) {
                // Delete file from R2
                if ($pahani->file_path) {
                    Storage::disk('r2')->delete($pahani->file_path);
                }
                $pahani->delete();
            }

            return response()->json([
                'success' => true,
                'message' => count($pahanis) . ' PDF(s) deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete PDFs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export PDFs list as CSV
     */
    public function export(Request $request)
    {
        $query = Pahani::with(['pahaniDocument', 'user', 'mandal', 'village'])
            ->where('physical_document', 'yes')
            ->whereNotNull('file_path')
            ->whereNull('deleted_at');

        // Apply same filters as index
        if ($request->filled('mandal_id')) {
            $query->where('mandal_id', $request->mandal_id);
        }
        if ($request->filled('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        $pahanis = $query->get();

        $csv = "User,Email,Mandal,Village,Document Name,File Name,File Size,Uploaded Date\n";

        foreach ($pahanis as $p) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s"',
                $p->user?->name ?? 'N/A',
                $p->user?->email ?? 'N/A',
                $p->mandal?->name ?? 'N/A',
                $p->village?->name ?? 'N/A',
                $p->document_name ?? 'N/A',
                $p->file_name ?? 'N/A',
                $p->file_size_human ?? 'N/A',
                $p->created_at?->format('Y-m-d H:i:s') ?? 'N/A'
            ) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pahani-pdfs-' . date('Y-m-d-H-i-s') . '.csv"',
        ]);
    }
}
