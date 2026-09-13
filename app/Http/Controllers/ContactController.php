<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use App\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(ContactRequest $request)
    {
        $validated = $request->validated();

        $category = Category::find($validated['category_id']);

        $tags = Tag::whereIn(
            'id',
            $validated['tag_ids'] ?? []
        )->get();

        return view('contact.confirm', compact(
            'validated',
            'category',
            'tags'
        ));
    }

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $tagIds = $validated['tag_ids'] ?? [];

        unset($validated['tag_ids']);

        $contact = Contact::create($validated);

        $contact->tags()->attach($tagIds);

        return redirect('/thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}