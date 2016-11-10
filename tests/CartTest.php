<?php

namespace Cart\Tests;

use Cart\Cart;
use Cart\Contracts\SessionContract;
use Cart\Item;


class CartTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function createCart()
    {
        $cart = new Cart();

        // Is a Cart
        $this->assertInstanceOf('Cart\Cart', $cart);

        // Check Properties
        foreach (['id', 'items'] as $property) {
            $this->assertObjectHasAttribute($property, $cart);
        }

        // Check methods
        foreach (['has', 'items', 'add', 'update', 'session'] as $method) {
            $this->assertTrue(
                method_exists($cart, $method),
                'Cart does not have method "' . $method . '"'
            );
        }
    }

    /** @test */
    public function createCartWithCustomId()
    {
        $id   = uniqid();
        $cart = new Cart($id);

        $this->assertEquals($cart->id(), $id);
    }

    /** @test */
    public function changeCartId()
    {
        $cart = new Cart();
        $id   = uniqid();
        $cart->id($id);

        $this->assertEquals($cart->id(), $id);
    }

    /** @test */
    public function changeSessionSystem()
    {
        $cart = new Cart();

        // The default session class is set
        $this->assertInstanceOf('Cart\Session', $cart->session());

        $cart->session(new TestingSession());

        $this->assertInstanceOf('Cart\Tests\TestingSession', $cart->session());
    }

    /** @test */
    public function cartPropertiesReturnsAnArray()
    {
        $cart = new Cart();

        $this->assertTrue(
            is_array($cart->properties()),
            'Cart properties does not return an array'
        );
    }

    /** @test */
    public function addItem()
    {
        $cart = new Cart();
        $id   = uniqid();
        $item = new Item($id);

        $this->assertEquals(count($cart->items()), 0);

        $cart->add($item);

        $this->assertEquals(count($cart->items()), 1);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cart->items()[0]->id(), $id);
    }

    /** @test */
    public function addMultipleItems()
    {
        $cart  = new Cart();
        $id0   = uniqid();
        $id1   = uniqid();
        $id2   = uniqid();
        $items = [
            new Item($id0),
            new Item($id1),
            new Item($id2),
        ];

        $this->assertEquals(count($cart->items()), 0);

        $cart->add($items);

        $this->assertEquals(count($cart->items()), 3);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cart->items()[0]->id(), $id0);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cart->items()[1]->id(), $id1);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cart->items()[2]->id(), $id2);
    }

    /** @test */
    public function addItemsWithFluent()
    {
        $cart = new Cart();
        $id0  = uniqid();
        $id1  = uniqid();
        $id2  = uniqid();

        $cart->add(new Item($id0))->add(new Item($id1))->add(new Item($id2));

        $this->assertEquals(count($cart->items()), 3);
    }

    /** @test */
    public function updateCartItem()
    {
        $cart     = new Cart();
        $id       = uniqid();
        $item     = new Item($id);
        $quantity = rand(1, 100);

        $cart->add($item);
        $item->quantity($quantity);
        $cart->update($item);

        $this->assertEquals(count($cart->items()), 1);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertTrue($cart->item($id)->quantity() === $quantity);
    }

    /** @test */
    public function updateCartItemWithAnotherItemObject()
    {
        $cart     = new Cart();
        $id       = uniqid();
        $quantity = rand(1, 10);

        $cart->add(new Item($id))->update(new Item([
            'id'       => $id,
            'quantity' => $quantity
        ]));

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($cart->items()[0]->quantity(), $quantity);
    }

    /** @test */
    public function whenAddingAnItemTwiceItGetsUpdated()
    {
        $cart = new Cart();
        $id   = uniqid();
        $item = new Item($id);

        $this->assertEquals(count($cart->items()), 0);

        $cart->add($item);

        $this->assertEquals(count($cart->items()), 1);

        /** @noinspection PhpUndefinedFieldInspection */
        $item->foo = 'bar';
        $cart->add($item);

        $this->assertEquals(count($cart->items()), 1);
        $this->assertObjectHasAttribute('foo', $cart->items()[0]);
    }

    /** @test */
    public function getCartItem()
    {
        $cart = new Cart();
        $id   = uniqid();
        $item = new Item($id);

        $cart->add($item);
        $retrievedItem = $cart->item($id);

        $this->assertEquals($item, $retrievedItem);
    }
}

// Session class for changeSessionSystem() test
class TestingSession implements SessionContract
{
    public function put($key, $value)
    {
    }

    public function get($key)
    {
    }

    public function has($key)
    {
    }

    public function forget($key)
    {
    }

    public function flush()
    {
    }
}