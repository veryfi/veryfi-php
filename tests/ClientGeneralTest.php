<?php

use veryfi\Client;

require_once __DIR__ . '/ClientTestCase.php';

class ClientGeneralTest extends ClientTestCase
{
    public function test_validate_signature(): void
    {
        $client_signature = "m89UF6aTlce2YcVbGw5LTZuA+bc5MPVS9AOicjkS7qM=";
        $client_secret = "fAKEB2oJMLbHwBN5jEd6h3f3Lj1o9gK5kcz2xAf8Kyi2X1PNaJ6F612344YcOsSllGkFAkeUiZV5ZTNoPkk6bXyctGGAdfcratu4Dl2CA2XtU6En5icHxjVRUNoSFGP";
        $payload = array("event" => "document.created", "data" => array("id" => 63184393, "created" => "2022-03-28 21:12:14"));
        $this->assertTrue(Client::verify_signature($payload["data"], $client_secret, $client_signature));
    }

    public function test_bad_credentials(): void
    {
        $veryfi_client = new Client('', '', '', '');
        $json_response = json_decode($veryfi_client->get_documents(), true);
        $this->assertEquals('fail', $json_response['status']);
    }
}
