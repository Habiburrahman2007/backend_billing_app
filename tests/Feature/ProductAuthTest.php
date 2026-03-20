<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_authenticate_with_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
        ]);
    }

    public function test_user_can_edit_own_product_with_token()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/products/{$product->id}", [
                'name' => 'Updated Name',
                'price' => 99.99,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_can_delete_own_product_with_token()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_user_cannot_edit_others_product()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $productOfUser2 = Product::factory()->create(['user_id' => $user2->id]);
        $token1 = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->putJson("/api/products/{$productOfUser2->id}", [
                'name' => 'Should Fail',
            ]);

        $response->assertStatus(403);
    }
}
