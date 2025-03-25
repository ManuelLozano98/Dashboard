<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DatabaseTest
 *
 * @author Usuario
 */
require_once "../database.php";

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {

    public function testQueryResults() {
        $sqlData = [
            ["John", "Doe"],
            ["John2", "Doe2"],
        ];
        $mock = $this->createMock(mysqli::class);
        $mock->method("query")->willReturn($sqlData);
        $result = $mock->query("SELECT * FROM TEST");
        $this->assertSame($result, $sqlData);
    }

}
