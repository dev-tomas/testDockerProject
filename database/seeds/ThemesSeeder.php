<?php

use Illuminate\Database\Seeder;
use App\Themes as Theme;

class ThemesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Theme::create([
            'name' => 'Default',
            'url'  => 'default-theme.jpg',
            'thumbnail' => 'default-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Autumn',
            'url'  => 'autumn-theme.jpg',
            'thumbnail' => 'autumn-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Autumn 2',
            'url'  => 'autumn-2-theme.jpg',
            'thumbnail' => 'autumn-2-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Beach',
            'url'  => 'beach-theme.jpg',
            'thumbnail' => 'beach-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Beach 2',
            'url'  => 'beach-2-theme.jpg',
            'thumbnail' => 'beach-2-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Beach 3',
            'url'  => 'beach-3-theme.jpg',
            'thumbnail' => 'beach-3-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Buildings',
            'url'  => 'buildings-theme.jpg',
            'thumbnail' => 'buildings-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'City',
            'url'  => 'city-theme.jpg',
            'thumbnail' => 'city-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'City 2',
            'url'  => 'city-2-theme.jpg',
            'thumbnail' => 'city-2-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Desert',
            'url'  => 'desert-theme.jpg',
            'thumbnail' => 'desert-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Desert 2',
            'url'  => 'desert-2-theme.jpg',
            'thumbnail' => 'desert-2-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Forest',
            'url'  => 'forest-theme.jpg',
            'thumbnail' => 'forest-theme-thumbnail.jpg'
        ]);
        Theme::create([
            'name' => 'Forest 2',
            'url'  => 'forest-2-theme.jpg',
            'thumbnail' => 'forest-2-theme-thumbnail.jpg'
        ]);
    }
}
