<?php

declare(strict_types=1);

namespace veryfi\classify;

trait ExtractDocument
{
    /**
     * Classify a document and extract data when its type is supported.
     * https://docs.veryfi.com/api/classify-and-possibly-extract-data-from-a-document/
     *
     * @param array $parameters Complete documented request body.
     * @return string Classification and extraction response JSON.
     */
    public function extract_document(array $parameters): string
    {
        return $this->request('POST', '/extract/', $parameters);
    }
}
