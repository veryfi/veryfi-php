<?php
namespace veryfi\client;
trait GetUrl
{
    /**
     * Get API Base URL with API Version.
     * @return string Base URL to Veryfi API.
     */
    private function get_url(): string
    {
        return "$this->base_url$this->api_version/partner";
    }
}
