<?php

declare(strict_types=1);

namespace veryfi\fraud;

trait FraudOperations
{
    /** https://docs.veryfi.com/api/get-devices-from-blocklist/ */
    public function get_blocklisted_devices(array $kwargs = []): string
    {
        return $this->request('GET', '/fraud/blocklist/', $kwargs);
    }

    /** https://docs.veryfi.com/api/add-devices-to-blocklist/ */
    public function add_blocklisted_devices(array $device_ids): string
    {
        return $this->request('POST', '/fraud/blocklist/', ['device_ids' => $device_ids]);
    }

    /** https://docs.veryfi.com/api/remove-a-device-from-blocklist/ */
    public function remove_blocklisted_device(string $device_id): string
    {
        return $this->request('DELETE', "/fraud/blocklist/$device_id/");
    }
}
