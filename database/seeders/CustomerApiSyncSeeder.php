<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Customer\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerApiSyncSeeder extends Seeder
{
    private $apiUrl = 'https://app-back-end.wardahlife.com/api/get/all/unique/customers';
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting Customer API Sync...');
        
        try {
            // Fetch data from API
            $response = Http::timeout(60)->get($this->apiUrl);
            
            if (!$response->successful()) {
                $this->command->error('Failed to fetch data from API. Status: ' . $response->status());
                return;
            }
            
            $data = $response->json();
            
            if (!isset($data['success']) || !$data['success'] || !isset($data['data'])) {
                $this->command->error('Invalid API response format');
                return;
            }
            
            $customers = $data['data'];
            $totalCustomers = $data['total_unique_customers'] ?? count($customers);
            $this->command->info("Found {$totalCustomers} customers to sync");
            
            $syncedCustomers = 0;
            $skippedCustomers = 0;
            $errors = 0;
            
            foreach ($customers as $customerData) {
                try {
                    // Validate required fields
                    if (empty($customerData['phone']) && empty($customerData['phone_original'])) {
                        $this->command->warn("Skipping customer ID {$customerData['id']}: No phone number");
                        $skippedCustomers++;
                        continue;
                    }
                    
                    // Sync customer
                    $customer = $this->syncCustomer($customerData);
                    if ($customer) {
                        $syncedCustomers++;
                    } else {
                        $skippedCustomers++;
                    }
                    
                } catch (\Exception $e) {
                    $errors++;
                    $this->command->error('Error syncing customer ID ' . ($customerData['id'] ?? 'unknown') . ': ' . $e->getMessage());
                    Log::error('Customer sync error: ' . $e->getMessage(), [
                        'customer_data' => $customerData,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            $this->command->info("✓ Sync completed!");
            $this->command->info("  - Synced: {$syncedCustomers}");
            $this->command->info("  - Skipped: {$skippedCustomers}");
            $this->command->info("  - Errors: {$errors}");
            
        } catch (\Exception $e) {
            $this->command->error('Fatal error: ' . $e->getMessage());
            Log::error('Customer API Sync Fatal Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Sync customer
     */
    private function syncCustomer($customerData)
    {
        // Use DB::table()->updateOrInsert to maintain same ID from API
        $customerId = $customerData['id'];
        
        // Validate and prepare data
        $phone = $this->validatePhone($customerData['phone'] ?? $customerData['phone_original'] ?? null);
        $phoneOriginal = $this->validatePhone($customerData['phone_original'] ?? null);
        $email = $this->validateEmail($customerData['email'] ?? null);
        $name = $customerData['full_name'] ?? $customerData['name'] ?? null;
        
        // Generate slug from name or phone
        $slug = $this->generateSlug($name, $phone, $customerId);
        
        $updateData = [
            'name' => $name,
            'full_name' => $customerData['full_name'] ?? null,
            'phone' => $phone,
            'phone_original' => $phoneOriginal,
            'email' => $email,
            'gender' => $this->validateGender($customerData['gender'] ?? null),
            'address' => $customerData['address'] ?? null,
            'thana' => $customerData['thana'] ?? null,
            'post_code' => $customerData['post_code'] ?? null,
            'city' => $customerData['city'] ?? null,
            'country' => $customerData['country'] ?? null,
            'order_id' => $customerData['order_id'] ?? null,
            'slug' => $slug,
            'status' => 'active', // Default status
            'updated_at' => $customerData['updated_at'] ?? now(),
        ];
        
        // Check if customer exists
        $exists = DB::table('customers')->where('id', $customerId)->exists();
        
        if ($exists) {
            // Update existing customer
            DB::table('customers')->where('id', $customerId)->update($updateData);
        } else {
            // Insert new customer with explicit ID
            $updateData['id'] = $customerId;
            $updateData['created_at'] = $customerData['created_at'] ?? now();
            DB::table('customers')->insert($updateData);
        }
        
        $customer = Customer::find($customerId);
        $this->command->info("  ✓ Customer: {$customer->name} (ID: {$customer->id}, Phone: {$customer->phone})");
        
        return $customer;
    }
    
    /**
     * Validate and format phone number
     */
    private function validatePhone($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Remove any non-digit characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Ensure phone doesn't exceed database limit (60 chars)
        return substr($phone, 0, 60);
    }
    
    /**
     * Validate email
     */
    private function validateEmail($email)
    {
        if (empty($email)) {
            return null;
        }
        
        // Validate email format
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        
        if ($email === false) {
            return null;
        }
        
        // Ensure email doesn't exceed database limit (100 chars)
        return substr($email, 0, 100);
    }
    
    /**
     * Validate gender
     */
    private function validateGender($gender)
    {
        if (empty($gender)) {
            return null;
        }
        
        $gender = strtolower(trim($gender));
        
        $validGenders = ['male', 'female', 'other'];
        
        if (in_array($gender, $validGenders)) {
            return $gender;
        }
        
        // Try to map common variations
        $genderMap = [
            'm' => 'male',
            'f' => 'female',
            'man' => 'male',
            'woman' => 'female',
            'men' => 'male',
            'women' => 'female',
        ];
        
        return $genderMap[$gender] ?? null;
    }
    
    /**
     * Generate unique slug
     */
    private function generateSlug($name, $phone, $customerId)
    {
        // Try to generate slug from name
        if (!empty($name)) {
            $baseSlug = Str::slug($name);
            if (!empty($baseSlug)) {
                $slug = $baseSlug;
                $counter = 1;
                
                // Check if slug already exists for different customer
                while (DB::table('customers')
                    ->where('slug', $slug)
                    ->where('id', '!=', $customerId)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                return $slug;
            }
        }
        
        // Fallback to phone-based slug
        if (!empty($phone)) {
            $phoneSlug = 'customer-' . preg_replace('/[^\d]/', '', $phone);
            $slug = $phoneSlug;
            $counter = 1;
            
            while (DB::table('customers')
                ->where('slug', $slug)
                ->where('id', '!=', $customerId)
                ->exists()) {
                $slug = $phoneSlug . '-' . $counter;
                $counter++;
            }
            
            return $slug;
        }
        
        // Final fallback
        return 'customer-' . $customerId;
    }
}

