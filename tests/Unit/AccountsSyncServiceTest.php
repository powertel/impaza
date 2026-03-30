<?php

namespace Tests\Unit;

use App\Services\AccountsSyncService;
use PHPUnit\Framework\TestCase;

class AccountsSyncServiceTest extends TestCase
{
    public function testNormalizeAccountSupportsAlternativeKeys(): void
    {
        $service = new AccountsSyncService();
        $method = new \ReflectionMethod($service, 'normalizeAccount');
        $method->setAccessible(true);

        $out = $method->invoke($service, [
            'accountNumber' => ' 700000371 ',
            'contractNumber' => ' 40108570 ',
            'customer_name' => ' ZETDC TRANSMISSION ',
            'address' => ' 1 Samora Machel ',
            'contactNumber' => ' 0770000000 ',
        ]);

        $this->assertSame('700000371', $out['account_number']);
        $this->assertSame('40108570', $out['contract_number']);
        $this->assertSame('ZETDC TRANSMISSION', $out['customer']);
        $this->assertSame('1 Samora Machel', $out['address']);
        $this->assertSame('0770000000', $out['contact_number']);
    }

    public function testNormalizeLinkSupportsAlternativeKeys(): void
    {
        $service = new AccountsSyncService();
        $method = new \ReflectionMethod($service, 'normalizeLink');
        $method->setAccessible(true);

        $out = $method->invoke($service, [
            'service' => ' Main Link ',
            'jccNumber' => ' JCC-1 ',
            'sap' => ' SAP-123 ',
            'quantity' => '2',
            'serviceType' => 'Fibre',
            'capacity' => '1Gbps',
            'cityId' => '1',
            'suburbId' => 2,
            'popId' => '3',
            'linkTypeId' => '2',
            'linkStatus' => '2',
            'contractNumber' => '40108570',
        ]);

        $this->assertSame('Main Link', $out['link']);
        $this->assertSame('JCC-1', $out['jcc_number']);
        $this->assertSame('SAP-123', $out['sapcodes']);
        $this->assertSame(2, $out['quantity']);
        $this->assertSame('Fibre', $out['service_type']);
        $this->assertSame('1Gbps', $out['capacity']);
        $this->assertSame(1, $out['city_id']);
        $this->assertSame(2, $out['suburb_id']);
        $this->assertSame(3, $out['pop_id']);
        $this->assertSame(2, $out['linkType_id']);
        $this->assertSame(2, $out['link_status']);
        $this->assertSame('40108570', $out['contract_number']);
    }
}

