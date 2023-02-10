<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

return [
    'user_id' => $faker->unique()->numberBetween(1, $index+1),
    'current_rating' => $faker->randomFloat(1, 1, 5),
    'star1' => $faker->randomElement([0, 1, 2, 3, 4, 5]),
    'star2' => $faker->randomElement([0, 1, 2, 3, 4, 5]),
    'star3' => $faker->randomElement([0, 1, 2, 3, 4, 5]),
    'star4' => $faker->randomElement([0, 1, 2, 3, 4, 5]),
    'star5' => $faker->randomElement([0, 1, 2, 3, 4, 5])
];