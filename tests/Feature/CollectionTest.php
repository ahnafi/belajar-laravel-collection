<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3]);
        self::assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach ($collection as $key => $item) {
            self::assertEquals($key + 1, $item);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);
        self::assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $pop = $collection->pop();
        self::assertEquals(3, $pop);
        self::assertEqualsCanonicalizing([1, 2], $collection->all());

        $prepend = $collection->prepend(0);
        self::assertEquals([0, 1, 2], $prepend->all());
//        self::assertEqualsCanonicalizing([1, 2], $collection->all());

        $pull = $prepend->pull(2);
        self::assertEquals(2, $pull);

        $put = $collection->put("a", 1);
        self::assertEquals([0, 1, "a" => 1], $put->all());
    }

}
