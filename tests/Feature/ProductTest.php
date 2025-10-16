<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Crea ruoli di base
        Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        Role::create(['name' => 'user', 'display_name' => 'User']);
    }

    /** @test */
    public function anyone_can_view_products_list()
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function anyone_can_view_single_product()
    {
        $product = Product::factory()->create([
            'nome' => 'Test Product',
            'prezzo' => 99.99,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'nome' => 'Test Product',
                'prezzo' => '99.99',
            ]);
    }

    /** @test */
    public function viewing_non_existent_product_returns_404()
    {
        $response = $this->getJson('/api/products/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_create_product()
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('test-token')->plainTextToken;

        $productData = [
            'nome' => 'New Product',
            'codice' => 'PROD001',
            'descrizione' => 'Test description',
            'prezzo' => 49.99,
            'categoria' => 'Elettronica',
            'scorte' => 100,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJson([
                'nome' => 'New Product',
                'prezzo' => '49.99',
            ]);

        $this->assertDatabaseHas('products', [
            'nome' => 'New Product',
            'codice' => 'PROD001',
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_product()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $productData = [
            'nome' => 'New Product',
            'prezzo' => 49.99,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/products', $productData);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_product()
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create(['nome' => 'Old Name']);

        $updateData = [
            'nome' => 'Updated Name',
            'prezzo' => 79.99,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'nome' => 'Updated Name',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'nome' => 'Updated Name',
        ]);
    }

    /** @test */
    public function admin_can_delete_product()
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    public function product_creation_requires_valid_data()
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('test-token')->plainTextToken;

        $invalidData = [
            'nome' => '', // Nome vuoto
            'prezzo' => -10, // Prezzo negativo
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/products', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'prezzo']);
    }

    /** @test */
    public function products_can_be_filtered_by_category()
    {
        Product::factory()->create(['categoria' => 'Elettronica']);
        Product::factory()->create(['categoria' => 'Abbigliamento']);
        Product::factory()->create(['categoria' => 'Elettronica']);

        $response = $this->getJson('/api/products?categoria=Elettronica');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function products_can_be_searched_by_name()
    {
        Product::factory()->create(['nome' => 'iPhone 15']);
        Product::factory()->create(['nome' => 'Samsung Galaxy']);
        Product::factory()->create(['nome' => 'iPhone 14']);

        $response = $this->getJson('/api/products?search=iPhone');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertCount(2, $products);
    }

    /** @test */
    public function product_price_must_be_positive()
    {
        $admin = $this->createAdmin();
        $token = $admin->createToken('test-token')->plainTextToken;

        $productData = [
            'nome' => 'Test Product',
            'prezzo' => -50,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['prezzo']);
    }

    // Helper methods
    protected function createAdmin(): User
    {
        $adminRole = Role::where('name', 'admin')->first();
        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole);
        return $admin;
    }

    protected function createUser(): User
    {
        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create();
        $user->roles()->attach($userRole);
        return $user;
    }
}
