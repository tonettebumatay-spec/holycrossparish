<?php

namespace Database\Seeders;

use App\Models\BookingRequirement;
use Illuminate\Database\Seeder;

class BookingRequirementSeeder extends Seeder
{
    public function run(): void
    {
        $requirements = [
            // ==================== BAPTISM ====================
            [
                'sacrament_type' => 'baptism',
                'requirement_name' => 'Birth Certificate (PSA)',
                'description' => 'Original and photocopy of the child\'s PSA birth certificate',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'sacrament_type' => 'baptism',
                'requirement_name' => 'Parents\' Marriage Certificate',
                'description' => 'If parents are married, bring a copy of their marriage certificate',
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'sacrament_type' => 'baptism',
                'requirement_name' => 'List of Godparents',
                'description' => 'Complete names of the chosen godparents (ninong and ninang)',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'sacrament_type' => 'baptism',
                'requirement_name' => 'Valid ID of Parent/Guardian',
                'description' => 'Any government-issued ID of the parent or guardian',
                'is_required' => true,
                'sort_order' => 4,
            ],

            // ==================== COMMUNION ====================
            [
                'sacrament_type' => 'communion',
                'requirement_name' => 'Baptismal Certificate',
                'description' => 'Original baptismal certificate from the parish where baptized',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'sacrament_type' => 'communion',
                'requirement_name' => 'First Communion Certificate',
                'description' => 'If already received First Communion, bring a copy',
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'sacrament_type' => 'communion',
                'requirement_name' => 'Valid ID',
                'description' => 'Any government-issued ID',
                'is_required' => true,
                'sort_order' => 3,
            ],

            // ==================== CONFIRMATION ====================
            [
                'sacrament_type' => 'confirmation',
                'requirement_name' => 'Baptismal Certificate',
                'description' => 'Original baptismal certificate from the parish where baptized',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'sacrament_type' => 'confirmation',
                'requirement_name' => 'Sponsor\'s Name',
                'description' => 'Complete name of the chosen sponsor',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'sacrament_type' => 'confirmation',
                'requirement_name' => 'Valid ID',
                'description' => 'Any government-issued ID',
                'is_required' => true,
                'sort_order' => 3,
            ],

            // ==================== WEDDING ====================
            [
                'sacrament_type' => 'wedding',
                'requirement_name' => 'Marriage License',
                'description' => 'Original marriage license from the local civil registrar',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'sacrament_type' => 'wedding',
                'requirement_name' => 'CENOMAR (Both)',
                'description' => 'Certificate of No Marriage from PSA for both parties',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'sacrament_type' => 'wedding',
                'requirement_name' => 'Birth Certificates (Both)',
                'description' => 'PSA birth certificates of both the groom and bride',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'sacrament_type' => 'wedding',
                'requirement_name' => 'Baptismal Certificates (Both)',
                'description' => 'Baptismal certificates of both the groom and bride',
                'is_required' => true,
                'sort_order' => 4,
            ],
            [
                'sacrament_type' => 'wedding',
                'requirement_name' => 'Banns',
                'description' => 'Marriage banns (if applicable)',
                'is_required' => false,
                'sort_order' => 5,
            ],
            [
                'sacrament_type' => 'wedding',
                'requirement_name' => 'Valid IDs (Both)',
                'description' => 'Any government-issued IDs of both parties',
                'is_required' => true,
                'sort_order' => 6,
            ],

            // ==================== FUNERAL ====================
            [
                'sacrament_type' => 'funeral',
                'requirement_name' => 'Death Certificate',
                'description' => 'Original death certificate from PSA or local civil registrar',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'sacrament_type' => 'funeral',
                'requirement_name' => 'Burial Permit',
                'description' => 'Burial permit from the local health office',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'sacrament_type' => 'funeral',
                'requirement_name' => 'Barangay Clearance',
                'description' => 'Barangay clearance of the deceased',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'sacrament_type' => 'funeral',
                'requirement_name' => 'Valid ID of Informant',
                'description' => 'Any government-issued ID of the person arranging the funeral',
                'is_required' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($requirements as $requirement) {
            BookingRequirement::create($requirement);
        }

        $this->command->info('✅ Booking requirements seeded!');
    }
}