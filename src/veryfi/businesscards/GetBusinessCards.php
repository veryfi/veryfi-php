<?php
namespace veryfi\businesscards;
trait GetBusinessCards
{
    /**
     * Get all Business cards. https://docs.veryfi.com/api/business-cards/get-business-cards/
     * @param array $kwargs Additional request parameters.
     * @return string A JSON with list of processes documents and metadata.
     */
    public function get_business_cards(array $kwargs = array()): string
    {
        $endpoint_name = '/business-cards/';
        return $this->request('GET', $endpoint_name, $kwargs);
    }
}
