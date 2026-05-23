<?php

namespace App\Services;

use App\Checks\Contracts\VulnerabilityCheck;
use App\Models\Audit;
use Throwable;

class ScannerService
{
    /** @var array<string, VulnerabilityCheck> */
    private array $checks = [];

    public function register(VulnerabilityCheck $check): void
    {
        $this->checks[$check->code()] = $check;
    }

    public function checks(): array
    {
        return array_values($this->checks);
    }

    public function run(Audit $audit): void
    {
        $audit->loadMissing('pages.endpoints');

        foreach ($audit->pages as $page) {
            foreach ($page->endpoints as $endpoint) {
                foreach ($this->checks as $check) {
                    try {
                        foreach ($check->check($endpoint) as $vulnerability) {
                            $vulnerability->endpoint_id = $endpoint->id;
                            $vulnerability->check_code  = $check->code();
                            $vulnerability->save();
                        }
                    } catch (Throwable $e) {
                        continue;
                    }
                }
            }
        }
    }
}
