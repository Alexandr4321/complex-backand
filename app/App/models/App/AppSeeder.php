<?php

namespace App\App\Seeders;

use App\App\Models\Card;
use App\App\Models\Faq;
use App\App\Models\Policy;
use App\Auth\Models\Access;
use App\Auth\Models\Permit;
use App\Auth\Types\TypeAccessCreate;
use App\System\Classes\Seeder;
use App\Auth\Models\User;

class AppSeeder extends Seeder
{
    public function run()
    {
        $this->seedUsers();
        $this->seedFaqs();
        $this->seedPolicy();
        $this->makeProduct();
    }

    public function seedUsers()
    {
        $model = User::create([
            'username' => 'admin',
            'email' => 'example@test.st',
            'firstName' => 'admin',
            'lastName' => 'admin',
            'login'=> 'admin',
            'password' => '123123',
            'isAdmin' => true,
        ]);

        Access::create(new TypeAccessCreate([
            'access' => Permit::query()->where('name', 'admin')->first(),
            'owner' => $model,
        ]));

        $model = User::create([
            'phone' => '+77777777777',
            'email' => 'example2@test.st',
            'firstName' => 'Alexandr',
            'lastName' => 'Vassilenko',
            'password' => '123123',
        ]);
        $model = User::create([
            'phone' => '+88888888888',
            'email' => 'example2@test.st',
            'firstName' => 'Dmitry',
            'lastName' => 'Vassilenko',
            'password' => '123123',
        ]);
    }



    public function makeProduct() {
        Card::create([
            'id' => 1,
            'userId' => 2,
            'title' => 'Тестовое объявление',
            'content' => 'Описание объявления',
            'price' => rand(10000, 50000),
            'floor' => rand(1, 10),
            'countRoom' => rand(1, 5),
            'square' => rand(30, 150),
            'squareKitchen' => rand(5, 20),
            'squareBath' => rand(3, 10),
            'repairType' => 'евро',
            'type' => 'жилье',
            'location' => 'центр',
            'countBed' => rand(1, 3),
            'countSofa' => rand(1, 2),
            'elevator' => true,
            'conditioning' => false,
            'balcony' => true,
            'internet' => true,
            'furniture' => true,
            'washingMachine' => false,
            'kindergarten' => true,
            'school' => true,
            'hospital' => false,
            'drugstore' => false,
            'park' => true,
            'market' => true,
            'childrenAllowed' => true,
            'petsAllowed' => false,
            'city' => 'Алматы',
            'district' => 'Бостандыкский',
            'address' => 'ул. Абая, д. 123',
            'homeName' => 'ЖК Тестовый',
            'status' => 'active',
            'createdAt' => now(),
        ]);
    }



    public function seedFaqs()
    {
        $faqs = [
            [
                'title' => [
                    'ru' => 'Заголовок1',
                    'kz' => 'Заголовок1',
                    'en' => 'Заголовок1',
                ],
                'content' => [
                    'ru' => 'Содержание1',
                    'kz' => 'Содержание1',
                    'en' => 'Содержание1',
                ],
            ],
            [
                'title' => [
                    'ru' => 'Заголовок2',
                    'kz' => 'Заголовок2',
                    'en' => 'Заголовок2',
                ],
                'content' => [
                    'ru' => 'Содержание2',
                    'kz' => 'Содержание2',
                    'en' => 'Содержание2',
                ],
            ],
            [
                'title' => [
                    'ru' => 'Заголовок3',
                    'kz' => 'Заголовок3',
                    'en' => 'Заголовок3',
                ],
                'content' => [
                    'ru' => 'Содержание3',
                    'kz' => 'Содержание3',
                    'en' => 'Содержание3',
                ],
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }

    public function seedPolicy() {
        $policyText = file_get_contents(database_path('files/policy'));
        Policy::create([
            'content' => [
                'ru' => $policyText,
                'en' => $policyText,
                'kz' => $policyText,
            ]
        ]);
    }
}
