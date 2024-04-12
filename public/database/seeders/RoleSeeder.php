<?php

namespace Database\Seeders;

use App\Models\ListRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesArray = [
            'administrator' => ['*'],
            'professional'  => [1, 2, 3],
            'recruiter'     => [1, 2, 4, 5]
        ];
        $listRoleObj = new ListRole();
        foreach($rolesArray as $name => $role){
            $roleJson = json_encode($role);
            if($listRoleObj::where('lroles_name', $name)->where('lroles_permissions', $roleJson)->first())
                continue;
            ListRole::create([
                'lroles_name' => $name,
                'lroles_permissions' => $roleJson
            ]);
        }
    }
}
