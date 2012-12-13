<?php

namespace EC\Bundle\VagrantBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * @author Eric Clemmons <eric@smarterspam.com>
 */
class VagrantGenerator extends Generator
{
    private $skeletonDir;

    public function __construct($skeletonDir)
    {
        $this->skeletonDir = $skeletonDir;
    }

    static public function generateIp()
    {
        $blocks = array(
            array('10.0.0.0', '10.255.255.255'),
            array('172.16.0.0', '172.31.255.255'),
            array('192.168.0.0', '192.168.255.255'),
        );

        $block  = $blocks[array_rand($blocks)];
        $range  = array_map('ip2long', $block);

        $long   = rand(current($range) + 1, end($range) - 1);
        $ip     = long2ip($long);

        return $ip;
    }

    public function generate($dir, $context)
    {
        $file       = sprintf('%s/Vagrantfile', $dir);
        $success    = $this->renderFile($this->skeletonDir, 'Vagrantfile', $file, $context);

        if ($success) {
            return array($file);
        } else {
            throw new \Exception('Unable to generate '.$file);
        }
    }
}
