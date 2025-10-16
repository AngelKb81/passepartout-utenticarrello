<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crea ruoli di base
        Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        Role::create(['name' => 'user', 'display_name' => 'User']);
    }

    /** @test */
    public function authenticated_user_can_view_cart()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'cart' => ['id', 'user_id', 'items'],
                'total'
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_cart()
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_add_product_to_cart()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create(['prezzo' => 50.00, 'scorte' => 10]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Prodotto aggiunto al carrello'
            ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function adding_same_product_increases_quantity()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create(['scorte' => 10]);

        // Prima aggiunta
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        // Seconda aggiunta dello stesso prodotto
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 3,
            ]);

        $response->assertStatus(200);

        // Verifica che la quantità sia 5 (2 + 3)
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        $this->assertEquals(5, $cartItem->quantity);
    }

    /** @test */
    public function cannot_add_more_than_available_stock()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create(['scorte' => 5]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 10, // Più delle scorte disponibili
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Quantità non disponibile'
            ]);
    }

    /** @test */
    public function user_can_update_cart_item_quantity()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create(['scorte' => 20]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'prezzo_unitario' => $product->prezzo,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/cart/update/{$cartItem->id}", [
                'quantity' => 5,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    /** @test */
    public function user_can_remove_item_from_cart()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create();

        $cart = Cart::create(['user_id' => $user->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'prezzo_unitario' => $product->prezzo,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/cart/remove/{$cartItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Prodotto rimosso dal carrello'
            ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    /** @test */
    public function user_can_checkout_cart()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;
        $product = Product::factory()->create(['prezzo' => 100.00, 'scorte' => 10]);

        $cart = Cart::create(['user_id' => $user->id, 'stato' => 'attivo']);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'prezzo_unitario' => $product->prezzo,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart/checkout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Ordine completato con successo'
            ]);

        // Verifica che il carrello sia stato completato
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'stato' => 'completato',
        ]);
    }

    /** @test */
    public function cannot_checkout_empty_cart()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart/checkout');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Il carrello è vuoto'
            ]);
    }

    /** @test */
    public function cart_total_is_calculated_correctly()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $product1 = Product::factory()->create(['prezzo' => 50.00]);
        $product2 = Product::factory()->create(['prezzo' => 30.00]);

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'prezzo_unitario' => $product1->prezzo,
        ]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'prezzo_unitario' => $product2->prezzo,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJson([
                'total' => 130.00 // (50*2) + (30*1)
            ]);
    }

    /** @test */
    public function user_cannot_modify_another_users_cart()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $token1 = $user1->createToken('test-token')->plainTextToken;

        $product = Product::factory()->create();
        $cart2 = Cart::create(['user_id' => $user2->id]);
        $cartItem2 = CartItem::create([
            'cart_id' => $cart2->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'prezzo_unitario' => $product->prezzo,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->deleteJson("/api/cart/remove/{$cartItem2->id}");

        $response->assertStatus(403);
    }

    // Helper method
    protected function createUser(): User
    {
        $userRole = Role::where('name', 'user')->first();
        $user = User::factory()->create();
        $user->roles()->attach($userRole);
        return $user;
    }
}
