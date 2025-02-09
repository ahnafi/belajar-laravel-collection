<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

    function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });
        self::assertEquals([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["budi"]);
        $res = $collection->mapInto(Person::class);
        self::assertEquals([new Person("budi")], $res->all());
    }

    function testMapSpread()
    {
        $collection = collect([["budiono", "siregar"], ["alex", 'budiman']]);
        $res = $collection->mapSpread(function ($firstName, $lastName) {
            return new Person($firstName . " " . $lastName);
        });

        self::assertEquals(
            [
                new Person("budiono siregar"),
                new Person("alex budiman"),
            ],
            $res->all()
        );
    }

    function testMapsToGroup()
    {
        $collection = collect([
            [
                "name" => "budiono",
                "department" => "IT"
            ],
            [
                "name" => "sebas",
                "department" => "IT"
            ],
            [
                "name" => "alex",
                "department" => "HR"
            ]
        ]);

        $res = $collection->mapToGroups(function ($item) {
            return [$item["department"] => $item["name"]];
        });

        self::assertEquals(
            [
                "IT" => collect(["budiono", "sebas"]),
                "HR" => collect(["alex"])
            ]
            ,
            $res->all()
        );
    }

    function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        self::assertEquals(
            [
                collect([1, 4]),
                collect([2, 5]),
                collect([3, 6]),
            ],
            $collection3->all()
        );
    }

    function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        self::assertEquals(
            [1, 2, 3, 4, 5, 6],
            $collection3->all()
        );
    }

    function testCombine()
    {
        $collection1 = ["name", "country"];
        $collection2 = ["budiono", "Indonesia"];
        $collection3 = collect($collection1)->combine($collection2);

        self::assertEquals(
            [
                "name" => "budiono",
                "country" => "Indonesia"
            ],
            $collection3->all()
        );
    }

    function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $result = $collection->collapse();
        self::assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "budi",
                "hobbies" => ["berenang", 'football']
            ],
            [
                "name" => "ani",
                "hobbies" => ["memasak", 'menulis']
            ],
        ]);

        $result = $collection->flatMap(function ($item) {
            return $item["hobbies"];
        });

        self::assertEquals(["berenang", 'football', "memasak", 'menulis'], $result->all());
    }

    function testJoin()
    {
        // string representation -> mengubah collection menjadi string
        $collection = collect(["budi", "ono", "siregar"]);

        $result = $collection->join('-');
        self::assertEquals("budi-ono-siregar", $result);

        $result = $collection->join("_", "-");
        self::assertEquals("budi_ono-siregar", $result);
    }

    function testFilter()
    {
        $collection = collect([
            "eko" => 100,
            "budi" => 200,
            "joko" => 300,
        ]);

        $result = $collection->filter(function ($item, $key) {
            return $item >= 150;
        });

        self::assertEquals([
            "budi" => 200,
            "joko" => 300,
        ], $result->all());
    }

    public function testPartitioning()
    {
        $collection = collect([
            "eko" => 100,
            "budi" => 200,
            "joko" => 300,
        ]);

        [$result1, $result2] = $collection->partition(function ($item, $key) {
            return $item >= 150;
        });

        self::assertEquals([
            "budi" => 200,
            "joko" => 300,
        ], $result1->all());

        self::assertEquals(expected: [
            "eko" => 100,
        ], actual: $result2->all());

    }

}
