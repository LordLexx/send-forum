<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'EHCPs & Legal',
                'icon' => '🛡️',
                'description' => 'Discussions about Education, Health and Care Plans, legal rights, mediation, and tribunals.',
            ],
            [
                'name' => 'Autism & ADHD',
                'icon' => '🧠',
                'description' => 'Specific chat about neurodiversity, diagnosis pathways, and sensory processing.',
            ],
            [
                'name' => 'Emotional Wellbeing',
                'icon' => '💙',
                'description' => 'Support for parent carer mental health, stress, and emotional challenges.',
            ],
            [
                'name' => 'Slough Schools',
                'icon' => '🏫',
                'description' => 'Reviews, experiences, and questions about local schools and education settings.',
            ],
            [
                'name' => 'Coffee Break',
                'icon' => '☕',
                'description' => 'General chat, introductions, and off-topic discussions. Grab a cuppa and say hello!',
            ],
            [
                'name' => 'Health & Medical',
                'icon' => '🏥',
                'description' => 'Topics related to paediatricians, therapies, and medical support in Slough.',
            ],
            [
                'name' => 'Speech & Language',
                'icon' => '🗣️',
                'description' => 'Support and strategies for speech, language, and communication needs.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'description' => $category['description'],
                    'status' => 'active',
                    'make_priority' => true, // ← NEW: Always show seeded categories on feed
                ]
            );
        }
    }
}
