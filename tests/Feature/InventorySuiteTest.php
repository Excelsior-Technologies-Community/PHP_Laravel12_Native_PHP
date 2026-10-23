<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Product $testProduct;
    protected Supplier $testSupplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testSupplier = Supplier::create([
            'name' => 'Apex Test Supplies',
            'contact_person' => 'Marcus Vance',
            'email' => 'marcus@apex.test',
            'phone' => '+1 555 1234',
            'payment_terms' => 'Net 30',
            'lead_time_days' => 5,
            'reliability_score' => 4.9,
            'notes' => 'Primary electronics vendor',
        ]);

        $this->testProduct = Product::create([
            'name' => 'Test Laptop Pro',
            'sku' => 'SKU-LAP-1001',
            'barcode' => '890123400001',
            'category' => 'Electronics',
            'description' => 'Fast test laptop',
            'price' => 999.00,
            'quantity' => 15,
            'minimum_stock' => 5,
            'status' => 'Active',
            'supplier_id' => $this->testSupplier->id,
        ]);
    }

    /**
     * Feature 1: Barcode & QR Code Studio & Webcam Scanner
     */
    public function test_barcode_studio_page_loads(): void
    {
        $response = $this->get(route('inventory.barcodes'));
        $response->assertStatus(200);
        $response->assertSee('Barcode &amp; QR Code Studio', false);
        $response->assertSee($this->testProduct->name, false);
    }

    public function test_webcam_scanner_page_loads(): void
    {
        $response = $this->get(route('inventory.scanner'));
        $response->assertStatus(200);
        $response->assertSee('Webcam Scanner Studio', false);
    }

    public function test_barcode_print_sheet_loads(): void
    {
        $response = $this->get(route('inventory.barcodes.print', [
            'selected_products' => [$this->testProduct->id],
            'repeat' => 2
        ]));
        $response->assertStatus(200);
        $response->assertSee('Printable Barcode & QR Sticker Sheet', false);
        $response->assertSee($this->testProduct->name, false);
    }

    public function test_scanner_lookup_api_finds_product(): void
    {
        $response = $this->postJson(route('inventory.scanner.lookup'), [
            'code' => $this->testProduct->barcode,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'product' => [
                'id' => $this->testProduct->id,
                'name' => $this->testProduct->name,
                'barcode' => $this->testProduct->barcode,
            ]
        ]);
    }

    public function test_scanner_lookup_api_can_adjust_stock(): void
    {
        $oldQty = $this->testProduct->quantity;

        $response = $this->postJson(route('inventory.scanner.lookup'), [
            'code' => $this->testProduct->barcode,
            'action' => 'increment',
        ]);

        $response->assertStatus(200);
        $this->assertEquals($oldQty + 1, $this->testProduct->fresh()->quantity);
    }

    /**
     * Feature 3: Stock Movement Audit Ledger & Reorder Watchdog
     */
    public function test_stock_movement_ledger_page_loads(): void
    {
        $response = $this->get(route('inventory.ledger'));
        $response->assertStatus(200);
        $response->assertSee('Stock Movement Audit Ledger', false);
    }

    public function test_can_record_manual_stock_movement(): void
    {
        $initialQty = $this->testProduct->quantity;

        $response = $this->post(route('inventory.ledger.store'), [
            'product_id' => $this->testProduct->id,
            'type' => 'stock_in',
            'quantity' => 10,
            'reason' => 'Shipment Batch Received',
            'reference_no' => 'PO-TEST-100',
        ]);

        $response->assertRedirect(route('inventory.ledger'));
        $this->assertEquals($initialQty + 10, $this->testProduct->fresh()->quantity);
    }

    public function test_reorder_watchdog_page_loads(): void
    {
        // Reduce stock below minimum
        $this->testProduct->update(['quantity' => 2, 'minimum_stock' => 5]);

        $response = $this->get(route('inventory.watchdog'));
        $response->assertStatus(200);
        $response->assertSee('Low-Stock Reorder Watchdog', false);
        $response->assertSee($this->testProduct->name, false);
    }

    public function test_purchase_order_generator_loads(): void
    {
        $this->testProduct->update(['quantity' => 1, 'minimum_stock' => 5]);

        $response = $this->get(route('inventory.po.generate', ['supplier_id' => $this->testSupplier->id]));
        $response->assertStatus(200);
        $response->assertSee('Purchase Order', false);
        $response->assertSee($this->testSupplier->name, false);
    }

    /**
     * Feature 5: Native Desktop Studio & OS Push Radar
     */
    public function test_desktop_studio_page_loads(): void
    {
        $response = $this->get(route('desktop.studio'));
        $response->assertStatus(200);
        $response->assertSee('Native Desktop Integration &amp; OS Radar', false);
    }

    public function test_spotlight_search_api_returns_results(): void
    {
        $response = $this->getJson(route('api.spotlight.search', ['q' => 'Laptop']));
        $response->assertStatus(200);
        $response->assertJsonStructure(['products', 'suppliers']);
    }

    public function test_notification_radar_api_returns_status(): void
    {
        $response = $this->getJson(route('api.test.notification'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'title', 'body', 'low_stock_count']);
    }

    /**
     * Feature 7: Suppliers Directory & Vendor Management
     */
    public function test_suppliers_index_page_loads(): void
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertStatus(200);
        $response->assertSee('Supplier &amp; Vendor Directory', false);
        $response->assertSee($this->testSupplier->name, false);
    }

    public function test_can_create_and_view_supplier(): void
    {
        $response = $this->post(route('suppliers.store'), [
            'name' => 'Quantum Logistics Ltd',
            'contact_person' => 'Rachel Zane',
            'email' => 'rzane@quantum.io',
            'phone' => '+1 (555) 777-8888',
            'payment_terms' => 'Net 30',
            'lead_time_days' => 6,
            'reliability_score' => 4.9,
            'notes' => 'Test supplier entry',
        ]);

        $supplier = Supplier::where('email', 'rzane@quantum.io')->first();
        $this->assertNotNull($supplier);

        $response->assertRedirect(route('suppliers.show', $supplier->id));

        $showResponse = $this->get(route('suppliers.show', $supplier->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Quantum Logistics Ltd', false);
    }
}
