<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class DNS_LookupTest extends TestCase
{
    public function testValidDomains(): void
    {
        $form_data = '[{"value":"www.google.com"},{"value":"www.fb.com"},{"value":"http://fb.com"}]';
        
        foreach (json_decode($form_data) as $domain) {
            $this->assertTrue(Ctrl\DNS_Lookup::isValidDomain($domain->value));
        }        
    }

    public function testInvalidDomains(): void
    {
        $form_data = '[{"value":"www.mp3#.com"},{"value":"http://www.fakey"},{"value":"www.fakey-.live"}]';
        
        foreach (json_decode($form_data) as $domain) {
            $this->assertFalse(Ctrl\DNS_Lookup::isValidDomain($domain->value));
        }        
    }

    public function testResultsNotFound(): void
    {
        $form_data = '[{"value":"http://gmail.com"}]';
        
        $result = (new Ctrl\DNS_Lookup($form_data))->getData();

        $this->assertEquals('error', json_decode($result)->status);
        $this->assertEquals('Data Not Found', json_decode($result)->msg);
    }

    public function testResultsFound(): void
    {
        $form_data = '[{"value":"www.gmail.com"}]';
        
        $result = (new Ctrl\DNS_Lookup($form_data))->getData();

        $this->assertEquals('ok', json_decode($result)->status);
    }
}