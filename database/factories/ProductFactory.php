<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name"=>$this->faker->randomElement($array = array ("jus d'orange","coca","pomme","poire","baguette","sauce tomate","spaghetti","couque","fanta")) ,
            "description"=>$this->faker->text(50),
            "cover_path"=>$this->faker->imageUrl($width = 640, $height = 480) ,
            "price"=>$this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 2),
            "quantity"=>$this->faker->randomNumber( $min = 5, $max = 30),
            "shop_id"=>1,
            "cart_id"=>null,
            "created_at"=>date("Y-m-d H:i:s") ,
            "updated_at"=>date("Y-m-d H:i:s") 

        ];
    }
}
