<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceNumberFormatTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $invoiceService;
    protected User $user;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoiceService = app(InvoiceService::class);
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();

        $this->organization->users()->attach($this->user->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    #[Test]
    public function it_generates_invoice_number_with_default_format()
    {
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN');

        $expectedYear = date('Y');
        $this->assertMatchesRegularExpression("/^TN-{$expectedYear}-\d{5}$/", $invoiceNumber);
        $this->assertEquals("TN-{$expectedYear}-00001", $invoiceNumber);
    }

    #[Test]
    public function it_generates_invoice_number_with_custom_year_month_format()
    {
        $this->organization->update([
            'billing_data' => [
                'invoice_number_format' => '{YEAR}{MONTH}-{COUNTER:4}',
            ],
        ]);

        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN', $this->organization);

        $expectedPrefix = date('Ym');
        $this->assertMatchesRegularExpression("/^{$expectedPrefix}-\d{4}$/", $invoiceNumber);
        $this->assertEquals("{$expectedPrefix}-0001", $invoiceNumber);
    }

    #[Test]
    public function it_generates_invoice_number_with_org_id_placeholder()
    {
        $this->organization->update([
            'billing_data' => [
                'invoice_number_format' => 'INV-{ORG}-{YEAR}-{COUNTER:3}',
            ],
        ]);

        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN', $this->organization);

        $expectedYear = date('Y');
        $orgId = $this->organization->id;
        $this->assertEquals("INV-{$orgId}-{$expectedYear}-001", $invoiceNumber);
    }

    #[Test]
    public function it_generates_invoice_number_with_custom_counter_length()
    {
        $this->organization->update([
            'billing_data' => [
                'invoice_number_format' => 'RE-{YEAR}-{COUNTER:8}',
            ],
        ]);

        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN', $this->organization);

        $expectedYear = date('Y');
        $this->assertEquals("RE-{$expectedYear}-00000001", $invoiceNumber);
    }

    #[Test]
    public function it_increments_counter_correctly()
    {
        $this->organization->update([
            'billing_data' => [
                'invoice_number_format' => 'RE-{YEAR}-{COUNTER:5}',
            ],
        ]);

        // Create first invoice
        $year = date('Y');
        Invoice::create([
            'invoice_number' => "RE-{$year}-00001",
            'user_id' => $this->user->id,
            'type' => 'platform_fee',
            'recipient_name' => 'Test',
            'recipient_email' => 'test@test.com',
            'recipient_address' => 'Test Address',
            'amount' => 100.00,
            'tax_rate' => 19.0,
            'tax_amount' => 19.00,
            'total_amount' => 119.00,
            'currency' => 'EUR',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'sent',
        ]);

        // Generate next invoice number
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN', $this->organization);

        $this->assertEquals("RE-{$year}-00002", $invoiceNumber);
    }

    #[Test]
    public function it_increments_counter_across_multiple_invoices()
    {
        $this->organization->update([
            'billing_data' => [
                'invoice_number_format' => '{YEAR}-{COUNTER:3}',
            ],
        ]);

        $year = date('Y');

        // Create multiple invoices
        for ($i = 1; $i <= 5; $i++) {
            Invoice::create([
                'invoice_number' => "{$year}-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'user_id' => $this->user->id,
                'type' => 'platform_fee',
                'recipient_name' => 'Test',
                'recipient_email' => 'test@test.com',
                'recipient_address' => 'Test Address',
                'amount' => 100.00,
                'tax_rate' => 19.0,
                'tax_amount' => 19.00,
                'total_amount' => 119.00,
                'currency' => 'EUR',
                'invoice_date' => now(),
                'due_date' => now()->addDays(14),
                'status' => 'sent',
            ]);
        }

        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN', $this->organization);

        $this->assertEquals("{$year}-006", $invoiceNumber);
    }

    #[Test]
    public function it_falls_back_to_default_format_when_no_custom_format()
    {
        // Organization without custom format
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('PF', $this->organization);

        $year = date('Y');
        $this->assertEquals("PF-{$year}-00001", $invoiceNumber);
    }

    #[Test]
    public function it_handles_month_placeholder_correctly()
    {
        $this->organization->update([
            'billing_data' => [
                'invoice_number_format' => 'INV-{YEAR}-{MONTH}-{COUNTER:4}',
            ],
        ]);

        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN', $this->organization);

        $year = date('Y');
        $month = date('m');
        $this->assertEquals("INV-{$year}-{$month}-0001", $invoiceNumber);
    }

    #[Test]
    public function organizer_can_update_invoice_number_format()
    {
        // Das Format wird in den Rechnungseinstellungen der Organisation gepflegt
        $this->actingAs($this->user);

        $response = $this->put(route('organizer.settings.invoice.update'), [
            'invoice_number_format_booking' => 'RE-{YEAR}-{COUNTER}',
            'invoice_number_counter_booking' => 1,
            'invoice_number_padding' => 5,
            'invoice_reset_yearly' => 1,
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(
            'RE-{YEAR}-{COUNTER}',
            $this->organization->fresh()->invoice_settings['invoice_number_format_booking']
        );
    }

    #[Test]
    public function invoice_number_format_field_is_required()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('organizer.settings.invoice.update'), [
            'invoice_number_counter_booking' => 1,
            'invoice_number_padding' => 5,
            'invoice_reset_yearly' => 1,
        ]);

        $response->assertSessionHasErrors('invoice_number_format_booking');
    }

    #[Test]
    public function invoice_number_format_must_contain_counter()
    {
        $this->actingAs($this->user);

        $response = $this->put(route('organizer.settings.invoice.update'), [
            'invoice_number_format_booking' => 'RE-{YEAR}',
            'invoice_number_counter_booking' => 1,
            'invoice_number_padding' => 5,
            'invoice_reset_yearly' => 1,
        ]);

        $response->assertSessionHasErrors('invoice_number_format_booking');
    }

    #[Test]
    public function it_resets_counter_for_new_year()
    {
        $this->organization->update([
            'billing_data' => [
                'invoice_number_format' => 'RE-{YEAR}-{COUNTER:5}',
            ],
        ]);

        // Create invoice from last year
        $lastYear = date('Y') - 1;
        Invoice::create([
            'invoice_number' => "RE-{$lastYear}-00099",
            'user_id' => $this->user->id,
            'type' => 'platform_fee',
            'recipient_name' => 'Test',
            'recipient_email' => 'test@test.com',
            'recipient_address' => 'Test Address',
            'amount' => 100.00,
            'tax_rate' => 19.0,
            'tax_amount' => 19.00,
            'total_amount' => 119.00,
            'currency' => 'EUR',
            'invoice_date' => now()->subYear(),
            'due_date' => now()->subYear()->addDays(14),
            'status' => 'sent',
        ]);

        // Generate new invoice for this year
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber('TN', $this->organization);

        $currentYear = date('Y');
        $this->assertEquals("RE-{$currentYear}-00001", $invoiceNumber);
    }
}
