<?php

declare(strict_types=1);

namespace KiH;

final class Parser
{
    public function parseFolder(string $json) : array
    {
        $data = $this->decode($json);

        if (!isset($data['value'])) {
            throw new Exception('The folder representation does not contain the "value" element');
        }

        return array_map(function (array $file) {
            $file['createdDateTime'] = new \DateTime($file['createdDateTime']);

            return $file;
        }, $data['value']);
    }

    public function parseItem(string $json) : array
    {
        return $this->decode($json);
    }

    private function decode(string $json) : array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Cannot decode directory data: ' . json_last_error_msg());
        }

        return $data;
    }
}
