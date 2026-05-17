<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminSnapshotViewTest extends TestCase
{
    public function test_snapshot_view_renders_nested_array_values_without_crashing(): void
    {
        $html = view('filament.admin.view-snapshot', [
            'snapshot' => [
                'name' => 'Jane Worker',
                'meta' => [
                    'skills' => ['PHP', 'Laravel'],
                    'experience' => [
                        ['role' => 'Cook', 'years' => 2],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('Jane Worker', $html);
        $this->assertStringContainsString('PHP', $html);
        $this->assertStringContainsString('Cook', $html);
    }
}
