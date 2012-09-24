<?php

namespace EC\Bundle\VagrantBundle\Command;

class Validators
{
    static public function validateBox($box, array $boxes = array())
    {
        switch (true) {
            case empty($box):
                throw new \InvalidArgumentException('Box must be defined');

            case empty($boxes):
                return $box;

            case is_numeric($box):
                $choices    = array_keys($boxes);
                $key        = $box - 1;

                if (isset($choices[$key])) {
                    $name = $choices[$key];

                    return $boxes[$name];
                } else {
                    throw new \InvalidArgumentException('Invalid choice');
                }

            case in_array($box, $boxes):
                return $boxes[$box];

            case filter_var($box, FILTER_VALIDATE_URL):
                if ('box' === pathinfo($box, PATHINFO_EXTENSION)) {
                    return $box;
                } else {
                    throw new \InvalidArgumentException('Box URL must have .box extension');
                }

            default:
                throw new \InvalidArgumentException('Invalid box');
        }
    }

    static public function validateHost($host)
    {
        if (empty($host)) {
            throw new \InvalidArgumentException('Host cannot be empty');
        }

        return $host;
    }

    static public function validateIp($ip)
    {
        $validated  = filter_var($ip, FILTER_VALIDATE_IP);
        $public     = filter_var($validated, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE);

        if (empty($ip)) {
            throw new \InvalidArgumentException('IP Address must be defined');
        } elseif (!$validated) {
            throw new \InvalidArgumentException('IP Address is not a valid format');
        } elseif ($public) {
            throw new \InvalidArgumentException('IP Address cannot be public');
        }

        return $validated;
    }

    static public function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}
