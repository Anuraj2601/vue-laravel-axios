<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\SocialMedia;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

            /* Permission::create(['name' => 'manage_users']);
            Permission::create(['name' => 'create_post']);
            Permission::create(['name' => 'edit_post']);
            Permission::create(['name' => 'delete_post']); */

           /*  $admin = Role::create(['name' => 'admin']);
            $user = Role::create(['name' => 'user']);
            
            $admin->givePermissionTo(['manage_users', 'create_post', 'edit_post', 'delete_post']);
            $user->givePermissionTo(['create_post', 'edit_post', 'delete_post']); */

            /* $user1 = User::find(1);
            $user1->assignRole('admin'); */

            /* Tag::create(['name' =>'error']);
            Tag::create(['name' =>'nature']);
            Tag::create(['name' =>'tag1']);
            Tag::create(['name' =>'tag2']);
            Tag::create(['name' =>'technology']); */

            SocialMedia::create(['platform' => ''])

    }
}
