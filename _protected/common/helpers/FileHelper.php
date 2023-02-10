<?php

namespace common\helpers;

use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\UploadedFile;

/**
 * Class BaseFilerService
 */
abstract class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * Gets dir path, if dir path not exists creates it. Resolves path alias if need.
     *
     * @param string $dirPath Dir path.
     *
     * @return string Returns resolved exist dir path.
     *
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getResolvedDirPath(string $dirPath): string
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $dirPath = Yii::getAlias($dirPath);

        if (!file_exists($dirPath)) {
            $created = FileHelper::createDirectory($dirPath);
            if (!$created) {
                throw new InvalidConfigException('Can not create directory ' . $dirPath);
            }
        }

        return $dirPath;
    }

    /**
     * Generates full path from and to. Appends `$name` to `$fromPath` and to `$toPath`.
     *
     * @param string $fromPath From path.
     * @param string $toPath To path.
     * @param string $name Name to append ro paths.
     *
     * @return array Returns array of `[$fullFromPath, $fullToPath]`.
     */
    public function getFullFromTo(string $fromPath, string $toPath, string $name): array
    {
        $fullFrom = rtrim($fromPath, '/') . '/' . $name;
        $fullTo = rtrim($toPath, '/') . '/' . $name;

        return [$fullFrom, $fullTo];
    }
    /**
     * Moves (rename) files or directory.
     *
     * > Note: Method does not checks paths on valid.
     * You can use before [[BaseFilerService::getResolvedDirPath()]] to validate, resolve and if need create path.
     *
     * @param string $fromResolved Full path from.
     * @param string $toResolved Full path to.
     *
     * @return bool Returns `true` on success move, otherwise returns `false`.
     */
    public function move(string $fromResolved, string $toResolved)
    {
        $moved = rename($fromResolved, $toResolved);

        return $moved;
    }

    /**
     * Deletes file or directory (recursively) if exist.
     *
     * @param string $fullPathTo Path to file or directory.
     *
     * @throws ErrorException
     */
    public function deleteIfExist(string $fullPathTo): void
    {
        if (!file_exists($fullPathTo)) {
            return;
        }

        if (is_dir($fullPathTo)) {
            self::removeDirectory($fullPathTo);
        } else {
            unlink($fullPathTo);
        }
    }
}
