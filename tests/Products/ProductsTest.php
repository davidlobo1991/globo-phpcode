<?php

namespace Tests\Products;

use App\Http\Requests\CreateReservationsRequest;
use App\Models\Web\Product;
use App\User;
use http\Env\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductsTest extends TestCase
{
    protected $faker;

    public function setUp()
    {
        parent::setUp();
        DB::beginTransaction();
    }

    public function tearDown()
    {
        DB::rollBack();
    }

    /**
     * @test
     */
    public function checkPassesTest()
    {
        $this->loginAsAdmin();

        $faker = Faker\Factory::create();

        $attributes = [
            'name' => $faker->text,
            'acronym' => $faker->text(5),
            'has_passes' => true
        ];

        $product = new \Globobalear\Products\Models\Product($attributes);


        $product->save();

            $responseTrue = $this->get('/data/checkPasses?. ' . $product->id . '');

            $test = json_decode($responseTrue->getContent());
            $this->assertFalse($test->status);

            $product->has_passes = false;
            $product->update();


            $responseFalse = $this->get('/data/checkPasses?. ' . $product->id . '');
            $test = json_decode($responseFalse->getContent());
            $this->assertFalse($test->status);
    }

    public function loginAsAdmin() {
        Session::start();
        $user = User::find(1);
        Auth::login($user, true);
    }

}
