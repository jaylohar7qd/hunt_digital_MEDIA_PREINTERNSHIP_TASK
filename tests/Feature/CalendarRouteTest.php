<?php

namespace Tests\Feature;

use Tests\TestCase;

class CalendarRouteTest extends TestCase
{
    public function test_calendar_page_is_accessible(): void
    {
        $response = $this->get('/html-page');

        $response->assertStatus(200);
        $response->assertSee('Add Event');
        $response->assertSee('app-calendar-wrapper');
    }
}
