<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'id_blog_head' => 1,
                'id_blog_body' => 1,
                'id_blog_footer' => 1,
                'link' => 'tu-bar-en-la-mira'
            ],
        ];

        foreach ($blogs as $blog) {
            DB::table('blogs')->updateOrInsert(
                ['link' => $blog['link']],
                $blog
            );
        }

    }
}
