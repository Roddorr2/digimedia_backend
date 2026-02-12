<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogHead;
use App\Models\BlogBody;
use App\Models\BlogFooter;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'link' => 'tu-bar-en-la-mira'
            ],
        ];

        foreach ($blogs as $b) {
            $head = BlogHead::where('titulo', 'Tu Bar, en la Mira')->first();
            $body = BlogBody::where('titulo', 'Tu Bar, en la Mira')->first();
            $footer = BlogFooter::where('titulo', 'Conclusion')->first();

            Blog::updateOrCreate(
                ['link' => $b['link']],
                [
                    'id_blog_head' => $head ? $head->id_blog_head : null,
                    'id_blog_body' => $body ? $body->id_blog_body : null,
                    'id_blog_footer' => $footer ? $footer->id_blog_footer : null,
                ]
            );
        }
    }
}
