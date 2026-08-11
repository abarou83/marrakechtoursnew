<?php

namespace Tests\Unit;

use App\Enums\BookingChannel;
use Tests\TestCase;

class BookingChannelTest extends TestCase
{
    public function test_ota_channels_are_identified(): void
    {
        $this->assertTrue(BookingChannel::Viator->isOta());
        $this->assertTrue(BookingChannel::GetYourGuide->isOta());
        $this->assertFalse(BookingChannel::Direct->isOta());
        $this->assertFalse(BookingChannel::WhatsApp->isOta());
    }

    public function test_options_contains_direct_channel(): void
    {
        $options = BookingChannel::options();

        $this->assertArrayHasKey('direct', $options);
        $this->assertArrayHasKey('viator', $options);
    }
}
