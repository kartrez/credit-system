<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $client;
    
    protected function setUp(): void
    {
        $this->client = new Client(
            'Test User',
            30,
            'PR',
            'Prague',
            2000.0,
            700,
            'TEST-12345',
            'test@example.com',
            '+42012345678'
        );
    }
    
    public function testGetName(): void
    {
        $this->assertEquals('Test User', $this->client->getName());
    }
    
    public function testGetAge(): void
    {
        $this->assertEquals(30, $this->client->getAge());
    }
    
    public function testGetRegion(): void
    {
        $this->assertEquals('PR', $this->client->getRegion());
    }
    
    public function testGetCity(): void
    {
        $this->assertEquals('Prague', $this->client->getCity());
    }
    
    public function testGetIncome(): void
    {
        $this->assertEquals(2000.0, $this->client->getIncome());
    }
    
    public function testGetScore(): void
    {
        $this->assertEquals(700, $this->client->getScore());
    }
    
    public function testGetPin(): void
    {
        $this->assertEquals('TEST-12345', $this->client->getPin());
    }
    
    public function testGetEmail(): void
    {
        $this->assertEquals('test@example.com', $this->client->getEmail());
    }
    
    public function testGetPhone(): void
    {
        $this->assertEquals('+42012345678', $this->client->getPhone());
    }
} 