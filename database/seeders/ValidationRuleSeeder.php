<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ValidationRule;

class ValidationRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // Work Order rules
            [
                'object_type' => 'WorkOrder',
                'expression' => 'ISBLANK(Priority)',
                'error_message' => 'Priority is required.',
                'is_active' => true,
            ],
            [
                'object_type' => 'WorkOrder',
                'expression' => 'Estimated_Cost = 0',
                'error_message' => 'Estimated cost must be greater than zero.',
                'is_active' => true,
            ],
            [
                'object_type' => 'WorkOrder',
                'expression' => 'Start_Date < TODAY()',
                'error_message' => 'Start date cannot be in the past.',
                'is_active' => true,
            ],
            [
                'object_type' => 'WorkOrder',
                'expression' => 'End_Date <= Start_Date',
                'error_message' => 'End date must be later than start date.',
                'is_active' => true,
            ],
            [
                'object_type' => 'WorkOrder',
                'expression' => 'AND(Status = "Open", ISBLANK(Priority))',
                'error_message' => 'Priority must be set when status is open.',
                'is_active' => true,
            ],
            
            // Contact rules
            [
                'object_type' => 'Contact',
                'expression' => 'ISBLANK(Email)',
                'error_message' => 'Email is required for contacts.',
                'is_active' => true,
            ],
            [
                'object_type' => 'Contact',
                'expression' => 'ISBLANK(Phone)',
                'error_message' => 'Phone number is required for contacts.',
                'is_active' => true,
            ],
            
            // Asset rules
            [
                'object_type' => 'Asset',
                'expression' => 'ISBLANK(Asset_Tag)',
                'error_message' => 'Asset tag is required.',
                'is_active' => true,
            ],
            [
                'object_type' => 'Asset',
                'expression' => 'Purchase_Price <= 0',
                'error_message' => 'Purchase price must be greater than zero.',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $ruleData) {
            ValidationRule::create($ruleData);
        }
    }
}
