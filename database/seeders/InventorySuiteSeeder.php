<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class InventorySuiteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Key Suppliers
        $suppliers = [
            [
                'name' => 'Apex Tech Distributors',
                'contact_person' => 'Marcus Vance',
                'email' => 'orders@apextech.io',
                'phone' => '+1 (555) 234-5678',
                'address' => '742 Evergreen Industrial Park, San Jose, CA',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 5,
                'reliability_score' => 4.9,
                'notes' => 'Primary tier-1 vendor for high-performance laptops and monitors.',
            ],
            [
                'name' => 'Logitech Global Supplies',
                'contact_person' => 'Elena Rostova',
                'email' => 'supply@logiglobal.com',
                'phone' => '+1 (555) 987-6543',
                'address' => '120 Silicon Way, Austin, TX',
                'payment_terms' => 'Net 15',
                'lead_time_days' => 3,
                'reliability_score' => 4.8,
                'notes' => 'Preferred vendor for ergonomic mice, keyboards, and office accessories.',
            ],
            [
                'name' => 'Nexus Micro Components',
                'contact_person' => 'Devon King',
                'email' => 'sales@nexusmicro.net',
                'phone' => '+1 (555) 345-8910',
                'address' => '88 Innovation Blvd, Boston, MA',
                'payment_terms' => 'Net 45',
                'lead_time_days' => 8,
                'reliability_score' => 4.6,
                'notes' => 'Supplies USB-C hubs, docking stations, and cable adapters.',
            ],
        ];

        $createdSuppliers = [];
        foreach ($suppliers as $data) {
            $createdSuppliers[] = Supplier::firstOrCreate(['email' => $data['email']], $data);
        }

        // 2. Backfill existing products with SKU, Barcode, and Supplier
        $products = Product::all();
        $supplierCount = count($createdSuppliers);

        foreach ($products as $index => $product) {
            $updates = [];
            if (empty($product->sku)) {
                $updates['sku'] = 'SKU-' . strtoupper(substr($product->category ?? 'IT', 0, 3)) . '-' . (1000 + $product->id);
            }
            if (empty($product->barcode)) {
                $updates['barcode'] = '8901234' . str_pad($product->id, 5, '0', STR_PAD_LEFT);
            }
            if (empty($product->supplier_id) && $supplierCount > 0) {
                $updates['supplier_id'] = $createdSuppliers[$index % $supplierCount]->id;
            }

            if (!empty($updates)) {
                $product->update($updates);
            }

            // Seed initial stock movement for existing products if none exists
            if ($product->stockMovements()->count() === 0) {
                $product->stockMovements()->create([
                    'type' => 'stock_in',
                    'quantity_change' => $product->quantity,
                    'previous_quantity' => 0,
                    'new_quantity' => $product->quantity,
                    'reason' => 'Initial Inventory Balance',
                    'reference_no' => 'INIT-STOCK-' . $product->id,
                    'created_by' => 'Inventory System',
                ]);
            }
        }
    }
}
