<?php

namespace Database\Seeders;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [



'Invoices',
'Invoice_List',
'Paid_Invoices',
'Partially_Paid_Invoices',
'Unpaid_Invoices',
            'Invoice-delete',

'Invoice_Archive',
'Reports',
'Invoice_Report',
'Customer_Report',
            'Users','User_List',
'User_Permissions',
'Settings',
'Products',
'Categories',


'Add_Invoice',
'Delete_Invoice',
'Export_to_Excel',
'Change_Payment_Status',
'Edit_Invoice',
'Archive_Invoice',
'Print_Invoice',


'Add_Attachment',
'Delete_Attachment',
            'Download_Attachment',
            'View_Attachment',


'control_User',//
'Add_User',//
'Edit_Profile',//
'Delete_User',//


'View_Permission',
'Add_Permission',
'Edit_Permission',
'Delete_Permission',

'Products_List',//
'Add_Product',//
'Edit_Product',//
'Delete_Product',//

'Categories_List',//
'Add_Category',//
'Edit_Category',//
'Delete_Category',//
'Notifications',
'Roles'

        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
