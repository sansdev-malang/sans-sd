<?php

namespace App\Http\Controllers;

use App\Models\GuestBook;
use App\Models\GuestBookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestBookCategoryController extends Controller
{
    /**
     * Store a new Guest Book Category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:guest_book_categories,name',
            'sub_label' => 'nullable|string|max:150',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'form_type' => 'required|string|max:50',
            'order' => 'nullable|integer',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama tersebut sudah ada.',
            'icon.required' => 'Ikon wajib dipilih.',
            'color.required' => 'Warna tema wajib dipilih.',
            'form_type.required' => 'Format formulir wajib dipilih.',
        ]);

        try {
            $validated['slug'] = Str::slug($validated['name']);
            if (GuestBookCategory::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] .= '-' . time();
            }

            if (empty($validated['order'])) {
                $maxOrder = GuestBookCategory::max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            $validated['is_active'] = true;

            GuestBookCategory::create($validated);

            return redirect()->back()->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->withInput()
                ->withErrors(['category_error' => 'Gagal menambahkan kategori: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing Guest Book Category.
     */
    public function update(Request $request, $id)
    {
        $category = GuestBookCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:guest_book_categories,name,' . $category->id,
            'sub_label' => 'nullable|string|max:150',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:50',
            'form_type' => 'required|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama tersebut sudah digunakan.',
            'icon.required' => 'Ikon wajib dipilih.',
            'color.required' => 'Warna tema wajib dipilih.',
            'form_type.required' => 'Format formulir wajib dipilih.',
        ]);

        try {
            $oldName = $category->name;
            $newName = $validated['name'];

            $validated['slug'] = Str::slug($newName);
            if (GuestBookCategory::where('slug', $validated['slug'])->where('id', '!=', $category->id)->exists()) {
                $validated['slug'] .= '-' . time();
            }

            $validated['is_active'] = $request->has('is_active');

            $category->update($validated);

            // Jika nama kategori berubah, perbarui riwayat buku tamu yang sudah ada
            if ($oldName !== $newName) {
                GuestBook::where('category', $oldName)->update(['category' => $newName]);
            }

            return redirect()->back()->with('success', "Kategori '{$newName}' berhasil diperbarui.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()
                ->withInput()
                ->withErrors(['category_error' => 'Gagal memperbarui kategori: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a category.
     */
    public function destroy($id)
    {
        try {
            $category = GuestBookCategory::findOrFail($id);
            $usageCount = GuestBook::where('category', $category->name)->count();

            if ($usageCount > 0) {
                return redirect()->back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena sudah memiliki {$usageCount} data riwayat buku tamu. Anda dapat menonaktifkannya saja.");
            }

            $category->delete();

            return redirect()->back()->with('success', "Kategori '{$category->name}' berhasil dihapus.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        try {
            $category = GuestBookCategory::findOrFail($id);
            $category->is_active = !$category->is_active;
            $category->save();

            $statusText = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Kategori '{$category->name}' berhasil {$statusText}.");
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->with('error', 'Gagal mengubah status kategori: ' . $e->getMessage());
        }
    }
}
