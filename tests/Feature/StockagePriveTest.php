<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockagePriveTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_business_plans_quittent_le_stockage_public(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('public/uploads/plan.pdf', 'contenu');
        Storage::disk('local')->put('public/uploads/orphelin.pdf', 'contenu');
        DB::table('funding_requests')->insert([
            'companyName' => 'Atelier', 'email' => 'a@exemple.bj', 'phone' => '01 00 00 00 00', 'mission' => 'm', 'vision' => 'v',
            'sector' => 's', 'productDescription' => 'p', 'productStatus' => 'idées', 'amountRequested' => 1000,
            'useOfFunds' => 'u', 'businessPlan' => 'storage/uploads/plan.pdf',
        ]);

        (include database_path('migrations/2026_10_07_000008_move_business_plans_to_private_storage.php'))->up();

        Storage::disk('local')->assertMissing('public/uploads/plan.pdf');
        Storage::disk('local')->assertMissing('public/uploads/orphelin.pdf');
        Storage::disk('local')->assertExists('prive/business-plans/plan.pdf');
        Storage::disk('local')->assertExists('prive/business-plans/orphelin.pdf');
        $this->assertSame('prive/business-plans/plan.pdf', DB::table('funding_requests')->value('businessPlan'));
    }

    public function test_aucune_route_ne_sert_les_anciens_fichiers(): void
    {
        $this->get('/uploads/plan.pdf')->assertNotFound();
    }
}
