<?php
namespace Database\Seeders;
use Hash;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = Role::where('name', 'admin')->first();
        $role_manager = Role::where('name', 'manager')->first();
        $role_picker = Role::where('name', 'picker')->first();

        if (!User::where('username', 'admin')->first()) {
            $admin = new User();
            $admin->name = 'Administrator';
            $admin->username = 'admin';
            $admin->email = '';
            $admin->password = Hash::make('admin');
            $admin->save();
            $admin->roles()->attach($role_admin);
        }
    }
}
