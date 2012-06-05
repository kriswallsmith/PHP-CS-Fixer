<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class VisibilityAltFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $content = preg_replace_callback('/^    ((?:(?:public|protected|private|static|var) +)+) *(\$[a-z0-9_]+)/im', function ($matches) {
            $flags = explode(' ', strtolower(trim($matches[1])));
            $parts = array();

            // static
            if (in_array('static', $flags)) {
                $parts[] = 'static';
            }

            // visibility
            if (in_array('protected', $flags)) {
                $parts[] = 'protected';
            } elseif (in_array('private', $flags)) {
                $parts[] = 'private';
            } else {
                $parts[] = 'public';
            }

            return '    '.implode(' ', $parts).' '.$matches[2];
        }, $content);

        $content = preg_replace_callback('/^    ((?:(?:public|protected|private|static|abstract|final) +)*)(function +[a-z0-9_]+.*)(;|{)/im', function ($matches) {
            $flags = explode(' ', strtolower(trim($matches[1])));
            $parts = array();

            // abstract
            if (in_array('abstract', $flags)) {
                $parts[] = 'abstract';
            }

            // final
            if (in_array('final', $flags)) {
                $parts[] = 'final';
            }

            // static
            if (in_array('static', $flags)) {
                $parts[] = 'static';
            }

            // visibility
            if (in_array('protected', $flags)) {
                $parts[] = 'protected';
            } elseif (in_array('private', $flags)) {
                $parts[] = 'private';
            } elseif (in_array('abstract', $flags) || ';' !== $matches[3]) {
                $parts[] = 'public';
            }

            if ($prefix = implode(' ', $parts)) {
                $prefix .= ' ';
            }

            return '    '.$prefix.$matches[2].$matches[3];
        }, $content);

        return $content;
    }

    public function getLevel()
    {
        return 0;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'visibility-alt';
    }

    public function getDescription()
    {
        return 'Visibility must be declared on all properties and methods; abstract and final must be declared before the visibility; static must be declared after the visibility.';
    }
}
