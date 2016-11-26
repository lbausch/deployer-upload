<?php

namespace Deployer;

use Deployer\Task\Context;
use Symfony\Component\Finder\Finder;

if (!function_exists('upload_dir')) {

    /**
     * Upload directory to current server.
     *
     * @param string $local
     * @param string $remote
     * @param array  $options
     *
     * @throws \RuntimeException
     */
    function upload_dir($local, $remote, $options = [])
    {
        $options = array_merge([
            'ignore_unreadable_dirs' => true,
            'ignore_vcs' => true,
            'ignore_dot_files' => false,
        ], $options);

        $server = Context::get()->getServer();

        $local = parse($local);

        if (!is_dir($local)) {
            throw new \RuntimeException("Directory '$local' does not exist.");
        }

        $remote = parse($remote);

        writeln("Upload from <info>$local</info> to <info>$remote</info>");

        $finder = new Finder();

        $files = $finder->files()
            ->ignoreUnreadableDirs($options['ignore_unreadable_dirs'])
            ->ignoreVCS($options['ignore_vcs'])
            ->ignoreDotFiles($options['ignore_dot_files'])
            ->in($local);

        /** @var $file \Symfony\Component\Finder\SplFileInfo */
        foreach ($files as $file) {
            $server->upload(
                $file->getRealPath(),

                $remote.DIRECTORY_SEPARATOR.$file->getRelativePathname()
            );
        }
    }
}
