<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Http\Requests\ExportContactRequest;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $validated = $request->validated();

        $query = Contact::query();

        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }

        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (!empty($validated['date'])) {
            $query->whereDate('created_at', $validated['date']);
        }

        $contacts = $query->paginate(7);

        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.index', compact(
            'contacts',
            'categories',
            'tags'
        ));
    }

    public function show(Contact $contact)
    {
        $contact->load('category', 'tags');

        return view('admin.show', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->tags()->detach();

        $contact->delete();

        return redirect('/admin');
    }

    public function export(ExportContactRequest $request)
    {
        $validated = $request->validated();

        $query = Contact::with('category');

        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', '%' . $keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }

        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (!empty($validated['date'])) {
            $query->whereDate('created_at', $validated['date']);
        }

        $contacts = $query->get();

        $csvHeader = [
            'ID',
            '姓',
            '名',
            '性別',
            'メールアドレス',
            '電話番号',
            '住所',
            '建物名',
            'お問い合わせの種類',
            'お問い合わせ内容',
        ];

        $callback = function () use ($contacts, $csvHeader) {
            $file = fopen('php://output', 'w');

            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, $csvHeader);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->id,
                    $contact->last_name,
                    $contact->first_name,
                    $contact->gender,
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category?->content,
                    $contact->detail,
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload(
            $callback,
            'contacts.csv',
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }

    public function editTag(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function updateTag(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect('/admin');
    }

    public function storeTag(StoreTagRequest $request)
    {
        Tag::create($request->validated());

        return redirect('/admin');
    }

    public function destroyTag(Tag $tag)
    {
        $tag->contacts()->detach();

        $tag->delete();

        return redirect('/admin');
    }
}