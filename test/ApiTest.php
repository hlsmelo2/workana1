<?php

namespace Test;

use Phug\Util\TestCase;
use Work\Soft_Expert\Api;

class ApiTest extends TestCase {
    protected int $products_count = 0;
    protected int $product_types_count = 0;
    protected int $taxes_count = 0;

    protected function init(): void {
        if ($this->products_count !== 0) {
            return;
        }
        
        $this->products_count = $this->test_if_be_able_to_get_products_list();
        $this->product_types_count = $this->test_to_get_product_types_list();
        $this->taxes_count = $this->test_to_get_taxes_list();        
    }

    public function test_if_be_able_to_get_products_list(): int{
        $products = Helpers::json_parse(Api::get_products([]));
        $this->assertIsArray($products);

        return count($products);
    }
        
    public function test_to_get_product_types_list(): int {
        $product_types = Helpers::json_parse( Api::get_product_types() );
        $this->assertIsArray($product_types);

        return count($product_types);
    }

    public function test_to_get_taxes_list(): int {
        $taxes = Helpers::json_parse( Api::get_taxes() );
        $this->assertIsArray($taxes);

        return count($taxes);
    }
    
    public function test_to_insert_products(): void {
        $this->init();
        $count = $this->products_count + 1;

        $product = [
            'name' => "Product {$count}",
            'description' => "Product {$count} description",
            'image' => '',
            'image_file' => null,
            'quantity' => '10',
            'product_type_id' => $count,
            'price' => Helpers::get_money_increment(10, $count),

        ];

        Api::register_product($product);
        $this->assertGreaterThan($this->products_count, $this->test_if_be_able_to_get_products_list());
    }
    
    public function test_to_insert_product_types(): void {
        $this->init();
        $count = $this->product_types_count + 1;

        $product_type = [
            'name' => "Product type {$count}",
            'description' => "Product type {$count} description",
            'tax_id' => $count,
        ];

        Api::register_product_type($product_type);
        $this->assertGreaterThan($this->product_types_count, $this->test_to_get_product_types_list());
    }

    public function test_to_insert_taxes(): void {
        $this->init();
        $count = $this->taxes_count + 1;

        $tax = [
            'name' => "Tax {$count}",
            'value' => Helpers::get_money_increment(10, $count),
        ];

        Api::register_tax($tax);
        $this->assertGreaterThan($this->taxes_count, $this->test_to_get_taxes_list());
    }

    public function test_to_get_full_product_list_and_check_tax_property_exists(): void {
        $products = Helpers::json_parse( Api::get_products(['full' => 1]) );
        $products = ( count($products) > 0 ) ? $products[0] : [[]];

        $this->assertArrayHasKey('tax', $products);
    }

    public function test_to_get_product_list_and_check_tax_property_dont_exists(): void {
        $products = Helpers::json_parse( Api::get_products([]) );
        $this->assertArrayNotHasKey('tax', $products[0]);
    }
}
