<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Organization;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvoiceService();
    }

    /** @test */
    public function it_generates_default_invoice_number_format()
    {
        $number = $this->service->generateInvoiceNumber('TN');

        $year = date('Y');
        $this->assertMatchesRegularExpression("/^TN-{$year}-\d{5}$/", $number);
    }

    /** @test */
    public function it_extracts_counter_from_format_with_year_placeholder()
    {
        $org = Organization::factory()->create([
            'billing_data' => [
                'invoice_number_format' => 'RE-{YEAR}-{COUNTER:5}',
            ],
        ]);

        $number = $this->service->generateInvoiceNumber('TN', $org);

        $year = date('Y');
        $this->assertEquals("RE-{$year}-00001", $number);
    }

    /** @test */
    public function it_handles_different_counter_lengths()
    {
        $formats = [
            'INV-{COUNTER:3}' => '/^INV-\d{3}$/',
            'RE-{COUNTER:6}' => '/^RE-\d{6}$/',
            '{YEAR}-{COUNTER:4}' => '/^\d{4}-\d{4}$/',
        ];

        foreach ($formats as $format => $pattern) {
            $org = Organization::factory()->create([
                'billing_data' => ['invoice_number_format' => $format],
            ]);

            $number = $this->service->generateInvoiceNumber('TN', $org);
            $this->assertMatchesRegularExpression($pattern, $number);
        }
    }

    /** @test */
    public function it_replaces_all_placeholders_correctly()
    {
        $org = Organization::factory()->create([
            'id' => 42,
            'billing_data' => [
                'invoice_number_format' => 'INV-{ORG}-{YEAR}-{MONTH}-{COUNTER:3}',
            ],
        ]);

        $number = $this->service->generateInvoiceNumber('TN', $org);

        $year = date('Y');
        $month = date('m');
        $this->assertEquals("INV-42-{$year}-{$month}-001", $number);
    }

    /** @test */
    public function it_pads_counter_with_zeros()
    {
        $org = Organization::factory()->create([
            'billing_data' => ['invoice_number_format' => '{COUNTER:8}'],
        ]);

        $number = $this->service->generateInvoiceNumber('TN', $org);

        $this->assertEquals('00000001', $number);
    }

    /** @test */
    public function it_increments_existing_counter()
    {
        $org = Organization::factory()->create([
            'billing_data' => ['invoice_number_format' => 'RE-{YEAR}-{COUNTER:5}'],
        ]);

        $year = date('Y');

        // Create existing invoice
        Invoice::factory()->create([
            'invoice_number' => "RE-{$year}-00005",
        ]);

        $number = $this->service->generateInvoiceNumber('TN', $org);

        $this->assertEquals("RE-{$year}-00006", $number);
    }

    /** @test */
    public function it_uses_fallback_for_format_without_counter()
    {
        $org = Organization::factory()->create([
            'billing_data' => ['invoice_number_format' => 'INV-{YEAR}-{MONTH}'],
        ]);

        $number = $this->service->generateInvoiceNumber('TN', $org);

        // Should have timestamp fallback
        $this->assertStringContainsString('INV-' . date('Y') . '-' . date('m'), $number);
    }

    /** @test */
    public function it_handles_organization_without_billing_data()
    {
        $org = Organization::factory()->create([
            'billing_data' => null,
        ]);

        $number = $this->service->generateInvoiceNumber('PF', $org);

        $year = date('Y');
        $this->assertEquals("PF-{$year}-00001", $number);
    }

    /** @test */
    public function it_handles_null_organization()
    {
        $number = $this->service->generateInvoiceNumber('TN', null);

        $year = date('Y');
        $this->assertMatchesRegularExpression("/^TN-{$year}-\d{5}$/", $number);
    }

    /** @test */
    public function counter_increments_independently_per_format_pattern()
    {
        $org = Organization::factory()->create([
            'billing_data' => ['invoice_number_format' => '{YEAR}-{MONTH}-{COUNTER:4}'],
        ]);

        $year = date('Y');
        $month = date('m');

        // Create invoice with current year/month
        Invoice::factory()->create([
            'invoice_number' => "{$year}-{$month}-0003",
        ]);

        // New invoice should increment
        $number = $this->service->generateInvoiceNumber('TN', $org);
        $this->assertEquals("{$year}-{$month}-0004", $number);
    }
}
