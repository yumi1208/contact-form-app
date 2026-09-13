<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        for ($i = 0; $i < 20; $i++) {
            $contact = Contact::create([
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->unique()->safeEmail(),
                'tel' => '090' . $faker->numerify('########'),
                'address' => $faker->prefecture() . $faker->city() . $faker->streetAddress(),
                'building' => $faker->optional()->secondaryAddress(),
                'category_id' => Category::inRandomOrder()->value('id'),
                'detail' => $faker->realText(80),
            ]);

            $tagIds = Tag::inRandomOrder()
                ->limit(rand(1, 3))
                ->pluck('id');

            $contact->tags()->attach($tagIds);
        }
    }
}