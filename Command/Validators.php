<?php

namespace EC\Bundle\VagrantBundle\Command;

use EC\Bundle\VagrantBundle\Entity\Box;
use EC\Bundle\VagrantBundle\Collection\BoxCollection;

/**
 * @author Eric Clemmons <eric@smarterspam.com>
 */
class Validators
{
    static public function validateBox($name, BoxCollection $boxes)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Box must be defined');
        }

        if (is_numeric($name) && $box = $boxes->getChoice($name)) {
            return $box;
        }

        if ($box = $boxes->get($name)) {
            return $box;
        }

        if ($url = filter_var($name, FILTER_VALIDATE_URL)) {
            return Box::fromUrl($url);
        }

        throw new \InvalidArgumentException('Invalid box');
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
