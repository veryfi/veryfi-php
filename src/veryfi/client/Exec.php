<?php
namespace veryfi\client;
trait Exec
{
    /**\internal
     * Exec the curl, needed for mock it.
     *
     * @param $curl Curl handle of request.
     * @return string A JSON response.
     */
    protected function exec_curl($curl): string
    {
        return curl_exec($curl);
    }
}
